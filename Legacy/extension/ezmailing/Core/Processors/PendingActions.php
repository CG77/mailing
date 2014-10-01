<?php
/**
 * Pending : Description.
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

use Novactive\eZPublish\Extension\eZMailing\Core\Models\ResendCampaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\User;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;
use Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Transport;
use eZPendingActions;
use eZLog;
use eZMail;
use eZINI;

class PendingActions extends Processor {

    public function __invoke() {
        $actions = eZPendingActions::fetchByAction( MailingList::SYNCHRONISATION_ACTION );
        foreach( $actions as $action ) {
            $params  = unserialize( $action->attribute( 'param' ) );
            $mailing = MailingList::novaFetchByKeys( $params['mailinglist_id'] );
            if ( $this->executeSynchro( $mailing ) ) {
                $action->remove();
            }
        }
        $actions = eZPendingActions::fetchByAction( ResendCampaign::RESEND_ACTION );
        foreach( $actions as $action ) {
            $params   = unserialize( $action->attribute( 'param' ) );
            $campaign = Campaign::novaFetchByKeys( $params['campaign_id'] );
            if ( $this->executeResend( $campaign ) ) {
                $action->remove();
            }
        }
    }

    protected function executeSynchro( $mailingList ) {
        $mailingList->setAttribute( 'state', MailingList::SYNCHRONISATION_IN_PROGRESS );
        $mailingList->store();
        if ( $ret = Transport::synchronizeSegment( $mailingList ) ) {
            $mailingList->setAttribute( 'state', MailingList::AVAILABLE );
            $mailingList->store();
            return true;
        }
        return false;
    }

    protected function executeResend( $campaign ) {
        $ezMailingIni = eZINI::instance( 'ezmailing.ini' );
        $errorFile    = $ezMailingIni->variable( 'LogSettings', 'ErrorFile' );

        $parametersSegment = array( 'segmentation' => array( 'description' => $campaign->attribute( 'subject' ),
                                                             'name'        => $campaign->attribute( 'subject' ),
                                                             'sampleType'  => 'ALL'

        ) );
        $lastReferer       = ResendCampaign::novaFetchObjectList( array( 'campaign_id' => $campaign->attribute( 'id' ),
                                                                         'sort_by'     => array( 'last_update' => 'desc' ),
                                                                         'limit'       => 1 ) );
        if ( $lastReferer && sizeof( $lastReferer ) ) {
            $remoteID = $lastReferer[0]->attribute( 'remote_id' );
        } else {
            $remoteID = $campaign->getRemoteId();
        }

        /* Si la campagne n'existe plus */
        if ( empty( $remoteID ) || !Transport::checkCampaignExist( $campaign ) ) {
            eZLog::write( "Resend campaign '{$campaign->attribute('subject')}' (id:{$campaign->attribute('id')}), remoteID '$remoteID' - FAILURE", $errorFile );
            return true; // Pour supprimer le pending action associe
        }
        
        $segmentID = Transport::createSegment( $parametersSegment );
        // Creation des critéres liés au segment
        $parametersCriteria = array( 'actionCriteria' => array( 'groupName'   => 'Relance',
                                                                'groupNumber' => '1',
                                                                'campaignId'  => $remoteID,
                                                                'operator'    => 'DIDNOTOPENEDMESSAGE_CAMP' ) );

        $result = Transport::createCriteria( (int)$segmentID, $parametersCriteria );
        if ( $result == true ) {
            /**
             * @var Campaign $nextItem
             */
            $nextItem = clone $campaign;
            $nextItem->setAttribute( 'sending_date', time() + 120 );
            unset( $content );
            
            /* Création de la future campagne chez le provider */
            Transport::createCampaignResend( $nextItem, $segmentID );
            return true;
        }
        return false;
    }
}
