<?php
/**
 * Handling Campaigns Action of eZ Mailing
 *
 * @author    //autogen//
 * @copyright //autogen//
 * @license   //autogen//
 * @version   //autogen//
 */

use Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\ResendCampaign;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\Registration;
use Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Transport;
use Novactive\eZPublish\Extension\eZMailing\Core\Models\MailingList;
use Novactive\eZPublish\Extension\eZMailing\Core\Utils\Audit;

/**
 * @var Novactive\eZPublish\Extension\eZMailing\Core\Models\Campaign $item
 * @var eZHTTPTool                                                   $http
 * @var eZTemplate                                                   $tpl
 * @var eZModule                                                     $Module
 */

/**
 * StoreCampaignAction
 */

if ( $Module->isCurrentAction( "ResendCampagnNoOpenAction" ) ) {

    /* Creation de la campagne */

    if ( $item->isSent() ) {

        $db     = eZDB::instance();
        $params = array( "campaign_id" => $item->attribute( "id" ) );
        $db->query( "INSERT INTO ezpending_actions( action, created, param ) VALUES ( '" . ResendCampaign::RESEND_ACTION . "', " . time() . ", '" . serialize( $params ) . "' )" );

        $tpl->setVariable( 'success_message', ezpI18n::tr( 'extension/ezmailing/text', 'This campaign is scheduled to be re-send.' ) );
    } else {

        $tpl->setVariable( 'errors', ezpI18n::tr( 'extension/ezmailing/text', "You have to send the campaign before re-send it." ) );
    }
}
if ( $Module->isCurrentAction( "StoreCampaignAction" ) ) {
    $aErrors = array();

    $subject      = substr( $http->postVariable( "subject" ), 0, 60 );
    $description  = $http->postVariable( "description" );
    $sender_name  = $http->postVariable( "sender_name" );
    $sender_email = $http->postVariable( "sender_email" );

    $report_email = $http->postVariable( "report_email" );

    $year  = $http->postVariable( "mailing_datetime_year_mailing" );
    $month = $http->postVariable( "mailing_datetime_month_mailing" );
    $day   = $http->postVariable( "mailing_datetime_day_mailing" );

    $hour   = $http->postVariable( "mailing_datetime_hour_mailing" );
    $minute = $http->postVariable( "mailing_datetime_minute_mailing" );
    $second = $http->postVariable( "mailing_datetime_second_mailing" );

    $sending_date = mktime( $hour, $minute, $second, $month, $day, $year );

    $content_type   = $http->postVariable( "content_type" );
    $content_manual = $http->postVariable( "manual_content" );

    $recurrencyValue  = $http->postVariable( "recurrency" );
    $recurrencyPeriod = $http->postVariable( "recurrency_period" );

    $siteaccess = $http->postVariable( "siteaccess" );

    $destination = $http->postVariable( "mailinglists_destination" );

    $node_id = ( $http->hasPostVariable( 'node_id' ) ? $http->postVariable( "node_id" ) : 0 );

    if ( $item->isEditable() ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', "The campaign state doesn’t allow its modification" );
    } else {

        if ( empty( $subject ) ) {
            $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must fill the subject of the campaign.' );
        }

        if ( empty( $sender_name ) ) {
            $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must fill the sender name of the campaign.' );
        }

        if ( empty( $sender_email ) ) {
            $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must fill the sender email of the campaign.' );
        } elseif ( !eZMail::validate( $sender_email ) ) {
            $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'The sender email syntax is not correct.' );
        }

        if ( Transport::isProvider() ) {
            if ( empty( $report_email ) ) {
                $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must fill the report email of the campaign.' );
            } elseif ( !eZMail::validate( $report_email ) ) {
                $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'The report email syntax is not correct.' );
            }
        }

        if ( $sending_date <= time() ) {
            $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must fill a date in the future.' );
        }

        if ( sizeof( $siteaccess ) <= 0 ) {
            $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must choose a siteaccess for the campaign.' );
        }

        if ( sizeof( $destination ) <= 0 ) {
            $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must choose at least a mailing list.' );
        }

        //@todo: ajouter un check sur la recurrence impossible lors de campaign static
        if ( $content_type == Campaign::STATIC_CONTENT && $recurrencyValue > 0 ) {
            $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', "You cannot choose a recurrence on static content" );
        }
    }

    $item->setAttribute( 'subject', $subject );
    $item->setAttribute( 'description', $description );
    $item->setAttribute( 'sender_name', $sender_name );
    $item->setAttribute( 'sender_email', $sender_email );
    $item->setAttribute( 'sending_date', $sending_date );
    $item->setAttribute( 'destination_mailing_list', implode( ':', $destination ) );
    $item->setAttribute( 'siteaccess', $siteaccess );
    $item->setAttribute( 'node_id', $node_id );
    $item->setAttribute( 'content_type', $content_type );
    $item->setAttribute( 'report_email', $report_email );
    $item->setAttribute( 'report_sent', 0 );

    // store of the native content
    $content = Transport::getNativeContent( $item );
    $item->setContent( $content );
    unset( $content );

    // recurrency
    if ( $recurrencyValue > 0 ) {
        switch( $recurrencyPeriod ) {
            case "day":
                $multiplicator = 24 * 3600;
                break;
            case "week":
                $multiplicator = 24 * 3600 * 7;
                break;
        }
        $recurrencyTime = $recurrencyValue * $multiplicator;
        $item->setAttribute( 'recurrency_period', intval( $recurrencyTime ) );
    }

    if ( sizeof( $aErrors ) > 0 ) {
        $tpl->setVariable( 'errors', $aErrors );
    } else {
        $item->setAttribute( 'state', Campaign::DRAFT );
        $item->setAttribute( 'state_updated', time() );
        $item->setAttribute( 'draft', 0 );
        $item->store();
        $tpl->setVariable( 'success_message', ezpI18n::tr( 'extension/ezmailing/text', 'This item was successfully stored.' ) );
    }
}

