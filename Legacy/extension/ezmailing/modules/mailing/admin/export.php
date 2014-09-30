<?php

set_time_limit( 0 );

use Novactive\eZPublish\Extension\eZMailing\Core\Functions\Collection;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\User;

$state     = $Params['state'];
$text      = $Params['text'];
$class     = $Params['class'];
$pack      = false;
$defintion = null;

// Get variables in configuration
$ini       = eZINI::instance( "ezmailing.ini" );
$labels    = $ini->variable( "ExportSettings", $class );
define( 'RETOUR_CHARIOT_CSV', "\n" );
define( 'SEPARATOR_CSV', ";" );
define( 'SEPARATOR_MAILINGLIST', " | " );

if ( $ini->variable( "ExportSettings", "CSVlongDates" ) == 'enabled' ) {
    $short_dates = false;
} else {
    $short_dates = true;
}

switch ( $class ) {

    case 'User':
        if ( !empty( $state ) && $state != 'search' ) {
            $filename = $class . "_state_" . $state . "_" . date( "Ymd-His" ) . ".csv";
        } elseif ( !empty( $state ) && $state == 'search' ) {
            $filters = array( 'or' => array() );
            foreach( User::novaGetSearchFields() as $field ) {
                $filters['or'][$field] = array( 'like',
                                                '%' . $text . '%' );
            }
            $filename = $class . "_" . $state . "_" . date( "Ymd-His" ) . ".csv";
        } else {
            $filename = $class . "_" . date( "Ymd-His" ) . ".csv";
        }
        $pack = true;
        $defintion = User::definition();
        break;
    case 'Registration':
        if ( isset( $Params["UserParameters"]["fix_item_type"] ) && isset( $Params["UserParameters"]["fix_item_id"] ) ) {
            $oMailingList = MailingList::novaFetchByKeys( $Params["UserParameters"]["fix_item_id"] );
            $objects = $oMailingList->getRegistrations();

            $filename = $class . "_for_" . $Params["UserParameters"]["fix_item_type"] . "_" . $Params["UserParameters"]["fix_item_id"] . "_" . date( "Ymd-His" ) . ".csv";
        } elseif ( !empty( $state ) && $state == 'search' ) {
            $filters = array( 'or' => array() );
            foreach( Registration::novaGetSearchFields() as $field ) {
                $filters['or'][$field] = array( 'like',
                                                '%' . $text . '%' );
            }
            $filename = $class . "_" . $state . "_" . date( "Ymd-His" ) . ".csv";
        } elseif ( !empty( $state ) && $state != 'search' ) {
            if ( preg_match( "/array\(([0-9, ]+)\)/", $state, $matchs ) ) {
                $tmpStates = explode( ", ", $matchs[1] );
                $states    = array(
                    'or' => array(
                        'state' => array( '=', $tmpStates )
                    )
                );
                $stateTMP  = '';
                foreach( $tmpStates as $st ) {
                    $stateTMP .= '_' . $st;
                }
                $filename = $class . "_state" . $stateTMP . "_" . date( "Ymd-His" ) . ".csv";
            } else {
                $filename = $class . "_state_" . $state . "_" . date( "Ymd-His" ) . ".csv";
            }
        } else {
            $filename = $class . "_" . date( "Ymd-His" ) . ".csv";
        }
        $pack = true;
        $defintion = Registration::definition();
        break;
    case 'Campaign':
        if ( !empty( $state ) && $state == 'search' ) {
            $filters = array( 'or' => array() );
            foreach( Campaign::novaGetSearchFields() as $field ) {
                $filters['or'][$field] = array( 'like',
                                                '%' . $text . '%' );
            }
            $results  = Collection::fetch( strtolower( $class ), $filters );
            $objects  = $results['result'];
            $filename = $class . "_" . $state . "_" . date( "Ymd-His" ) . ".csv";
        } elseif ( !empty( $state ) && $state != 'search' ) {
            if ( preg_match( "/array\(([0-9, ]+)\)/", $state, $matchs ) ) {
                $tmpStates = explode( ", ", $matchs[1] );
                $states    = array(
                    'or' => array(
                        'state' => array( '=', $tmpStates )
                    )
                );
                $stateTMP  = '';
                foreach( $tmpStates as $st ) {
                    $stateTMP .= '_' . $st;
                }
                $objects = Campaign::novaFetchObjectList( $states );
                $filename = $class . "_state" . $stateTMP . "_" . date( "Ymd-His" ) . ".csv";
            } else {
                $objects = Campaign::novaFetchObjectList( array( 'state' => $state ) );
                $filename = $class . "_state_" . $state . "_" . date( "Ymd-His" ) . ".csv";
            }
        } else {
            $objects  = Campaign::novaFetchObjectList();
            $filename = $class . "_" . date( "Ymd-His" ) . ".csv";
        }
        $defintion = Campaign::definition();
        break;
    case 'MailingList':
        if ( !empty( $state ) && $state == 'search' ) {
            $filters = array( 'or' => array() );
            foreach( MailingList::novaGetSearchFields() as $field ) {
                $filters['or'][$field] = array( 'like',
                                                '%' . $text . '%' );
            }
            $results  = Collection::fetch( strtolower( $class ), $filters );
            $objects  = $results['result'];
            $filename = $class . "_" . $state . "_" . date( "Ymd-His" ) . ".csv";
        } elseif ( !empty( $state ) && $state != 'search' ) {
            if ( preg_match( "/array\(([0-9, ]+)\)/", $state, $matchs ) ) {
                $tmpStates = explode( ", ", $matchs[1] );
                $states    = array(
                    'or' => array(
                        'state' => array( '=', $tmpStates )
                    )
                );
                $stateTMP  = '';
                foreach( $tmpStates as $st ) {
                    $stateTMP .= '_' . $st;
                }
                $objects = MailingList::novaFetchObjectList( $states );
                $filename = $class . "_state" . $stateTMP . "_" . date( "Ymd-His" ) . ".csv";
            } else {
                $objects = MailingList::novaFetchObjectList( array( 'state' => $state ) );
                $filename = $class . "_state_" . $state . "_" . date( "Ymd-His" ) . ".csv";
            }
        } else {
            $objects  = MailingList::novaFetchObjectList();
            $filename = $class . "_" . date( "Ymd-His" ) . ".csv";
        }
        $defintion = MailingList::definition();
        break;
}

