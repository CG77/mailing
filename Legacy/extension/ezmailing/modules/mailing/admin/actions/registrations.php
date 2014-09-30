<?php
/**
 * Handling Campaigns Action of eZ Mailing
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;

/**
 * @var Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration $item
 * @var eZHTTPTool $http
 * @var eZTemplate $tpl
 * @var eZModule $Module
 */


/**
 * SearchRegistrationAction
 */
if ( $Module->isCurrentAction( "SearchRegistrationAction" ) ) {
   return $Module->redirectModule( $Module, "registrations", array( 'search', $http->postVariable( 'SearchRegistrationText' ) ) );
}