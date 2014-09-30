<?php

/**
 * Sendmail
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Standard;

use eZSendmailTransport;

class Sendmail extends Transport {

    protected function _getMailTransport() {
        return new eZSendmailTransport();
    }
}