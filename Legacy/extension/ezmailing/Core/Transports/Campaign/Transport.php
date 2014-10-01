<?php

/**
 * Transport
 *
 ** eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>
 * @copyright 2014 Novactive
 * @link      http://www.novactive.com
 *
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign;

use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\User;

use eZINI;
use ezpExtensionOptions;
use eZExtension;
use eZDebug;
use eZLog;
use eZHTTPTool;

abstract class Transport {

    abstract protected function _getMailTransport();

    abstract protected function _sendCampaignForTest( Campaign $campaign, $aRecipients );

    abstract protected function _sendCampaign( Campaign $campaign );

    abstract protected function _removeRegistration( Registration $registration );

    abstract protected function _cancelCampaign( Campaign $campaign );

    abstract protected function _checkCampaignState( Campaign $campaign );

    abstract protected function _checkCampaignExist( Campaign $campaign );
    
    abstract protected function _synchronizeMailingList( MailingList $mailingList );

    abstract protected function _isProvider();

    abstract protected function _getBounce( Campaign $campaign );

    abstract protected function _updateMemberByEmail( $user, $aField );

    abstract protected function _sendReportForCampaign( Campaign $campaign );

    abstract protected function _createCampaign( Campaign $campaign );

    abstract protected function _createSegment( array $parameters );

    abstract protected function _createCriteria( $segmentID, array $parameters );

    abstract protected function _createCampaignResend( Campaign $campaign, $segmentID );


    protected static $_logFile = 'ezmailing-sending.log';

    /**
     * Get the good handler
     * @param $name
     * @param $params
     * @return bool
     */
    public static function __callstatic( $name, $params ) {
        $ini           = eZINI::instance();
        $transportType = trim( $ini->variable( 'MailSettings', 'CampaignTransport' ) );
        $optionArray   = array( 'iniFile'      => 'site.ini',
                                'iniSection'   => 'MailSettings',
                                'iniVariable'  => 'TransportCampaignAlias',
                                'handlerIndex' => strtolower( $transportType ) );
        $options       = new ezpExtensionOptions( $optionArray );
        /**
         * @var Transport $transportClass
         */
        $tabException   = array( 'createSegment',
                                 'createCriteria' );
        $transportClass = eZExtension::getHandlerClass( $options );
        if ( !is_object( $transportClass ) ) {
            eZDebug::writeError( "No class available for campaign transport type '$transportType', cannot send mail", __METHOD__ );
            return false;
        }
        $oeZMailing = null;
        if ( isset( $params[0] ) ) {
            $oeZMailing = $params[0];
        }
        if ( !in_array( $name, $tabException ) && $oeZMailing && !$oeZMailing instanceof Campaign && !$oeZMailing instanceof Registration && !$oeZMailing instanceof MailingList && !$oeZMailing instanceof User ) {
            eZDebug::writeError( "params[0] is not an eZMailing Object', cannot send mail", __METHOD__ );
            return false;
        }

        switch( $name ) {
            case "sendCampaign":
                if ( $oeZMailing->isRecurring() && $oeZMailing->attribute( 'content_type' ) == Campaign::DYN_CONTENT ) {
                    $content = Transport::getNativeContent( $oeZMailing );
                    $oeZMailing->setContent( $content );
                    $oeZMailing->store();
                    unset( $content );
                }
                return $transportClass->_sendCampaign( $oeZMailing );
                break;
            case "sendCampaignForTest":
                // re store of the native content
                if ( $oeZMailing->attribute( 'content_type' ) == Campaign::DYN_CONTENT ) {
                    $content = Transport::getNativeContent( $oeZMailing );
                    $oeZMailing->setContent( $content );
                    $oeZMailing->store();
                    unset( $content );
                }
                return $transportClass->_sendCampaignForTest( $oeZMailing, $params[1] );
                break;
            case "removeRegistration":
                $transportClass->_removeRegistration( $oeZMailing );
                break;
            case "cancelCampaign":
                $transportClass->_cancelCampaign( $oeZMailing );
                break;
            case "getNativeContent":
                return $transportClass->_getNativeContent( $oeZMailing );
            case "checkCampaignState":
                return $transportClass->_checkCampaignState( $oeZMailing );
            case "checkCampaignExist":
                return $transportClass->_checkCampaignExist( $oeZMailing );
                break;
            case "synchronizeSegment":
                return $transportClass->_synchronizeMailingList( $oeZMailing );
                break;
            case "isProvider":
                return $transportClass->_isProvider();
            case "getBounce":
                return $transportClass->_getBounce( $oeZMailing );
            case "updateMember":
                $transportClass->_updateMemberByEmail( $oeZMailing, $params[1] );
                break;
            case "sendReportForCampaign":
                return $transportClass->_sendReportForCampaign( $oeZMailing );
                break;
            case "createCampaign":
                return $transportClass->_createCampaign( $oeZMailing, $params[1] );
                break;
            case "createCampaignResend":
                return $transportClass->_createCampaignResend( $oeZMailing, $params[1] );
                break;
            case "createSegment":
                return $transportClass->_createSegment( $params[0] );
                break;
            case "createCriteria":
                return $transportClass->_createCriteria( $params[0], $params[1] );
                break;
            default;
                return false;
                break;
        }
    }

    /**
     * Treat the content by adding some rewrite url and tag
     * @param Campaign $item
     * @return mixed
     */
    protected function _treatContent( Campaign $item ) {
        return $item->getContent();
    }

    /**
     * Get native the content
     * @param Campaign $item
     * @return mixed
     */
    protected function _getNativeContent( Campaign $item ) {
        $http = eZHTTPTool::instance();

        // if campaign non static campaing alors
        if ( $item->attribute( 'content_type' ) == Campaign::DYN_CONTENT ) {
            $content = eZHTTPTool::getDataByURL( $item->getDynamicUrl() );
        } else {
            $content = $http->postVariable( 'manual_content' );
        }
        // else
        // on prend juste le html dans le champs (qui aura était placé dans l'edition)

        $search  = array( '/#WEBVIEW_LINK#/' );
        $replace = array( $item->getCampaignUrl() );
        $content = preg_replace( $search, $replace, $content );
        return $content;
    }

    /**
     * Return the paramated Email tests
     * @return array
     */
    protected function getTestEmails() {
        $ezMailingIni = eZINI::instance( 'ezmailing.ini' );
        $testEmails   = $ezMailingIni->variable( 'MailingSettings', 'TestEmails' );
        $elements     = array();
        foreach( $testEmails as $email ) {
            $elements[] = array( 'email' => $email );
        }
        return $elements;
    }
}

?>
