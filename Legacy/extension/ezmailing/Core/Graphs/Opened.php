<?php
/**
 * Repartition : Description.
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Graphs;

use Novactive\eZPublish\Extension\eZMailing\Core\Functions\Collection;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;

use ezpI18n;
use ezcGraphArrayDataSet;
use eZINI;

class Opened extends AbstractGraph {

    public function __construct( Campaign $item ) {
        parent::__construct( "ezcGraphPieChart" );
        $result    = Collection::fetchStats( $item->attribute( 'id' ) );
        $dataSet   = array();
        $opened    = $result['result']['opened'];
        $cible     = $item->attribute( 'receivers_count' );
        $notopened = $cible - $opened;
        if ( $notopened < 0 ) {
            $notopened = 0;
        }
        $dataSet[ezpI18n::tr( 'extension/ezmailing/text', 'Open' )]     = $opened;
        $dataSet[ezpI18n::tr( 'extension/ezmailing/text', 'Not open' )] = $notopened;
        $this->_graph->title                                            = ezpI18n::tr( 'extension/ezmailing/text', 'Opened Mails' );
        $this->_graph->data["dataSet"]                                  = new ezcGraphArrayDataSet( $dataSet );
    }
}
