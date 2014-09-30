<?php
/**
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;

include( __DIR__ . "/bootstrap.php" );

$itemID    = $Params["id"];

if ( $itemID > 0 ) {
    $item = Campaign::novaFetchByKeys( $itemID );
    if ( $item instanceof Campaign ) {
        print $item->getContent();
    }
}
eZExecution::cleanExit();
?>