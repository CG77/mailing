<?php
/**
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 *
 */

require 'autoload.php';

use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;

$cli                              = eZCLI::instance();
$scriptSettings                   = array();
$scriptSettings['description']    = 'Remove all provider informations';
$scriptSettings['use-session']    = true;
$scriptSettings['use-modules']    = true;
$scriptSettings['use-extensions'] = true;
$script                           = eZScript::instance( $scriptSettings );
$script->startup();
$script->initialize();
$cli->output( '== Remove all provider informations ==' );

//@todo: Performance optimization here : make on sql queries

$campaigns = Campaign::novaFetchObjectList();
foreach( $campaigns as $campaign ) {
    $campaign->setAttribute( 'remote_id', '' );
    $campaign->setAttribute( 'last_synchro', '' );
    $campaign->store();
}

$mailinglists = MailingList::novaFetchObjectList();
foreach( $mailinglists as $mailinglist ) {
    $mailinglist->setAttribute( 'remote_id', '' );
    $mailinglist->setAttribute( 'last_synchro', '' );
    $mailinglist->setAttribute( 'count_remote_registration', 0 );
    $mailinglist->store();
}

$cli->output( 'Finished!' );
$script->shutdown( 0 );
?>
