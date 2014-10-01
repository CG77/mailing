<?php
/**
 * Bootstrap for eZ Mailing Module/Action
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