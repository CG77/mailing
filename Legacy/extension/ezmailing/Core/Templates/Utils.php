<?php
/**
 * Utils : Template operator
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

namespace Novactive\eZPublish\Extension\eZMailing\Core\Templates;

use Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Transport;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use eZContentObjectTreeNode;
use eZINI;

class Utils {

    function operatorList() {
        return array( 'is_campaign',
                      'get_campaigns',
                      'is_provided' );
    }

    function namedParameterPerOperator() {
        return true;
    }

    function namedParameterList() {
        return array( 'is_campaign'         => array(),
                      'get_campaigns'       => array(),
                      'is_provided'         => array() );
    }

    function modify( $tpl, $operatorName, $operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters ) {
        switch( $operatorName ) {
            case 'is_campaign':
                $operatorValue = $this->isCampaign( $operatorValue );
                break;
            case 'get_campaigns':
                $operatorValue = $this->getCampaigns( $operatorValue );
                break;
            case 'is_provided':
                $operatorValue = $this->isProvided();
                break;
        }
    }

    /**
     * Get the connected campaigns
     * @param $node
     * @return array|\eZPersistentObject
     */
    protected function getCampaigns( $node ) {
        if ( $this->isCampaign( $node ) ) {
            return Campaign::novaFetchObjectList( array( 'node_id'=> $node->attribute( 'node_id' ) ) );
        }
        return array();
    }

    /**
     * Check if this node is set for a campaign
     * @param $node
     * @return bool
     */
    protected function isCampaign( $node ) {
        if ( !$node instanceof eZContentObjectTreeNode ) {
            return false;
        }

        $count = Campaign::novaFetchObjectListCount( array( 'node_id'=> $node->attribute( 'node_id' ) ) );
        if ( $count > 0 ) {
            return true;
        }
        return false;
    }

    /**
     * Check if the system is under provider
     * @return bool
     */
    protected function isProvided() {
        return Transport::isProvider();
    }
}