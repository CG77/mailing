<?php
/**
 * Read : Mark as read.
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Utils\Browser;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Stat;

$http   = eZHTTPTool::instance();
$Module = $Params["Module"];
$id     = $Params["id"];
$key    = $Params["key"];

$item = Campaign::novaFetchByKeys( $id );

if ( $item instanceof Campaign ) {
    $browser = Browser::instance();
    $stat    = Stat::create();
    $stat->setAttribute( 'url', Stat::OPENED_STATKEY );
    $stat->setAttribute( 'clicked', time() );
    $stat->setAttribute( 'campaign_id', $id );
    $stat->setAttribute( 'user_key', $key );
    $stat->setAttribute( 'os_name', $browser->getPlatform() );
    $stat->setAttribute( 'browser_name', $browser->getName() );
    $stat->store();
}

/* return the pixel */
header( "Content-Type: image/png" );
$im    = imagecreatetruecolor( 1, 1 );
$black = imagecolorallocate( $im, 0, 0, 0 );
imagecolortransparent( $im, $black );
imagepng( $im );
imagedestroy( $im );

eZExecution::cleanExit();