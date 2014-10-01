<?php
/**
 * RemoteState : Description.
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

namespace Novactive\eZPublish\Extension\eZMailing\Core\Processors;

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Transport;
use eZINI;

class RemoteState extends Processor {

    public function __invoke() {

        $ini         = eZINI::instance( 'ezmailing.ini' );
        $elapsedTime = $ini->variable( 'ProviderSettings', 'ReportSendAfterElapsedTime' ) * 24 * 3600;

        $campaigns = Campaign::novaFetchObjectList( array( 'state'             => Campaign::SENT,
                                                           'sending_date'      => array( '<=', time() - $elapsedTime ),
                                                           'remote_id'         => array( '!=', '' ) ) );
        foreach( $campaigns as $item ) {
            /**
             * @var Campaign $item
             */
            if ( $remote_id = $item->getRemoteId() && !$item->isReportSent() && $item->hasReportEmail() ) {
                if ( Transport::sendReportForCampaign( $item ) ) {
                    $item->setAttribute( 'report_sent', 1 );
                    $item->store();
                }
            }
        }

        $campaigns = Campaign::novaFetchObjectList( array( 'state'     => Campaign::SENDING_IN_PROGRESS,
                                                           'remote_id' => array( '!=',
                                                                                 '' ) ) );
        foreach( $campaigns as $item ) {
            /**
             * @var Campaign $item
             */
            if ( $remote_id = $item->getRemoteId() ) {
                $state = Transport::checkCampaignState( $item );
                if ( $state == Campaign::SENT ) {
                    $item->setAttribute( 'state', Campaign::SENT );
                    $item->store();
                }
            }
        }
    }
}
