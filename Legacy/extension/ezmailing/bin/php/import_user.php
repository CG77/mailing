#!/usr/bin/env php
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

use Novactive\eZPublish\Extension\eZMailing\Core\Utils\MailingListUserImport;

$cli = eZCLI::instance();

$script = eZScript::instance(
    array(
        'description' => "eZMailing mailinglist user import.\n\n",
        'use-session' => true,
        'use-modules' => true,
        'use-extensions' => true
    )
);

$import = new MailingListUserImport( $script, $cli );
$import->run();

$script->shutdown( 0 );

?>