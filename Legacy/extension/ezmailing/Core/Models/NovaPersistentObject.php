<?php
/**
 * NovaPersistentObject
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Models;

use eZPersistentObject;

abstract class NovaPersistentObject extends eZPersistentObject {

    /**
     * Constructor
     * @param array $row
     */
    protected function __construct( $row ) {
        parent::eZPersistentObject( $row );
    }

    /**
     * Wrapper for eZPersistentObject::fetchObject
     * @param array $conds
     * @return eZPersistentObject
     */

    public static function novaFetchObject( $conds = null ) {
        return static::fetchObject( static::definition(), null, $conds );
    }

    /**
     * Wrapper for eZPersistentObject::fetchObjectList
     * @param array $conds
     * @return eZPersistentObject
     */
    public static function novaFetchObjectList( $conds = null ) {

        if ( is_array( $conds ) && array_key_exists( 'sort_by', $conds ) ) {
            $sort = $conds['sort_by'];
            unset( $conds['sort_by'] );
        } else {
            $sort = null;
        }
        $limit = array( 'offset' => 0,
                        'limit'  => NULL );
        if ( is_array( $conds ) && array_key_exists( 'offset', $conds ) ) {
            $limit['offset'] = $conds['offset'];
            unset( $conds['offset'] );
        }
        if ( is_array( $conds ) && array_key_exists( 'limit', $conds ) ) {
            $limit['limit'] = $conds['limit'];
            unset( $conds['limit'] );
        }

        $custom_conds = null;
        if ( is_array( $conds ) && array_key_exists( "or", $conds ) ) {
            if ( count( $conds ) > 1 ) {
                $custom_conds = " AND ";
            }
            $custom_conds .= " ( ";

            $first = true;
            foreach( $conds["or"] as $field => $subConds ) {
                if ( $first ) {
                    $first = false;
                } else {
                    $custom_conds .= " OR ";
                }
                $custom_conds .= $field . " " . $subConds[0] . " '" . $subConds[1] . "' ";
            }
            $custom_conds .= " ) ";
            unset( $conds['or'] );
        }

        return static::fetchObjectList( static::definition(), null, $conds, $sort, $limit, true, false, null, null, $custom_conds );
    }

    /**
     * Wrapper for eZPersistentObject::count
     * @param array $conds
     * @return integer
     */
    public static function novaFetchObjectListCount( $conds = null ) {

        $custom_conds = null;
        if ( array_key_exists( "or", $conds ) ) {
            if ( count( $conds ) > 1 ) {
                $custom_conds = " AND ";
            }
            $custom_conds .= " ( ";

            $first = true;
            foreach( $conds["or"] as $field => $subConds ) {
                if ( $first ) {
                    $first = false;
                } else {
                    $custom_conds .= " OR ";
                }
                $custom_conds .= $field . " " . $subConds[0] . " '" . $subConds[1] . "' ";
            }
            $custom_conds .= " ) ";
            unset( $conds['or'] );

            $customFields = array( array( 'operation' => 'COUNT(*)',
                                          'name'      => 'row_count' ) );
            $rows         = static::fetchObjectList( static::definition(), array(), $conds, array(), null, false, false, $customFields, null, $custom_conds );
            return $rows[0]['row_count'];
        }
        return static::count( static::definition(), $conds );
    }

    /**
     * Fetch By ID
     * @param integer|array $key
     * @return eZPersistentObject
     */
    public static function novaFetchByKeys( $keys ) {
        if ( !is_array( $keys ) ) {
            $keys = array( $keys );
        }
        $defs  = static::definition();
        $conds = array();
        foreach( $defs['keys'] as $index => $keyName ) {
            $conds[$keyName] = $keys[$index];
        }
        return static::novaFetchObject( $conds );
    }

    /**
     * Create a new object
     * @param array $row
     * @return PersistentObject
     */
    public static function create( array $row = array() ) {
        $object = new static( $row );
        return $object;
    }

    /**
     * __toString
     * @return string
     */
    public function __toString() {
        $str  = "NovaPersistentObject (" . get_called_class() . ")\n";
        $defs = static::definition();
        foreach( $defs['fields'] as $identifier => $value ) {
            $str .= "\t" . $value['name'] . " = {$this->attribute($identifier)} \n";
        }
        return $str;
    }

    /**
     * Get the string fields for searching requests
     * @return array
     */
    public static function novaGetSearchFields() {
        $aReturn    = array();
        $definition = static::definition();
        foreach( $definition['fields'] as $key => $field ) {
            if ( $field["datatype"] == "string" ) {
                $aReturn[] = $key;
            }
        }
        return $aReturn;
    }
}