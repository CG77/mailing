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

class Repartition extends AbstractGraph {

    public function __construct( Campaign $item ) {
        parent::__construct( "ezcGraphPieChart" );
        $result  = Collection::fetchStats( $item->attribute( 'id' ) );
        $dataSet = array();
        foreach( $result['result']['urls'] as $url => $nb ) {
            $siteINI               = eZINI::instance( 'site.ini' );
            $siteUrl               = $siteINI->variable( "SiteSettings", "SiteURL" );
            $formatedURL           = str_replace( "http://$siteUrl", "", $url );
            $dataSet[$formatedURL] = $nb;
        }
        $this->_graph->title = ezpI18n::tr( 'extension/ezmailing/text', 'Urls clicked repartition' );

        $this->_graph->data["dataSet"] = new ezcGraphArrayDataSet( $dataSet );
    }
}
