<?php
/**
 * Browser
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

class Browser {

    protected $_userAgent;
    protected $_name;
    protected $_version;
    protected $_platform;
    protected $_pattern;

    /**
     * Singleton
     * @static
     * @return Browser
     */
    public static function instance() {
        static $_instance;
        if ( !$_instance ) {
            $_instance = new Browser( $_SERVER['HTTP_USER_AGENT'] );
        }
        return $_instance;
    }

    /**
     * Constructor
     * @param $u_agent
     */
    public function __construct( $u_agent ) {
        $bname    = 'Unknown';
        $platform = 'Unknown';
        $version  = "";

        //First get the platform?
        if ( preg_match( '/linux/i', $u_agent ) ) {
            $platform = 'Linux';
        } elseif ( preg_match( '/macintosh|mac os x/i', $u_agent ) ) {
            $platform = 'Mac';
        } elseif ( preg_match( '/windows|win32/i', $u_agent ) ) {
            $platform = 'Windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if ( preg_match( '/MSIE/i', $u_agent ) && !preg_match( '/Opera/i', $u_agent ) ) {
            $bname = 'Internet Explorer';
            $ub    = "MSIE";
        } elseif ( preg_match( '/Firefox/i', $u_agent ) ) {
            $bname = 'Mozilla Firefox';
            $ub    = "Firefox";
        } elseif ( preg_match( '/Chrome/i', $u_agent ) ) {
            $bname = 'Google Chrome';
            $ub    = "Chrome";
        } elseif ( preg_match( '/Safari/i', $u_agent ) ) {
            $bname = 'Apple Safari';
            $ub    = "Safari";
        } elseif ( preg_match( '/Opera/i', $u_agent ) ) {
            $bname = 'Opera';
            $ub    = "Opera";
        } elseif ( preg_match( '/Netscape/i', $u_agent ) ) {
            $bname = 'Netscape';
            $ub    = "Netscape";
        }

        // finally get the correct version number
        $known   = array( 'Version',
                          $ub,
                          'other' );
        $pattern = '#(?<browser>' . join( '|', $known ) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if ( !preg_match_all( $pattern, $u_agent, $matches ) ) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count( $matches['browser'] );
        if ( $i != 1 ) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if ( strripos( $u_agent, "Version" ) < strripos( $u_agent, $ub ) ) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ( $version == null || $version == "" ) {
            $version = "?";
        }

        $this->_userAgent = $u_agent;
        $this->_name      = $bname;
        $this->_version   = $version;
        $this->_platform  = $platform;
        $this->_pattern   = $pattern;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getFullName() {
        return "{$this->_name} - ({$this->_version})";
    }

    /**
     * @return string
     */
    public function getPattern() {
        return $this->_pattern;
    }

    /**
     * @return string
     */
    public function getPlatform() {
        return $this->_platform;
    }

    /**
     * @return mixed
     */
    public function getUserAgent() {
        return $this->_userAgent;
    }

    /**
     * @return string
     */
    public function getVersion() {
        return $this->_version;
    }
}
