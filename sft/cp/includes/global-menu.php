<?php
global $C;

if( !isset($C['base_url']) ):
?>
<script language="JavaScript" type="text/javascript">
$(function()
{
    $('a[href="_xGlobalSettingsShow"]').click();
});
</script>
<?php
endif;
//var_dump($_REQUEST['r']);
?>

    <div id="menu-bar">
      <div>
        <a href="index.php"><?php if (empty($_REQUEST['r']) || (!empty($_REQUEST['r']) && ($_REQUEST['r']=='_xStatsOverallShow' || $_REQUEST['r']=='_xIndexShow'))) {?><img src="images/refresh-32x32.png" border="0" title="Refresh Overal Stats"><?php } else { ?><img src="images/stats-32x32.png" border="0" title="Overal Stats"><?php } ?></a>
        <a href="/" target="_blank" class="home-btn" title="Site Home"><img src="images/home-32x32.png" border="0"></a>
        <?php
        if (!empty($C['cms_url'])) {
        ?>
        <a href="<?php echo $C['cms_url']; ?>" target="_blank" class="home-btn" title="CMS Login"><img src="images/cms-32x32.png" border="0"></a>
        <?php } ?>
        <span id="server_date_time">0000-00-00<br>00:00:00</span>
        <!--a href="index.php?r=_xStatsOverallShow"><img src="images/stats-reload-40x32.png" title="Go to Stats and Refresh" border="0"></a-->
        <a href="docs/index.html" target="_blank"><img src="images/help-22x22.png" border="0"></a>
        <a href="index.php?r=_xLogout"><img src="images/logout-32x32.png" border="0"></a>

        <span class="menu">
          <a href="index.php?r=_xStatsOverallShow">&lsaquo; Stats &rsaquo;</a>
          <div>
            <a href="index.php?r=_xStatsOverallShow">Overall</a>
            <a href="index.php?r=_xStatsHourlyShow">Hourly</a>
            <a href="index.php?r=_xStatsHistoryShow">History</a>
            <a href="index.php?r=_xStatsCountriesShow">Countries</a>
            <a href="index.php?r=_xStatsLanguagesShow">Languages</a>
            <a href="index.php?r=_xStatsLandingsShow">Landings</a>
            <a href="index.php?r=_xStatsPagesShow">Pages</a>
            <a href="index.php?r=_xStatsLinksShow">Links</a>
            <a href="index.php?r=_xStatsSearchTermsShow">Search Terms</a>
            <a href="index.php?r=_xOutlistsShow">Outlists</a>
          </div>
        </span>

        <span class="menu">
          <a href="_xTradesAddShow" class="dialog">&lsaquo; Trades &rsaquo;</a>
          <div>
            <a href="_xTradesAddShow" class="dialog">Add Trade</a>
            <a href="_xTradesBulkAddShow" class="dialog">Bulk Add Trades</a>
            <a href="_xTradesDefaultsShow" class="dialog">New Trade Defaults</a>
            <a href="index.php?r=_xNetworkSyncShow&SyncAddedTrades=1">Sync Last Added/Edited</a>
          </div>
        </span>

        <span class="menu">
          <a href="index.php?r=_xToplistsShow">&lsaquo; Toplists &rsaquo;</a>
          <div>
            <a href="index.php?r=_xToplistsShow">Manage Toplists</a>
            <a href="_xToplistsAddShow" class="dialog">Add a Toplist</a>
            <a href="_xToplistsBuildAll" class="xhr">Build All Toplists</a>
          </div>
        </span>

        <span class="menu">
          <a href="index.php?r=_xNetworkStatsShow">&lsaquo; Network &rsaquo;</a>
          <div>
            <a href="index.php?r=_xNetworkStatsShow">Stats</a>
            <a href="index.php?r=_xNetworkSitesShow">Manage Sites</a>
            <a href="_xNetworkSitesAddShow" class="dialog">Add a Site</a>
            <a href="index.php?r=_xNetworkSyncShow">Sync Settings</a>
          </div>
        </span>

        <span class="menu">
          <a href="index.php?r=_xSiteTemplatesShow">&lsaquo; Templates &rsaquo;</a>
          <div>
            <a href="index.php?r=_xSiteTemplatesShow">Site Templates</a>
            <a href="index.php?r=_xEmailTemplatesShow">E-mail Templates</a>
            <a href="_xEmailSignatureShow" class="dialog">E-mail Greeting &amp; Signature</a>
            <a href="_xTemplatesRecompileAll" class="xhr">Recompile Templates</a>
          </div>
        </span>

        <span class="menu">
          <a href="index.php?r=_xUpdateShow">&lsaquo; Tools <span id="upd-icon" class="upd d-none">upd</span> &rsaquo;</a>
          <div>
            <a href="index.php?r=_xUpdateShow">Check For Update</a>
            <a href="_xLinkGenerateShow" class="dialog">Link Generator</a>
            <a href="_xUrlEncodeShow" class="dialog">URL Encode/Decode</a>
            <a href="_xTradesExportShow" class="dialog">Export Trades</a>
            <a href="index.php?r=_xLogsShow">Logs</a>
          </div>
        </span>

        <span class="menu">
          <a href="_xGlobalSettingsShow" class="dialog">&lsaquo; Settings &rsaquo;</a>
          <div>
            <a href="_xGlobalSettingsShow" class="dialog">Global Settings</a>
            <a href="index.php?r=_xSkimSchemesShow">Skim Schemes</a>
            <a href="_xOutListSettingsShow" class="dialog">Outlist Settings</a>
            <a href="_xGroupsShow" class="dialog">Groups</a>
            <a href="_xCategoriesShow" class="dialog">Categories</a>
            <a href="_xBlacklistShow" class="dialog">Blacklist</a>
            <a href="_xCountriesShow" class="dialog">Countries</a>
            <a href="_xSearchEnginesShow" class="dialog">Search Engines</a>
            <a href="_xSpidersShow" class="dialog">Spiders</a>
            <a href="_xTradeRulesShow" class="dialog">Trade Rules</a>
            <a href="_xChangeLoginShow" class="dialog">Change Login</a>
          </div>
        </span>

      </div>
    </div>


<script language="JavaScript" type="text/javascript">
  // Show server time
  function printNumbersInterval() {
    var unix_timestamp = <?=time();?>;
    var timerId = setInterval(function() {
      unix_timestamp++;
      var date = new Date(unix_timestamp*1000);
      date = date.toISOString().replace(/[A-Z]|\.(.*?)$/g, ' ').replace(' ', '<br>');
      $("#server_date_time").html(date);
    }, 1000);
  }
  printNumbersInterval();
</script>

