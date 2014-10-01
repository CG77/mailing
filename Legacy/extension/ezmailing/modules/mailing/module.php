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

$Module   = array( 'name' => 'eZ Mailing' );
$ViewList = array();

/* Admin */

$ViewList['dashboard'] = array( 'script'                  => 'admin/dashboard.php',
                                'functions'               => array( 'dashboard' ),
                                'default_navigation_part' => 'ezmailingnavigationpart' );

$ViewList['campaigns'] = array( 'script'                  => 'admin/campaigns.php',
                                'functions'               => array( 'manage' ),
                                'params'                  => array( 'subaction',
                                                                    'id' ),
                                'default_navigation_part' => 'ezmailingnavigationpart',
                                'unordered_params'        => array( 'offset'        => 'Offset',
                                                                    'order_field'   => 'order_field',
                                                                    'order_dir'     => 'order_dir' ),
                                'single_post_actions'     => array( 'RemoveCampaignButton'      => 'RemoveCampaignAction',
                                                                    'StoreCampaignButton'       => 'StoreCampaignAction',
                                                                    'CreateCampaignButton'      => 'CreateCampaignAction',
                                                                    'CampaignHTMLExportButton'  => 'CampaignHTMLExportAction',
                                                                    'CampaignEditButton'        => 'CampaignEditAction',
                                                                    'CampaignSendButton'        => 'CampaignSendAction',
                                                                    'CampaignStatsButton'       => 'CampaignStatsAction',
                                                                    'SendTestButton'            => 'SendTestAction',
                                                                    'ConfirmButton'             => 'ConfirmAction',
                                                                    'ValidSendingButton'        => 'ValidSendingAction',
                                                                    'CancelToDraftButton'       => 'CancelToDraftAction',
                                                                    'CancelButton'              => 'CancelAction',
                                                                    'CreateMailingListButton'   => 'CreateMailingListAction',
                                                                    'SearchCampaignButton'      => 'SearchCampaignAction',
                                                                    'ResendCampagnNoOpenButton' => 'ResendCampagnNoOpenAction' ) );

$ViewList['mailinglists'] = array( 'script'                  => 'admin/mailinglists.php',
                                   'functions'               => array( 'manage' ),
                                   'params'                  => array( 'subaction',
                                                                       'id' ),
                                   'default_navigation_part' => 'ezmailingnavigationpart',
                                   'single_post_actions'     => array( 'RemoveMailingListsButton'       => 'RemoveMailingListsAction',
                                                                       'StoreMailingButton'             => 'StoreMailingAction',
                                                                       'RemoveRegistrationsButton'      => 'RemoveRegistrationsAction',
                                                                       'CreateMailingListButton'        => 'CreateMailingListAction',
                                                                       'OrderMailingListButton'         => 'OrderMailingListAction',
                                                                       'SearchMailingListButton'        => 'SearchMailingListAction',
                                                                       'SynchronizeRegistrationsButton' => 'SynchronizeRegistrationsAction' ),
                                   'unordered_params'        => array( 'offset'        => 'Offset',
                                                                       'order_field'   => 'order_field',
                                                                       'order_dir'     => 'order_dir' ) );

$ViewList['registrations'] = array( 'script'                  => 'admin/registrations.php',
                                    'functions'               => array( 'manage' ),
                                    'params'                  => array( 'subaction',
                                                                        'id' ),
                                    'default_navigation_part' => 'ezmailingnavigationpart',
                                    'unordered_params'        => array( 'offset'        => 'Offset',
                                                                        'order_field'   => 'order_field',
                                                                        'order_dir'     => 'order_dir' ),
                                    'single_post_actions'     => array( 'RemoveRegistrationsButton' => 'RemoveRegistrationsAction',
                                                                        'SearchRegistrationButton'  => 'SearchRegistrationAction' ) );

$ViewList['users'] = array( 'script'                  => 'admin/users.php',
                            'functions'               => array( 'manage' ),
                            'params'                  => array( 'subaction',
                                                                'id' ),
                            'default_navigation_part' => 'ezmailingnavigationpart',
                            'unordered_params'        => array( 'offset'        => 'Offset',
                                                                'order_field'   => 'order_field',
                                                                'order_dir'     => 'order_dir' ),
                            'single_post_actions'     => array( 'StoreButton'              => 'StoreUserAction',
                                                                'RemoveRegistrationsButton'=> 'RemoveRegistrationsAction',
                                                                'RemoveUsersButton'        => 'RemoveUsersAction',
                                                                'CreateUserButton'         => 'CreateUserAction',
                                                                'SearchUserButton'         => 'SearchUserAction' ) );

$ViewList['import'] = array( 'script'                  => 'admin/import.php',
                             'default_navigation_part' => 'ezmailingnavigationpart',
                             'single_post_actions'     => array( 'MailingImportButton' => 'MailingImportAction' ) );

/** Stats */

$ViewList['read'] = array( 'script'                  => 'stats/read.php',
                           'params'                  => array( 'id',
                                                               'key' ) );

$ViewList['continue'] = array( 'script'              => 'stats/continue.php',
                               'params'              => array( 'id',
                                                               'key',
                                                               'url' ) );

$ViewList['graph'] = array( 'script'           => 'stats/graph.php',
                            'functions'        => array( 'stats' ),
                            'params'           => array( 'name',
                                                         'id',
                                                         'imagetype' ),
                            'unordered_params' => array( 'unit' => 'Unit' ) );

/** Registration */

$ViewList['register']   = array( 'script'                  => 'front/register.php',
                                 'single_post_actions'     => array( 'MailingRegisterButton' => 'MailingRegisterAction', ),
                                 'params'                  => array( 'key' ) );
$ViewList['unregister'] = array( 'script'                  => 'front/unregister.php',
                                 'single_post_actions'     => array( 'MailingUnregisterButton' => 'MailingUnregisterAction', ),
                                 'params'                  => array( 'key' ) );

$ViewList['export'] = array( 'script'                  => 'admin/export.php',
                             'params'                  => array( 'class',
                                                                 'state',
                                                                 'text' ),
                             'unordered_params'        => array( 'fix_item_type' => 'fix_item_type',
                                                                 'fix_item_id'   => 'fix_item_id' ) );

/* Front */

$ViewList['show'] = array( 'script'                  => 'front/show.php',
                           'params'                  => array( 'id',
                                                               'subject' ) );

$FunctionList['dashboard'] = array();
$FunctionList['manage']    = array( 'campaigns',
                                    'mailinglists',
                                    'users',
                                    'usersdeleted',
                                    'usersblocked',
                                    'settings',
                                    'history' );
$FunctionList['stats']     = array();

?>