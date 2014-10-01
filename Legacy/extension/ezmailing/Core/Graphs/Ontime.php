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
use ezcGraphChartElementLabeledAxis;
use ezcGraphAxisRotatedLabelRenderer;
use ezcGraphChartElementNumericAxis;
use ezcGraph;
use eZINI;

class Ontime extends AbstractGraph {

    public function __construct( Campaign $item, $unit ) {
        parent::__construct( "ezcGraphLineChart", false );
        $results = Collection::fetchStats( $item->attribute( 'id' ), $unit );
        $dataSet = array();
        foreach( $results['result'] as $result ) {
            $siteINI     = eZINI::instance( 'site.ini' );
            $siteUrl     = $siteINI->variable( "SiteSettings", "SiteURL" );
            $formatedURL = str_replace( "http://$siteUrl", "", $result['url'] );

            $formatedUnit                         = str_replace( "-", " ", $result['unit'] );
            $dataSet[$formatedURL][$formatedUnit] = $result['count'];
        }

        $this->_graph->title = ezpI18n::tr( 'extension/ezmailing/text', 'Progression per ' . $unit . 's' );
        foreach( $dataSet as $title => $data ) {
            $this->_graph->data[$title] = new ezcGraphArrayDataSet( $data );
        }
    }

    public function setRenderer( $svgOutput = true ) {
        parent::setRenderer( $svgOutput );
        $this->_graph->yAxis                           = new ezcGraphChartElementNumericAxis();
        $this->_graph->yAxis->label                    = ezpI18n::tr( 'extension/ezmailing/text', 'Click count' );
        $this->_graph->xAxis                           = new ezcGraphChartElementLabeledAxis();
        $this->_graph->xAxis->label                    = ezpI18n::tr( 'extension/ezmailing/text', 'Time unit' );
        $this->_graph->xAxis->axisLabelRenderer        = new ezcGraphAxisRotatedLabelRenderer();
        $this->_graph->xAxis->axisLabelRenderer->angle = 45;
        $this->_graph->xAxis->axisSpace                = .15;
        $this->_graph->options->fillLines              = 225;
        $this->_graph->additionalAxis['OPENED']        = $nAxis = new ezcGraphChartElementNumericAxis();
        $nAxis->position                               = ezcGraph::BOTTOM;
        $nAxis->chartPosition                          = 0.999;
        $nAxis->label                                  = ezpI18n::tr( 'extension/ezmailing/text', 'Open count' );
        $this->_graph->data['OPENED']->yAxis           = $nAxis;
    }
}
