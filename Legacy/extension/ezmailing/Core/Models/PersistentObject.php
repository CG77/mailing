<?php
/**
 * PersistentObject
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

namespace Novactive\eZPublish\Extension\eZMailing\Core\Models;

abstract class PersistentObject extends NovaPersistentObject {

    /**
     * Override the store method to handle the date change
     * @see eZPersistentObject::store()
     */
    public function store( $fieldFilters = null ) {

        if ( $this->hasAttribute( 'updated' ) ) {
            $this->setAttribute( 'updated', time() );
        }
        if ( $this->hasAttribute( 'created' ) && ( $this->attribute( 'created' ) <= 0 ) ) {
            $this->setAttribute( 'created', time() );
        }

        if ( $this->hasAttribute( 'registred' ) && ( $this->attribute( 'registred' ) <= 0 ) ) {
            $this->setAttribute( 'registred', time() );
        }

        parent::store( $fieldFilters );
    }

    /**
     * Wrapper for eZNovaPersistentObject::novaFetchObjectList
     * We add the draft filter if is not set
     * @param array $conds
     * @return array
     */
    public static function novaFetchObjectList( $conds = null ) {
        $def = static::definition();
        if ( ( is_array( $conds ) && !array_key_exists( "draft", $conds ) ) && ( is_array( $def['fields'] ) && array_key_exists( 'draft', $def['fields'] ) ) ) {
            $conds['draft'] = 0;
        }
        return parent::novaFetchObjectList( $conds );
    }

    /**
     * Wrapper for eZNovaPersistentObject::novaFetchObjectListCount
     * We add the draft filter if is not set
     * @param array $conds
     * @return integer
     */
    public static function novaFetchObjectListCount( $conds = null ) {
        $def = static::definition();
        if ( !array_key_exists( "draft", $conds ) && array_key_exists( 'draft', $def['fields'] ) ) {
            $conds['draft'] = 0;
        }
        return parent::novaFetchObjectListCount( $conds );
    }
}