header( "Content-Encoding: UTF-8" );
header( "Content-type: application/csv; charset=UTF-8" );
header( "Content-Disposition: attachment; filename=" . $filename );
header( "Pragma: no-cache" );
header( "Expires: 0" );

$specificFields = array( 'node_id'                   => 'node_name',
                         'destination_mailing_list'  => 'destination_mailing_list_name',
                         'mailinglist_id'            => 'mailing_list_name',
                         'mailing_user_id'           => 'mailing_user' );

if ( $defintion ) {

    // HEADERS
    CSVAddHeader( $defintion, $specificFields, $labels, $class );

    if ( $pack != false ) {
        $totalRows = 0;
        if ( !empty( $state ) && $state == "search" ) {
            $results = Collection::fetchCount( strtolower( $class ), $filters );
            $totalRows = (int)$results['result'];
        } elseif ( !empty( $state ) ) {
            if ( $class == "Registration" && isset( $states ) ) {
                $results = Collection::fetchCount( strtolower( $class ), $states );
            } elseif ( $class == "Registration" ) {
                $results = Collection::fetchCount( strtolower( $class ), array( 'or' => array( 'state' => array( '=', $state ) ) ) );
            } else {
                $results = Collection::fetchCount( $state );
            }
            $totalRows = (int)$results['result'];
        } else {
            $results = Collection::fetchCount( strtolower( $class ) );
            $totalRows = (int)$results['result'];
        }
        $i = 0;

        while ( $i < $totalRows ) {
            if ( !empty( $state ) && $state == "search" ) {
                $results  = Collection::fetch( strtolower( $class ), $filters, $i, 10000 );
            } elseif ( !empty( $state ) ) {
                if ( $class == "Registration" && isset( $states ) ) {
                    $results = Collection::fetchCount( strtolower( $class ), $states );
                } elseif ( $class == "Registration" ) {
                    $results  = Collection::fetch( strtolower( $class ), array( 'or' => array( 'state' => array( '=', $state ) ) ), $i, 10000 );
                } else {
                    $results  = Collection::fetch( $state, array(), $i, 10000 );
                }
            } else {
                $results = Collection::fetch( strtolower( $class ), null, $i, 10000 );
            }
            CSVAddRow( $results['result'], $defintion, $specificFields, $labels, $short_dates, $class );
            $i += 10000;
        }
    } else {
        CSVAddRow( $objects, $defintion, $specificFields, $labels, $short_dates, $class );
    }
}

