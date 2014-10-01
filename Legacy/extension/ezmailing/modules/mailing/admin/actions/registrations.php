<?php
/**
 * Handling Campaigns Action of eZ Mailing
 *
 * eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>

 * @link      http://www.novactive.com
 *
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