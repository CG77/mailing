<?php
/**
 *
 * eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>

 * @link      http://www.novactive.com
 *
 */
require 'autoload.php';

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;

$scriptName = "Monitoring of eZ Mailing";

$scriptSettings                   = array();
$scriptSettings['description']    = $scriptName;
$scriptSettings['use-session']    = true;
$scriptSettings['use-modules']    = true;
$scriptSettings['use-extensions'] = true;
$script                           = eZScript::instance( $scriptSettings );
$script->startup();
$script->initialize();

$output = new ezcConsoleOutput();
$input  = new ezcConsoleInput();

// Color Options
$output->formats->question->color     = 'cyan';
$output->formats->question->style     = array( 'bold' );
$output->formats->campaign->color     = 'blue';
$output->formats->campaign->style     = array( 'bold' );
$output->formats->subquestion->color  = 'white';
$output->formats->subquestion->style  = array( 'bold',
                                               'italic' );
$output->formats->done->color         = 'green';
$output->formats->done->style         = array( 'bold' );
$output->formats->error->color        = 'red';
$output->formats->fatal->color        = 'red';
$output->formats->fatal->style        = array( 'bold',
                                               'underlined' );
$output->formats->headline->color     = 'white';
$output->formats->headline->style     = array( 'bold' );
$output->formats->headBorder->color   = 'blue';
$output->formats->normalBorder->color = 'green';
$output->formats->errorBorder->color  = 'red';

$helpOption               = $input->registerOption( new ezcConsoleOption( 'h', 'help', ezcConsoleInput::TYPE_NONE, null, false, 'Print this help' ) );
$helpOption->isHelpOption = true;

$input->process();
$args = $input->getArguments();
if ( $helpOption->value === true ) {
    $output->outputLine( $input->getHelpText( $scriptName ) );
    $output->outputLine( "Done.", "done" );
    exit( 0 );
}

$output->outputLine( "========================", "headline" );
$output->outputLine( $scriptName, "headline" );
$output->outputLine( "========================", "headline" );

// Menus
$Menus = array( "o"       => "See one campaign",
                "w"       => "See waiting for sending campaign",
                "s"       => "See current sending campaign",
                "q"       => "Quit" );

$question                     = new ezcConsoleMenuDialog( $output );
$question->options            = new ezcConsoleMenuDialogOptions();
$question->options->format    = "question";
$question->options->text      = "What do you want ? :";
$question->options->validator = new ezcConsoleMenuDialogDefaultValidator( $Menus );

try {
    while( $choice != "q" ) {
        $choice = ezcConsoleDialogViewer::displayDialog( $question );
        switch( $choice ) {
            case "w":
                $campaigns      = Campaign::novaFetchObjectList( array( 'state'=> Campaign::WAITING_FOR_SEND ) );
                $campaignsCount = sizeof( $campaigns );
                $output->outputLine( "$campaignsCount campaigns found.", "done" );
                foreach( $campaigns as $oCampaign ) {
                    /**
                     * @var Campaign $oCampaign
                     */
                    $output->outputLine( "\t - {$oCampaign->attribute('subject')} [{$oCampaign->attribute('id')}]: {$oCampaign->getCountRegistrations()} recipients" );
                }
                if ( $campaignsCount == 0 ) {
                    $output->outputLine( "No waiting campaign found.", "done" );
                }
                break;
            case "s":
                $campaigns      = Campaign::novaFetchObjectList( array( 'state'=> Campaign::SENDING_IN_PROGRESS ) );
                $campaignsCount = sizeof( $campaigns );
                $output->outputLine( "$campaignsCount campaigns found.", "done" );
                foreach( $campaigns as $oCampaign ) {
                    /**
                     * @var Campaign $oCampaign
                     */
                    $output->outputLine( "\t - {$oCampaign->attribute('subject')} [{$oCampaign->attribute('id')}]: {$oCampaign->getCountRegistrations()} recipients" );
                }
                if ( $campaignsCount == 0 ) {
                    $output->outputLine( "No sending in progress campaign found.", "done" );
                }
                break;
            case "o":

                $campaignQuestion                  = new ezcConsoleQuestionDialog( $output );
                $campaignQuestion->options->format = "campaign";
                $campaignQuestion->options->text   = "Give the campaign id : ";
                $campaign_id                       = ezcConsoleDialogViewer::displayDialog( $campaignQuestion );

                $campaign = Campaign::novaFetchByKeys( $campaign_id );

                if ( !$campaign instanceof Campaign ) {
                    $output->outputLine( "Campaign not found ! ", "fatal" );
                    $script->shutdown( -1 );
                }

                $subMenus = array( "i" => "Informations",
                                   "rn"=> "Recipients Number",
                                   "d" => "Sending date",
                                   "r" => "Recipients",
                                   "q" => "Return" );

                $subQuestion                     = new ezcConsoleMenuDialog( $output );
                $subQuestion->options            = new ezcConsoleMenuDialogOptions();
                $subQuestion->options->format    = "subquestion";
                $subQuestion->options->text      = "What do you see ? :";
                $subQuestion->options->validator = new ezcConsoleMenuDialogDefaultValidator( $subMenus );

                while( $subChoice != "q" ) {
                    $subChoice = ezcConsoleDialogViewer::displayDialog( $subQuestion );

                    switch( $subChoice ) {
                        case "i":
                            $output->outputLine( $campaign );
                            break;
                        case "d":
                            $output->outputLine( date( "d/m/Y", $campaign->attribute( 'sending_date' ) ) );
                            break;
                        case "rn":
                            $recipientsCount = $campaign->getCountRegistrations();
                            $output->outputLine( "This campaign have {$recipientsCount} recipients", "done" );
                            break;
                        case "r":
                            $registrations = $campaign->getRegistrations();
                            foreach( $registrations as $registration ) {
                                /**
                                 * @var Registration $registration
                                 */
                                $user = $registration->getUser();
                                $output->outputLine( "\t - {$registration->getStateString()} : {$user->attribute('email')}" );
                            }
                            break;
                    }
                }
                $subChoice = false;
                break;
        }
        $output->outputLine( "-------\n" );
    }
} catch( Exception $e ) {
    $output->outputLine( $e->getMessage(), "fatal" );
    $script->shutdown( -1 );
}

$script->shutdown( 0 );