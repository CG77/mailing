<?php
/**
 * Check and change the state of a remote mailing
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
