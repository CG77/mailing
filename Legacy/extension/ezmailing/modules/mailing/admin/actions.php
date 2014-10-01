<?php
/**
 * Handling Action of eZ Mailing
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

/**
 * User Actions
 */

include( __DIR__ . "/actions/users.php" );

/**
 * Mailing Actions
 */

include( __DIR__ . "/actions/mailinglists.php" );

/**
 * Campaign Actions
 */

include( __DIR__ . "/actions/campaigns.php" );

/**
 * Import Actions
 */

include( __DIR__ . "/actions/import.php" );

/**
 * Registration Actions
 */

include( __DIR__ . "/actions/registrations.php" );



