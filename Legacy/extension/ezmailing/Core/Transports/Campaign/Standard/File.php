<?php

/**
 * File
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Standard;

use eZFileTransport;

class File extends Transport {

    protected function _getMailTransport() {
        return new eZFileTransport();
    }
}