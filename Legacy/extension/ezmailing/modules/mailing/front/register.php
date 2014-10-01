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

/**
 * @var eZTemplate $tpl
 * @var eZHttpTool $http
 */

$path = array( array( 'url'  => 'mailing/register',
                      'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Register' ) ) );

$email       = "";
$mailingList = array();

include( __DIR__ . "/actions.php" );

foreach( $templateVars as $key=> $value ) {
    $tpl->setVariable( $key, $value );
}

$tpl->setVariable( 'fields', array( 'Email'         => $email,
                                    "MailingListID" => $mailingList ) );

$tpl->setVariable( 'path', $path );
$Result['content'] = $tpl->fetch( 'design:mailing_registration/register.tpl' );
$Result['path']    = $path;


?>