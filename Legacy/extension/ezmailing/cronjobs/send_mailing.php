<?php
/**
 * Send mailing if it's ok.
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Processors\Sending as SendingProcessor;

/**
 * @var eZCli                $cli
 */

if ( !$isQuiet ) {
    $cli->output( "Sending eZ Mailing objects." );
}

$processor = new SendingProcessor( $isQuiet ? null : $cli );
$processor();

if ( !$isQuiet ) {
    $cli->output( "Done." );
}
