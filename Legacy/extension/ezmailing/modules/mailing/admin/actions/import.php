<?php
/**
 * Handling Users Action of eZ Mailing
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Models\User;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;
use eZDir;

/**
 * @var User                                                     $item
 * @var eZHTTPTool                                               $http
 * @var eZTemplate                                               $tpl
 * @var eZModule                                                 $Module
 */

/**
 * StoreUserAction
 */

if ( $Module->isCurrentAction( "MailingImportAction" ) ) {
    $aErrors             = array();
    $ini                 = eZINI::instance( 'ezmailing.ini' );
    $target_pathSettings = $ini->variable( 'PathImport', 'Path' );
    $target_path         = eZSys::storageDirectory() . "/" . $target_pathSettings;

    if ( $http->hasPostVariable( 'itemsActionCheckbox' ) ) {
        $itemsIDs = $http->postVariable( 'itemsActionCheckbox' );
    } else {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must choose at least one mailing list to import users.' );
    }

    $allowedMimeTypes = array( "application/vnd.ms-excel", "text/csv" );

    if ( !isset( $_FILES['input_file_import'] ) || !in_array( $_FILES['input_file_import']['type'], $allowedMimeTypes ) ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must select a CSV file.' );
    } else {
        eZDir::mkdir( $target_path, false, true );
        $target_path .= "/" . $_FILES['input_file_import']['name'] . date( 'Ymd_his' ) . '_waiting';
        if ( !move_uploaded_file( $_FILES['input_file_import']['tmp_name'], $target_path ) ) {
            $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', "The CSV file cannot be uploaded." );
        }
    }

    // check of first row
    $handle = fopen( $target_path, "r" );
    $buffer = fgets( $handle, 4096 );
    $row = explode( ';', trim( $buffer ) );
    fclose( $handle );
    $availableTypes = $ini->variable( 'MailingUserAccountSettings', 'Attributes' );
    $availableTypesKey = array_keys( $availableTypes );
    foreach( $row as $headerField ) {
        if ( !in_array( trim($headerField), $availableTypesKey ) ) {
            $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', "The field %fieldname doesn't exist in your configuration.", null , array( '%fieldname' => $headerField ) );
            throw new Exception();
        }
    }

    if ( sizeof( $aErrors ) > 0 ) {
        $tpl->setVariable( 'errors', $aErrors );
    } else {
        $db     = eZDB::instance();

        $params = array( "mailinglists" => $itemsIDs,
                     "file_path"    => $target_path );
        foreach( $itemsIDs as $mailingID ) {
            $m = MailingList::novaFetchByKeys( $mailingID );
            $m->setAttribute( "state", MailingList::IMPORT_IN_PROGRESS );
            $m->store();
        }
        $db->query( "INSERT INTO ezpending_actions( action, created, param ) VALUES ( '" . MailingList::IMPORT_ACTION . "', " . time() . ", '" . serialize( $params ) . "' )" );
        $tpl->setVariable( 'success_message', ezpI18n::tr( 'extension/ezmailing/text', 'Your file has been uploaded. This file will be imported into your mailing lists in a few minutes.' ) );

    }
}
