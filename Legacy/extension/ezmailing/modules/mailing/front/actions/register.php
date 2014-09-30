<?php
/**
 * Register : Description.
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\User;
use Novactive\eZPublish\Extension\eZMailing\Core\Utils\SiteAccess;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Key;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Utils\Mail;

/**
 * @var Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign $item
 * @var eZHTTPTool                                                   $http
 * @var eZTemplate                                                   $tpl
 * @var eZModule                                                     $Module
 */

$templateVars = array();

if ( isset( $Params['key'] ) ) {
    $Key = Key::fetchByHashKey( $Params['key'] );
    if ( $Key instanceof Key ) {
        if ( $Key->attribute( 'time' ) <= time() ) {
            $Key->remove();
            $templateVars['confirmation'] = ezpI18n::tr( 'extension/ezmailing/text', 'Your activation key is invalid.' );
        } else {
            $keyParams = $Key->getParams();

            $User = User::fetchByEmail( $keyParams['email'] );

            $mailingListNames = array();
            foreach( $keyParams['mailingListIDs'] as $mailingListID ) {
                $registration = Registration::fetchByUserAndMailingID( $User->attribute( 'id' ), $mailingListID );
                if ( $registration->isPending() ) {
                    $registration->setAttribute( 'state', Registration::REGISTRED );
                    $registration->store();

                    $mailingList        = MailingList::novaFetchByKeys( $mailingListID );
                    $mailingListNames[] = $mailingList->attribute( 'name' );
                }
            }

            $templateVars['confirmation']     = ezpI18n::tr( 'extension/ezmailing/text', 'Registration succeed.' );
            $templateVars['mailingListNames'] = $mailingListNames;

            $Key->remove();
        }
    }else{
        $templateVars['confirmation'] = 'Votre inscription à la newsletter a été déjà confirmée';
    }
}

// This action can be called by eZJSCore
if ( ( isset( $Module ) && $Module->isCurrentAction( "MailingRegisterAction" ) ) || ( $eZJSCoreModuleAction == "MailingRegisterAction" ) ) {

    $errors      = array();
    $email       = $http->postVariable( "email" );
    $mailingList = $http->postVariable( "MailingListID" );

    $ezMailingIni       = eZINI::instance( 'ezmailing.ini' );
    $attributes         = (array)$ezMailingIni->variable( 'MailingUserAccountSettings', 'Attributes' );
    $attributesRequired = (array)$ezMailingIni->variable( 'MailingUserAccountSettings', 'RequiredAttributes' );

    foreach( $attributesRequired as $identifier ) {
        if ( ( !$http->hasVariable( $identifier ) ) || ( trim( $http->postVariable( $identifier ) ) == "" ) ) {
            $errors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must fill the ' . $attributes[$identifier] . '.' );
        }
    }

    if ( !eZMail::validate( $email ) ) {
        $errors[] = ezpI18n::tr( 'kernel/classes/datatypes', 'The email address is not valid.' );
    }

    if ( sizeof( $mailingList ) <= 0 ) {
        $errors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must choose at least one mailing list.' );
    }

    if ( sizeof( $errors ) > 0 ) {
        $templateVars['errors'] = $errors;
    } else {
        // everything is ok
        $MailingUser = User::fetchByEmail( $email );
        if ( !$MailingUser instanceof User ) {
            $MailingUser = User::create( array( 'email' => $email,
                                                'origin'=> SiteAccess::current( "name" ) ) );
            foreach( $attributes as $identifier => $label ) {
                if ( ( $http->hasPostVariable( $identifier ) ) && ( trim( $http->postVariable( $identifier ) ) != "" ) ) {
                    $MailingUser->setAttribute( $identifier, trim( $http->postVariable( $identifier ) ) );
                }
            }
            $MailingUser->store();
        }

        $ini = eZINI::instance( 'ezmailing.ini' );
        if ( $ini->variable( "UserRegistration", "ConfirmRegister" ) == 'enabled' ) {
            $state = Registration::PENDING;

            // Create key
            $time                     = $ini->variable( "UserRegistration", "ValidationTime" ) + time();
            $params                   = array();
            $params['email']          = $MailingUser->attribute( 'email' );
            $params['mailingListIDs'] = $mailingList;
            $hash                     = sha1( $params['email'] . $time );

            $Key = Key::create( array( 'hash_key'  => $hash,
                                       'time'      => $time,
                                       'params'    => serialize( $params ) ) );
            $Key->store();

            $newsletters = array();
            foreach( $params["mailingListIDs"] as $mailingListID ) {
                $mailingListForKey = MailingList::novaFetchByKeys( $mailingListID );
                $newsletters[]     = $mailingListForKey->attribute( 'name' );
            }

            $mail = Mail::wrapMail( $params['email'], 'mail/registerconfirmation.tpl', array( 'newsletters'   => $newsletters,
                                                                                              'key'           => $hash,
                                                                                              'user'          => $MailingUser, ), ezpI18n::tr( 'extension/ezmailing/design/standard/mail', 'Confirm your subscription' ) );

            eZMailTransport::send( $mail );

            $templateVars['confirmation'] = ezpI18n::tr( 'extension/ezmailing/text', 'Registration in progress, a confirmation mail was sent to your email adress.' );
        } else {
            $state                        = Registration::REGISTRED;
            $templateVars['confirmation'] = ezpI18n::tr( 'extension/ezmailing/text', 'Registration succeed.' );
        }

        foreach( $mailingList as $idMailing ) {
            $Registration = Registration::create( array( 'mailing_user_id' => $MailingUser->attribute( 'id' ),
                                                         'mailinglist_id'  => $idMailing,
                                                         'state'           => $state,
                                                         'state_updated'   => time(), ) );
            $Registration->store();
        }
    }
}