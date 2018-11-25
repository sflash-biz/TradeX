<?php

require_once 'includes/functions.php';


if( $_REQUEST['r'] == 'btl' )
{
    build_all_toplists();
    return;
}


// First access:
if( !isset($C['base_url']) )
{
    recompile_templates();  // Recompile templates
}


headers_no_cache();
prepare_request();


$reset_access_exist = file_exists('reset-access.php');
$install_exist = file_exists('../install.php');
if( $reset_access_exist || $install_exist )
{
	if ($reset_access_exist && $install_exist) $warning_message = 'The <u style="color: #0000FF">install.php</u> and cp/<u style="color: #0000FF">reset-access.php</u> files must be removed from your server before you can access the control panel';
	if ($reset_access_exist && !$install_exist) $warning_message = 'The cp/<u style="color: #0000FF">reset-access.php</u> file must be removed from your server before you can access the control panel';
	if (!$reset_access_exist && $install_exist) $warning_message = 'The <u style="color: #0000FF">install.php</u> file must be removed from your server before you can access the control panel';

    echo '<div style="font-weight: bold; color: red; font-size: 14pt; text-align: center; font-family: Arial, Helvetica;">' .
		$warning_message .
         '</div>';
    exit;
}


if( ($auth_error = cp_authenticate()) === true )
{
    cp_exec($_REQUEST['r'], '_xStatsOverallShow');
}
else
{
    include 'login.php';
}

function _xIndexShow()
{
    _xStatsOverallShow();
}

function _xUpdateShow()
{
    include 'update.php';
}

function _xLogsShow()
{
    include 'logs.php';
}

function _xSkimSchemesShow()
{
    include 'skim-schemes.php';
}

function _xToplistsShow()
{
    include 'toplists.php';
}

function _xOutlistsShow()
{
    include 'outlists.php';
}

function _xStatsHistoryShow()
{
    include 'stats-history.php';
}

function _xGraphDataHistoryStats()
{
    require_once 'stats.php';

    list($date_start, $date_end) = get_date_range($_REQUEST);
    $history = new StatsHistory(null, $date_start, $date_end, $_REQUEST['breakdown']);

    include 'trades-graph-data-historical-stats.php';
}

function _xGraphDataHistoryProd()
{
    require_once 'stats.php';

    list($date_start, $date_end) = get_date_range($_REQUEST);
    $history = new StatsHistory(null, $date_start, $date_end, $_REQUEST['breakdown']);

    include 'trades-graph-data-historical-prod.php';
}

function _xNetworkSyncShow()
{
    $syncAddedTrades = (!empty($_REQUEST['SyncAddedTrades'])) ? true : false;
    include 'network-sync.php';
}

function _xNetworkSitesShow()
{
    include 'network-sites.php';
}

function _xNetworkStatsShow()
{
    include 'network-stats.php';
}

function _xSiteTemplatesShow()
{
    include 'site-templates.php';
}

function _xEmailTemplatesShow()
{
    include 'email-templates.php';
}

function _xStatsOverallShow()
{
    include 'stats-overall.php';
}

function _xStatsHourlyShow()
{
    include 'stats-hourly.php';
}

function _xTradesGraphDataHistoryStats()
{
    require_once 'stats.php';

    list($date_start, $date_end) = get_date_range($_REQUEST);
    $history = new StatsHistory($_REQUEST['domain'], $date_start, $date_end, $_REQUEST['breakdown']);

    include 'trades-graph-data-historical-stats.php';
}

function _xTradesGraphDataHistoryProd()
{
    require_once 'stats.php';

    list($date_start, $date_end) = get_date_range($_REQUEST);
    $history = new StatsHistory($_REQUEST['domain'], $date_start, $date_end, $_REQUEST['breakdown']);

    include 'trades-graph-data-historical-prod.php';
}

function _xTradesGraphDataHourly()
{
    require_once 'stats.php';
    require_once 'dirdb.php';

    $db = get_trade_db($_REQUEST['domain']);
    $trade = $db->Retrieve($_REQUEST['domain']);
    $stats = new StatsHourly($trade);

    include 'trades-graph-data-hourly.php';
}

function _xTradesGraphDataProdReturn()
{
    require_once 'stats.php';
    require_once 'dirdb.php';

    $db = get_trade_db($_REQUEST['domain']);
    $trade = $db->Retrieve($_REQUEST['domain']);
    $stats = new StatsHourly($trade);

    include 'trades-graph-data-prod-return.php';
}

function _xTradesCountriesData()
{
    $stat = $_REQUEST['stat'];
    $domain = $_REQUEST['domain'];

    include 'trades-countries-data.php';
}

function _xStatsCountriesShow()
{
    include 'stats-countries.php';
}

function _xStatsLanguagesShow()
{
    include 'stats-languages.php';
}

function _xStatsLandingsShow()
{
    include 'stats-landings.php';
}

function _xStatsPagesShow()
{
    include 'stats-pages.php';
}

function _xStatsLinksShow()
{
    include 'stats-links.php';
}

function _xStatsSearchTermsShow()
{
    include 'stats-search-terms.php';
}

function _xLogout()
{
    cp_logout();
    include 'login.php';
}

?>
