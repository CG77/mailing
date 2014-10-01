<?php
/**
 * Campaign
 *
 ** eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>

 * @link      http://www.novactive.com
 *
 */
namespace Novactive\eZPublish\Extension\eZMailing\Core\Models;

use Novactive\eZPublish\Extension\eZMailing\Core\Utils\SiteAccess;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\ResendCampaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Stat;

use ezpI18n;
use eZDB;
use eZContentObjectTreeNode;
use eZPendingActions;

class Campaign extends PersistentObject {

    const DRAFT               = 10;
    const TESTED              = 20;
    const CONFIRMED           = 30;
    const WAITING_FOR_SEND    = 40;
    const SENDING_IN_PROGRESS = 50;
    const SENT                = 60;
    const CANCELLED           = 70;
    const DELETED             = 99;
    const DYN_CONTENT         = 0;
    const STATIC_CONTENT      = 1;


    const TABLE_NAME = "ezmailingcampaign";

    public static $aStates = array( self::DRAFT               => "Draft",
                                    self::TESTED              => "Tested",
                                    self::CONFIRMED           => "Confirmed",
                                    self::WAITING_FOR_SEND    => "Waiting for sending",
                                    self::SENDING_IN_PROGRESS => "Sending in progress",
                                    self::SENT                => "Sent",
                                    self::CANCELLED           => "Cancelled",
                                    self::DELETED             => "Deleted", );

