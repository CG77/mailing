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

use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;

/**
 * @var Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList $item
 * @var eZTemplate                                                      $tpl
 * @var eZModule                                                        $Module
 */

if ( $itemID > 0 ) {
    $item = MailingList::novaFetchByKeys( $itemID );
}

include( __DIR__ . "/actions.php" );

switch( $subAction ) {
    case "view":
        $templateName = "view";
        $path         = array(
                            array( 'url'  => 'mailing/mailinglists','text' => ezpI18n::tr( 'extension/ezmailing/text', 'Mailing Lists' ) ),
                            array( 'url'  => 'mailing/mailinglists/view/' . $itemID, 'text' => $item->attribute( 'name' ) )
                    );
        $tpl->setVariable( 'item', $item );
        break;
    case "edit":
        if ( !$item instanceof MailingList ) {
            return $Module->redirectModule( $Module, "mailinglists", array( 'list' ) );
        }
        $templateName = "edit";
        $path         = array(
                               array( 'url'  => 'mailing/mailinglists','text' => ezpI18n::tr( 'extension/ezmailing/text', 'Mailing Lists' ) ),
                               array( 'url'  => 'mailing/mailinglists/view/' . $itemID, 'text' => $item->attribute( 'name' ) )
                    );
        $tpl->setVariable( 'item', $item );
        $tpl->setVariable( 'available_translations', eZContentLanguage::fetchList() );
        break;
    case "search":
        $templateName = "search";
        $itemIDClean = strip_tags( $itemID );
        $path         = array(
                            array( 'url'  => 'mailing/mailinglists','text' => ezpI18n::tr( 'extension/ezmailing/text', 'Mailing Lists' ) ),
                            array( 'url'  => 'mailing/mailinglists/search/' . $itemIDClean, 'text' => 'search' )
                    );
        $tpl->setVariable( 'search_text', $itemIDClean );
        break;
    case "delete":
        $templateName = "list";
        $path         = array( array( 'url'  => 'mailing/mailinglists', 'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Mailing Lists' ) ) );
        break;
    default:
        $templateName = "list";
        $path         = array( array( 'url'  => 'mailing/mailinglists', 'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Mailing Lists' ) ) );
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
$Result['content'] = $tpl->fetch( 'design:mailing/mailinglists/' . $templateName . '.tpl' );
$Result['path']    = $path;


?>