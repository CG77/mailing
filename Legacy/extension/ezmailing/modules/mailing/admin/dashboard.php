<?php
/**
 *
 * eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>

 * @link      http://www.novactive.com
 *
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Functions\Collection;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Stat;
use Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Transport;
use DateTime;

include( __DIR__ . "/bootstrap.php" );

/**
 * @var eZTemplate $tpl
 */

$path = array( array( 'url'  => 'mailing/dashboard', 'text' => ezpI18n::tr( 'extension/ezmailing/text', 'Dashboard' ) ) );

$countMailSent   = 0;
$opened          = 0;
$clicked         = 0;
$bounceBack      = 0;
$openedDelay     = 0;

if ( !Transport::isProvider() ) {
    
    $dateTwoWeek     = time() - 14 * 24 * 3600;
    
    $campaigns = Campaign::novaFetchObjectList( array( 'state'			  => Campaign::SENT,
    												  'sending_date'      => array( '>', $dateTwoWeek ),
                                                      'remote_id'         => array( '=', '' ) ) 
                                                );
    
    foreach ( $campaigns as $campaign ) {
        
        $countMailSent += $campaign->getCountRegistrations();
        
        $results = Collection::fetchStats( $campaign->attribute( 'id' ) );
        
        /*Ratio d'ouverture en minute, fonctionnel ambigu*/
        
        $resultsBis = Collection::fetchStats( $campaign->attribute( 'id' ), 'minute' );
//
        foreach ( $resultsBis['result'] as $res ) {
            if ( $res['url'] == Stat::OPENED_STATKEY && ( new DateTime( $res['unit'] ) < new DateTime( date( "Y-m-d H:i" , $campaign->attribute( 'sending_date' ) + 1800 ) ) )) {
                $openedDelay++;
            }
        }
        
        $opened += $results['result']['opened'];
        
        foreach ( $results['result']['urls'] as $url => $nb ) {
            if ( $nb > $clicked ) {
                $clicked = $nb;
            }
        }
        
    }
    
}

$tpl->setVariable( 'countMailSent', $countMailSent );
$tpl->setVariable( 'opened', $opened );
$tpl->setVariable( 'clicked', $clicked );
$tpl->setVariable( 'bounce', $bounceBack );
$tpl->setVariable( 'openedDelay', $openedDelay );

$tpl->setVariable( 'path', $path );
$Result['content'] = $tpl->fetch( 'design:mailing/dashboard.tpl' );
$Result['path']    = $path;


?>