    /**
     * Set up the definition
     */
    public static function definition() {
        static $def = array( 'fields'              => array( 'id'                       => array( 'name'     => 'id',
                                                                                                  'datatype' => 'integer',
                                                                                                  'default'  => 0,
                                                                                                  'required' => true ),
                                                             'subject'                  => array( 'name'     => 'subject',
                                                                                                  'datatype' => 'string',
                                                                                                  'default'  => '',
                                                                                                  'required' => false ),
                                                             'description'              => array( 'name'     => 'description',
                                                                                                  'datatype' => 'string',
                                                                                                  'default'  => '',
                                                                                                  'required' => false ),
                                                             'content'                  => array( 'name'     => 'content',
                                                                                                  'datatype' => 'string',
                                                                                                  'default'  => '',
                                                                                                  'required' => false ),
                                                             'sender_name'              => array( 'name'     => 'sender_name',
                                                                                                  'datatype' => 'string',
                                                                                                  'default'  => '',
                                                                                                  'required' => false ),
                                                             'sender_email'             => array( 'name'     => 'sender_email',
                                                                                                  'datatype' => 'string',
                                                                                                  'default'  => '',
                                                                                                  'required' => false ),
                                                             'report_email'             => array( 'name'     => 'report_email',
                                                                                                  'datatype' => 'string',
                                                                                                  'default'  => '',
                                                                                                  'required' => false ),
                                                             'content_type'             => array( 'name'     => 'content_type',
                                                                                                  'datatype' => 'integer',
                                                                                                  'default'  => 0,
                                                                                                  'required' => false ),
                                                             'node_id'                  => array( 'name'     => 'node_id',
                                                                                                  'datatype' => 'integer',
                                                                                                  'default'  => 0,
                                                                                                  'required' => false ),
                                                             'siteaccess'               => array( 'name'     => 'siteaccess',
                                                                                                  'datatype' => 'string',
                                                                                                  'default'  => '',
                                                                                                  'required' => false ),
                                                             'destination_mailing_list' => array( 'name'     => 'destination_mailing_list',
                                                                                                  'datatype' => 'string',
                                                                                                  'default'  => '',
                                                                                                  'required' => false ),
                                                             'sending_date'             => array( 'name'     => 'sending_date',
                                                                                                  'datatype' => 'integer',
                                                                                                  'default'  => 0,
                                                                                                  'required' => false ),
                                                             'recurrency_period'        => array( 'name'     => 'recurrency_period',
                                                                                                  'datatype' => 'integer',
                                                                                                  'default'  => 0,
                                                                                                  'required' => false ),
                                                             'state'                    => array( 'name'     => 'state',
                                                                                                  'datatype' => 'integer',
                                                                                                  'default'  => 0,
                                                                                                  'required' => false ),
                                                             'report_sent'              => array( 'name'     => 'report_sent',
                                                                                                  'datatype' => 'integer',
                                                                                                  'default'  => 0,
                                                                                                  'required' => false ),
                                                             'last_synchro'             => array( 'name'     => 'last_synchro',
                                                                                                  'datatype' => 'integer',
                                                                                                  'default'  => 0,
                                                                                                  'required' => false ),
                                                             'remote_id'                => array( 'name'     => 'remote_id',
                                                                                                  'datatype' => 'string',
                                                                                                  'default'  => '',
                                                                                                  'required' => false ),
                                                             'state_updated'            => array( 'name'     => 'state_updated',
                                                                                                  'datatype' => 'integer',
                                                                                                  'default'  => 0,
                                                                                                  'required' => false ),
                                                             'created'                  => array( 'name'     => 'created',
                                                                                                  'datatype' => 'integer',
                                                                                                  'default'  => 0,
                                                                                                  'required' => false ),
                                                             'updated'                  => array( 'name'     => 'updated',
                                                                                                  'datatype' => 'integer',
                                                                                                  'default'  => 0,
                                                                                                  'required' => false ),
                                                             'draft'                    => array( 'name'     => 'draft',
                                                                                                  'datatype' => 'integer',
                                                                                                  'default'  => 0,
                                                                                                  'required' => false ),
                                                             'updated'                  => array( 'name'     => 'updated',
                                                                                                  'datatype' => 'integer',
                                                                                                  'default'  => 0,
                                                                                                  'required' => false ),

                                                             'updated'                  => array( 'name'     => 'updated',
                                                                                                  'datatype' => 'integer',
                                                                                                  'default'  => 0,
                                                                                                  'required' => false ),

                                                             'updated'                  => array( 'name'     => 'updated',
                                                                                                  'datatype' => 'integer',
                                                                                                  'default'  => 0,
                                                                                                  'required' => false )

        ),
                             'keys'                => array( 'id' ),
                             'increment_key'       => "id",
                             'class_name'          => 'Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign',
                             'function_attributes' => array( "url"                            => "getUrl",
        													 "get_content"					  => "getContent",
                                                             "state_string"                   => "getStateString",
                                                             "state_string_stand"			  => "getStateStringStandard",
                                                             "mailing_lists"                  => "getMailingLists",
                                                             "campaign_url"                   => "getCampaignUrl",
                                                             "remoteid"                       => "getRemoteId",
                                                             "receivers_count"                => "getCountRegistrations",
                                                             "recurrency"                     => "getRecurencyInformations",
                                                             "resend_actions"                 => "getResendActions",
                                                             "scheduled"				      => "scheduled",
                                                             "node"                           => "node",

                                                             "is_draft"                       => "isDraft",
                                                             "is_tested"                      => "isTested",
                                                             "is_confirmed"                   => "isConfirmed",
                                                             "is_waiting_for_send"            => "isWaitingForSend",
                                                             "is_sending_in_progress"         => "isSendingInProgress",
                                                             "is_sent"                        => "isSent",
                                                             "is_cancelled"                   => "isCancelled",
                                                             "is_deleted"                     => "isDeleted",
                                                             "is_draft_succeed"               => "isDraftSucceed",
                                                             "is_tested_succeed"              => "isTestedSucceed",
                                                             "is_confirmed_succeed"           => "isConfirmedSucceed",
                                                             "is_waiting_for_send_succeed"    => "isWaitingForSendSucceed",
                                                             "is_sending_in_progress_succeed" => "isSendingInProgressSucceed",
                                                             "is_editable"                    => "isEditable"
                                                             ),
                             'name'                => self::TABLE_NAME );
        return $def;
    }

    /**
     * Return the system url of the object
     * @return string
     */
    public function getUrl() {
        return "/mailing/campaigns/view/" . $this->attribute( 'id' );
    }

    /**
     * Return the front url of the object
     * @return string
     */
    public function getCampaignUrl() {
        $siteAccessList = SiteAccess::getSiteaccess();
        $newsSiteAccess = $siteAccessList[$this->attribute( 'siteaccess' )];
        return $newsSiteAccess['siteAccessUrl'] . "/mailing/show/" . $this->attribute( 'id' ) . "/" . $this->attribute( 'subject' );
    }

    /**
     * Get the node
     * @return eZContentObjectTreeNode
     */
    public function node() {
        return eZContentObjectTreeNode::fetch( $this->attribute( 'node_id' ) );
    }

    // alias
    public function getNode() {
        return $this->node();
    }

    /**
     * Set compress content
     * @param $content
     */
    public function setContent( $content ) {
        $this->setAttribute( "content", base64_encode( gzcompress( $content ) ) );
    }

    /**
     * Get the uncompressed content
     * @return string
     */
    public function getContent() {
        return gzuncompress( base64_decode( $this->attribute( 'content' ) ) );
    }

    /**
     * Return the string state of the campaign
     * @return string
     */
    public function getStateString() {
        return ezpI18n::tr( 'extension/ezmailing/text', static::$aStates[$this->attribute( 'state' )] );
    }

