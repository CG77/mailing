<?php

/**
 * SiteAccess
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Utils;

use eZINI;
use eZSiteAccess;

class SiteAccess {

    /**
     * Util method the get information on all siteacess, a get a instant access
     * @return array
     */
    public static function getSiteaccess() {
        $ini            = eZINI::instance( 'ezmailing.ini' );
        $siteAccessList = $ini->variable( 'MailingSettings', 'DesignSiteAccess' );
        $aReturn        = array();
        foreach( $siteAccessList as $siteAccessName ) {
            $specificIni              = eZSiteAccess::getIni( $siteAccessName, "site.ini" );
            $aReturn[$siteAccessName] = array( "siteAccessName"   => $siteAccessName,
                                               "siteAccessLocale" => $specificIni->variable( 'RegionalSettings', 'Locale' ),
                                               "siteAccessUrl"    => "http://" . $specificIni->variable( 'SiteSettings', 'SiteURL' ) );
        }
        return $aReturn;
    }

    /**
     * @static
     * @param null $key
     * @return null
     */
    public static function current( $key = null ) {
        $sa = eZSiteAccess::current();
        if ( $key ) {
            return $sa[$key];
        }
        return $sa;
    }
}