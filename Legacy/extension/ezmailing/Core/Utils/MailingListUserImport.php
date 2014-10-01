<?php
/**
 *
 * eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>
 * @copyright 2014 Novactive
 * @link      http://www.novactive.com
 *
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Utils;

use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\User;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;
use Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Transport;
use eZPendingActions;
use eZLog;
use eZMail;
use eZINI;
use eZDB;
use eZDBException;
use eZExecution;
use eZContentObject;
use eZContentCacheManager;
use eZUser;

class MailingListUserImport {

    private $CLI;
    private $Script;
    private $Options;
    private $Executable;
    private $ProcessLimit = 4;
    private $Limit = 200;

    private $dataFile;
    private $data;
    private $csvHeader;
    private $mailingListsID;
    private $mailingList;

    function __construct( $script, $cli ) {
        $this->Script = $script;
        $this->CLI = $cli;
        $this->Options = null;
        $this->Executable = "/usr/bin/php";

    }

    public function run() {
        $this->Script->startup();

        $this->Options = $this->Script->getOptions(
            "[process:][limit:][csv:][mailingid:]",
            "",
            array(
                'process'   => 'Parallelization, number of concurent processes to use',
                'limit'     => 'Number of csv line for one process',
                'csv'       => '*For internal use only*',
                'mailingid' => '*For internal use only*'
            )
        );

        $this->Script->initialize();

        if ( !empty( $this->Options["process"] ) ) {
            $this->ProcessLimit     = ( $this->Options["process"] <= 10 ) ? $this->Options["process"] : 10;
        }
        if ( !empty( $this->Options["limit"] ) ) {
            $this->Limit            = $this->Options["limit"];
        }
        if ( !empty( $this->Options["csv"] ) ) {
            $this->data             = unserialize( $this->Options["csv"] );
        }
        if ( !empty( $this->Options["mailingid"] ) ) {
            $this->mailingListsID   = explode( ",", $this->Options["mailingid"] );
        }

        if ( !empty( $this->data ) ) {
            $bloc = $this->data;
        }

        if ( !isset( $bloc ) ) {
            $this->CLI->output( 'Starting user import' );

            // First execution
            $actions = eZPendingActions::fetchByAction( MailingList::IMPORT_ACTION );

            foreach($actions as $key=>$action)
            {

                $params = unserialize( $action->attribute( 'param' ) );
                $this->filePath         = $params['file_path'];
                $this->mailingListsID   = $params['mailinglists'];

                $this->CLI->output("$key ) FilePath: ".$this->filePath.", mailingListsID: ".count($this->mailingListsID));

                if ( !empty( $this->filePath ) ) {
                    $this->runMain();
                    $action->remove();
                } else {
                    $this->CLI->error( 'Invalid parameters provided.' );
                   // $this->Script->shutdown( 2 );
                }
            }

            eZExecution::cleanExit();

        } elseif ( isset( $bloc ) && !empty( $this->mailingListsID ) ) {
            $this->CLI->output( 'Starting bloc import' );
            $this->runSubProcess( $bloc );
        } else {
            $this->CLI->error( 'Invalid parameters provided.' );
            $this->Script->shutdown( 2 );
        }
    }


    /**
     * Run main process
     */
    protected function runMain() {

        $useFork = ( function_exists( 'pcntl_fork' ) &&
            function_exists( 'posix_kill' ) );
        if ( $useFork ) {
            $this->CLI->output( 'Using fork.' );
        } else {
            $processLimit = 1;
        }

        $this->CLI->output( 'Using ' . $this->ProcessLimit . ' concurent process(es)' );

        $processList = array();
        $processListTime = array();
        for ( $i = 0; $i < $this->ProcessLimit; $i++ ) {
            $processList[$i] = -1;
            $processListTime[] = 0;
        }

        $this->dataFile = file( $this->filePath );
        if ( $this->dataFile === false ) {
            $this->CLI->error("Impossible d'ouvrir le fichier cible");
            return;
        }

        $userCount = count( $this->dataFile );
        $this->csvHeader = $this->dataFile[0];

        $this->CLI->output( 'Number of user to import: ' . $userCount );

        $this->mailingList = array();
        foreach( $this->mailingListsID as $it ) {
            $mailing = MailingList::novaFetchByKeys( $it );
            if ( $mailing instanceof MailingList ) {
                $this->mailingList[] = $mailing;
                // we forced the state in case of..
                $mailing->setAttribute( 'state', MailingList::IMPORT_IN_PROGRESS );
                $mailing->store();
            }
        }

        $offset = 0;

        while( $offset < $userCount )
        {
            // Loop trough the available processes, and see if any has finished.
            $a_pids = array();
            for ( $i = 0; $i < $this->ProcessLimit; $i++ )
            {
                $pid = pcntl_fork();

                if ($pid === -1)
                {
                    eZExecution::cleanExit();
                }
                elseif ($pid)
                {
                    $a_pids[] = $pid;
                    usleep(100000);
                }
                else
                {
                    $this->execute($offset, $this->Limit);

                    if ( $offset > $userCount ) { $percent = 100; }
                    else { $percent = (($offset+$this->Limit)/$userCount) * 100; }
                    $this->CLI->output('Executed : '.round($percent, 2).'%');

                    eZExecution::cleanup();
                    eZExecution::setCleanExit();
                    exit($i);
                }
                $offset += $this->Limit;

                if ( $offset >= $userCount )
                {
                    break;
                }
            }

            while (pcntl_waitpid(0, $status) != -1) {
                $status = pcntl_wexitstatus($status);
            }


            unset( $GLOBALS['eZContentObjectContentObjectCache'] );
            unset( $GLOBALS['eZContentObjectDataMapCache'] );
            unset( $GLOBALS['eZContentObjectVersionCache'] );
            unset( $GLOBALS['eZContentClassAttributeCache'] );

            if ( $offset >= $userCount )
            {
                break;
            }

        }

        // reopen db connexion to update mailinglist and remove action
        $db = eZDB::instance();
        $db->close();
        $db = null;
        eZDB::setInstance( null );

        foreach( $this->mailingList as $mailing ) {
            $mailing->setAttribute( 'state', MailingList::AVAILABLE );
            $mailing->store();
        }

        rename( $this->filePath, str_replace( "_waiting", "_parsed", $this->filePath ) );

        $this->CLI->output( 'Finished importing the user.' );


        return;
    }


    /**
     * Execute import
     *
     * @param int $offset
     * @param int $limit
     *
     * @return int Number of user imported.
     */
    protected function execute( $offset, $limit ) {

        // use DB exceptions so that errors can be fully handled

            eZDB::instance();
            $blocCsv = array_slice( $this->dataFile, $offset, $limit );
            if ( $offset > 0 ) {
                array_unshift( $blocCsv, $this->csvHeader );
            }

            return $this->runSubProcess( $blocCsv );
    }

    protected function runSubProcess( $data )
    {

        eZDB::setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );

        try {
            $db = eZDB::instance();
            $db->close();
            $db = null;
            eZDB::setInstance( null );

            $index        = 0;
            $indexEmail   = -1;
            $indexOrigin  = -1;
            $entete       = array();
            $mailingList  = array();
            $ezMailingIni = eZINI::instance( 'ezmailing.ini' );
            $logFile      = $ezMailingIni->variable( 'LogSettings', 'SendingFile' );

            foreach( $data as $raw )
            {
                if ( empty( $raw ) )
                {
                    continue;
                }

                $raw = utf8_encode( $raw );
                $row = explode( ';', $raw );
                if ( $index == 0 && count( $row ) > 0 ) {
                    foreach( $row as $idx => $field ) {
                        $field        = trim( $field );
                        $entete[$idx] = $field;

                        if ( $field == 'email' ) {
                            $indexEmail = $idx;
                        }
                        if ( $field == 'origin' ) {
                            $indexOrigin = $idx;
                        }
                    }
                    $index++;
                } elseif ( count( $row ) > 0 && count( $row ) == count( $entete ) && $indexEmail >= 0 ) {

                    $email  = str_replace(CHR(13).CHR(10),"",trim( $row[$indexEmail] ));
                    $origin = $indexOrigin >= 0 ? str_replace(CHR(13).CHR(10),"",trim( $row[$indexOrigin] )) : '';
                    $User   = User::fetchByEmail( $email );

                    if ( eZMail::validate( $email ) ) {
                        if ( !$User instanceof User ) {
                            $User = User::create( array( 'email'  => $email,
                                                         'origin' => ( $origin == '' ) ? "import" : $origin,
                                                         'draft'  => 0 ) );
                        }
                        foreach( $entete as $idx => $field ) {
                            if ( $User->hasAttribute( $field ) && ( $idx != $indexEmail ) && ( $idx != $indexOrigin ) ) {
                                $User->setAttribute( $field, str_replace(CHR(13).CHR(10),"",$row[$idx]) );
                            }
                        }
                        $User->store();
                        foreach( $this->mailingList as $mailing ) {
                            $reg = Registration::create( array( 'mailing_user_id' => $User->attribute( 'id' ),
                                                                'mailinglist_id'  => $mailing->attribute( 'id' ),
                                                                'state'           => Registration::REGISTRED,
                                                                'state_updated'   => time(), ) );
                            $reg->store();
                        }
                    } else {
                        eZLog::write( "The email {$email} is not a valid format and cannot be import.", $logFile );
                    }
                }
            }

            return 0;
        } catch ( eZDBException $e ) {
            try {
                $db = eZDB::instance();
                $db->close();
                $db = null;
                eZDB::setInstance( null );
            } catch(  eZDBException $e ) {}

            sleep( 1 );
        }
    }
}

?>