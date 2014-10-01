<?php
/**
 * Cleanup some stuff
 *
 * eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>

 * @link      http://www.novactive.com
 *
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Key;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;

$draftClasses = array( 'Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign',
                       'Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList',
                       'Novactive\eZPublish\Extension\eZMailing\Core\Models\User' );

/**
 * @var eZCli                $cli
 */

if ( !$isQuiet ) {
    $cli->output( "Cleaning up eZ Mailing draft objects." );
}

$draftList = array();

foreach( $draftClasses as $class ) {
    $a_week_in_hours = 7 * 24;
    $draftList       = $class::novaFetchObjectList( array( 'draft'   => 1,
                                                           'updated' => array( '<', mktime( -1 * $a_week_in_hours ) ) ) );
    if ( count( $draftList ) > 0 ) {
        $cli->output( " - Cleaning up eZ Mailing draft " . $class . " objects." );
        foreach( $draftList as $draft ) {
            $draft->remove( true );
        }
        $cli->output( "     => cleaned." );
    } else {
        $cli->output( " - No draft for " . $class . " objects." );
    }
}

$cli->output( " - Cleaning up eZ Mailing Expired Registration objects." );

if ( !$isQuiet ) {
    $cli->output( "Done." );
}
