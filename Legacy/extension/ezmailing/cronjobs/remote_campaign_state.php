<?php
/**
 * Check and change the state of a remote mailing
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Processors\RemoteState as RemoteStateProcessor;

/**
 * @var eZCli                $cli
 */

if ( !$isQuiet ) {
    $cli->output( "Check State eZ Mailing remote objects." );
}

$processor = new RemoteStateProcessor( $isQuiet ? null : $cli );
$processor();

if ( !$isQuiet ) {
    $cli->output( "Done." );
}
