<?php
/**
 * AbstractGraph : Description.
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

namespace Novactive\eZPublish\Extension\eZMailing\Core\Graphs;

use ezcGraphRenderer3d;
use ezcGraphGdDriver;
use ezcGraph;

abstract class AbstractGraph {

    protected $_graph;

    public function __construct( $typeChart, $thirdDimensions = true, $withoutLegend = false ) {
        $this->_graph          = new $typeChart();
        $this->_graph->palette = new Palette();
        if ( $withoutLegend ) {
            $this->_graph->legend = false;
        }
        if ( $thirdDimensions ) {
            $this->_graph->renderer = new ezcGraphRenderer3d();
        }
    }

    public function __get( $name ) {
        return $this->_graph->$name;
    }

    public function __set( $name, $value ) {
        return $this->_graph->__set( $name, $value );
    }

    public function __call( $name, $arguments ) {
        return call_user_func_array( array( $this->_graph,
                                            $name ), $arguments );
    }

    public function setRenderer( $svgOutput = true ) {
        /* png */
        if ( !$svgOutput ) {
            $this->_graph->driver                         = new ezcGraphGdDriver();
            $this->_graph->options->font                  = __DIR__ . "/../../../../design/standard/fonts/arial.ttf";
            $this->_graph->driver->options->supersampling = 1;
            $this->_graph->driver->options->jpegQuality   = 100;
            $this->_graph->driver->options->imageFormat   = IMG_PNG;
        } else {
            $this->_graph->options->font->name        = 'Verdana';
            $this->_graph->options->font->minFontSize = 12;
            $this->_graph->options->font->maxFontSize = 18;
            $this->_graph->title->font->maxFontSize   = 30;
        }

        $this->_graph->legend->background = '#FFFFFF';
        $this->_graph->legend->position   = ezcGraph::BOTTOM;

        $this->_graph->renderer->options->moveOut             = .2;
        $this->_graph->renderer->options->pieChartOffset      = 42;
        $this->_graph->renderer->options->pieChartGleam       = .3;
        $this->_graph->renderer->options->pieChartGleamBorder = 2;
        $this->_graph->renderer->options->pieChartShadowSize  = 5;
    }
}

?>