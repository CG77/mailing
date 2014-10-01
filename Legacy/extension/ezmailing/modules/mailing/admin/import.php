<?php
/**
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

include( __DIR__ . "/bootstrap.php" );

use Novactive\eZPublish\Extension\eZMailing\Core\Models\User;

/**
 * @var eZTemplate $tpl
 */

include( __DIR__ . "/actions.php" );

$templateName = "users";
$path         = array( array( 'url'  => 'mailing/import/users',
                              'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Import Users' ) ) );

$tpl->setVariable( 'path', $path );
$Result['content'] = $tpl->fetch( 'design:mailing/import/' . $templateName . '.tpl' );
$Result['path']    = $path;


?>