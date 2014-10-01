<?php
/**
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

$FunctionList = array();

$FunctionList['fetch'] = array( 'name'            => 'fetch',
                                'operation_types' => 'read',
                                'call_method'     => array( 'class'        => 'Novactive\eZPublish\Extension\eZMailing\Core\Functions\Collection',
                                                            'include_file' => 'extension/ezmailing/Core/Functions/Collection.php',
                                                            'method'       => 'fetch' ),
                                'parameter_type'  => 'standard',
                                'parameters'      => array( array( 'name'     => 'type',
                                                                   'type'     => 'string',
                                                                   'required' => true,
                                                                   'default'  => '' ),
                                                            array( 'name'     => 'filter',
                                                                   'type'     => 'array',
                                                                   'required' => false,
                                                                   'default'  => array() ),
                                                            array( 'name'     => 'offset',
                                                                   'type'     => 'integer',
                                                                   'required' => false,
                                                                   'default'  => 0 ),
                                                            array( 'name'     => 'limit',
                                                                   'type'     => 'integer',
                                                                   'required' => false,
                                                                   'default'  => null ),
                                                            array( 'name'     => 'sort_by',
                                                                   'type'     => 'array',
                                                                   'required' => false,
                                                                   'default'  => null ) ) );

$FunctionList['fetch_count'] = array( 'name'            => 'fetch_count',
                                      'operation_types' => 'read',
                                      'call_method'     => array( 'class'        => 'Novactive\eZPublish\Extension\eZMailing\Core\Functions\Collection',
                                                                  'include_file' => 'extension/ezmailing/Core/Functions/Collection.php',
                                                                  'method'       => 'fetchCount' ),
                                      'parameter_type'  => 'standard',
                                      'parameters'      => array( array( 'name'     => 'type',
                                                                         'type'     => 'string',
                                                                         'required' => true,
                                                                         'default'  => '' ),
                                                                  array( 'name'     => 'filter',
                                                                         'type'     => 'array',
                                                                         'required' => false,
                                                                         'default'  => array() ) ) );

$FunctionList['stats'] = array( 'name'            => 'stats',
                                'operation_types' => 'read',
                                'call_method'     => array( 'class'        => 'Novactive\eZPublish\Extension\eZMailing\Core\Functions\Collection',
                                                            'include_file' => 'extension/ezmailing/Core/Functions/Collection.php',
                                                            'method'       => 'fetchStats' ),
                                'parameter_type'  => 'standard',
                                'parameters'      => array( array( 'name'     => 'mailing_id',
                                                                   'type'     => 'integer',
                                                                   'required' => true,
                                                                   'default'  => 0 ),
                                                            array( 'name'     => 'unit',
                                                                   'type'     => 'string',
                                                                   'required' => false,
                                                                   'default'  => '' ) ) );

$FunctionList['browserstats'] = array( 'name'            => 'browserstats',
                                       'operation_types' => 'read',
                                       'call_method'     => array( 'class'        => 'Novactive\eZPublish\Extension\eZMailing\Core\Functions\Collection',
                                                                   'include_file' => 'extension/ezmailing/Core/Functions/Collection.php',
                                                                   'method'       => 'fetchBrowserStats' ),
                                       'parameter_type'  => 'standard',
                                       'parameters'      => array( array( 'name'     => 'mailing_id',
                                                                          'type'     => 'integer',
                                                                          'required' => true,
                                                                          'default'  => 0 ),
                                                                   array( 'name'     => 'type',
                                                                          'type'     => 'string',
                                                                          'required' => true,
                                                                          'default'  => '' ) ) );

?>