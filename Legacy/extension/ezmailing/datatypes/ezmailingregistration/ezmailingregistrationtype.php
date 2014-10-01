<?php
/**
 *
 * eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>

 * @link      http://www.novactive.com
 *
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\User as eZMailingUser;
use Novactive\eZPublish\Extension\eZMailing\Core\Utils\SiteAccess;
use Novactive\eZPublish\Extension\eZMailing\Core\Utils\Audit;

class eZMailingRegistrationType extends eZDataType {
    const DATA_TYPE_STRING = 'ezmailingregistration';

    const SEPARATOR = "|#";

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'extension/ezmailing/text', "eZ Mailing Registration", 'Datatype name' ) );
    }

    /**
     * Validate post data, these are then used by
     * {@link eZMailingRegistrationType::fetchObjectAttributeHTTPInput()}
     *
     * @param eZHTTPTool               $http
     * @param string                   $base
     * @param eZContentObjectAttribute $contentObjectAttribute
     */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute ) {
        $classAttribute = $contentObjectAttribute->contentClassAttribute();
        if ( $http->hasPostVariable( $base . '_data_ezmailing_mailingListID_' . $contentObjectAttribute->attribute( 'id' ) ) ) {
            $mailingListsChecked = $http->postVariable( $base . '_data_ezmailing_mailingListID_' . $contentObjectAttribute->attribute( 'id' ) );
            foreach( $mailingListsChecked as $mailingListsCheckedID ) {
                if ( !is_numeric( $mailingListsCheckedID ) ) {
                    return eZInputValidator::STATE_INVALID;
                }
            }
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Set parameters from post data, expects post data to be validated by
     * {@link eZMailingRegistrationType::validateObjectAttributeHTTPInput()}
     *
     * @param eZHTTPTool               $http
     * @param string                   $base
     * @param eZContentObjectAttribute $contentObjectAttribute
     */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute ) {
        if ( $http->hasPostVariable( 'PublishButton' ) ) {
            if ( $http->hasPostVariable( $base . '_data_ezmailing_mailingListID_' . $contentObjectAttribute->attribute( 'id' ) ) ) {
                $mailingListsChecked = $http->postVariable( $base . '_data_ezmailing_mailingListID_' . $contentObjectAttribute->attribute( 'id' ) );
                $contentObjectAttribute->setAttribute( 'data_text', implode( static::SEPARATOR, $mailingListsChecked ) );

                // storing
                $Email       = static::getEmail( $contentObjectAttribute );
                $MailingUser = eZMailingUser::fetchByEmail( $Email );
                if ( !empty( $Email ) ) {
                    if ( !$MailingUser instanceof eZMailingUser ) {
                        $MailingUser = eZMailingUser::create( array( 'email' => $Email,
                                                                     'origin'=> SiteAccess::current( "name" ) ) );
                        $MailingUser->store();
                    } else {
                        // unregister the missing in the list
                        $existingRegistration = $MailingUser->getRegistrations();
                        foreach( $existingRegistration as $registration ) {
                            /**
                             * @var Registration $registration
                             */
                            if ( !in_array( $registration->attribute( 'id' ), $mailingListsChecked ) ) {
                                if ( $registration->isActive() ) {
                                    Audit::trace( "[datatypes/eZMailingRegistration/eZMailingRegistrationType] {fetchObjectAttributeHTTPInput} remove| ", array( "registrationId" => $registration->attribute( 'id' ) ) );
                                    $registration->remove();
                                }
                            }
                        }
                    }
                    foreach( $mailingListsChecked as $MailingID ) {
                        $Registration = Registration::create( array( 'mailing_user_id' => $MailingUser->attribute( 'id' ),
                                                                     'mailinglist_id'  => $MailingID,
                                                                     'state'           => Registration::REGISTRED,
                                                                     'state_updated'   => time() ) );
                        $Registration->store();
                        Audit::trace( "[datatypes/eZMailingRegistration/eZMailingRegistrationType] {fetchObjectAttributeHTTPInput} create| ", array( "mailing_id" => $MailingID) );
                    }

                    /** fill up the rest of information */
                    $ezMailingIni = eZINI::instance( 'ezmailing.ini' );
                    $attributes   = (array)$ezMailingIni->variable( 'MailingUserAccountSettings', 'MappingEzAttributes' );

                    if ( sizeof( $attributes ) > 0 ) {
                        $object  = $contentObjectAttribute->object();
                        $dataMap = $object->dataMap();
                        foreach( $attributes as $identifier_eZ => $identifier_Mailing ) {
                            if ( isset ( $dataMap[$identifier_eZ] ) ) {
                                if ( $attr_content = $dataMap[$identifier_eZ]->content() ) {
                                    $MailingUser->setAttribute( $identifier_Mailing, $attr_content );
                                }
                            }
                        }
                        $MailingUser->store();
                    }
                } else {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Stores the content, as set by {@link eZMailingRegistrationType::fetchObjectAttributeHTTPInput()}
     * or {@link eZMailingRegistrationType::initializeObjectAttribute()}
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return bool
     */
    function storeObjectAttribute( $contentObjectAttribute ) {
        return true;
    }

    /**
     * Init attribute ( also handles version to version copy, and attribute to attribute copy )
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param int|null                 $currentVersion
     * @param eZContentObjectAttribute $originalContentObjectAttribute
     */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute ) {
        // do nothing
    }

    /**
     * Return content (eZMailingRegistration object), either stored one or a new empty one based on
     * if attribute has data or not (as signaled by data_int)
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return array
     */
    function objectAttributeContent( $contentObjectAttribute ) {
        return static::getRegistrations( $contentObjectAttribute );
    }

    /**
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return mixed
     */
    function getEmail( eZContentObjectAttribute $contentObjectAttribute ) {
        $ezMailingIni             = eZINI::instance( 'ezmailing.ini' );
        $emailObjectAttributeName = $ezMailingIni->variable( 'DataTypeSettings', 'EmailObjectAttributeName' );

        // find the email
        $object    = $contentObjectAttribute->object();
        $dataMap   = $object->dataMap();
        $attrEmail = $dataMap[$emailObjectAttributeName]->content();

        switch( $dataMap[$emailObjectAttributeName]->attribute( 'data_type_string' ) ) {
            case eZUserType::DATA_TYPE_STRING:
                return $attrEmail->attribute( 'email' );
                break;
            case eZStringType::DATA_TYPE_STRING:
                return $attrEmail->content();
                break;
            case eZEmailType::DATA_TYPE_STRING:
                return $attrEmail->content();
                break;
        }
        return false;
    }

    /**
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return array
     */
    function getRegistrations( eZContentObjectAttribute $contentObjectAttribute ) {
        $Email                 = static::getEmail( $contentObjectAttribute );
        $existingRegistrations = array();
        $MailingUser           = eZMailingUser::fetchByEmail( $Email );
        if ( !empty( $Email ) && $MailingUser instanceof eZMailingUser ) {
            $existingRegistrations = $MailingUser->getRegistrations();
        }
        return $existingRegistrations;
    }

    /**
     * Indicates if attribute has content or not (data_int is used for this)
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return bool
     */
    function hasObjectAttributeContent( $contentObjectAttribute ) {
        return sizeof( static::getRegistrations( $contentObjectAttribute ) > 0 );
    }

    /**
     * Generate meta data of attribute
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return string
     */
    function metaData( $contentObjectAttribute ) {
        return static::toString( $contentObjectAttribute );
    }

    /**
     * Indicates that datatype is searchable {@link eZMailingRegistrationType::metaData()}
     *
     * @return bool
     */
    function isIndexable() {
        return false;
    }

    /**
     * Returns sort value for attribute
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return string
     */
    function sortKey( $contentObjectAttribute ) {
        return null;
    }

    /**
     * Tells what kind of sort value is returned, see {@link eZMailingRegistrationType::sortKey()}
     *
     * @return string
     */
    function sortKeyType() {
        return 'string';
    }

    /**
     * Return string data for cosumption by {@link eZMailingRegistrationType::fromString()}
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return string
     */
    function toString( $contentObjectAttribute ) {
        $aMailingList = $contentObjectAttribute->attribute( 'content' );
        $aIDs         = array();
        foreach( $aMailingList as $mailing ) {
            /**
             * @var MailingList $mailing
             */
            $aIDs[] = $mailing->attribute( 'id' );
        }
        return implode( static::SEPARATOR, $aIDs );
    }

    /**
     * Store data from string format as created in  {@link eZMailingRegistrationType::toString()}
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param string                   $string
     */
    function fromString( $contentObjectAttribute, $string ) {
        $aMailingListID = $string !== '' && strpos( $string, static::SEPARATOR ) !== false ? explode( static::SEPARATOR, $string ) : null;
        $data           = array();
        if ( $aMailingListID != null ) {
            foreach( $aMailingListID as $mailingListID ) {
                $mailing = MailingList::novaFetchByKeys( $mailingListID );
                if ( $mailing instanceof MailingList ) {
                    $data[] = $mailingListID;
                }
            }
            $contentObjectAttribute->setAttribute( 'data_text', implode( static::SEPARATOR, $data ) );
        }
    }

    /**
     * Generate title of attribute
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param string|null              $name
     * @return string
     */
    function title( $contentObjectAttribute, $name = null ) {
        $aMailingList = $contentObjectAttribute->attribute( 'content' );
        $aTitle       = array();
        foreach( $aMailingList as $mailing ) {
            /**
             * @var MailingList $mailing
             */
            $aTitle[] = $mailing->attribute( 'name' );
        }
        return implode( ",", $aTitle );
    }

    /**
     * Delete map data when attribute (version) is removed
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param int|null                 $version (Optional, deletes all versions if null)
     */
    function deleteStoredObjectAttribute( $contentObjectAttribute, $version = null ) {
        // do nothing here
    }
}

eZDataType::register( eZMailingRegistrationType::DATA_TYPE_STRING, 'eZMailingRegistrationType' );

?>
