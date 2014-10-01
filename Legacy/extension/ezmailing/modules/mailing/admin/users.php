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

use Novactive\eZPublish\Extension\eZMailing\Core\Models\User;

/**
 * @var eZTemplate $tpl
 */

if ( $itemID > 0 ) {
    $item = User::novaFetchByKeys( $itemID );
}


include( __DIR__ . "/actions.php" );

switch( $subAction ) {
    case "view":
        $templateName = "view";
        $path         = array(
                            array( 'url'  => 'mailing/users', 'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Users' ) ),
                            array( 'url'  => 'mailing/users/view/' . $itemID, 'text' => $item->attribute( 'name' ) )
        );
        $tpl->setVariable( 'item', $item );
        break;
    case "edit":
        $templateName = "edit";
        $path         = array(
                            array( 'url'  => 'mailing/users', 'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Users' ) ),
                            array( 'url'  => 'mailing/users/view/' . $itemID, 'text' => $item->attribute( 'name' ) )
        );
        $tpl->setVariable( 'item', $item );
        break;
    case "orphan":
        $templateName = "list";
        $path         = array( array( 'url'  => 'mailing/usersorphan', 'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Orphan users' ) ) );
        $tpl->setVariable( 'fetchkey', "orphanusers" );
        break;
    case "search":
        $templateName = "search";
        $itemIDClean = strip_tags( $itemID );
        $path         = array(
                            array( 'url'  => 'mailing/registrations','text' => ezpI18n::tr( 'extension/ezmailing/text', 'Users' ) ),
                            array( 'url'  => 'mailing/registrations/search/' . $itemIDClean, 'text' => 'search' )
                    );
        $tpl->setVariable( 'search_text', $itemIDClean );
        $tpl->setVariable( 'search_fields', User::novaGetSearchFields() );
        break;

    case "delete":
        $templateName = "list";
        $path         = array( array( 'url'  => 'mailing/mailinglists', 'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Users' ) ) );
        $tpl->setVariable( 'fetchkey', "registredusers" );
        break;
    default:
        $templateName = "list";
        $tpl->setVariable( 'fetchkey', "registredusers" );
        $path = array( array( 'url'  => 'mailing/mailinglists', 'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Users' ) ) );
        break;
}
$viewParameters = array( 'order_field' => $Params["order_field"],
                         'order_dir'   => $Params["order_dir"],
                         'offset'      => $Params["Offset"] );

$tpl->setVariable( 'view_parameters', $viewParameters );

if ( $Params["order_field"] && $Params["order_dir"] ) {
    if ( $Params["order_field"] == 'name' ) {
        $tpl->setVariable( 'sort_by', array( 'first_name'    => $Params["order_dir"],
                                             'last_name'     => $Params["order_dir"],
                                             'email'         => $Params["order_dir"] ) );
    } else {
        $tpl->setVariable( 'sort_by', array( $Params["order_field"] => $Params["order_dir"] ) );
    }
}

$tpl->setVariable( 'path', $path );
$tpl->setVariable( 'subaction', $subAction );
$Result['content'] = $tpl->fetch( 'design:mailing/users/' . $templateName . '.tpl' );
$Result['path']    = $path;


?>