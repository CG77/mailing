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