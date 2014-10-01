<?php
/**
 * Repartition : Description.
 *
 * eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>

 * @link      http://www.novactive.com
 *
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Graphs;

use Novactive\eZPublish\Extension\eZMailing\Core\Functions\Collection;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;

use ezpI18n;
use ezcGraphArrayDataSet;
use eZINI;

class Browser extends AbstractGraph {

    public function __construct( Campaign $item ) {
        parent::__construct( "ezcGraphPieChart" );
        $result  = Collection::fetchBrowserStats( $item->attribute( 'id' ), 'browser_name' );
        $dataSet = array();
        foreach( $result['result'] as $info ) {
            $dataSet[$info['type']]++;
        }
        $this->_graph->title           = ezpI18n::tr( 'extension/ezmailing/text', 'Browser Repartition' );
        $this->_graph->data["dataSet"] = new ezcGraphArrayDataSet( $dataSet );
    }
}
