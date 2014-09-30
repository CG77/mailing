<?php
/**
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
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