/**
 * CreateCampaignAction
 */

if ( $Module->isCurrentAction( "CreateCampaignAction" ) ) {
    $campaign = Campaign::create();
    $campaign->setAttribute( 'draft', 1 );
    $campaign->setAttribute( 'sending_date', time() );

    $campaign->store();
    return $Module->redirectModule( $Module, "campaigns", array( 'edit',  $campaign->attribute( 'id' ) ) );
}

/**
 * CampaignSendAction
 */

if ( $Module->isCurrentAction( "CampaignSendAction" ) ) {
    return $Module->redirectModule( $Module, "campaigns", array( 'send', $http->postVariable( 'CampaignID' ) ) );
}

/**
 * CampaignStatsAction
 */

if ( $Module->isCurrentAction( "CampaignStatsAction" ) ) {
    return $Module->redirectModule( $Module, "campaigns", array( 'stats', $http->postVariable( 'CampaignID' ) ) );
}

/**
 * RemoveRegistrationsAction
 */

if ( $Module->isCurrentAction( "RemoveRegistrationsAction" ) ) {
    $itemsIDs = $http->postVariable( 'itemsActionCheckbox' );
    foreach( $itemsIDs as $it ) {
        list( $mailinglist_id, $user_id ) = explode( ':', $it );
        $registration = Registration::fetchByUserAndMailingID( $user_id, $mailinglist_id );
        if ( $registration instanceof Registration ) {
            Audit::trace( "[actions/campaigns] {RemoveRegistrationsAction} remove| ", array( "userId" => $user_id, "mailingListId" => $mailinglist_id ) );
            if ( !$registration->isActive() ) {
                Transport::removeRegistration( $registration );
            }
            $registration->remove();
        }
    }
}

/**
 * RemoveCampaignAction
 */

if ( $Module->isCurrentAction( "RemoveCampaignAction" ) or ( $currentView == "campaigns" && isset( $subAction ) && $subAction == "delete" ) ) {
    if ( $subAction == "delete" ) {
        $itemsIDs = array( $itemID );
    } else {
        $itemsIDs = $http->postVariable( 'itemsActionCheckbox' );
    }

    foreach( $itemsIDs as $it ) {
        $campaign = Campaign::novaFetchByKeys( $it );
        $success  = array();
        if ( $campaign instanceof Campaign ) {

            if ( $campaign->attribute( 'state' ) != Campaign::SENDING_IN_PROGRESS ) {
                $success[] = $campaign->attribute( 'subject' );
                $campaign->remove();
            } else {
                $aErrors[] = $campaign->attribute( 'subject' ) . " " . ezpI18n::tr( 'extension/ezmailing/text', 'cannot be removed because this campaign is currently being sent.' );
            }
        }
    }

    if ( sizeof( $aErrors ) > 0 ) {
        $tpl->setVariable( 'errors', $aErrors );
        $tpl->setVariable( 'title_errors', ezpI18n::tr( 'extension/ezmailing/text', 'Failed to delete' ) );
    }

    if ( sizeof( $success ) > 0 ) {
        $tpl->setVariable( 'success_message', ezpI18n::tr( 'extension/ezmailing/text', 'These items were successfully deleted :' ) . " " . implode( ',', $success ) );
    }
}

