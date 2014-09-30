<?php
/**
 * Handling Users Action of eZ Mailing
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Transport;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\User;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;

use Novactive\eZPublish\Extension\eZMailing\Core\Utils\SiteAccess;
use Novactive\eZPublish\Extension\eZMailing\Core\Utils\Audit;

/**
 * @var Novactive\eZPublish\Extension\eZMailing\Core\Models\User $item
 * @var eZHTTPTool                                               $http
 * @var eZTemplate                                               $tpl
 * @var eZModule                                                 $Module
 */

/**
 * StoreUserAction
 */

if ( $Module->isCurrentAction( "StoreUserAction" ) ) {
    $aErrors                       = array();
    $email                         = $http->postVariable( "email" );
    $haveToBeRegistredMailingArray = array();
    if ( $http->hasPostVariable( "registrations" ) ) {
        $haveToBeRegistredMailingArray = $http->postVariable( "registrations" );
    }

    $ezMailingIni       = eZINI::instance( 'ezmailing.ini' );
    $attributes         = (array)$ezMailingIni->variable( 'MailingUserAccountSettings', 'Attributes' );
    $attributesRequired = (array)$ezMailingIni->variable( 'MailingUserAccountSettings', 'RequiredAttributes' );

    foreach( $attributesRequired as $identifier ) {
        if ( ( !$http->hasVariable( $identifier ) ) || ( trim( $http->postVariable( $identifier ) ) == "" ) ) {
            $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must fill the field %attribute.', null, array( '%attribute' => ezpI18n::tr( 'extension/ezmailing/text', $attributes[$identifier] ) ) );
        }
    }

    if ( empty( $email ) ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must fill the user email.' );
    } elseif ( !eZMail::validate( $email ) ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'The email syntax is not correct.' );
    }

    /* check email unicity */
    $emailUser = User::fetchByEmail( $email );
    if ( ( $emailUser ) && $emailUser->attribute( 'id' ) != $item->attribute( 'id' ) ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'This email address is already use.' );
    }
    unset( $emailUser );
    unset( $cond );

    /* If email is modified and user got registration, update old */
    if ( sizeof( $aErrors ) == 0 && $email != $item->attribute( 'email' ) && sizeof( $item->getMailingLists() ) > 0 ) {
        Transport::updateMember( $item, array( 'name'  => 'SEGMENT',
                                               'value' => '' ) );
    }

    foreach( $attributes as $identifier => $label ) {
        if ( $http->hasPostVariable( $identifier ) ) {
            $item->setAttribute( $identifier, trim( $http->postVariable( $identifier ) ) );
        }
    }

    if ( $http->hasPostVariable( "mailing_datetime_day_mailing" ) && $http->hasPostVariable( "mailing_datetime_month_mailing" ) && $http->hasPostVariable( "mailing_datetime_year_mailing" )
    ) {

        $day   = (int)$http->postVariable( "mailing_datetime_day_mailing" );
        $month = (int)$http->postVariable( "mailing_datetime_month_mailing" );
        $year  = (int)$http->postVariable( "mailing_datetime_year_mailing" );

        $birthday = mktime( 0, 0, 0, $month, $day, $year );

        $item->setAttribute( 'birthday', $birthday );
    }

    if ( $item->attribute( 'email' ) && $email != $item->attribute( 'email' ) ) {
        Transport::updateMember( $item, array( 'name'  => 'email', 'value' => $email ) );
    }

    $item->setAttribute( 'email', $email );

    if ( sizeof( $aErrors ) > 0 ) {
        $tpl->setVariable( 'errors', $aErrors );
    } else {
        $item->setAttribute( 'draft', 0 );

        /* remap the registrations */
        $mailings = MailingList::novaFetchObjectList();
        foreach( $mailings as $mailing ) {
            /**
             * @var Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList $mailing
             */
            // getregistration for this user and this mailing
            $reg = $item->getRegistrationByMailingId( $mailing->attribute( 'id' ) );
            if ( is_array( $haveToBeRegistredMailingArray ) && in_array( $mailing->attribute( 'id' ), $haveToBeRegistredMailingArray )
            ) {
                if ( $reg instanceof Registration ) {
                    if ( !$reg->isActive() ) {
                        $reg->setAttribute( 'state', Registration::REGISTRED );
                        $reg->setAttribute( 'state_updated', time() );
                        $reg->store();
                    }
                } else {
                    $reg = Registration::create( array( 'mailing_user_id' => $item->attribute( 'id' ),
                                                        'mailinglist_id'  => $mailing->attribute( 'id' ),
                                                        'state'           => Registration::REGISTRED,
                                                        'state_updated'   => time(), ) );
                    $reg->store();
                }
            } else {
                if ( ( $reg instanceof Registration ) && ( $reg->isActive() ) ) {
                    $reg->remove();
                }
            }
        }
        $item->store();
        $tpl->setVariable( 'success_message', ezpI18n::tr( 'extension/ezmailing/text', 'This item was successfully stored.' ) );
    }
}

/**
 * CreateUserAction
 */

if ( $Module->isCurrentAction( "CreateUserAction" ) ) {
    $user = User::create( array( 'origin' => SiteAccess::current( "name" ) ) );
    $user->setAttribute( 'draft', 1 );
    $user->store();
    return $Module->redirectModule( $Module, "users", array( 'edit', $user->attribute( 'id' ) ) );
}

/**
 * RemoveUsersAction
 */

if ( $Module->isCurrentAction( "RemoveUsersAction" )    or ( $currentView == "users" && isset( $subAction ) && $subAction == "delete" ) ) {

    if ( $subAction == "delete" ) {
        $itemsIDs = array( $itemID );
    } else {
        $itemsIDs = $http->postVariable( 'itemsActionCheckbox' );
    }
    foreach( $itemsIDs as $it ) {
        $user = User::novaFetchByKeys( $it );
        if ( $user instanceof User ) {
            Audit::trace( array( "[actions/users] {RemoveUsersAction}", array("userName" => $user->getName() ) ) );
            $registrations = $user->getRegistrations();
            foreach( $registrations as $registration ) {
                $registration->remove();
            }
            Transport::updateMember( $user, array( 'name'  => 'SEGMENT',
                                                   'value' => '' ) );
            $user->remove();
            $tpl->setVariable( 'success_message', ezpI18n::tr( 'extension/ezmailing/text', 'These items were successfully deleted.' ) );
        }
    }
}

/**
 * SearchUserAction
 */
if ( $Module->isCurrentAction( "SearchUserAction" ) ) {
    return $Module->redirectModule( $Module, "users", array( 'search', $http->postVariable( 'SearchUserText' ) ) );
}