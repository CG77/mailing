<?php
/**
 * ResendCampaign
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */
namespace Novactive\eZPublish\Extension\eZMailing\Core\Models;

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Stat;
use ezpI18n;
use eZDB;
use eZContentObjectTreeNode;
use eZPendingActions;

class ResendCampaign extends PersistentObject {

    const TABLE_NAME    = "JNT_resendcampaign";
    const RESEND_ACTION = "ezmailing_resend";

    /**
     * Set up the definition
     */
    public static function definition() {
        static $def = array( 'fields'              => array( 'id'                         => array( 'name'     => 'id',
                                                                                                    'datatype' => 'integer',
                                                                                                    'default'  => 0,
                                                                                                    'required' => true ),
                                                             'campaign_id'                => array( 'name'     => 'campaign_id',
                                                                                                    'datatype' => 'integer',
                                                                                                    'default'  => 0,
                                                                                                    'required' => false ),
                                                             'remote_id'                  => array( 'name'     => 'remote_id',
                                                                                                    'datatype' => 'integer',
                                                                                                    'default'  => 0,
                                                                                                    'required' => false ),
                                                             'last_update'                => array( 'name'     => 'last_update',
                                                                                                    'datatype' => 'integer',
                                                                                                    'default'  => 0,
                                                                                                    'required' => false ),
                                                             'message'                    => array( 'name'     => 'message',
                                                                                                    'datatype' => 'string',
                                                                                                    'default'  => '',
                                                                                                    'required' => false ) ),
                             'keys'                => array( 'id',
                                                             'remote_id' ),
                             'increment_key'       => "id",
                             'class_name'          => 'Novactive\eZPublish\Extension\eZMailing\Core\Models\ResendCampaign',
                             'function_attributes' => array(),
                             'name'                => self::TABLE_NAME );
        return $def;
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
}