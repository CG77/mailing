<?php
/**
 * Bootstrap for eZ Mailing Module/Action
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

$http        = eZHTTPTool::instance();
$Module      = $Params["Module"];
$tpl         = eZTemplate::factory();
$subAction   = $Params["subaction"];
$currentView = $Module->currentView();
$itemID      = $Params["id"];

if ( isset( $Params['Offset'] ) ) {
    $offset = (int)$Params['Offset'];
}
$viewParameters = array( 'offset' => $offset );
$tpl->setVariable( 'view_parameters', $viewParameters );

use Novactive\eZPublish\Extension\eZMailing\Core\Utils\Audit;
Audit::trace( "[Bootstrap] ", array( "uri" => $_SERVER['REQUEST_URI'] ) );