    /**
     * Return the string state eng of the campaign
     * @return string
     */
    public function getStateStringStandard() {
        return static::$aStates[$this->attribute( 'state' )];
    }

    /**
     * Return the url of the campaign
     * @return string
     */
    public function getDynamicUrl() {
        $siteAccessList = SiteAccess::getSiteaccess();
        $newsSiteAccess = $siteAccessList[$this->attribute( 'siteaccess' )];

        $version = \eZSiteData::fetchByName( 'ezpublish-version' );
        if ( preg_match( '/5\.[0-9]/', $version->attribute( 'value' ) ) ) {
            $ini = \eZINI::instance( 'site.ini.append.php', 'settings/siteaccess/cg77_front' );
            $node = eZContentObjectTreeNode::fetch($this->attribute( 'node_id' ));
            $urlAlias = $node->attribute('url_alias');
            $urlAlias = implode( '/',array_diff( explode ( '/' , $urlAlias ), array( $ini->variable( 'SiteAccessSettings', 'PathPrefix' ) ) ) );
            return $newsSiteAccess['siteAccessUrl'] . "/" . $urlAlias;
        } else {
            return $newsSiteAccess['siteAccessUrl'] . "/layout/set/mailing/content/view/mailing/" . $this->attribute( 'node_id' );
        }
    }

    /**
     * Return the remote id of the campaign
     * @return string
     */
    public function getRemoteId() {
        if ( preg_match( '/([0-9]+)/', $this->attribute( 'remote_id' ), $results ) ) {
            return $results[1];
        }
        return false;
    }

    /**
     * Get the mailing lists destination of the campaign
     * @return array
     */
    public function getMailingLists() {
        $mailings_list = explode( ":", $this->attribute( "destination_mailing_list" ) );
        $aReturn       = array();
        foreach( $mailings_list as $ml ) {
            if ( $ml ) {
                $aReturn[] = MailingList::novaFetchByKeys( $ml );
            }
        }
        return $aReturn;
    }

    /**
     * @return bool
     */
    public function isAllMailingListsAvailable() {
        $mailings_list = explode( ":", $this->attribute( "destination_mailing_list" ) );
        $db            = eZDB::instance();
        $result        = $db->arrayQuery( "SELECT id from " . MailingList::TABLE_NAME . " WHERE id in (" . implode( ",", $mailings_list ) . ") AND state != " . MailingList::AVAILABLE . " " );

        return ( !sizeof( $result ) );
    }

    /**
     * Get the number of registrations
     * @return number
     */
    public function getCountRegistrations() {
        $sum           = 0;
        $mailingsLists = $this->getMailingLists();
        foreach( $mailingsLists as $ml ) {
            $sum += $ml->attribute( "registrations_count" );
        }
        return $sum;
    }

    /**
     * Get the registrations
     * @return array
     */
    public function getRegistrations() {
        $registrations = array();
        $mailingsLists = $this->getMailingLists();
        foreach( $mailingsLists as $ml ) {
            $registrations = array_merge( $registrations, $ml->getRegistrations() );
        }
        return $registrations;
    }

    /**
     * if the email to send the report campaign exists
     * @return bool
     */
    public function hasReportEmail() {
        if ( $this->attribute( 'report_email' ) != "" ) {
            return true;
        }
        return false;
    }

    /**
     * Override of remove method to change the state on time before the true deleted
     * @see eZPersistentObject::remove()
     */
    public function remove( $conditions = null, $extraConditions = null ) {
        if ( is_null( $conditions ) ) {
            $forceRemove = false;
        } else {
            $forceRemove = true;
        }
        if ( ( $this->attribute( 'state' ) == static::DELETED ) || ( $forceRemove == true ) ) {
            parent::remove();
        } else {
            $this->setAttribute( 'state', static::DELETED );
            $this->setAttribute( 'state_updated', time() );
            $this->store();
        }
    }

    /**
     * Override of remove method to change the state on time before the true deleted
     * @param MailingList $mailingList
     */
    public function removeDestination( MailingList $mailingList ) {
        $oldDestinations = explode( ':', $this->attribute( 'destination_mailing_list' ) );
        $keeped          = array();
        foreach( $oldDestinations as $mdestId ) {
            if ( $mdestId != $mailingList->attribute( 'id' ) ) {
                $keeped[] = $mdestId;
            }
        }
        $this->setAttribute( 'destination_mailing_list', implode( ':', $keeped ) );
        $this->setAttribute( 'state_updated', time() );
        $this->store();
    }

