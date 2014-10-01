<?php
/**
 * Sending : Description.
 *
 * eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>

 * @link      http://www.novactive.com
 *
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Processors;

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Transport;

class Sending extends Processor {

    public function __invoke() {
        $campaigns = Campaign::novaFetchObjectList( array( 'state'        => Campaign::WAITING_FOR_SEND,
                                                           'sending_date' => array( '<=',
                                                                                    time() ) ) );
        $nbSend    = 0;
        foreach( $campaigns as $item ) {
            /**
             * @var Campaign $item
             */
            if ( $item->isAllMailingListsAvailable() ) {
                $this->write( "Sending mailing {$item->attribute('subject')} (id:{$item->attribute('id')}) " );
                // delete tests stats
                $item->removeStats();
                $return = Transport::sendCampaign( $item );

                if ( $return ) {
                    if ( $item->isRecurring() ) {
                        $nextItem = clone $item;
                        $nextItem->store();
                        // store of the native content
                        $content = Transport::getNativeContent( $nextItem );
                        $nextItem->setContent( $content );
                        unset( $content );
                        $nextItem->store();

                        /* Création de la future campagne chez le provider */
                        Transport::createCampaign( $nextItem );
                    }
                    $nbSend++;
                } else {
                    $this->write( "{$item->attribute('subject')} wasn't send." );
                }
            } else {
                $this->write( "{$item->attribute('subject')} can't be sent because one of its mailinglist is unavailable." );
            }
        }
        $this->write( "$nbSend sent." );
    }
}
