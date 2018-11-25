<?php

require_once 'includes/functions.php';
require_once 'network-util.php';

headers_no_cache();
prepare_request();

if( ($auth_error = cp_authenticate(false)) === true )
{
    cp_exec($_REQUEST['r'], NETWORK_FNC_GET_STATS);
}
else
{
    echo 'Username and/or password invalid';
}

function _xStatsGet()
{
    require_once 'stats.php';

    network_success(load_site_stats());
}

function _xSync()
{
    $settings = unserialize(base64_decode($_REQUEST['sync']));

    // Sync blacklist
    if( isset($settings[NETWORK_SYNC_BLACKLIST]) && is_array($settings[NETWORK_SYNC_BLACKLIST]) )
    {
        foreach( $settings[NETWORK_SYNC_BLACKLIST] as $bl_file => $file_contents )
        {
            file_write(DIR_BLACKLIST . '/' . $bl_file, $file_contents);
        }
    }


    // Sync categories
    if( isset($settings[NETWORK_SYNC_CATEGORIES]) )
    {
        file_write(FILE_CATEGORIES, $settings[NETWORK_SYNC_CATEGORIES]);
    }


    // Sync countries
    if( isset($settings[NETWORK_SYNC_COUNTRIES]) )
    {
        file_write(FILE_COUNTRIES, $settings[NETWORK_SYNC_COUNTRIES]);
    }


    // Sync groups
    if( isset($settings[NETWORK_SYNC_GROUPS]) )
    {
        file_write(FILE_GROUPS, $settings[NETWORK_SYNC_GROUPS]);
    }


    // Sync network sites
    if( isset($settings[NETWORK_SYNC_NETWORK_SITES]) && is_array($settings[NETWORK_SYNC_NETWORK_SITES]) )
    {
        require_once 'textdb.php';

        $db = new NetworkDB();

        foreach( $settings[NETWORK_SYNC_NETWORK_SITES] as $site )
        {
            if( $db->Exists($site['domain']) )
            {
                $db->Update($site['domain'], $site);
            }
            else
            {
                $db->Add($site);
            }
        }

        network_site_update_stored_values();
    }


    // Sync search engines
    if( isset($settings[NETWORK_SYNC_SEARCH_ENGINES]) )
    {
        file_write(FILE_SEARCH_ENGINES, $settings[NETWORK_SYNC_SEARCH_ENGINES]);
    }


    // Sync skim schemes
    if( isset($settings[NETWORK_SYNC_SKIM_SCHEMES]) && is_array($settings[NETWORK_SYNC_SKIM_SCHEMES]) )
    {
        foreach( $settings[NETWORK_SYNC_SKIM_SCHEMES] as $scheme => $ss_file )
        {
            file_write(DIR_SKIM_SCHEMES . '/' . $scheme, $ss_file['merged']);
            file_write(DIR_SKIM_SCHEMES_BASE . '/' . $scheme,  $ss_file['base']);
            file_write(DIR_SKIM_SCHEMES_DYNAMIC . '/' . $scheme, $ss_file['dynamic']);
        }
    }


    // Sync trades
    if( isset($settings[NETWORK_SYNC_TRADES]) && is_array($settings[NETWORK_SYNC_TRADES]) )
    {
        require_once 'dirdb.php';

        $db = new TradeDB();

        foreach( $settings[NETWORK_SYNC_TRADES] as $trade )
        {
            if( $db->Exists($trade['domain']) )
            {
                // Update existing
                $db->Update($trade['domain'], $trade);
            }
            else
            {
                // Create new, no confirmation
                $trade['flag_confirm'] = 0;
                trade_add($trade);
            }
        }
    }


    //////////////////////////////////////////
    // Delete trades
    if( isset($settings[NETWORK_DELETE_TRADES]) )
    {
        require_once 'dirdb.php';

        $db = new TradeDB();

        foreach( $settings[NETWORK_DELETE_TRADES] as $trade )
        {
            // Delete existing
            $db->Delete($trade['domain']);
        }
    }
    //////////////////////////////////////////


    // Sync trade rules
    if( isset($settings[NETWORK_SYNC_TRADE_RULES]) )
    {
        file_write(FILE_TRADE_RULES, $settings[NETWORK_SYNC_TRADE_RULES]);
    }


    network_success(true);
}

?>