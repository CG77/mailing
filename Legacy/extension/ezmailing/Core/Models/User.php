<?php
/**
 * User
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

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;
use eZDB;
use eZUser;

class User extends PersistentObject {

    const TABLE_NAME = "ezmailinguser";

    /**
     * Set up the definition
     */
    public static function definition() {
        static $def = array( 'fields'              => array( 'id'                  => array( 'name'     => 'id',
                                                                                             'datatype' => 'integer',
                                                                                             'default'  => 0,
                                                                                             'required' => true ),
                                                             'email'               => array( 'name'     => 'email',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'first_name'          => array( 'name'     => 'first_name',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'last_name'           => array( 'name'     => 'last_name',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'gender'              => array( 'name'     => 'gender',
                                                                                             'datatype' => 'integer',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'birthday'            => array( 'name'     => 'birthday',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'phone'               => array( 'name'     => 'phone',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'fax'                 => array( 'name'     => 'fax',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'address'             => array( 'name'     => 'address',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'zipcode'             => array( 'name'     => 'zipcode',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'city'                => array( 'name'     => 'city',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'country'             => array( 'name'     => 'country',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'state'               => array( 'name'     => 'state',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'profession'          => array( 'name'     => 'profession',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'company'             => array( 'name'     => 'company',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'company_member'      => array( 'name'     => 'company_member',
                                                                                             'datatype' => 'integer',
                                                                                             'default'  => 0,
                                                                                             'required' => false ),
                                                             'profession_status'   => array( 'name'     => 'profession_status',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'number_icom'         => array( 'name'     => 'number_icom',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'family_status'       => array( 'name'     => 'family_status',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'children_count'      => array( 'name'     => 'children_count',
                                                                                             'datatype' => 'integer',
                                                                                             'default'  => 0,
                                                                                             'required' => false ),
                                                             'house_member_count'  => array( 'name'     => 'house_member_count',
                                                                                             'datatype' => 'integer',
                                                                                             'default'  => 0,
                                                                                             'required' => false ),
                                                             'origin'              => array( 'name'     => 'origin',
                                                                                             'datatype' => 'string',
                                                                                             'default'  => '',
                                                                                             'required' => false ),
                                                             'registred'           => array( 'name'     => 'registred',
                                                                                             'datatype' => 'integer',
                                                                                             'default'  => 0,
                                                                                             'required' => false ),
                                                             'updated'             => array( 'name'     => 'updated',
                                                                                             'datatype' => 'integer',
                                                                                             'default'  => 0,
                                                                                             'required' => false ),
                                                             'draft'               => array( 'name'     => 'draft',
                                                                                             'datatype' => 'integer',
                                                                                             'default'  => 0,
                                                                                             'required' => false ) ),
                             'keys'                => array( 'id' ),
                             'increment_key'       => "id",
                             'function_attributes' => array( "registrations"                                       => "getRegistrations",
                                                             "registrationsSummary"                                => "getRegistrationsSummary",
                                                             "url"                                                 => "getUrl",
                                                             "ezuser"                                              => "getEzUser",
                                                             "name"                                                => "getName",
                                                             "mailing_lists"                                       => "getMailingLists",
                                                             "all_mailing_lists_with_registrations"                => "getAllMailingWithRegistrations" ),
                             'class_name'          => 'Novactive\eZPublish\Extension\eZMailing\Core\Models\User',
                             'name'                => self::TABLE_NAME );
        return $def;
    }

    /**
     * Return the system url of the object
     * @return string
     */
    public function getUrl() {
        return "/mailing/users/view/" . $this->attribute( 'id' );
    }

    /**
     * Return the list of registration of the user (all states)
     * @return array
     */
    public function getRegistrations( $cond = array() ) {
        if ( sizeof( $cond ) > 0 ) {
            $cond = array_merge( $cond, array( 'mailing_user_id' => $this->attribute( 'id' ) ) );
        } else {
            $cond = array( 'mailing_user_id' => $this->attribute( 'id' ) );
        }
        return Registration::novaFetchObjectList( $cond );
    }

    /**
     * @return array
     */
    public function getRegistrationsSummary() {
        $sqlText = "
                    SELECT
                        count(*) AS c,
                        " . Registration::TABLE_NAME . ".state
                    FROM
                        " . Registration::TABLE_NAME . "
                    INNER JOIN " . MailingList::TABLE_NAME . " ON " . Registration::TABLE_NAME . ".mailinglist_id = " . MailingList::TABLE_NAME . ".id
                    WHERE
                        " . Registration::TABLE_NAME . ".mailing_user_id = " . $this->attribute( 'id' ) . "
                    GROUP BY
                        " . Registration::TABLE_NAME . ".state
        ";

        $db     = eZDB::instance();
        $rows   = $db->arrayQuery( $sqlText );
        $result = array();
        foreach( $rows as $row ) {
            $result[] = array( "state"=> Registration::stateStringFor( $row['state'], true ),
                               "count"=> $row['c'] );
        }
        return $result;
    }

    /**
     * Return the mailing list of the user
     * @return array
     */
    public function getMailingLists() {
        $MailingLists = array();
        //@optim: we could make a direct sql query
        $registrations = $this->getRegistrations();
        foreach( $registrations as $registration ) {
            $MailingLists[] = $registration->getMailingList();
        }
        return $MailingLists;
    }

    /**
     * Return the active mailing list of the user
     * @return array
     */
    public function getActiveMailingLists() {
        $MailingLists = array();
        //@optim: we could make a direct sql query
        $registrations = $this->getRegistrations( array( 'state' => Registration::REGISTRED ) );
        foreach( $registrations as $registration ) {
            $MailingLists[] = $registration->getMailingList();
        }
        return $MailingLists;
    }

    /**
     * Return a formated name for the user
     * @return string
     */
    public function getName() {
        $fName = $this->attribute( "first_name" );
        $lName = $this->attribute( "last_name" );
        $email = $this->attribute( "email" );
        $name  = trim( "$fName $lName" );
        if ( $name != "" ) {
            $name .= " - ";
        }
        $name .= $email;
        return $name;
    }

    /**
     * Return a formated name for the user to sent by email
     * @return string
     */
    public function getRecipientName() {
        $fName = $this->attribute( "first_name" );
        $lName = $this->attribute( "last_name" );
        return "$fName $lName";
    }

    /**
     * Get the email
     * @return string
     */
    public function getEmail() {
        return $this->attribute( "email" );
    }

    /**
     * @return eZUser
     */
    public function getEzUser() {
        return eZUser::fetchByEmail( $this->getEmail() );
    }

    /**
     * Override of remove to delete the related registration of a user
     * @see eZPersistentObject::remove()
     */
    public function remove( $conditions = null, $extraConditions = null ) {
        $registrations = $this->getRegistrations();
        foreach( $registrations as $registration ) {
            $registration->remove( true );
        }
        parent::remove( $conditions = null, $extraConditions = null );
    }

    /**
     * Return All the registration connection with all mailing list (registred or not)
     * @return array array( "mailing" => MailingList, "registration" => Registration|NULL );
     */
    public function getAllMailingWithRegistrations() {
        $sqlText = "SELECT " . MailingList::TABLE_NAME . ".id as Mid, " . Registration::TABLE_NAME . ".mailing_user_id as RMUid, " . Registration::TABLE_NAME . ".mailinglist_id as RMLid  FROM " . MailingList::TABLE_NAME . "
        				LEFT JOIN " . Registration::TABLE_NAME . " 
        					ON ( " . MailingList::TABLE_NAME . ".id = " . Registration::TABLE_NAME . ".mailinglist_id AND 
        						" . Registration::TABLE_NAME . ".mailing_user_id = " . $this->attribute( 'id' ) . " )
        			WHERE draft=0";

        $db      = eZDB::instance();
        $rows    = $db->arrayQuery( $sqlText );
        $mapping = array();
        if ( $rows !== false ) {
            foreach( $rows as $row ) {
                $registration = Registration::novaFetchByKeys( array( $row['RMUid'],
                                                                      $row['RMLid'] ) );
                $mailing      = MailingList::novaFetchByKeys( $row['Mid'] );
                $mapping[]    = array( "mailing"      => $mailing,
                                       "registration" => $registration );
            }
        }
        return $mapping;
    }

    /**
     * Get the registration for a specific mailing list id
     * @param integer $mailing_list_id
     * @return Registration
     */
    public function getRegistrationByMailingId( $mailing_list_id ) {
        return Registration::novaFetchByKeys( array( $this->attribute( 'id' ),
                                                     $mailing_list_id ) );
    }

    /**
     * Get a user by email
     * @param string $email
     * @return User
     */
    public static function fetchByEmail( $email ) {
        $conds = array( 'email' => $email );
        return static::novaFetchObject( $conds );
    }
}