function CSVAdd( $input ) {
    $input = utf8_decode( $input );
    echo '"' . str_replace( '"', '""', $input ) . '"';
}

function CSVAddHeader( $defintion, $specificFields, $labels, $class, $first = true ) {
    foreach( $defintion['fields'] as $field ) {
        if ( !isset( $labels[$field['name']] ) && !isset( $labels[$specificFields[$field['name']]] ) ) {
            continue;
        }

        if ( $first ) {
            $first = false;
        } else {
            echo SEPARATOR_CSV;
        }

        if ( isset( $labels[$field['name']] ) && $field['name'] == 'email' ) {
            CSVAdd( ucfirst( ezpI18n::tr( 'extension/ezmailing/text', $labels[$field['name']] ) ) );
            echo SEPARATOR_CSV;
            CSVAdd( ezpI18n::tr( 'extension/ezmailing/text', "Mailing list" ) );
            if ( isset( $labels[$specificFields[$field['name']]] ) ) {
                echo SEPARATOR_CSV;
            }

        } elseif ( isset( $labels[$field['name']] ) ) {
            // Specifics fields
            CSVAdd( ucfirst( ezpI18n::tr( 'extension/ezmailing/text', $labels[$field['name']] ) ) );
            if ( isset( $labels[$specificFields[$field['name']]] ) ) {
                echo SEPARATOR_CSV;
            }

        } elseif ( isset( $labels[$specificFields[$field['name']]] ) ) {
            // Specifics fields
            CSVAdd( ucfirst( ezpI18n::tr( 'extension/ezmailing/text', $labels[$specificFields[$field['name']]] ) ) );

        }
    }
    echo RETOUR_CHARIOT_CSV;
}

