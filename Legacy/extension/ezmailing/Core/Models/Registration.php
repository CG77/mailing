<?php
/**
 * Registration
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

use ezpI18n;
use eZDB;

class Registration extends PersistentObject {

    const REGISTRED           = 10;
    const UNREGISTRED         = 20;
    const PENDING             = 30;
    const BLOCKED             = 90;
    const HARD_BOUNCE_BLOCKED = 91;
    const SOFT_BOUNCE_BLOCKED = 92;

    const TABLE_NAME = "ezmailingregistration";

    public static $aStates = array( self::REGISTRED           => "Registred",
                                    self::UNREGISTRED         => "Unregistred",
                                    self::PENDING             => "Waiting for confirmation",
                                    self::BLOCKED             => "Blocked",
                                    self::HARD_BOUNCE_BLOCKED => "Hard bounce blocked",
                                    self::SOFT_BOUNCE_BLOCKED => "Soft bounce" );

    /**
     * Set up the definition
     */
    public static function definition() {
        static $def = array( 'fields'              => array( 'mailing_user_id' => array( 'name'     => 'mailing_user_id',
                                                                                         'datatype' => 'integer',
                                                                                         'default'  => 0,
                                                                                         'required' => true ),
                                                             'mailinglist_id'  => array( 'name'     => 'mailinglist_id',
                                                                                         'datatype' => 'integer',
                                                                                         'default'  => 0,
                                                                                         'required' => false ),
                                                             'registred'       => array( 'name'     => 'registred',
                                                                                         'datatype' => 'integer',
                                                                                         'default'  => 0,
                                                                                         'required' => false ),
                                                             'state'           => array( 'name'     => 'state',
                                                                                         'datatype' => 'integer',
                                                                                         'default'  => 0,
                                                                                         'required' => false ),
                                                             'state_updated'   => array( 'name'     => 'state_updated',
                                                                                         'datatype' => 'integer',
                                                                                         'default'  => 0,
                                                                                         'required' => false ),

        ),
                             'keys'                => array( 'mailing_user_id',
                                                             'mailinglist_id' ),
                             'function_attributes' => array( "mailing_list"          => "getMailingList",
                                                             "user"                  => "getUser",
                                                             "state_string"          => "getStateString",
                                                             "state_string_stand"    => "getStateStringStandard",
                                                             "is_active"             => "isActive",
                                                             "is_pending"            => "isPending" ),
                             'class_name'          => 'Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration',
                             'name'                => self::TABLE_NAME );
        return $def;
    }

    /**
     * Return the mailing list connected to the registration
     * @return MailingList
     */
    public function getMailingList() {
        return MailingList::novaFetchByKeys( $this->attribute( 'mailinglist_id' ) );
    }

    /**
     * Return the user connected to the registration
     * @return User
     */
    public function getUser() {
        return User::novaFetchByKeys( $this->attribute( 'mailing_user_id' ) );
    }

    /**
     * Return the string state of the registration
     * @return string
     */
    public function getStateString() {
        return static::stateStringFor( $this->attribute( 'state' ), true );
    }

    /**
     * Return the string state eng of the registration
     * @return string
     */
    public function getStateStringStandard() {
        return static::stateStringFor( $this->attribute( 'state' ), false );
    }

    /**
     * @param      $stateId
     * @param bool $withTranslation
     * @return string
     */
    public static function stateStringFor( $stateId, $withTranslation = false ) {
        if ( static::$aStates[$stateId] ) {
            if ( $withTranslation ) {
                return ezpI18n::tr( 'extension/ezmailing/text', static::$aStates[$stateId] );
            } else {
                return static::$aStates[$stateId];
            }
        }
        return $stateId;
    }

    /**
     * Allow us to know if the registration is in state REGISTRED
     * @return boolean
     */
    public function isActive() {
        return ( $this->attribute( 'state' ) == static::REGISTRED );
    }

    /**
     * Allow us to know if the registration is in state PENDING
     * @return boolean
     */
    public function isPending() {
        return ( $this->attribute( 'state' ) == static::PENDING );
    }

    /**
     * Override of remove method to change the state on time before the true deleted
     * @see eZPersistentObject::remove()
     */
    public function remove( $conditions = null, $extraConditions = null ) {
        if ( is_null( $conditions ) ) {
            $forceRemove = false;
        } else {
            $forceRemove = true;
        }
        if ( ( $this->attribute( 'state' ) == static::UNREGISTRED ) || ( $forceRemove == true ) ) {
            parent::remove();
        } else {
            $this->setAttribute( 'state', static::UNREGISTRED );
            $this->setAttribute( 'state_updated', time() );
            $this->store();
        }
    }

    /**
     * Fetch the registration corresponding to one user and one mailing list (by id)
     * @param integer $user_id
     * @param integer $mailing_id
     */
    public static function fetchByUserAndMailingID( $user_id, $mailing_id ) {
        $conds = array( 'mailing_user_id' => $user_id,
                        'mailinglist_id'  => $mailing_id );
        return static::novaFetchObject( $conds );
    }

    /**
     * Get last registrations
     * @param timestamp $date
     * @param integer   $mailing_id
     * @return array
     */
    public static function getLastRegistrations( $date, $mailing_id = false ) {
        $args = array( 'state_updated' => array( '>=',
                                                 $date ) );
        if ( $mailing_id ) {
            $args['mailinglist_id'] = $mailing_id;
        }
        return Registration::novaFetchObjectList( $args );
    }

    /**
     * Fetch registrations and sort them by username
     * @param string $dir
     * @param mixed  $offset
     * @param mixed  $limit
     * @param array  $additionalFilters
     * @return array
     */
    public static function getObjectsSortByUsernames( $dir = 'asc', $offset = false, $limit = false, $additionalFilters = array() ) {
        $db    = eZDB::instance();
        $query = "SELECT r.* FROM " . self::TABLE_NAME . " r INNER JOIN " . User::TABLE_NAME . " u ON mailing_user_id=u.id ";
        if ( count( $additionalFilters ) > 0 ) {
            $query .= "WHERE ";
            $first = true;
            foreach( $additionalFilters as $attrName => $attrValue ) {
                if ( $first ) {
                    $first = false;
                } else {
                    $query .= "AND ";
                }
                $query .= "r." . $attrName . "='" . $db->escapeString( $attrValue ) . "' ";
            }
        }
        $query .= "ORDER BY CONCAT( u.first_name, u.last_name, u.email) " . $dir;
        if ( $limit ) {
            if ( !$offset ) {
                $offset = 0;
            }
            $query .= " LIMIT " . (int)$offset . ", " . (int)$limit;
        }
        $db  = eZDB::instance();
        $res = $db->arrayQuery( $query );
        return static::handleRows( $res, __NAMESPACE__ . '\Registration', true );
    }

    /**
     * Fetch registrations and sort them by MailingList
     * @param string $dir
     * @param mixed  $offset
     * @param mixed  $limit
     * @param array  $additionalFilters
     * @return array
     */
    public static function getObjectsSortByMailingList( $dir = 'asc', $offset = false, $limit = false, $additionalFilters = array() ) {
        $query = "SELECT r.* FROM " . self::TABLE_NAME . " r INNER JOIN " . MailingList::TABLE_NAME . " ml ON mailinglist_id=ml.id ";
        $db    = eZDB::instance();
        if ( count( $additionalFilters ) > 0 ) {
            $query .= "WHERE ";
            $first = true;
            foreach( $additionalFilters as $attrName => $attrValue ) {
                if ( $first ) {
                    $first = false;
                } else {
                    $query .= "AND ";
                }
                $query .= "r." . $attrName . "='" . $db->escapeString( $attrValue ) . "' ";
            }
        }
        $query .= "ORDER BY ml.name " . $db->escapeString( $dir );
        if ( $limit ) {
            if ( !$offset ) {
                $offset = 0;
            }

            $query .= " LIMIT " . (int)$offset . ", " . (int)$limit;
        }
        $db  = eZDB::instance();
        $res = $db->arrayQuery( $query );
        return static::handleRows( $res, __NAMESPACE__ . '\Registration', true );
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
        $aReturn[] = User::TABLE_NAME . '.email';
        $aReturn[] = User::TABLE_NAME . '.first_name';
        $aReturn[] = User::TABLE_NAME . '.last_name';
        $aReturn[] = User::TABLE_NAME . '.last_name';
        $aReturn[] = MailingList::TABLE_NAME . '.name';

        return $aReturn;
    }

    /**
     * Wrapper for eZPersistentObject::fetchObjectList
     * @param array $conds
     * @return eZPersistentObject
     */
    public static function novaFetchObjectList( $conds = null ) {
        if ( array_key_exists( "or", $conds ) ) {
            $db = eZDB::instance();

            $query = "SELECT " . static::TABLE_NAME . ".* ";
            $query .= "FROM " . MailingList::TABLE_NAME . " ";
            $query .= "LEFT JOIN " . static::TABLE_NAME . " ";
            $query .= "ON " . MailingList::TABLE_NAME . ".id=" . static::TABLE_NAME . ".mailinglist_id ";
            $query .= "INNER JOIN " . User::TABLE_NAME . " ";
            $query .= "ON " . static::TABLE_NAME . ".mailing_user_id=" . User::TABLE_NAME . ".id ";
            $query .= "WHERE ( ";

            $first = true;
            foreach( $conds["or"] as $field => $subcond ) {
                if ( $first ) {
                    $first = false;
                } else {
                    $query .= "OR ";
                }

                if ( !strstr( $field, "." ) ) {
                    $query .= static::TABLE_NAME . ".";
                }
                $query .= trim( $field ) . " " . trim( $subcond[0] );
                $query .= " '" . str_replace( "'", "\'", trim( $subcond[1] ) ) . "' ";
            }
            $query .= ") ";

            if ( !empty( $conds["sort_by"] ) ) {
                $query .= "ORDER BY ";

                $first = true;
                foreach( $conds["sort_by"] as $field => $dir ) {
                    if ( $first ) {
                        $first = false;
                    } else {
                        $query .= ", ";
                    }

                    if ( $field == "name" ) {
                        $query .= "CONCAT( ";
                        $query .= User::TABLE_NAME . ".first_name, ";
                        $query .= User::TABLE_NAME . ".last_name, ";
                        $query .= User::TABLE_NAME . ".email) " . $dir . " ";
                    } elseif ( $field == "mailing_list" ) {
                        $query .= MailingList::TABLE_NAME . ".name " . trim( $dir ) . " ";
                    } elseif ( !strstr( $field, "." ) ) {
                        $query .= static::TABLE_NAME . "." . trim( $field ) . " " . trim( $dir ) . " ";
                    } else {
                        $query .= trim( $field ) . " " . trim( $dir ) . " ";
                    }
                }
            }

            if ( empty( $conds["offset"] ) ) {
                $conds["offset"] = 0;
            }
            if ( !empty( $conds["limit"] ) ) {
                $query .= "LIMIT " . (int)$conds["offset"] . ", " . (int)$conds["limit"];
            }

            $res = $db->arrayQuery( $query );

            return static::handleRows( $res, __NAMESPACE__ . '\Registration', true );
        } elseif ( isset( $conds["sort_by"] ) && array_key_exists( 'name', $conds["sort_by"] ) ) {
            $additionalFilters = array();
            foreach( $conds as $key => $value ) {
                if ( !in_array( $key, array( "offset",
                                             "limit",
                                             "sort_by" ) )
                ) {
                    $additionalFilters[$key] = $value;
                }
            }
            return static::getObjectsSortByUsernames( $conds["sort_by"]["name"], $conds["offset"], $conds["limit"], $additionalFilters );
        } elseif ( isset( $conds["sort_by"] ) && array_key_exists( 'mailing_list', $conds["sort_by"] ) ) {
            $additionalFilters = array();
            foreach( $conds as $key => $value ) {
                if ( !in_array( $key, array( "offset",
                                             "limit",
                                             "sort_by" ) )
                ) {
                    $additionalFilters[$key] = $value;
                }
            }
            return static::getObjectsSortByMailingList( $conds["sort_by"]["mailing_list"], $conds["offset"], $conds["limit"], $additionalFilters );
        } else {
            return parent::novaFetchObjectList( $conds );
        }
    }

    /**
     * Wrapper for eZPersistentObject::count
     * @param array $conds
     * @return integer
     */
    public static function novaFetchObjectListCount( $conds = null ) {
        if ( array_key_exists( "or", $conds ) ) {
            $db = eZDB::instance();

            $query = "SELECT COUNT(*) as nb ";
            $query .= "FROM " . MailingList::TABLE_NAME . " ";
            $query .= "LEFT JOIN " . Registration::TABLE_NAME . " ";
            $query .= "ON " . MailingList::TABLE_NAME . ".id = " . Registration::TABLE_NAME . ".mailinglist_id ";
            $query .= "INNER JOIN " . User::TABLE_NAME . " ";
            $query .= "ON " . Registration::TABLE_NAME . ".mailing_user_id = " . User::TABLE_NAME . ".id ";
            $query .= "WHERE ( ";

            $first = true;
            foreach( $conds["or"] as $field => $subcond ) {
                if ( $first ) {
                    $first = false;
                } else {
                    $query .= "OR ";
                }
                if ( !strstr( $field, "." ) ) {
                    $query .= Registration::TABLE_NAME . ".";
                }
                $query .= trim( $field ) . " " . trim( $subcond[0] );
                $query .= " '" . str_replace( "'", "\'", trim( $subcond[1] ) ) . "' ";
            }
            $query .= ") ";

            $res = $db->arrayQuery( $query );

            return $res[0]["nb"];
        } else {
            return parent::novaFetchObjectListCount( $conds );
        }
    }

    /**
     * Reset Soft Bounce and set status to REGISTRED
     */
    public static function setToRegistredSoftBounced() {
        $registrations = Registration::novaFetchObjectList( array( 'state' => Registration::SOFT_BOUNCE_BLOCKED ) );
        foreach( $registrations as $registration ) {
            $registration->setAttribute( 'state', 10 );
            $registration->setAttribute( 'state_updated', time() );
            $registration->store();
        }
    }
}