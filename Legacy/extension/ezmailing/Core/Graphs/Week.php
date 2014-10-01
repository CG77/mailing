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
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Stat;

use ezpI18n;
use ezcGraphArrayDataSet;
use ezcGraphChartElementLabeledAxis;
use ezcGraphAxisRotatedLabelRenderer;
use ezcGraphChartElementNumericAxis;
use ezcGraph;
use eZINI;

class Week extends AbstractGraph {
    
    public function __construct( $nbWeeks = 1 ) {
        parent::__construct( "ezcGraphBarChart", false );
        $results = Collection::fetchStatsWeeks( $nbWeeks );
        $campaigns = Campaign::novaFetchObjectList( array(  'state' => Campaign::SENT,
        													'sending_date' => array( '>', time() - 3600 * 24 * 7 * $nbWeeks ),
                                                            'remote_id' => array( '=', '' ) ) );
        $dataSetOpened = array();
        $dataSetSent = array();
        /*PAS DE RELANCE A PRENDRE EN COMPTE EN STANDARD*/
        foreach( $results['result'] as $result ) {
            $formatedDate = substr( $result['unit'], 0, 4 ) . " " . substr( $result['unit'], 5 );
            if ( !isset( $dataSetOpened[$formatedDate] ) ) {
                $dataSetOpened[$formatedDate] = 0;
            }
            $dataSetOpened[$formatedDate] += $result['count'];
        }
        //var_dump($dataSetOpened);exit();
        foreach ( $campaigns as $campaign ) {
            $date = date( 'Y m-d', $campaign->attribute( 'sending_date' ) );
            if ( !isset( $dataSetSent[$date] ) ) {
                $dataSetSent[$date] = 0;
            }
            $dataSetSent[$date] += $campaign->getCountRegistrations();
        }
        
        $this->_graph->data['SENT'] = new ezcGraphArrayDataSet ( $dataSetSent );
        $this->_graph->data['SENT']->color = "#6E99A2";
        $this->_graph->data[Stat::OPENED_STATKEY] = new ezcGraphArrayDataSet ( $dataSetOpened );
        $this->_graph->data[Stat::OPENED_STATKEY]->color = "#E46809";
        $this->_graph->data[Stat::OPENED_STATKEY]->displayType = ezcGraph::LINE;
    }

    public function setRenderer( $svgOutput = true ) {
        parent::setRenderer( $svgOutput );
        $this->_graph->options->fillLines                 = 100;
        $this->_graph->legend                             = true;
        $this->_graph->options->font->maxFontSize         = 10;
        $this->_graph->data[Stat::OPENED_STATKEY]->symbol = ezcGraph::NO_SYMBOL;
        $this->_graph->data['SENT']->symbol               = ezcGraph::SQUARE;
        $this->_graph->xAxis->axisSpace                   = .2;
        $this->_graph->xAxis                              = new ezcGraphChartElementLabeledAxis();
        $this->_graph->xAxis->axisLabelRenderer           = new ezcGraphAxisRotatedLabelRenderer();
        $this->_graph->xAxis->axisLabelRenderer->angle    = 45;
        $this->_graph->renderer->options->barMargin       = .45;
        $this->_graph->renderer->options->barPadding      = .45; 
        $this->_graph->renderer->options->dataBorder      = 0;
    }
}
