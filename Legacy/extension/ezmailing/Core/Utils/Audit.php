<?php
/**
 * Audit
 *
 * eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>

 * @link      http://www.novactive.com
 *
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Utils;

use eZIni;
use eZLog;

class Audit {

    protected static $logfile;
    protected static $enabled;

    public static function loadSetting() {
        $ezMailingIni = eZINI::instance( 'ezmailing.ini' );
        self::$logfile = $ezMailingIni->variable( 'LogSettings', 'AuditFile' );
        self::$enabled = $ezMailingIni->variable( 'LogSettings', 'EnabledAudit' );
    }

    public static function write( $text ) {

        if ( empty( self::$logfile) ) {
            self::loadSetting();
        }

        if ( !self::$enabled ) { return ; }
        
        eZLog::write( $text, self::$logfile);
    }

    public static function trace( $context = "", $aDetails = array() ) {
        $s = "";
        
        $s .= $context;

        if ( !empty( $aDetails['uri'] ) ) {
            $s .= $aDetails['uri'];
        } else {
            foreach($aDetails as $key => $val ) {
                $s .= $key . " -> '" . $val . "' ";
            }
        }

        self::write( $s );
    }

}