function CSVAddRow( $objects, $defintion, $specificFields, $labels, $short_dates, $class ) {
    // ROWS
    foreach( $objects as $object ) {
        $first = true;
        if ( $object->hasAttribute( 'draft' ) && $object->attribute( 'draft' ) ) {
            continue;
        }

        foreach( $defintion['fields'] as $field ) {
            if ( !isset( $labels[$field['name']] ) && !isset( $labels[$specificFields[$field['name']]] ) ) {
                continue;
            }

            if ( $first ) {
                $first = false;
            } else {
                echo SEPARATOR_CSV;
            }

            switch( $field['name'] ) {
                // Human friendly format for dates
                case 'last_synchro':
                case 'created':
                case 'updated':
                case 'sending_date':
                case 'state_updated':
                case 'registred':
                case 'birthday':
                    $timestamp = $object->attribute( $field['name'] );
                    if ( $timestamp ) {
                        $date = new eZDateTime( $timestamp );
                        CSVAdd( $date->toString( $short_dates ) );
                    } else {
                        CSVAdd( "-" );
                    }
                    break;

                // Seconds to day convertion for period
                case 'recurrency_period':
                    $secInADay = 60 * 60 * 24;
                    CSVAdd( ( $object->attribute( $field['name'] ) / $secInADay ) );
                    break;

                // From Ids to names
                case 'node_id':
                    $node_id = $object->attribute( $field['name'] );
                    if ( isset( $labels[$field['name']] ) ) {
                        CSVAdd( $node_id );
                        if ( isset( $labels[$specificFields[$field['name']]] ) ) {
                            echo SEPARATOR_CSV;
                        }
                    }

                    if ( isset( $labels[$specificFields[$field['name']]] ) ) {
                        $node = eZContentObjectTreeNode::fetch( $node_id );
                        if ( $node instanceof eZContentObjectTreeNode ) {
                            CSVAdd( $node->getName() );
                        } else {
                            CSVAdd( ezpI18n::tr( 'extension/ezmailing/text', "NO CONTENT" ) );
                        }
                    }
                    break;

                case 'destination_mailing_list':
                case 'mailinglist_id':
                    $mailingList_ids = $object->attribute( $field['name'] );
                    if ( isset( $labels[$field['name']] ) ) {
                        CSVAdd( str_replace( ':', ' - ', $mailingList_ids ) );
                        if ( isset( $labels[$specificFields[$field['name']]] ) ) {
                            echo SEPARATOR_CSV;
                        }
                    }

                    if ( isset( $labels[$specificFields[$field['name']]] ) ) {
                        $mailingLists     = array();
                        foreach( explode( ':', $mailingList_ids ) as $mailingList_id ) {
                            $mailingLists[] = MailingList::novaFetchByKeys( $mailingList_id );
                        }

                        $firstRegistration = true;
                        $names = '';
                        foreach( $mailingLists as $mailingList ) {
                            if ( $mailingList instanceof MailingList ) {
                                if ( $firstRegistration ) {
                                    $firstRegistration = false;
                                } else {
                                    $names .= ' - ';
                                }
                                $names .= $mailingList->attribute( 'name' );
                            } else {
                                $names .= ezpI18n::tr( 'extension/ezmailing/text', "DELETED" );
                            }
                        }
                        CSVAdd( $names );
                    }
                    break;

                case 'mailing_user_id':
                    $user_id = $object->attribute( $field['name'] );
                    if ( isset( $labels[$field['name']] ) ) {
                        CSVAdd( $user_id );
                        if ( isset( $labels[$specificFields[$field['name']]] ) ) {
                            echo SEPARATOR_CSV;
                        }
                    }

                    if ( isset( $labels[$specificFields[$field['name']]] ) ) {
                        $user = User::novaFetchByKeys( $user_id );
                        if ( $user instanceof User ) {
                            CSVAdd( $user->attribute( 'name' ) );
                        } else {
                            CSVAdd( ezpI18n::tr( 'extension/ezmailing/text', "DELETED" ) );
                        }
                    }
                    break;

                // Human friendly state
                case 'state':
                    if ( $class != 'User' ) {
                        if ( $class == 'Campaign' ) {
                            CSVAdd( ezpI18n::tr( 'extension/ezmailing/text', Campaign::$aStates[$object->attribute( $field['name'] )] ) );
                        } elseif ( $class == 'Registration' ) {
                            CSVAdd( ezpI18n::tr( 'extension/ezmailing/text', Registration::$aStates[$object->attribute( $field['name'] )] ) );
                        }
                    } else {
                        CSVAdd( $object->attribute( $field['name'] ) );
                    }
                    break;

                // Human friendly for type of Newsletter content
                case 'content_type':
                    if ( $class == 'Campaign' ) {
                        if ( $object->attribute( $field['name'] ) ) {
                            CSVAdd( ezpI18n::tr( 'extension/ezmailing/text', "Custom content" ) );
                        } else {
                            CSVAdd( ezpI18n::tr( 'extension/ezmailing/text', "eZ Publish content" ) );
                        }
                    } else {
                        CSVAdd( $object->attribute( $field['name'] ) );
                    }
                    break;

                // Human friendly to stipulate if the report have been sent or not
                case 'report_sent':
                    if ( $class == 'Campaign' ) {
                        if ( $object->attribute( $field['name'] ) ) {
                            CSVAdd( ezpI18n::tr( 'extension/ezmailing/text', "Yes" ) );
                        } else {
                            CSVAdd( ezpI18n::tr( 'extension/ezmailing/text', "No" ) );
                        }
                    } else {
                        CSVAdd( $object->attribute( $field['name'] ) );
                    }
                    break;

                // Human friendly for gender
                case 'gender':
                    if ( $object->attribute( $field['name'] ) ) {
                        CSVAdd( ezpI18n::tr( 'extension/ezmailing/text', "Female" ) );
                    } else {
                        CSVAdd( ezpI18n::tr( 'extension/ezmailing/text', "Male" ) );
                    }
                    break;

                // Add translation to family status
                case 'family_status':
                case 'profession_status':
                    if ( $object->attribute( $field['name'] ) ) {
                        CSVAdd( ezpI18n::tr( 'extension/ezmailing/text', ucfirst( $object->attribute( $field['name'] ) ) ) );
                    } else {
                        CSVAdd( "" );
                    }
                    break;

                case 'email':
                    CSVAdd( $object->attribute( $field['name'] ) );
                    //Add Mailinglist related
                    if ( $object instanceof User ) {
                        $aMailingLists = $object->getActiveMailingLists();
                        $sMailingLists = "";
                        $firstMailingList = true;
                        echo SEPARATOR_CSV;
                        foreach ( $aMailingLists as $oMailingList ) {
                            if ( !$firstMailingList ) {
                                $sMailingLists .= SEPARATOR_MAILINGLIST;
                            }
                            $sMailingLists .= $oMailingList->attribute( 'name' );
                            $firstMailingList = false;
                        }
                        CSVAdd( $sMailingLists );
                    }
                    break;

                // Normal usage
                default :
                    CSVAdd( $object->attribute( $field['name'] ) );
                    break;
            }
        }
        echo RETOUR_CHARIOT_CSV;
    }
}

eZExecution::cleanExit();
?>