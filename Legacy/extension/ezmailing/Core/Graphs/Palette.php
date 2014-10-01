<?php
/**
 * Palette : Description.
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

use ezcGraphPaletteTango;
use ezcGraph;

class Palette extends ezcGraphPaletteTango {
    protected $axisColor = '#00709F';
    protected $majorGridColor = '#D3D7DF';
    protected $dataSetSymbol = array( ezcGraph::BULLET );
    protected $fontName = 'sans-serif';
    protected $fontColor = '#1E1E1E';
    protected $chartBackground = '#FFFFFFFF';
}
