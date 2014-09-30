<?php
/**
 * Check and change the state of a remote mailing
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Processors\BounceBack as BounceBackProcessor;

/**
 * @var eZCli                $cli
 */

if ( !$isQuiet ) {
    $cli->output( "Retrieve Bounces Back from Campaigns." );
}

$processor = new BounceBackProcessor( $isQuiet ? null : $cli );
$processor();

if ( !$isQuiet ) {
    $cli->output( "Done." );
}
