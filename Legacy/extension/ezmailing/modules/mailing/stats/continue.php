<?php
/**
 * Continue : Forward and stat
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
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Stat;
use Novactive\eZPublish\Extension\eZMailing\Core\Utils\Browser;

$http       = eZHTTPTool::instance();
$Module     = $Params["Module"];
$encodedUrl = $Params["url"];
$id         = $Params["id"];
$key        = $Params["key"];

$urlContinue = base64_decode( $encodedUrl );
$item        = Campaign::novaFetchByKeys( $id );

if ( $item instanceof Campaign ) {
    $browser = Browser::instance();
    $stat    = Stat::create();
    $stat->setAttribute( 'url', $urlContinue );
    $stat->setAttribute( 'clicked', time() );
    $stat->setAttribute( 'campaign_id', $id );
    $stat->setAttribute( 'user_key', $key );
    $stat->setAttribute( 'os_name', $browser->getPlatform() );
    $stat->setAttribute( 'browser_name', $browser->getName() );
    $stat->store();
}
header( "Location: $urlContinue" );
eZExecution::cleanExit();