/**
 * Send Campaigns Test
 */
if ( $Module->isCurrentAction( "SendTestAction" ) ) {
    $mailsTest     = explode( ',', $http->postVariable( 'SendTestEmail' ) );
    $realMailsTest = array();
    $aErrors       = array();

    if ( sizeof( $mailsTest ) && ( sizeof( $mailsTest ) > 1 || $mailsTest[0] != "email@email.net" ) ) {
        foreach( $mailsTest as $tMail ) {
            if ( !eZMail::validate( $tMail ) ) {
                $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', '%mail is not a valid syntax for a mail.', '', array( '%mail'=> $tMail ) );
            } else {
                $realMailsTest[] = array( 'email' => $tMail );
            }
        }
        if ( sizeof( $realMailsTest ) <= 0 ) {
            $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'There is no valid email in your test list.' );
        } else {
        }
    } else {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You must fill the sender test email for this campaign.' );
    }

    if ( $item->isSent() ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', "You cannot test a campaign already sent." );
    } elseif ( $item->isWaitingForSend() ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', "You cannot test a campaign waiting to be sent." );
    } elseif ( $item->isSendingInProgress() ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', "You cannot test a campaign in sending." );
    } elseif ( $item->isCancelled() ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', "You cannot test a campaign cancelled." );
    } elseif ( $item->isDeleted() ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', "You cannot test a campaign deleted." );
    }

    if ( $item->attribute( 'sending_date' ) < time() ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', "Warning, the sending date of the campaign is expired." );
    }

    if(!empty($aErrors))
    {
        $tpl->setVariable('errors', $aErrors);
        return;
    }

    if( Transport::sendCampaignForTest($item, $realMailsTest) )
    {
        $item->setAttribute('state', Campaign::TESTED);
        $item->store();
    }
    else
    {
        $aErrors[] = ezpI18n::tr('extension/ezmailing/text', "The synchronization service is temporarily unavailable.");
        $tpl->setVariable('errors', $aErrors);
        return;
    }
}

/**
 * Confirm Campaign
 */
if ( $Module->isCurrentAction( "ConfirmAction" ) ) {
    $aErrors = array();
    if ( $item->attribute( 'sending_date' ) < time() ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', "Warning, the sending date of the campaign is expired." );
    }

    if ( sizeof( $aErrors ) ) {
        $tpl->setVariable( 'errors', $aErrors );
    } else {
        $item->setAttribute( 'state', Campaign::CONFIRMED );
        $item->store();
    }
}

/**
 * Validate Campaign
 */
if ( $Module->isCurrentAction( "ValidSendingAction" ) ) {

    if ( $item->attribute( 'sending_date' ) < time() ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', "Warning, the sending date of the campaign is expired." );
        $tpl->setVariable( 'errors', $aErrors );
    }

    if ( $item->attribute( 'state' ) == Campaign::SENT ) {
        $aErrors[] = ezpI18n::tr( 'extension/ezmailing/text', 'You can\'t edit a campaign already sent.' );
        $tpl->setVariable( 'errors', $aErrors );
    } else {
        $item->setAttribute( 'state', Campaign::WAITING_FOR_SEND );
        $item->store();
    }
}

/**
 * Cancel to Draft Campaign
 */
if ( $Module->isCurrentAction( "CancelToDraftAction" ) ) {
    if ( !$item->isEditable() ) {
        $item->setAttribute( 'state', Campaign::DRAFT );
        $item->store();
    }
}

/**
 * Cancel Campaign
 */
if ( $Module->isCurrentAction( "CancelAction" ) ) {
    if ( !$item->isEditable() ) {
        $item->setAttribute( 'state', Campaign::CANCELLED );
        $item->store();
    }
}

/**
 * Get HTML
 */
if ( $Module->isCurrentAction( "CampaignHTMLExportAction" ) ) {
    $content = $item->getContent();
    header( "Content-type: text/html" );
    header( "Content-Disposition: attachment; filename=" . urlencode( $item->attribute( "subject" ) ) . ".html" );
    header( "Pragma: no-cache" );
    header( "Expires: 0" );
    print $content;
    eZExecution::cleanExit();
}

/**
 * SearchCampaignAction
 */
if ( $Module->isCurrentAction( "SearchCampaignAction" ) ) {
    return $Module->redirectModule( $Module, "campaigns", array( 'search', $http->postVariable( 'SearchCampaignText' ) ) );
}