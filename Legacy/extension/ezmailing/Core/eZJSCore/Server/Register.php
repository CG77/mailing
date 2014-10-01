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

namespace Novactive\eZPublish\Extension\eZMailing\Core\eZJSCore\Server;

use ezjscServerFunctionsJs;
use eZHTTPTool;

class Register extends ezjscServerFunctionsJs {

    public static function register( $args ) {
        $eZJSCoreModuleAction = "MailingRegisterAction";
        $http                 = eZHTTPTool::instance();
        include( __DIR__ . "/../../../modules/mailing/front/actions.php" );
        return $templateVars;
    }
}
