<?php

/**
 * Collection
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

namespace Novactive\eZPublish\Extension\eZMailing\Core\Functions;

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\User;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Stat;

use eZDB;
use eZPersistentObject;
use eZLog;

class Collection {

    /**
     * fetch method function useful in template to fetch object
     * @param string  $type
     * @param array   $filter
     * @param integer $offset
     * @param integer $limit
     * @param array   $sortBy
     * @return array
     */
    public static function fetch( $type, $filter = array(), $offset = 0, $limit = null, $sortBy = null ) {
        $conds   = array( "offset"  => $offset,
                          "limit"   => $limit,
                          "sort_by" => $sortBy, );
        $Filters = array_merge( $conds, $filter );
        return static::fetchWrapper( $type, $Filters );
    }

    /**
     * fetch method function useful in template to fetch count of a list of object
     * @param string $type
     * @param array  $filter
     * @return array
     */
    public static function fetchCount( $type, $filter = array() ) {
        return static::fetchWrapper( $type, $filter, true );
    }

    public static function fetchBrowserStats( $id, $type ) {
        $db      = eZDB::instance();
        $sqlText = "
                SELECT
					`$type` as type,
                    COUNT(id) as Nb,
                    user_key
                FROM " . Stat::TABLE_NAME . "
                WHERE campaign_id = '" . (int)$id . "' AND url = '" . Stat::OPENED_STATKEY . "'
                GROUP BY user_key, type
        ";

        $results = $db->arrayQuery( $sqlText );
        return array( 'result' => static::formatByBrowserStats( $results, $type ) );
    }

    /**
     * Fetch statistic data
     * @param $id
     * @return array
     */
    public static function fetchStats( $id, $by_unit = false ) {
        $authorizedUnits = array( 'day'   => "%Y-%m-%d",
                                  'month' => "%Y-%m",
                                  'minute'=> "%Y-%m-%d %H:%i",
                                  'hour'  => "%Y-%m-%d %H:",
                                  'week'  => "%Y-%u" );
        $selectByUnit    = "";

        if ( $by_unit && array_key_exists( $by_unit, $authorizedUnits ) ) {
            $selectByUnit = 'DATE_FORMAT(FROM_UNIXTIME(clicked), "' . $authorizedUnits[$by_unit] . '") as unit,';
        }

        $sqlText = "
                SELECT 
                	user_key,
                    url as Url,
                    $selectByUnit
                    COUNT(id) as nb
                FROM " . Stat::TABLE_NAME . "
                WHERE campaign_id = '" . (int)$id . "'
                GROUP BY url, user_key " . ( ( $selectByUnit == "" ) ? "" : ",unit ORDER BY unit ASC" ) . "
        ";
        $db      = eZDB::instance();
        $results = $db->arrayQuery( $sqlText );

        if ( !$by_unit ) {
            return array( 'result' => static::formatSimpleStats( $results ) );
        } else {
            return array( 'result' => static::formatByUnitStats( $results, $by_unit ) );
        }
    }
    
	/**
     * Fetch week(s) statistic data
     * @param $nbWeeks
     * @return array
     */
    public static function fetchStatsWeeks( $nbWeeks ) {
        
        $authorizedUnits = array( 'day'   => "%Y-%m-%d" );
        $selectByUnit    = 'DATE_FORMAT(FROM_UNIXTIME(clicked), "' . $authorizedUnits["day"] . '") as unit,';
        
        $sqlText = "
                SELECT 
                    $selectByUnit
                    COUNT(id) as nb
                FROM " . Stat::TABLE_NAME . "
                WHERE clicked > ( UNIX_TIMESTAMP( NOW() ) - 3600 * 24 * " . 7 * $nbWeeks . " ) AND url = '" . Stat::OPENED_STATKEY . "'
                GROUP BY unit ORDER BY unit ASC";
        $db      = eZDB::instance();
        $results = $db->arrayQuery( $sqlText );

        $debut = time() - 3600 * 24 * 7 * $nbWeeks;
        $fin = time() + 3600 * 24;
        
        $aDate = array();
        $formatedResults = array();
        
        foreach ( $results as $day ) {
            $aDate[$day['unit']] = $day;
        }
        
        while ( $debut < $fin ) {
            if ( array_key_exists( date( "Y-m-d", $debut ) , $aDate) ) {
                $formatedResults[] = $aDate[date( "Y-m-d", $debut )];
            } else {
                $formatedResults[] = array( 'unit' => date( "Y-m-d", $debut ), 'nb' => 0 );
            }
            $debut += 3600 * 24;
        }
        
        return array( 'result' => static::formatByUnitStats( $formatedResults ) );
    }

    /**
     * @static
     * @param $rows
     * @return array
     */
    protected static function formatByBrowserStats( $rows ) {
        $Result = array();
        foreach( $rows as $row ) {
            if ( $row['type'] ) {
                $Result[] = array( 'type'  => $row['type'],
                                   'count' => $row['Nb'] );
            }
        }
        return $Result;
    }

    /**
     * @static
     * @param $rows
     * @return array
     */
    protected static function formatByUnitStats( $rows ) {
        $Result = array();
        foreach( $rows as $row ) {
            $Result[] = array( 'url'  => $row['Url'],
                               'unit' => $row['unit'],
                               'count'=> $row['nb'] );
        }
        return $Result;
    }

    /**
     * @static
     * @param $rows
     * @return array
     */
    protected static function formatSimpleStats( $rows ) {
        $Result           = array();
        $Result['opened'] = 0;
        $uniqUser         = array();
        foreach( $rows as $row ) {
            if ( $row['Url'] == Stat::OPENED_STATKEY ) {
                $Result['opened']++;
            } else {
                if ( !array_key_exists( $row['Url'], $Result['urls'] ) ) {
                    $Result['urls'][$row['Url']] = 0;
                }
                $Result['urls'][$row['Url']]++;
                $Result['clicked']++;
            }
        }
        return $Result;
    }

    /**
     * Util method the improve the treatment of several sql queries
     * @param string $placeholder
     * @param string $sqlText
     * @param array  $Filters
     * @return string
     */
    private static function treatPlaceHolders( $placeholder, $sqlText, $Filters, $concatFilters = false ) {
        $sqlText = str_replace( '{placeholder}', $placeholder, $sqlText );
        $db      = eZDB::instance();
        $limit   = ( array_key_exists( 'limit', $Filters ) ? (int)$Filters['limit'] : 0 );
        $offset  = ( array_key_exists( 'offset', $Filters ) ? (int)$Filters['offset'] : 0 );
        $sort_by = ( array_key_exists( 'sort_by', $Filters ) ? $db->escapeString( $Filters['sort_by'] ) : null );

        if ( $limit > 0 ) {
            if ( $offset > 0 ) {
                $sqlText = str_replace( '{limit}', "LIMIT $offset,$limit", $sqlText );
            } else {
                $sqlText = str_replace( '{limit}', "LIMIT $limit", $sqlText );
            }
        } else {
            $sqlText = str_replace( '{limit}', '', $sqlText );
        }

        if ( $sort_by ) {
            $strSort = "ORDER BY";
            if ( ( count( $sort_by ) > 1 ) && $concatFilters ) {
                $strSort .= " CONCAT(";
            }
            $adComma = false;
            foreach( $sort_by as $key => $order ) {
                if ( $adComma ) {
                    $strSort .= ", ";
                }
                if ( ( count( $sort_by ) > 1 ) && $concatFilters ) {
                    $strSort .= " $key ";
                } else {
                    $strSort .= " $key $order ";
                }
                $adComma = true;
            }
            if ( ( count( $sort_by ) > 1 ) && $concatFilters ) {
                $strSort .= " ) " . $order;
            }
            $sqlText = str_replace( '{order}', $strSort, $sqlText );
        } else {
            $sqlText = str_replace( '{order}', '', $sqlText );
        }
        return $sqlText;
    }

    /**
     * Fetch wrapper used by fetch and fectchCount to filter the content
     * @param string  $type
     * @param array   $Filters
     * @param boolean $count
     * @return array|integer
     */
    public static function fetchWrapper( $type, $Filters = array(), $count = false ) {
        $Result       = array();
        $listFunction = ( $count ) ? "novaFetchObjectListCount" : "novaFetchObjectList";
        switch( $type ) {
            case "campaigns":
            case "campaign":
                $Result = Campaign::$listFunction( $Filters );
                break;
            case "mailinglist":
            case "mailinglists":
                $Result = MailingList::$listFunction( $Filters );
                break;
            case "users":
            case "user":
                $Result = User::$listFunction( $Filters );
                break;
            case "registrations":
            case "registration":
                $Result = Registration::$listFunction( $Filters );
                break;
            case "stats":
                $Result = Stat::$listFunction( $Filters );
                break;
            case "orphanusers":
                $sqlText = 'SELECT {placeholder} FROM ' . User::TABLE_NAME . ' WHERE draft = 0 AND id NOT IN ( SELECT ' . Registration::TABLE_NAME . '.mailing_user_id from ' . Registration::TABLE_NAME . ' ) {order} {limit}';
                $db      = eZDB::instance();
                if ( !$count ) {
                    $sqlText = static::treatPlaceHolders( User::TABLE_NAME . '.*', $sqlText, $Filters, true );
                    $rows    = $db->arrayQuery( $sqlText );
                    if ( $rows !== false ) {
                        $Result = eZPersistentObject::handleRows( $rows, 'Novactive\eZPublish\Extension\eZMailing\Core\Models\User', true );
                    }
                } else {
                    $sqlText = static::treatPlaceHolders( 'COUNT(' . User::TABLE_NAME . '.id) as nb', $sqlText, $Filters );
                    $res     = $db->query( $sqlText )->fetch_assoc();
                    $Result  = (int)$res['nb'];
                }
                break;
            case "registredusers":
                $sqlText = 'SELECT {placeholder} FROM ' . User::TABLE_NAME . ' INNER JOIN ' . Registration::TABLE_NAME . ' ON ' . Registration::TABLE_NAME . '.mailing_user_id = ' . User::TABLE_NAME . '.id WHERE draft = 0 GROUP BY ' . User::TABLE_NAME . '.id {order} {limit}';
                $db      = eZDB::instance();
                if ( !$count ) {
                    $sqlText = static::treatPlaceHolders( User::TABLE_NAME . '.*', $sqlText, $Filters, true );
                    $rows    = $db->arrayQuery( $sqlText );
                    if ( $rows !== false ) {
                        $Result = eZPersistentObject::handleRows( $rows, 'Novactive\eZPublish\Extension\eZMailing\Core\Models\User', true );
                    }
                } else {
                    $sqlText = static::treatPlaceHolders( 'COUNT(' . User::TABLE_NAME . '.id) as nb', $sqlText, $Filters );
                    $res     = $db->arrayQuery( $sqlText );
                    $Result  = (int)count( $res );
                }
                break;
        }
        return array( 'result' => $Result );
    }
}

?>