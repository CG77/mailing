<?php
/**
 * Handle pending actions
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Processors\PendingActions as PendingActionsProcessor;

/**
 * @var eZCli                $cli
 */

if ( !$isQuiet ) {
    $cli->output( "Pending eZ Mailing actions." );
}

$processor = new PendingActionsProcessor( $isQuiet ? null : $cli );
$processor();

if ( !$isQuiet ) {
    $cli->output( "Done." );
}
