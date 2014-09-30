<?php
/**
 * BounceBack : Description.
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Processors;

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Transport;

class BounceBack extends Processor {

    public function __invoke() {
        $campaigns = Campaign::novaFetchObjectList( array( 'state'        => Campaign::SENT,
                                                           'sending_date' => array( '>', time() - 3600 * 24 * 2 ),
                                                           'remote_id'    => array( '!=', '' ) ) );
        foreach( $campaigns as $item ) {
            /**
             * @var Campaign $item
             */
            if ( $remote_id = $item->getRemoteId() ) {
                Transport::getBounce( $item );
            }
        }
    }
}
