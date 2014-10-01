<?php

/**
 * SMTP
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

use eZSMTPTransport;

class SMTP extends Transport {

    public function _getMailTransport() {
        return new eZSMTPTransport();
    }
}