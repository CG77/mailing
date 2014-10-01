<?php

/**
 * File
 *
 * eZMailing extension
 *
 * @category  eZpublish
 * @package   eZpublish.eZMailing
 * @author    Novactive <ezmailing@novactive.com>

 * @link      http://www.novactive.com
 *
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Standard;

use eZFileTransport;

class File extends Transport {

    protected function _getMailTransport() {
        return new eZFileTransport();
    }
}