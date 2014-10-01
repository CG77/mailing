<?php
/**
 * Processor : Abstract
 *
 * eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>

 * @link      http://www.novactive.com
 *
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
