<?php

/**
 * Transport
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

namespace Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Standard;

use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\User;
use Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Transport as CampaignTransport;
use eZINI;
use eZExtension;
use eZDebug;
use eZMail;
use eZLog;

abstract class Transport extends CampaignTransport {

    /**
     * Send a Campaign to his receivers
     * @param Campaign $campaign
     */
    protected function _sendCampaign( Campaign $campaign ) {
        $ezMailingIni = eZINI::instance( 'ezmailing.ini' );
        $logFile      = $ezMailingIni->variable( 'LogSettings', 'SendingFile' );

        $campaign->setAttribute( 'state', Campaign::SENDING_IN_PROGRESS );
        $campaign->store();
        eZLog::write( "Start sending mailing {$campaign->attribute('subject')} (id:{$campaign->attribute('id')})", $logFile );

        $transport       = $this->_getMailTransport();
        $recipientsCount = $campaign->getCountRegistrations();
        $registrations   = $campaign->getRegistrations();
        $mailToSend      = $this->_wrapCampaignInMail( $campaign );

        $mailBegin = clone $mailToSend;
        $mailBegin->setSubject( "[START] {$mailBegin->Subject}" );
        $mailBegin->setBody( $this->_treatContent( $campaign ) );
        $transport->sendMail( $mailBegin );
        unset( $mailBegin );

        $sendingCounter = 0;
        while( $sendingCounter < $recipientsCount ) {
            $mail = clone $mailToSend;
            $user = $registrations[$sendingCounter]->getUser();
            $mail->setBody( $this->_treatContent( $campaign ) );
            $mail->setReceiver( $user->getEmail(), $user->getRecipientName() );
            $mail->setBccElements( array() );

            $transport->sendMail( $mail );
            $sendingCounter++;
        }

        $mailEnd = clone $mailToSend;
        $mailEnd->setSubject( "[END] {$mailEnd->Subject}" );
        $mailEnd->setBody( $this->_treatContent( $campaign ) );
        $transport->sendMail( $mailEnd );
        unset( $mailEnd );

        $campaign->setAttribute( 'state', Campaign::SENT );
        $campaign->store();
        eZLog::write( "Sending finish with success", $logFile );
        return true;
    }

    /**
     * Send Campaign to the test receivers
     * @param Campaign $campaign
     * @param          $aRecipients
     */
    protected function _sendCampaignForTest( Campaign $campaign, $aRecipients ) {
        $transport = $this->_getMailTransport();
        $mail      = $this->_wrapCampaignInMail( $campaign );
        $mail->setBody(  $this->_treatContent( $campaign)  );
        $mail->setCcElements( $aRecipients );

        return $transport->sendMail( $mail );
    }

    /**
     * Wrap a Campaign in a eZMail object
     * @param Campaign $item
     * @return eZMail
     */
    protected function _wrapCampaignInMail( Campaign $item ) {
        $ezMailingIni = eZINI::instance( 'ezmailing.ini' );

        $mail = new eZMail();
        $mail->setContentType( "text/html" );
        $mail->setSender( $item->attribute( 'sender_email' ), $item->attribute( 'sender_name' ) );
        $mail->setSubject( $item->attribute( 'subject' ) );

        // set the main recipients
        $mainRecipients = $ezMailingIni->variable( 'MailingSettings', 'MainRecipients' );
        if ( !is_array( $mainRecipients ) || ( sizeof( $mainRecipients ) == 0 ) ) {
            $mainRecipients = array( array( 'email' => $item->attribute( 'sender_email' ),
                                            'name'  => $item->attribute( 'sender_name' ) ) );
        } else {
            $reFormatedRecipients = array();
            foreach( $mainRecipients as $mainRecipient ) {
                $reFormatedRecipients[] = array( 'email' => $mainRecipient );
            }
            $mainRecipients = $reFormatedRecipients;
        }
        $mail->setReceiverElements( $mainRecipients );
        $mail->setBccElements( $this->getTestEmails() );

        return $mail;
    }

    /**
     * @inheritance
     */
    protected function _treatContent( Campaign $item ) {
        $content     = parent::_treatContent( $item );
        $siteINI     = eZINI::instance( 'site.ini' );
        $siteUrl     = $siteINI->variable( "SiteSettings", "SiteURL" );
        $mailingID   = $item->attribute( 'id' );
        $uniqId      = uniqid( 'ezmailing-', true );
        $readMarker  = '<img src="http://' . $siteUrl . '/mailing/read/' . $mailingID . '/' . $uniqId . '" width="1" height="1" />';
        $continueUrl = 'http://' . $siteUrl . '/mailing/continue/' . $mailingID . '/' . $uniqId . '/';
        $content     = preg_replace_callback( '/<a(.[^>]*)href="http(.[^"]*)"/um', function ( $aInput ) use ( $continueUrl ) {
            return "<a" . $aInput[1] . "href='" . $continueUrl . base64_encode( 'http' . $aInput[2] ) . "'";
        }, $content );

        $search  = array( // some regex
            '/<\/body>/um' );
        $replace = array( // some replacement
            "$readMarker</body>" );
        $content = preg_replace( $search, $replace, $content );
        return $content;
    }

    protected function _removeRegistration( Registration $registration ) {
        // nothing to do in this native transport
    }

    protected function _cancelCampaign( Campaign $campaign ) {
        // nothing to do in this native transport
    }

    protected function _checkCampaignState( Campaign $campaign ) {
        // nothing to do in this native transport
    }

    protected function _checkCampaignExist( Campaign $campaign ) {
        // nothing to do in this native transport
    }

    protected function _synchronizeMailingList( MailingList $mailingList ) {
        // nothing to do in this native transport
    }

    protected function _isProvider() {
        // nothing to do in this native transport
        return false;
    }

    protected function _getBounce( Campaign $campaign ) {
        // nothing to do in this native transport
    }

    protected function _updateMemberByEmail( $user, $aField ) {
        // nothing to do in this native transport
    }

    protected function _sendReportForCampaign( Campaign $campaign ) {
        // nothing to do in this native transport
    }

    protected function _createCampaign( Campaign $campaign ) {
        // nothing to do in this native transport
    }

    protected function _createSegment( array $parameters ) {
        // nothing to do in this native transport
    }

    protected function _createCriteria( $segmentID, array $parameters ) {
        // nothing to do in this native transport
    }

    protected function _createCampaignResend( Campaign $campaign, $segmentID ) {
        // nothing to do in this native transport
    }
}

?>
