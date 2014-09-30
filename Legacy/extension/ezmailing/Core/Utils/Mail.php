<?php

/**
 * Mail
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Utils;

use eZINI;
use eZTemplate;
use eZSys;
use eZMail;

class Mail {

    /**
     * Generate an eZMail
     * @param      $emailReceiver
     * @param      $template
     * @param      $tplVarArray
     * @param bool $subject
     * @return eZMail
     */
    public static function wrapMail( $emailReceiver, $template, $tplVarArray, $subject = false ) {
        $ini      = eZINI::instance();
        $tpl      = eZTemplate::factory();
        $hostname = eZSys::hostname();

        $mail = new eZMail();

        $tpl->resetVariables();

        foreach( $tplVarArray as $var => $value ) {
            $tpl->setVariable( $var, $value );
        }

        $tpl->setVariable( 'hostname', $hostname );

        $layoutContent = $tpl->fetch( 'design:mail/layout.tpl' );
        $contentResult = $tpl->fetch( 'design:' . $template );

        $templateResult = str_replace( "#EZMAIL_CONTENT#", $contentResult, $layoutContent );

        if ( $tpl->hasVariable( 'content_type' ) ) {
            $mail->setContentType( $tpl->variable( 'content_type' ) );
        }

        $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
        if ( $tpl->hasVariable( 'email_sender' ) ) {
            $emailSender = $tpl->variable( 'email_sender' );
        } else {
            if ( !$emailSender ) {
                $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
            }
        }

        if ( $tpl->hasVariable( 'subject' ) ) {
            $subject = $tpl->variable( 'subject' );
        } elseif ( !$subject ) {
            $subject = $hostname;
        }

        $mail->setSender( $emailSender );
        $mail->setReceiver( $emailReceiver );
        $mail->setSubject( $subject );
        $mail->setBody( $templateResult );

        return $mail;
    }
}