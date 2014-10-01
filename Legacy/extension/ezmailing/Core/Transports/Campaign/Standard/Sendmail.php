<?php

/**
 * Sendmail
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

namespace Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Standard;

use eZSendmailTransport;

class Sendmail extends Transport {

    protected function _getMailTransport() {
        return new eZSendmailTransport();
    }
}