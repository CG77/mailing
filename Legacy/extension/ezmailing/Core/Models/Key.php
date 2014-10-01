<?php
/**
 * User
 *
 * eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>

 * @link      http://www.novactive.com
 *
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Models;

use Novactive\eZPublish\Extension\eZMailing\Core\Utils\Audit;

class Key extends PersistentObject {

    const TABLE_NAME = "ezmailingkey";

    /**
     * Set up the definition
     */
    public static function definition() {
        static $def = array( 'fields'              => array( 'id'         => array( 'name'     => 'id',
                                                                                    'datatype' => 'integer',
                                                                                    'default'  => 0,
                                                                                    'required' => true ),
                                                             'hash_key'   => array( 'name'     => 'hash_key',
                                                                                    'datatype' => 'string',
                                                                                    'default'  => '',
                                                                                    'required' => false ),
                                                             'time'       => array( 'name'     => 'time',
                                                                                    'datatype' => 'integer',
                                                                                    'default'  => 0,
                                                                                    'required' => false ),
                                                             'params'     => array( 'name'     => 'params',
                                                                                    'datatype' => 'text',
                                                                                    'default'  => '',
                                                                                    'required' => false ) ),
                             'keys'                => array( 'id' ),
                             'increment_key'       => "id",
                             'function_attributes' => array(),
                             'class_name'          => 'Novactive\eZPublish\Extension\eZMailing\Core\Models\Key',
                             'name'                => self::TABLE_NAME );
        return $def;
    }

    /**
     * Return the key object which match the hash key string
     * @return Key
     */
    public static function fetchByHashKey( $key ) {
        $keys = static::novaFetchObjectList( array( 'hash_key'=> $key ) );
        if ( $keys[0] instanceof self ) {
            return $keys[0];
        }
        return false;
    }

    /**
     * Clean database of expired keys
     * @return void
     */
    public static function cleanUpExpired() {
        $items = static::novaFetchObjectList( array( 'time'=> array( '<',
                                                                     time() ) ) );
        foreach( $items as $key ) {
            $key->remove();
        }
        return;
    }

    /**
     * Return the params array of the key object
     * @return array
     */
    public function getParams() {
        $return = unserialize( $this->attribute( 'params' ) );
        return $return;
    }

    /**
     * Override of remove to delete the related registration of a user
     * @see eZPersistentObject::remove()
     */
    public function remove() {
        $params = $this->getParams();
        $user   = User::fetchByEmail( $params['email'] );
        if ( $user instanceof User ) {
            $user_id = $user->attribute( 'id' );
            foreach( $params['mailingListIDs'] as $mailingListID) {
                Audit::trace( "[Models/Key] {remove}", array( "userId" => $user_id, "mailinglistId" => $mailingListID ) );
                $registration = Registration::fetchByUserAndMailingID( $user_id, $mailingListID );
                if ( ( $registration instanceof Registration ) && ( $registration->attribute( 'state' ) == Registration::PENDING ) ) {
                    $registration->remove();
                }
            }
        }
        parent::remove();
    }
}