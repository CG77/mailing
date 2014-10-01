<?php
/**
 *
 * eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>

 * @link      http://www.novactive.com
 *
 */

require 'autoload.php';

use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;

$cli                              = eZCLI::instance();
$scriptSettings                   = array();
$scriptSettings['description']    = 'Test eZ Mailing';
$scriptSettings['use-session']    = true;
$scriptSettings['use-modules']    = true;
$scriptSettings['use-extensions'] = true;
$script                           = eZScript::instance( $scriptSettings );
$script->startup();
$script->initialize();
$cli->output( '== Test eZ Mailing ==' );

$xml = simplexml_load_file( "extension/ezmailing/translations/fre-FR/translation.ts" );

$doublons = array();

$passed = array();
foreach( $xml->context->message as $message ) {
    $mess = (string)$message->source;
    $hash = md5( $mess );

    if ( in_array( $hash, $passed ) ) {
        print $mess."\n";
    }
    $passed[] = $hash;
}

$cli->output( 'Finished!' );
$script->shutdown( 0 );
?>
