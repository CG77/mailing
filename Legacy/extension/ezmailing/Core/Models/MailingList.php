<?php
/**
 * MailingList
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

use eZDB;

class MailingList extends PersistentObject {

    const TABLE_NAME = "ezmailingmailinglist";

    const AVAILABLE                   = 0;
    const SYNCHRONISATION_IN_PROGRESS = 10;
    const SYNCHRONISATION_ACTION      = "ezmailing_synchro";
    const IMPORT_IN_PROGRESS          = 20;
    const IMPORT_ACTION               = "ezmailing_import";

    /**
     * Set up the definition
     */
    public static function definition() {
        static $def = array( 'fields'              => array( 'id'                              => array( 'name'     => 'id',
                                                                                                         'datatype' => 'integer',
                                                                                                         'default'  => 0,
                                                                                                         'required' => true ),
                                                             'name'                            => array( 'name'     => 'name',
                                                                                                         'datatype' => 'string',
                                                                                                         'default'  => '',
                                                                                                         'required' => false ),
                                                             'lang'                            => array( 'name'     => 'lang',
                                                                                                         'datatype' => 'string',
                                                                                                         'default'  => 0,
                                                                                                         'required' => false ),
                                                             'last_synchro'                    => array( 'name'     => 'last_synchro',
                                                                                                         'datatype' => 'integer',
                                                                                                         'default'  => 0,
                                                                                                         'required' => false ),
                                                             'remote_id'                       => array( 'name'     => 'remote_id',
                                                                                                         'datatype' => 'string',
                                                                                                         'default'  => '',
                                                                                                         'required' => false ),
                                                             'state'                           => array( 'name'     => 'state',
                                                                                                         'datatype' => 'integer',
                                                                                                         'default'  => 0,
                                                                                                         'required' => false ),
                                                             'count_remote_registration'       => array( 'name'     => 'count_remote_registration',
                                                                                                         'datatype' => 'integer',
                                                                                                         'default'  => 0,
                                                                                                         'required' => false ),
                                                             'created'                         => array( 'name'     => 'created',
                                                                                                         'datatype' => 'integer',
                                                                                                         'default'  => 0,
                                                                                                         'required' => false ),
                                                             'updated'                         => array( 'name'     => 'updated',
                                                                                                         'datatype' => 'integer',
                                                                                                         'default'  => 0,
                                                                                                         'required' => false ),
                                                             'draft'                           => array( 'name'     => 'draft',
                                                                                                         'datatype' => 'integer',
                                                                                                         'default'  => 0,
                                                                                                         'required' => false ) ),
                             'keys'                => array( 'id' ),
                             'increment_key'       => "id",
                             'class_name'          => 'Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList',
                             'function_attributes' => array( "url"                                    => "getUrl",
                                                             "registrations"                          => "getRegistrations",
                                                             "last_registrations"                     => "getLastRegistrations",
                                                             "remoteid"                               => "getRemoteId",
                                                             "registrations_count"                    => "getRegistrationsCount",
                                                             "campaign_count"                         => "getCampaignCount",
                                                             "is_available"                           => "isAvailable",
                                                             "is_importing"                           => "isImporting",
                                                             "is_synchronizing"                       => "isSynchronizing",

                             ),
                             'name'                => self::TABLE_NAME );
        return $def;
    }

    /**
     * Get the system url of the object.
     * @return string
     */
    public function getUrl() {
        return "/mailing/mailinglists/view/" . $this->attribute( 'id' );
    }

    /**
     * @return bool
     */
    public function isAvailable() {
        return $this->attribute( 'state' ) == static::AVAILABLE;
    }

    /**
     * @return bool
     */
    public function isImporting() {
        return $this->attribute( 'state' ) == static::IMPORT_IN_PROGRESS;
    }

    /**
     * @return bool
     */
    public function isSynchronizing() {
        return $this->attribute( 'state' ) == static::SYNCHRONISATION_IN_PROGRESS;
    }

    /**
     * Get the registrations connected to this mailing list
     * @return array
     */
    public function getRegistrations( $sort_array = false ) {
        $args = array( 'mailinglist_id' => $this->attribute( 'id' ),
                       'state'          => Registration::REGISTRED );
        if ( $sort_array ) {
            $args['sort_by'] = $sort_array;
        }
        return Registration::novaFetchObjectList( $args );
    }

    /**
     * Get all registrations with all state
     * @return array
     */
    public function getAllRegistrations() {
        $args = array( 'mailinglist_id' => $this->attribute( 'id' ) );
        return Registration::novaFetchObjectList( $args );
    }

    public function getLastRegistrations() {
        $args = array( 'mailinglist_id' => $this->attribute( 'id' ),
                       'state'          => Registration::REGISTRED,
                       'limit'          => 50,
                       'sort_by'        => array( 'registred'=> "desc" ) );
        return Registration::novaFetchObjectList( $args );
    }

    /**
     * Get the remote_id for this mailing list
     * @return string
     */
    public function getRemoteId() {
        if ( preg_match( '/([0-9]+)/', $this->attribute( 'remote_id' ), $results ) ) {
            return $results[1];
        }
        return false;
    }

    /**
     * Get the campaigns connected to this mailing list
     * @return array
     */
    public function getCampaigns( $field_filters = array() ) {

        if ( sizeof( $field_filters ) == 0 ) {
            $fields = '*';
        } else {
            $fields = implode( ',', $field_filters );
        }

        $id      = $this->attribute( 'id' );
        $sqlText = 'SELECT ' . $fields . ' FROM ' . Campaign::TABLE_NAME . ' WHERE
                destination_mailing_list = ' . $id . ' OR
                destination_mailing_list LIKE "' . $id . ':%" OR
                destination_mailing_list LIKE "%:' . $id . '" OR
                destination_mailing_list LIKE "%:' . $id . ':%"
            ';

        $db         = eZDB::instance();
        $rows       = $db->arrayQuery( $sqlText );
        $objectList = static::handleRows( $rows, __NAMESPACE__ . '\Campaign', true );
        return $objectList;
    }

    public function getCampaignCount() {

        $id      = $this->attribute( 'id' );
        $sqlText = 'SELECT count(ezmailingcampaign.id) AS nb_rows FROM ' . Campaign::TABLE_NAME . ' WHERE
                destination_mailing_list = ' . $id . ' OR
                destination_mailing_list LIKE "' . $id . ':%" OR
                destination_mailing_list LIKE "%:' . $id . '" OR
                destination_mailing_list LIKE "%:' . $id . ':%"
            ';

        $db     = eZDB::instance();
        $result = $db->arrayQuery( $sqlText );
        return $result[0]['nb_rows'];
    }

    /**
     * Get the number of registration REGISTRED
     * @return integer
     */
    public function getRegistrationsCount() {
        return Registration::novaFetchObjectListCount( array( 'mailinglist_id' => $this->attribute( 'id' ),
                                                              'state'          => Registration::REGISTRED ) );
    }

    /**
     * Override of remove method to delete the registration connected
     * @see eZPersistentObject::remove()
     */
    public function remove( $conditions = null, $extraConditions = null ) {
        $registrations = $this->getAllRegistrations();
        $campaigns     = $this->getCampaigns();

        foreach( $campaigns as $campaign ) {
            $campaign->removeDestination( $this );
        }
        foreach( $registrations as $registration ) {
            $registration->remove( true );
        }
        parent::remove( $conditions = null, $extraConditions = null );
    }
}