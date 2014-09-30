<?php
/**
 *
 * Created by Sébastien Morel for eZ 47 - Extensions.
 * Aka: Plopix
 * Date: 2013-05-06
 * Time: 10:25
 *
 * Copyright ${PROJECT_AUTHOR} 2013
 */

namespace Novactive\eZPublish\Extension\eZMailing\Core\Processors;

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use eZINI;
use eZDebug;
use eZExtension;

class Automate extends Processor {

    public function __invoke() {

        $ini      = eZINI::instance( 'ezmailing.ini' );
        $handlers = array_unique( $ini->variable( 'AutomatedSettings', 'ExtensionDirectories' ) );

        foreach( $handlers as $extensionDirectory ) {

            $fileName = eZExtension::baseDirectory() . '/' . $extensionDirectory . '/autocampaign/' . $extensionDirectory . 'Handler.php';
            if ( file_exists( $fileName ) ) {
                $className = $extensionDirectory . 'AutoCampaignHandler';
                if ( class_exists( $className ) ) {
                    /**
                     * @var \Novactive\eZPublish\Extension\eZMailing\Core\Utils\AutomatedHandler $handler
                     */
                    $handler   = new $className();
                    $campaigns = $handler->getCampaigns();
                    foreach( $campaigns as $campaign ) {
                        if ( $campaign instanceof Campaign ) {
                            $campaign->store();
                        }
                    }
                } else {
                    eZDebug::writeError( 'Cound not find automated campaign handler class ( defined in ezmailing.ini ) : ' . $fileName );
                }
            } else {
                eZDebug::writeError( 'Cound not find automated campaign handler ( defined in ezmailing.ini ) : ' . $fileName );
            }
        }
    }
}