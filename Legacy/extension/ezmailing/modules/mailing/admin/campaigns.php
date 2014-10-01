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

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Utils\SiteAccess;

/**
 * @var Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign $item
 * @var eZTemplate                                                   $tpl
 * @var eZModule                                                     $Module
 */

if ( $itemID > 0 ) {
    $item = Campaign::novaFetchByKeys( $itemID );
}

include( __DIR__ . "/actions.php" );

switch( $subAction ) {
    case "view":
        $templateName = "view";
        $path         = array(
                            array( 'url'  => 'mailing/campaigns', 'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Campaigns' ) ),
                            array( 'url'  => 'mailing/campaigns/view/' . $itemID, 'text' => $item->attribute( 'subject' ) )
                    );
        $tpl->setVariable( 'item', $item );
        break;
    case "edit":
        if ( !$item instanceof Campaign ) {
            return $Module->redirectModule( $Module, "campaigns", array( 'list' ) );
        }
        $templateName = "edit";
        $path         = array(
                            array( 'url'  => 'mailing/campaigns', 'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Campaigns' ) ),
                            array( 'url'  => 'mailing/campaigns/view/' . $itemID, 'text' => $item->attribute( 'subject' ) )
                    );
        $tpl->setVariable( 'newsSiteAccessList', SiteAccess::getSiteaccess() );
        $tpl->setVariable( 'item', $item );
        break;
    case "send":
        if ( !$item instanceof Campaign ) {
            return $Module->redirectModule( $Module, "campaigns", array( 'list' ) );
        }
        $templateName = "send";
        $path         = array(
                                array( 'url'  => 'mailing/campaigns', 'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Campaigns' ) ),
                                array( 'url'  => 'mailing/campaigns/view/' . $itemID, 'text' => $item->attribute( 'subject' ) )
                    );
        $tpl->setVariable( 'item', $item );
        break;
    case "search":
        $templateName = "search";
        $itemIDClean = strip_tags( $itemID );
        $path         = array(
                            array( 'url'  => 'mailing/campaigns','text' => ezpI18n::tr( 'extension/ezmailing/text', 'Campaigns' ) ),
                            array( 'url'  => 'mailing/campaigns/search/' . $itemIDClean, 'text' => 'search' )
                    );
        $tpl->setVariable( 'search_text', $itemIDClean );
        $tpl->setVariable( 'search_fields', Campaign::novaGetSearchFields() );
        break;
    case "stats":
        if ( !$item instanceof Campaign ) {
            return $Module->redirectModule( $Module, "campaigns", array( 'list' ) );
        }
        $templateName = "stats";
        $path         = array(
            array( 'url'  => 'mailing/campaigns', 'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Campaigns' ) ),
            array( 'url'  => 'mailing/campaigns/view/' . $itemID, 'text' => $item->attribute( 'subject' ) )
        );
        $tpl->setVariable( 'item', $item );
        break;
    case "drafts":
    case "tested":
    case "confirmed":
    case "waiting":
    case "sending":
    case "sent":
    case "cancelled":
    case "deleted":
        $templateName = "list";
        if ( $subAction == "drafts" ) {
            $state = Campaign::DRAFT;
        }
        if ( $subAction == "tested" ) {
            $state = Campaign::TESTED;
        }
        if ( $subAction == "confirmed" ) {
            $state = Campaign::CONFIRMED;
        }
        if ( $subAction == "waiting" ) {
            $state = Campaign::WAITING_FOR_SEND;
        }
        if ( $subAction == "sending" ) {
            $state = Campaign::SENDING_IN_PROGRESS;
        }
        if ( $subAction == "sent" ) {
            $state = Campaign::SENT;
            $tpl->setVariable( 'openStats', true );
        }
        if ( $subAction == "cancelled" ) {
            $state = Campaign::CANCELLED;
        }
        if ( $subAction == "deleted" ) {
            $state = Campaign::DELETED;
        }
        $path = array(
                    array( 'url'  => 'mailing/campaigns', 'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Campaigns' ) ),
                    array( 'url'  => 'mailing/campaigns/' . $subAction, 'text' => ezpI18n::tr( 'extension/ezmailing/text', Campaign::$aStates[$state] ) )
        );
        $tpl->setVariable( 'state_filter', $state );

        break;
    default:
        $templateName = "list";
        $path         = array( array( 'url'  => 'mailing/campaigns/drafts', 'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Campaigns' ) ) );
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
$Result['content'] = $tpl->fetch( 'design:mailing/campaigns/' . $templateName . '.tpl' );
$Result['path']    = $path;

?>