<?php
/**
 * Processor : Abstract
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Processors;

abstract class Processor {

    protected $_output;

    protected function write( $text ) {
        if ( $this->_output !== null ) {
            $this->_output->output( $text );
        }
    }

    public function __construct( $o = null ) {
        $this->_output = $o;
    }
}