    /**
     * Remove Stats of a campaign
     */
    public function removeStats() {
        $db = eZDB::instance();
        $db->query( "DELETE FROM " . Stat::TABLE_NAME . " WHERE campaign_id = {$this->attribute( 'id' )}" );
    }

    /**
     * Get the next sending date for a reccuring campaign
     * @return int
     */
    protected function getNextSendingDate() {
        return (int)$this->attribute( "sending_date" ) + (int)$this->attribute( "recurrency_period" );
    }

    /**
     * Clone and change the state
     */
    public function __clone() {
        $this->setAttribute( 'remote_id', '' );
        if ( $this->isRecurring() ) {
            $this->setAttribute( "id", 0 );
            $this->setAttribute( "sending_date", $this->getNextSendingDate() );
            $this->setAttribute( 'state', static::WAITING_FOR_SEND );
            $this->setAttribute( 'state_updated', time() );
            $this->setContent( '' );
        }
    }

    /**
     * get informations about period of recurrency
     * @return array
     */
    public function getRecurencyInformations() {
        $time             = (int)$this->attribute( "recurrency_period" );
        $weekModePossible = false;

        $weekLimit = 3600 * 24 * 7;
        $dayLimit  = 3600 * 24;

        if ( ( $time / $dayLimit ) % 7 == 0 ) {
            $weekModePossible = true;
        }

        if ( ( $time >= $weekLimit ) && ( $weekModePossible ) ) {
            $period = "week";
            $value  = intval( $time / $weekLimit );
        } else {
            $period = "day";
            $value  = intval( $time / ( 3600 * 24 ) );
        }
        return array( "period" => $period,
                      "value"  => $value );
    }

    /**
     * get resend actions scheduled
     * @return array
     */
    public function getResendActions() {
        $actions = ResendCampaign::novaFetchObjectList( array( 'campaign_id' => $this->attribute( 'id' ),
                                                               'sort_by'     => array( 'last_update' => 'desc' ) ) );
        return $actions;
    }

    /**
     * get pending action
     * @return bool
     */
    public function scheduled() {
        $actions = eZPendingActions::fetchObjectList( eZPendingActions::definition(), null, array( 'param' => serialize( array( 'campaign_id' => $this->attribute( "id" ) ) ) ) );
        return sizeof( $actions );
    }

    public function __toString() {
        $str  = "(" . get_called_class() . ")\n";
        $defs = static::definition();
        foreach( $defs['fields'] as $identifier => $value ) {

            $fieldName = $value['name'];
            if ( $identifier == "content" ) {
                $fieldValue = "{binary}";
            } else {
                $fieldValue = $this->attribute( $identifier );
            }
            $str .= "\t{$fieldName} = {$fieldValue} \n";
        }
        return $str;
    }

    /**
     * State methods
     * @return boolean
     */
    public function isDraft() {
        return $this->attribute( 'state' ) == static::DRAFT;
    }

    public function isDraftSucceed() {
        return $this->attribute( 'state' ) > static::DRAFT;
    }

    public function isTested() {
        return $this->attribute( 'state' ) == static::TESTED;
    }

    public function isTestedSucceed() {
        return $this->attribute( 'state' ) > static::TESTED;
    }

    public function isConfirmed() {
        return $this->attribute( 'state' ) == static::CONFIRMED;
    }

    public function isConfirmedSucceed() {
        return $this->attribute( 'state' ) > static::CONFIRMED;
    }

    public function isWaitingForSend() {
        return $this->attribute( 'state' ) == static::WAITING_FOR_SEND;
    }

    public function isWaitingForSendSucceed() {
        return $this->attribute( 'state' ) > static::WAITING_FOR_SEND;
    }

    public function isSendingInProgress() {
        return $this->attribute( 'state' ) == static::SENDING_IN_PROGRESS;
    }

    public function isSendingInProgressSucceed() {
        return $this->attribute( 'state' ) > static::SENDING_IN_PROGRESS;
    }

    public function isSent() {
        return $this->attribute( 'state' ) == static::SENT;
    }

    public function isCancelled() {
        return $this->attribute( 'state' ) == static::CANCELLED;
    }

    public function isDeleted() {
        return $this->attribute( 'state' ) == static::DELETED;
    }

    public function isEditable() {
        return $this->attribute( 'state' ) > static::WAITING_FOR_SEND;
    }

    /**
     * If the campaign is recurring
     * @return bool
     */
    public function isRecurring() {
        return $this->attribute( "recurrency_period" ) > 0;
    }

    /**
     * If the report of the campaign is already sent
     * @return bool
     */
    public function isReportSent() {
        return $this->attribute( "report_sent" ) == 1;
    }
}