<?php
/**
 * Handling Mailing Lists Action of eZ Mailing
 *
 * eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>

 * @link      http://www.novactive.com
 *
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;

/**
 * @var Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList $item
 * @var eZHTTPTool                                                      $http
 * @var eZTemplate                                                      $tpl
 * @var eZModule                                                        $Module
 */

/**
 * StoreMailingAction
 */

if ( $Module->isCurrentAction( "StoreMailingAction" ) ) {
    $aErrors = array();
    $name    = trim( $http->postVariable( "name" ) );
    $lang    = $http->postVariable( "lang" );

    if ( empty( $name ) ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must fill the name of the mailing list.' );
    }

    if ( empty( $lang ) ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must fill the language of the mailing list.' );
    }

    $item->setAttribute( 'name', $name );
    $item->setAttribute( 'lang', $lang );

    if ( sizeof( $aErrors ) > 0 ) {
        $tpl->setVariable( 'errors', $aErrors );
    } else {
        $item->setAttribute( 'draft', 0 );
        $item->store();
        $tpl->setVariable( 'success_message', ezpI18n::tr( 'extension/ezmailing/text', 'This item was successfully stored.' ) );
    }
}

/**
 * CreateMailingListAction
 */
if ( $Module->isCurrentAction( "CreateMailingListAction" ) ) {
    $mailing = MailingList::create();
    $mailing->setAttribute( 'draft', 1 );
    $mailing->store();
    return $Module->redirectModule( $Module, "mailinglists", array( 'edit', $mailing->attribute( 'id' ) ) );
}

/**
 * RemoveMailingListsAction
 */
if ( $Module->isCurrentAction( "RemoveMailingListsAction" )  or ( $currentView == "mailinglists" && isset( $subAction ) && $subAction == "delete" ) ) {

    if ( $subAction == "delete" ) {
        $itemsIDs = array( $itemID );
    } else {
        $itemsIDs = $http->postVariable( 'itemsActionCheckbox' );
    }

    $aErrors = array();
    $success = array();
    foreach( $itemsIDs as $it ) {
        $mailing = MailingList::novaFetchByKeys( $it );
        if ( $mailing instanceof MailingList ) {

            $nb = $mailing->getCampaignCount();
            if ( $nb > 0 ) {
                $aErrors[] = $mailing->name . " " . ezpI18n::tr( 'extension/ezmailing/text', 'cannot be removed because this campaign is currently used.' );
            } else {
                $success[] = $mailing->name;
                $mailing->remove();
            }
        }
    }

    if ( sizeof( $aErrors ) > 0 ) {
        $tpl->setVariable( 'errors', $aErrors );
        $tpl->setVariable( 'title_errors', ezpI18n::tr( 'extension/ezmailing/text', 'Failed to delete' ) );
    }

    if ( sizeof( $success ) > 0 ) {
        $tpl->setVariable( 'success_message', ezpI18n::tr( 'extension/ezmailing/text', 'These items were successfully deleted :' ) . " " . implode( ',', $success ) );
    }
}

/**
 * OrderMailingListAction
 */
if ( $Module->isCurrentAction( "OrderMailingListAction" ) ) {
    $tpl->setVariable( 'sort_by', array( $http->postVariable( 'order_field' ) => $http->postVariable( 'order_dir' ) ) );
}

/**
 * Synchronize Registation to Remote
 */
if ( $Module->isCurrentAction( 'SynchronizeRegistrationsAction' ) ) {
    $db     = eZDB::instance();
    $params = array( "mailinglist_id" => $item->attribute( "id" ) );
    $item->setAttribute( "state", MailingList::SYNCHRONISATION_IN_PROGRESS );
    $item->store();
    $db->query( "INSERT INTO ezpending_actions( action, created, param ) VALUES ( '" . MailingList::SYNCHRONISATION_ACTION . "', " . time() . ", '" . serialize( $params ) . "' )" );
    $tpl->setVariable( 'success_message', ezpI18n::tr( 'extension/ezmailing/text', 'The mailing list was successfully stored. This list will be synchronized in a few minutes.' ) );
}

/**
 * SearchMailingListAction
 */

if ( $Module->isCurrentAction( "SearchMailingListAction" ) ) {
    return $Module->redirectModule( $Module, "mailinglists", array( 'search',  $http->postVariable( 'SearchMailingListText' ) ) );
}