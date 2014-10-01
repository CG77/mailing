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

include( __DIR__ . "/bootstrap.php" );

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;

/**
 * @var eZTemplate $tpl
 */

include( __DIR__ . "/actions.php" );

switch( $subAction ) {
    case "registred":
    case "unregistred":
    case "orphan":
    case "blocked":
    case "hardbounce":
    case "softbounce":
        $templateName = "list";
        if ( $subAction == "registred" ) {
            $state = Registration::REGISTRED;
        }
        if ( $subAction == "unregistred" ) {
            $state = Registration::UNREGISTRED;
        }
		if ( $subAction == "orphan" ) {
            $state = Registration::PENDING;
        }
        if ( $subAction == "blocked" ) {
            $state = Registration::BLOCKED;
        }
        if ( $subAction == "hardbounce" ) {
            $state = Registration::HARD_BOUNCE_BLOCKED;
        }
        if ( $subAction == "softbounce" ) {
            $state = Registration::SOFT_BOUNCE_BLOCKED;
        }
        $path = array(
                    array( 'url'  => 'mailing/registrations', 'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Registrations' ) ),
                    array( 'url'  => 'mailing/registrations/' . $subAction, 'text' => ezpI18n::tr( 'extension/ezmailing/text', Registration::$aStates[$state] ) )
        );
        $tpl->setVariable( 'state_filter', $state );
        break;
    case "search":
        $templateName = "search";
        $itemIDClean = strip_tags( $itemID );
        $path         = array(
                            array( 'url'  => 'mailing/registrations','text' => ezpI18n::tr( 'extension/ezmailing/text', 'Registration' ) ),
                            array( 'url'  => 'mailing/registrations/search/' . $itemIDClean, 'text' => 'search' )
                    );
        $tpl->setVariable( 'search_text', $itemIDClean );
        $tpl->setVariable( 'search_fields', Registration::novaGetSearchFields() );
        break;
    default:
        $templateName = "list";
        $path         = array( array( 'url'  => 'mailing/registrations',
                                      'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Registrations' ) ) );
        break;
}

$viewParameters = array( 'order_field' => $Params["order_field"],
                         'order_dir'   => $Params["order_dir"],
                         'offset'      => $Params["Offset"] );

$tpl->setVariable( 'view_parameters', $viewParameters );

if ( $Params["order_field"] && $Params["order_dir"] ) {
    $tpl->setVariable( 'sort_by', array( $Params["order_field"] => $Params["order_dir"] ) );
}

$tpl->setVariable( 'path', $path );
$tpl->setVariable( 'subaction', $subAction );

$Result['content'] = $tpl->fetch( 'design:mailing/registrations/' . $templateName . '.tpl' );
$Result['path']    = $path;


?>