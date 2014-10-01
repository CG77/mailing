<?php
/**
 * Graph : Make the stat graph
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

use Novactive\eZPublish\Extension\eZMailing\Core\Graphs\Repartition as RepartitionGraph;
use Novactive\eZPublish\Extension\eZMailing\Core\Graphs\Opened as OpenedGraph;
use Novactive\eZPublish\Extension\eZMailing\Core\Graphs\Ontime as OntimeGraph;
use Novactive\eZPublish\Extension\eZMailing\Core\Graphs\Week as WeekGraph;
use Novactive\eZPublish\Extension\eZMailing\Core\Graphs\OS as OSGraph;
use Novactive\eZPublish\Extension\eZMailing\Core\Graphs\Browser as BrowserGraph;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;

$Module    = $Params["Module"];
$graphName = $Params["name"];
$id        = $Params["id"];

if ( $Params['Unit'] ) {
    $unit = (string)$Params['Unit'];
} else {
    $unit = "day";
}
$isSVG = isset( $Params['imagetype'] ) ? false : true;

try {
    if ( $graphName != "week" ) {
        $item = Campaign::novaFetchByKeys( $id );
    }
    switch( $graphName ) {
        case "repartition":
            $graph = new RepartitionGraph( $item );
            break;
        case "opened":
            $graph = new OpenedGraph( $item );
            break;
        case "ontime":
            $graph = new OntimeGraph( $item, $unit );
            break;
        case "os":
            $graph = new OSGraph( $item );
            break;
        case "browser":
            $graph = new BrowserGraph( $item );
            break;
        case "week":
            $graph = new WeekGraph( $id );
            break;
    }
    
    $graph->setRenderer( $isSVG );
    $graph->renderToOutput( 800, 400 );
} catch( Exception $e ) {
    print $e->getMessage();
}
eZExecution::cleanExit();