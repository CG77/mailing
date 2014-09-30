<?php
/**
 * Automaticaly create campaign depending user rules
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Processors\Automate as AutomateProcessor;

/**
 * @var eZCli                $cli
 */

if ( !$isQuiet ) {
    $cli->output( "Create automated campaigns." );
}

$processor = new AutomateProcessor( $isQuiet ? null : $cli );
$processor();

if ( !$isQuiet ) {
    $cli->output( "Done." );
}

?>