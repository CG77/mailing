<?php
/**
 * Bootstrap for eZ Mailing Module/Action
 *
 * eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>

 * @link      http://www.novactive.com
 *
 */

$http   = eZHTTPTool::instance();
$Module = $Params["Module"];
$tpl    = eZTemplate::factory();

