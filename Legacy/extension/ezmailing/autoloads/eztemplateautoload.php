<?php
/**
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
$eZTemplateOperatorArray   = array();
$eZTemplateOperatorArray[] = array( 'script'         => 'extension/ezmailing/Core/Templates/Utils.php',
                                    'class'          => 'Novactive\eZPublish\Extension\eZMailing\Core\Templates\Utils',
                                    'operator_names' => array( 'is_campaign',
                                                               'get_campaigns',
                                                               'is_provided' ) );
?>