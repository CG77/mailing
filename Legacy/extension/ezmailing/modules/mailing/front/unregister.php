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

include( __DIR__ . "/bootstrap.php" );

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\User;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Key;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Utils\Mail;
use Novactive\eZPublish\Extension\eZMailing\Core\Utils\Audit;

/**
 * @var eZTemplate $tpl
 * @var eZHttpTool $http
 * @var User       $MailingUser
 */

$path = array( array( 'url'  => 'mailing/unregister',
                      'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Unregister' ) ) );

if ( isset( $Params['key'] ) ) {

    $Key = Key::fetchByHashKey( $Params['key'] );

    if ( is_object( $Key ) ) {

        if ( $Key->attribute( 'time' ) <= time() ) {
            $Key->remove();
        } else {
            $keyParams = $Key->getParams();

            $User = User::fetchByEmail( $keyParams['email'] );

            $mailingListNames = array();
            foreach( $keyParams['mailingListIDs'] as $mailingListID ) {
                $registration = Registration::fetchByUserAndMailingID( $User->attribute( 'id' ), $mailingListID );

                if ( ( $registration instanceof Registration ) && ( $registration->attribute( 'state' ) != Registration::UNREGISTRED ) ) {
                    $registration->setAttribute( 'state', Registration::UNREGISTRED );
                    $registration->store();

                    $mailingList        = MailingList::novaFetchByKeys( $mailingListID );
                    $mailingListNames[] = $mailingList->attribute( 'name' );
                }
            }

            $tpl->setVariable( "confirmation", ezpI18n::tr( 'extension/ezmailing/text', 'Unregistration succeed.' ) );
            $tpl->setVariable( "mailingListNames", $mailingListNames );

            $Key->remove();
        }
    }else{
        $tpl->setVariable( "confirmation", ezpI18n::tr( 'extension/ezmailing/text', 'Your activation key is invalid.' ) );
    }
}

if ( $Module->isCurrentAction( "MailingUnregisterAction" ) ) {

    $errors      = array();
    $email       = $http->postVariable( "email" );
    $mailingList = $http->postVariable( "MailingListID" );

    if ( !eZMail::validate( $email ) ) {
        $errors[] = ezpI18n::tr( 'kernel/classes/datatypes', 'The email address is not valid.' );
    }

    if ( sizeof( $mailingList ) <= 0 ) {
        $errors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must choose at least one mailing list.' );
    }

    $MailingUser = User::fetchByEmail( $email );
    if ( ( !$MailingUser instanceof User ) || ( sizeof( $MailingUser->getRegistrations() ) == 0 ) ) {
        $errors[] = ezpI18n::tr( 'extension/ezmailing/text', "You don't have any registration." );
    }

    if ( sizeof( $errors ) > 0 ) {
        $tpl->setVariable( "errors", $errors );
    } else {

        $ini = eZINI::instance( 'ezmailing.ini' );
        if ( $ini->variable( "UserRegistration", "ConfirmUnregister" ) == 'enabled' ) {

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
                $mailingList   = MailingList::novaFetchByKeys( $mailingListID );
                $newsletters[] = $mailingList->attribute( 'name' );
            }

            $mail = Mail::wrapMail( $params['email'], 'mail/unregisterconfirmation.tpl', array( 'newsletters'   => $newsletters,
                                                                                                'key'           => $hash,
                                                                                                'user'          => $MailingUser, ), ezpI18n::tr( 'extension/ezmailing/design/standard/mail', 'Confirm your unsubscription' ) );
            eZMailTransport::send( $mail );
            $tpl->setVariable( "confirmation", ezpI18n::tr( 'extension/ezmailing/text', 'Unregistration in progress, a confirmation mail was sent to your email adress.' ) );
        } else {
            // everything is ok
            foreach( $mailingList as $idMailing ) {
                $registration = $MailingUser->getRegistrationByMailingId( $idMailing );
                if ( $registration instanceof Registration ) {
                    if ( $registration->isActive() ) {
                        Audit::trace( "[front/unregister] {MailingUnregisterAction} remove| ", array( "maillingListId" => $idMailing ) );
                        $registration->remove();
                    }
                }
            }
            $tpl->setVariable( "confirmation", ezpI18n::tr( 'extension/ezmailing/text', 'Unregistration succeed.' ) );
        }
    }
} else {
    $email       = "";
    $mailingList = array();
}

$tpl->setVariable( 'fields', array( 'Email'         => $email,
                                    "MailingListID" => $mailingList ) );

$tpl->setVariable( 'path', $path );
$Result['content'] = $tpl->fetch( 'design:mailing_registration/unregister.tpl' );
$Result['path']    = $path;


?>