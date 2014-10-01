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

namespace Novactive\eZPublish\Extension\eZMailing\Core\Utils;

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Transport;

abstract class AutomatedHandler {

    /**
     * Get the campaign list
     * @return mixed
     */
    abstract public function getCampaigns();


    /**
     * Create a model object Campaign
     * @param string       $subject
     * @param string       $description
     * @param string       $sender_name
     * @param string       $sender_email
     * @param string       $sending_date
     * @param array        $destination
     * @param string       $siteaccess
     * @param int          $node_id
     * @param string       $content
     * @param int          $content_type
     * @param null         $report_email
     * @param int         $recurrencyValueInDay
     * @return \Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign
     */
    public function createCampaign( $subject, $description, $sender_name, $sender_email, $sending_date, array $destination, $siteaccess, $node_id = null, $content = null, $content_type = Campaign::DYN_CONTENT, $report_email = null, $recurrencyValueInDay = null ) {

        $destinations = array();
        foreach( $destination as $dest ) {
            if ( $dest instanceof MailingList ) {
                $destinations[] = $dest->attribute( 'id' );
            }
        }

        $item = Campaign::create();

        // basics state
        $item->setAttribute( 'draft', 0 );
        $item->setAttribute( 'report_sent', 0 );
        $item->setAttribute( 'state', Campaign::DRAFT );
        $item->setAttribute( 'state_updated', time() );

        // standard informations
        $item->setAttribute( 'subject', $subject );
        $item->setAttribute( 'description', $description );
        $item->setAttribute( 'sender_name', $sender_name );
        $item->setAttribute( 'sender_email', $sender_email );
        $item->setAttribute( 'sending_date', $sending_date );
        $item->setAttribute( 'destination_mailing_list', implode( ':', $destinations ) );
        $item->setAttribute( 'siteaccess', $siteaccess );


        // content type and content infos
        $item->setAttribute( 'content_type', $content_type );
        if ( $content_type == Campaign::DYN_CONTENT ) {
            $item->setAttribute( 'node_id', $node_id );
        }

        // content
        $content = Transport::getNativeContent( $item );
        $item->setContent( $content );
        unset( $content );

        // report email
        if ( $report_email ) {
            $item->setAttribute( 'report_email', $report_email );
        }

        // recurrency
        if ( $recurrencyValueInDay ) {
            $recurrencyTime = 24 * 3600 * $recurrencyValueInDay;
            $item->setAttribute( 'recurrency_period', intval( $recurrencyTime ) );
        }

        return $item;
    }
}