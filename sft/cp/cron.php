<?php

$path = realpath(dirname(__FILE__));
chdir($path);

require_once 'includes/functions.php';


$start = date('r');
CronAppendLog(FILE_LOG_CRON, 'Cron starting...', true);

switch($GLOBALS['argv'][1])
{
    case '--grab-thumbs':
        _xCronGrabThumbs();
        break;


    case '--build-toplists':
        build_all_toplists();
        break;

    default:
        CronAppendLog(FILE_LOG_CRON, 'Invalid command line argument: ' . $GLOBALS['argv'][1]);
        break;
}

CronAppendLog(FILE_LOG_CRON, 'Cron exiting...', true);

function _xCronGrabThumbs()
{
    global $start;

    CronAppendLog(FILE_LOG_GRABBER, 'Grabber starting...', true);

    require_once 'dirdb.php';

    $db = new TradeDB();

    foreach( $db->RetrieveAll() as $trade )
    {
        if( $trade['flag_grabber'] )
        {
            if( string_is_empty($trade['grabber_url']) )
            {
                $trade['grabber_url'] = $trade['return_url'];
            }

            $thumbnails = grab_thumbs($trade['domain'], $trade['grabber_url'], $trade['trigger_strings']);

            switch($thumbnails)
            {
                case null:
                    CronAppendLog(FILE_LOG_GRABBER, 'Thumbnails could not be downloaded from ' . $trade['domain']);
                    break;

                case 0:
                    CronAppendLog(FILE_LOG_GRABBER, 'HTTP connection for ' . $trade['domain'] . ' has failed');
                    break;

                default:
                    $db->Update($trade['domain'], array('thumbnails' => $thumbnails));
                    break;
            }
        }
    }

    CronAppendLog(FILE_LOG_GRABBER, 'Grabber exiting...', true);
}

function CronAppendLog($file, $message, $time = false)
{
    if( $time )
    {
        $message = '[' . date('r') . '] ' . $message;
    }
    else
    {
        $message = "\t$message";
    }

    file_append($file, "$message\n");
}

function ParseCommandLine()
{
    $args = array();

    foreach( $GLOBALS['argv'] as $arg )
    {
        // Check if this is a valid argument in --ARG or --ARG=SOMETHING format
        if( preg_match('~--([a-z0-9\-_]+)(=?)(.*)?~i', $arg, $matches) )
        {
            if( $matches[2] == '=' )
            {
                $args[$matches[1]] = $matches[3];
            }
            else
            {
                $args[$matches[1]] = true;
            }

        }
    }

    return $args;
}


