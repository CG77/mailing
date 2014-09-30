<?php

/**
 * SMTP
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Standard;

use eZSMTPTransport;

class SMTP extends Transport {

    public function _getMailTransport() {
        return new eZSMTPTransport();
    }
}