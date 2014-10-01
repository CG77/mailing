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

use eZDB;

class Stat extends PersistentObject {

    const TABLE_NAME = "ezmailingstat";

    const OPENED_STATKEY = "OPENED";

    /**
     * Set up the definition
     */
    public static function definition() {
        static $def = array( 'fields'              => array( 'id'                    => array( 'name'     => 'id',
                                                                                               'datatype' => 'integer',
                                                                                               'default'  => 0,
                                                                                               'required' => true ),
                                                             'campaign_id'           => array( 'name'     => 'campaign_id',
                                                                                               'datatype' => 'integer',
                                                                                               'default'  => 0,
                                                                                               'required' => true ),
                                                             'url'                   => array( 'name'     => 'url',
                                                                                               'datatype' => 'string',
                                                                                               'default'  => '',
                                                                                               'required' => false ),
                                                             'os_name'               => array( 'name'     => 'os_name',
                                                                                               'datatype' => 'string',
                                                                                               'default'  => '',
                                                                                               'required' => false ),
                                                             'browser_name'          => array( 'name'     => 'browser_name',
                                                                                               'datatype' => 'string',
                                                                                               'default'  => '',
                                                                                               'required' => false ),
                                                             'clicked'               => array( 'name'     => 'clicked',
                                                                                               'datatype' => 'integer',
                                                                                               'default'  => 0,
                                                                                               'required' => false ),
                                                             'user_key'              => array( 'name'     => 'user_key',
                                                                                               'datatype' => 'string',
                                                                                               'default'  => '',
                                                                                               'required' => false ) ),
                             'keys'                => array( 'id' ),
                             'increment_key'       => "id",
                             'function_attributes' => array(),
                             'class_name'          => 'Novactive\eZPublish\Extension\eZMailing\Core\Models\Stat',
                             'name'                => self::TABLE_NAME );
        return $def;
    }
}