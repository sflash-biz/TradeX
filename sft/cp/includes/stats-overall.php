<?php
include 'global-header.php';
include 'global-menu.php';
require_once 'stats.php';
require_once 'dirdb.php';

$defaults = array('status' => null,
                  'group' => null,
                  'category' => null);

$_REQUEST = array_merge($defaults, $_REQUEST);
?>

<script>
var COOKIE_NAME_TRADES = 'so_trades';
//var COOKIE_NAME_SYSTEM = 'so_system';
var STATS_HOURLY = false;
</script>

<script type="text/javascript" src="js/stats-overall.js"></script>

    <div class="centered-header">
      Overall Stats: <span id="num-items"><?php echo format_int_to_string(count(dir_read_files(DIR_TRADES))); ?></span> Total Trades
    </div>


  <!-- GRAND TOTAL STATS START -->
<?php
$so_trades = new StatsOverall(array('domain' => 'Trade Totals'));
$trade_stats = load_overall_stats_trades();
foreach( $trade_stats as /** @var StatsOverall */ $so )
{
  $so_trades->AddStats($so);
}
unset($trade_stats);

$so_system = new StatsOverall(array('domain' => 'System Totals'));
$system_stats = load_overall_stats_system();
foreach( $system_stats as /** @var StatsOverall */ $so )
{
  if (empty($so->trade['flag_exlude_global'])) $so_system->AddStats($so);
  if ($so->trade['domain'] == 'search-engine') $so_se = $so;
}
unset($system_stats);

$so_total = new StatsOverall(array('domain' => 'Grand Total'));
$so_total->AddStats($so_trades);
$so_total->AddStats($so_system);
$so = $so_total;

?>
  <table id="top-system-stats" class="item-table no-wrap" border="0" cellspacing="0" cellpadding="4" align="center" style="min-width: 700px;">
      <tr>
        <td>Hits</td>
        <td>In</td>
        <td>U.In</td>
        <td>Out</td>
        <td>Clks</td>
        <td>Qual</td>
        <td id="item-search-engine" width="1">SE In/<b>U.In</b></td>
        <td id="item-prod">Prod</td>
        <td>Skim</td>
        <td>Ret</td>
      </tr>
      <tr>
        <td>60 Minutes</td>
        <td class="int"><?php echo format_int_to_string($so->i_raw_60); ?></td>
        <td class="int"><?php echo STATS_UNKNOWN; ?></td>
        <td class="int"><?php echo format_int_to_string($so->o_raw_60); ?></td>
        <td class="int"><?php echo format_int_to_string($so->c_raw_60); ?></td>
        <td style="padding: 2px 0px;" class="qly">
          <div class="quality quality-good" style="width: <?php echo $so->i_ctry_g_pct_60; ?>%" title="Good: <?php echo $so->i_ctry_g_pct_60; ?>%"></div>
          <div class="quality quality-normal" style="width: <?php echo $so->i_ctry_n_pct_60; ?>%" title="Normal: <?php echo $so->i_ctry_n_pct_60; ?>%"></div>
          <div class="quality quality-bad" style="width: <?php echo $so->i_ctry_b_pct_60; ?>%" title="Bad: <?php echo $so->i_ctry_b_pct_60; ?>%"></div>
        </td>
        <td class="int" id="item-search-engine"><?php echo format_int_to_string($so_se->i_raw_60) . ' / ' . STATS_UNKNOWN; ?></td>
        <td class="pct" id="item-prod"><?php echo $so->prod_60; ?>%</td>
        <td class="pct"><?php echo STATS_UNKNOWN; ?></td>
        <td class="pct"><?php echo $so_trades->return_60; ?>%</td>
      </tr>
      <tr>
        <td>24 Hours</td>
        <td class="int"><?php echo format_int_to_string($so->i_raw_24); ?></td>
        <td class="int" style="font-weight: bold;"><?php echo format_int_to_string($so->i_uniq_24); ?></td>
        <td class="int"><?php echo format_int_to_string($so->o_raw_24); ?></td>
        <td class="int"><?php echo format_int_to_string($so->c_raw_24); ?></td>
        <td style="padding: 2px 0px;" class="qly">
          <div class="quality quality-good" style="width: <?php echo $so->i_ctry_g_pct_24; ?>%" title="Good: <?php echo $so->i_ctry_g_pct_24; ?>%"></div>
          <div class="quality quality-normal" style="width: <?php echo $so->i_ctry_n_pct_24; ?>%" title="Normal: <?php echo $so->i_ctry_n_pct_24; ?>%"></div>
          <div class="quality quality-bad" style="width: <?php echo $so->i_ctry_b_pct_24; ?>%" title="Bad: <?php echo $so->i_ctry_b_pct_24; ?>%"></div>
        </td>
        <td class="int" id="item-search-engine"><?php echo format_int_to_string($so_se->i_raw_24) . ' / <b>' . format_int_to_string($so_se->i_uniq_24) . '</b>'; ?></td>
        <td class="pct" id="item-prod"><?php echo $so->prod_24; ?>%</td>
        <td class="pct"><?php echo $so->skim_24; ?>%</td>
        <td class="pct"><?php echo $so_trades->return_24; ?>%</td>
      </tr>
  </table>
  <!-- GRAND TOTAL STATS END -->








    <!-- SEARCH OPTIONS START -->
    <div class="ta-center block-center search-fields">
      <form action="index.php" method="post">
        <b>Status:</b>
        <select name="status">
          <option value="">-- ALL --</option>
          <?php
          $statuses = array(STATUS_UNCONFIRMED,STATUS_NEW,STATUS_ACTIVE,STATUS_AUTOSTOPPED,STATUS_DISABLED);
          echo form_options($statuses, $_REQUEST['status']);
          ?>
        </select>

        <b style="margin-left: 15px;">Group:</b>
        <select name="group">
          <option value="">-- ALL --</option>
          <?php
          $groups = array_map('trim', file(FILE_GROUPS));
          echo form_options($groups, $_REQUEST['group']);
          ?>
        </select>

        <b style="margin-left: 15px;">Category:</b>
        <select name="category">
          <option value="">-- ALL --</option>
          <?php
          $categories = array_map('trim', file(FILE_CATEGORIES));
          echo form_options_multi($categories, $_REQUEST['category']);
          ?>
        </select>

        <input type="submit" value="Submit" style="margin-left: 15px;">
        <input type="hidden" name="r" value="_xStatsOverallShow">
      </form>
    </div>
    <!-- SEARCH OPTIONS END -->



    <!-- LEGEND START -->
    <div class="ta-center fw-bold" style="margin-bottom: 8px; display: none;">
      <span class="unconfirmed">Unconfirmed</span>
      <span class="new">New</span>
      <span class="active">Active</span>
      <span class="autostopped">Autostopped</span>
      <span class="disabled">Disabled</span>
    </div>
    <!-- LEGEND END -->



    <!-- TRADE STATS START -->
    <table id="trade-stats" class="item-table no-wrap" border="0" cellspacing="0" cellpadding="4" align="center" style="min-width: 1000px;">
      <?php
      _stats_overall_table_header();
      $so_trades = new StatsOverall(array('domain' => 'Trade Totals'));
      $trade_stats = load_overall_stats_trades();
      ?>
      <tbody>
      <?php

      foreach( $trade_stats as /** @var StatsOverall */ $so )
      {
          $so_trades->AddStats($so);
          _stats_overall_table_row($so);
      }
      ?>
      </tbody>
      <tfoot>
      <?php
      _stats_overall_table_row($so_trades, null);
      unset($trade_stats);
      ?>
      </tfoot>
    </table>
    <!-- TRADE STATS END -->


    <br>


    <!-- SYSTEM TRADE STATS START -->
    <table id="system-stats" class="item-table no-wrap" border="0" cellspacing="0" cellpadding="4" align="center" style="min-width: 1000px;">
      <?php
      _stats_overall_table_header('System Trade');
      $so_system = new StatsOverall(array('domain' => 'System Totals'));
      $system_stats = load_overall_stats_system();
      ?>
      <tbody>
      <?php
      foreach( $system_stats as /** @var StatsOverall */ $so )
      {
          if (empty($so->trade['flag_exlude_global'])) $so_system->AddStats($so);
          _stats_overall_table_row($so, 'system-action-menu', true);
      }
      ?>
      </tbody>
      <tfoot>
      <?php
      _stats_overall_table_row($so_system, null, true);
      unset($system_stats);
      ?>
      <tr style="background-color: #fff;"><td colspan="999"> &nbsp; </td></tr>
      <?php
      $so_total = new StatsOverall(array('domain' => 'Grand Total'));
      $so_total->AddStats($so_trades);
      $so_total->AddStats($so_system);
      _stats_overall_table_row($so_total, null, true);
      ?>
      </tfoot>
    </table>
<br>
    <!-- SYSTEM TRADE STATS END -->



    <!-- TOOLBAR START -->
    <div id="toolbar">
      <div id="toolbar-content">
        <div id="toolbar-icons">
          <a href="_xTradesAddShow" class="dialog" title="Add Trade"><img src="images/add-32x32.png" border="0"></a>
          <a href="index.php?r=_xNetworkSyncShow&SyncAddedTrades=1" title="Sync Last Added\Edited Trades"><img src="images/sync-added-edited-32x32.png" border="0"></a>
          <img src="images/toolbar-separator-2x32.png">
          <a href="index.php?r=_xStatsOverallShow"><img src="images/reload-32x32.png" title="Refresh" border="0"></a>
          <img src="images/toolbar-separator-2x32.png">
          <img src="images/edit-32x32.png" class="action" title="Edit">
          <img src="images/disable-32x32.png" class="action" title="Disable">
          <img src="images/enable-32x32.png" class="action" title="Enable">
          <img src="images/email-32x32.png" class="action" title="E-mail">
          <img src="images/reset-32x32.png" class="action" title="Reset">
          <img src="images/delete-32x32.png" class="action" title="Delete">
          <img src="images/toolbar-separator-2x32.png">
          <a href="docs/stats-overall.html" title="Documentation" target="_blank"><img src="images/help-32x32.png" border="0"></a>
        </div>
      </div>
    </div>

    <div id="toolbar-vspacer"></div>
    <!-- TOOLBAR END -->



    <!-- TRADE ACTION MENU START -->
    <div id="trade-action-menu" class="icon-menu" style="top: 8px; left: 8px;" tabindex="0">
      <div>
        <div fnc="_xTradesDetailedShow"><img src="images/detailed-16x16.png"> <span>Detailed</span></div>
        <div fnc="_xTradesGraphShow"><img src="images/graph-16x16.png"> <span>Graph</span></div><br>
        <div fnc="_xTradesHistoryShow"><img src="images/history-16x16.png"> <span>History</span></div>
        <div fnc="_xTradesCountriesShow"><img src="images/earth-16x16.png"> <span>Countries</span></div><br>
        <div fnc="_xTradesLanguagesShow"><img src="images/language-16x16.png"> <span>Languages</span></div>
        <div fnc="_xTradesReferrersShow"><img src="images/referrer-16x16.png"> <span>Referrers</span></div><br>
        <div fnc="_xTradesLandingsShow"><img src="images/landing-16x16.png"> <span>Landings</span></div>
        <div fnc="_xTradesPagesShow"><img src="images/page-16x16.png"> <span>Pages</span></div><br>
        <div fnc="_xTradesLinksShow"><img src="images/link-16x16.png"> <span>Links</span></div>
        <div fnc="_xTradesEmailShow"><img src="images/email-16x16.png"> <span>E-mail</span></div><br>
        <div fnc="_xTradesEnable"><img src="images/enable-16x16.png"> <span>Enable</span></div>
        <div fnc="_xTradesDisable"><img src="images/disable-16x16.png"> <span>Disable</span></div><br>
        <!--div fnc="_xTradesEditShow"><img src="images/edit-16x16.png"> <span>Edit</span></div-->
        <div fnc="_xTradesReset" confirm="Are you sure you want to reset the stats?"><img src="images/reset-16x16.png"> <span>Reset</span></div>
        <div fnc="_xTradesDeleteShow"><img src="images/delete-16x16.png"> <span>Delete</span></div>
      </div>
    </div>
    <!-- TRADE ACTION MENU END -->



    <!-- SYSTEM MENU START -->
    <div id="system-action-menu" class="icon-menu" style="top: 8px; left: 8px;" tabindex="0">
      <div>
        <div fnc="_xTradesDetailedShow"><img src="images/detailed-16x16.png"> <span>Detailed</span></div>
        <div fnc="_xTradesGraphShow"><img src="images/graph-16x16.png"> <span>Graph</span></div><br>
        <div fnc="_xTradesHistoryShow"><img src="images/history-16x16.png"> <span>History</span></div>
        <div fnc="_xTradesCountriesShow"><img src="images/earth-16x16.png"> <span>Countries</span></div><br>
        <div fnc="_xTradesLanguagesShow"><img src="images/language-16x16.png"> <span>Languages</span></div>
        <div fnc="_xTradesReferrersShow"><img src="images/referrer-16x16.png"> <span>Referrers</span></div><br>
        <div fnc="_xTradesLandingsShow"><img src="images/landing-16x16.png"> <span>Landings</span></div>
        <div fnc="_xTradesPagesShow"><img src="images/page-16x16.png"> <span>Pages</span></div><br>
        <div fnc="_xTradesLinksShow"><img src="images/link-16x16.png"> <span>Links</span></div>
        <!--div fnc="_xSystemTradesEditShow"><img src="images/edit-16x16.png"> <span>Edit</span></div><br /-->
        <div fnc="_xTradesReset" confirm="Are you sure you want to reset the stats?"><img src="images/reset-16x16.png"> <span>Reset</span></div>
      </div>
    </div>
    <!-- SYSTEM MENU END -->


    <!-- FLOATING HEADER START -->
    <table id="thead-float" class="item-table no-wrap" border="0" cellspacing="0" cellpadding="4" align="center" style="min-width: 1000px; position: fixed; top: 0px; display: none;">
    <?php _stats_overall_table_header('Trade', false); ?>
    </table>
    <!-- FLOATING HEADER END -->

<?php
include 'global-footer.php';

function _stats_overall_table_header($item = 'Trade', $checkbox = true)
{
?>
      <thead>
        <tr class="ta-center">
  <?php if ($item == 'Trade') { ?>
          <td colspan="3"></td>
          <td colspan="6">60 Minutes</td>
  <?php } else { ?>
          <td colspan="4"></td>
          <td colspan="5">60 Minutes</td>
  <?php } ?>
          <td class="column-separator" style="padding: 0;"></td>
          <td colspan="8">24 Hours</td>
          <?php if ($item == 'Trade') { ?>
          <td class="column-separator" style="padding: 0;"></td>
          <td colspan="3">Forces</td>
          <?php } ?>
        </tr>
        <tr>
          <th width="25"><?php if( $checkbox ): ?><input type="checkbox" class="check-all" title="Left: check\uncheck all, Right: inverse"><?php endif; ?></th>
          <th><div style="width: 220px;"><?php echo $item; ?></div></th>
          <th width="90"><?php if( $item !== 'System Trade' ) echo 'Nick'; else echo 'Send Method'; ?></th>
          <?php if( $item == 'System Trade' ) echo '<th width="65">Skim</th>'; ?>
          <th width="65">In</th>
          <th width="65">Out</th>
          <th width="65">Clks</th>
          <th width="65">Qual</th>
          <th width="40">Prod</th>
  <?php if ($item == 'Trade') { ?>
          <th width="40">Ret</th>
  <?php } ?>
          <th class="column-separator" style="padding: 0;"></th>
          <th width="65">In</th>
          <th width="65">U.In</th>
          <th width="65">Out</th>
          <th width="65">Clks</th>
          <th width="60">Qual</th>
          <th width="40">Prod</th>
          <th width="40">Skim</th>
          <?php if ($item == 'Trade') { ?>
          <th width="40">Ret</th>
          <th class="column-separator" style="padding: 0;"></th>
          <th width="40">Instant</th>
          <th width="40">Hourly</th>
          <th width="40">High</th>
          <?php } ?>
        </tr>
      </thead>
<?php
}

function _stats_overall_table_row($so, $menu = 'trade-action-menu', $system = false)
{
    $domain = htmlspecialchars($so->trade['domain']);
    $status = isset($so->trade['status']) ? $so->trade['status'] : null;
    $status_lc = strtolower($status);
?>

<?php if ( $domain == 'Grand Total' ) { ?>
  <tr class="grand-total-ttitle">
    <td colspan="4"></td>
    <td colspan="5">60 Minutes</td>
    <td class="column-separator" style="padding: 0;"></td>
    <td colspan="8">24 Hours</td>
  </tr>
  <tr class="grand-total-ttitle">
    <td colspan="3"></td>
    <td></td>
    <td>In</td>
    <td>Out</td>
    <td>Clks</td>
    <td>Qual</td>
    <td>Prod</td>
    <td class="column-separator" style="padding: 0;"></td>
    <td>In</td>
    <td>U.In</td>
    <td>Out</td>
    <td>Clks</td>
    <td>Qual</td>
    <td>Prod</td>
    <td>Skim</td>
  </tr>
<?php } ?>

        <tr <?php if( !empty($so->trade['color']) ) echo 'style="background-color: ' . $so->trade['color'] . ';"'; ?> id="item-<?php echo str_replace(' ', '_', $domain); ?>" class="ta-right<?php if ($system && !empty($so->trade['flag_exlude_global'])) echo ' light-font-color"'; ?>">
          <?php if( !empty($menu) ): ?>
          <td class="ta-center">
            <input type="checkbox" value="<?php echo $domain; ?>">
          </td>
          <?php endif; ?>
          <td class="va-middle" style="padding-right: 4px;"<?php if( empty($menu) ): ?> colspan="2"<?php endif; ?>>
            <?php
            if( isset($so->trade['return_url']) ):
            ?>
            <a href="<?php echo $so->trade['return_url']; ?>" target="_blank" class="trade-link fw-bold <?php echo $status_lc; ?>" title="<?php echo $domain; ?> - <?php echo $status; ?>"><?php echo $domain; ?></a>
            <?php
            else:
                echo "<b>$domain</b>";
            endif;

            if( !$system && !empty($menu) ):
            ?>
            <img src="images/edit-16x16.png" class="trade-edit" fnc="_xTradesEditShow">
            <span class="trade-info-container">
              <img src="images/info-16x16.png">
              <div class="trade-info" trade="<?php echo $domain; ?>"></div>
            </span>
            <?php
            endif;

            if( !empty($menu) ): ?>
              <?php if( $system ) { ?> <img src="images/edit-16x16.png" class="trade-edit" fnc="_xSystemTradesEditShow"><?php } ?>
            <span class="icon-menu-container" menu="#<?php echo $menu; ?>" style="position: relative;"><img src="images/actions-16x16.png"></span>
            <?php
            endif;
            ?>
          </td>
          <?php if( !$system ) { ?>
          <td align="left" title="<?php if (!empty($so->trade['icq'])) echo 'ICQ: ' . $so->trade['icq']; if (!empty($so->trade['email'])) echo ', Email: ' . $so->trade['email']; ?>"><?php echo $so->trade['nickname']; ?></td>
          <?php } elseif( $domain != 'System Totals' && $domain != 'Grand Total' ) { ?>
          <td align="left" style="font-size: 10px;"><?php
            $send_methods = array('Normal', 'Only to trades', 'Content, Traffic URL', 'Traffic URL');
            echo $send_methods[(Int)$so->trade['send_method']]; ?></td>
          <td align="left" style="font-size: 10px;"><?php echo $so->trade['skim_scheme']; ?></td>
          <?php } else { ?>
          <td></td>
          <td></td>
          <?php } ?>
          <td class="int"><?php echo format_int_to_string($so->i_raw_60); ?></td>
          <td class="int overal-out"><?php if( $system && $domain != 'Grand Total' ) echo STATS_UNKNOWN; else echo format_int_to_string($so->o_raw_60); ?></td>
          <td class="int"><?php echo format_int_to_string($so->c_raw_60); ?></td>
          <td style="padding: 2px 0px;" class="qly">
            <div class="quality quality-good" style="width: <?php echo $so->i_ctry_g_pct_60; ?>%" title="Good: <?php echo $so->i_ctry_g_pct_60; ?>%"></div>
            <div class="quality quality-normal" style="width: <?php echo $so->i_ctry_n_pct_60; ?>%" title="Normal: <?php echo $so->i_ctry_n_pct_60; ?>%"></div>
            <div class="quality quality-bad" style="width: <?php echo $so->i_ctry_b_pct_60; ?>%" title="Bad: <?php echo $so->i_ctry_b_pct_60; ?>%"></div>
          </td>
          <td class="pct overal-prod"><?php echo $so->prod_60; ?>%</td>
          <?php if( !$system ) { ?>
          <td class="pct"><?php if( $system ) echo STATS_UNKNOWN; else echo $so->return_60; ?>%</td>
          <?php } ?>

          <td class="column-separator" style="padding: 0; border: none;"></td>

          <td class="int" style="border-left: none;"><?php echo format_int_to_string($so->i_raw_24); ?></td>
          <td class="int overal-uniq"><?php echo format_int_to_string($so->i_uniq_24); ?></td>
          <td class="int overal-out"><?php if( $system && $domain != 'Grand Total' ) echo STATS_UNKNOWN; else echo format_int_to_string($so->o_raw_24); ?></td>
          <td class="int"><?php echo format_int_to_string($so->c_raw_24); ?></td>
          <td style="padding: 2px 0px;" class="qly">
            <div class="quality quality-good" style="width: <?php echo $so->i_ctry_g_pct_24; ?>%" title="Good: <?php echo $so->i_ctry_g_pct_24; ?>%"></div>
            <div class="quality quality-normal" style="width: <?php echo $so->i_ctry_n_pct_24; ?>%" title="Normal: <?php echo $so->i_ctry_n_pct_24; ?>%"></div>
            <div class="quality quality-bad" style="width: <?php echo $so->i_ctry_b_pct_24; ?>%" title="Bad: <?php echo $so->i_ctry_b_pct_24; ?>%"></div>
          </td>

          <td class="pct overal-prod"><?php echo $so->prod_24; ?>%</td>
          <td class="pct"><?php echo $so->skim_24; ?>%</td>
          <?php if( !$system ) { ?>
          <td class="pct"><?php if( $system ) echo STATS_UNKNOWN; else echo $so->return_24; ?>%</td>

          <td class="column-separator" style="padding: 0; border: none;"></td>

          <td class="pct"><?php if ( !empty($so->trade['force_instant_owed']) ) echo $so->trade['force_instant_owed']; else echo ''; ?></td>
          <td class="pct"<?php if ($so->trade['force_hourly_end']) echo " style=\"text-decoration: underline;\" title=\"End: {$so->trade['force_hourly_end']}\""; else echo " style=\"color: red;\""; ?>><?php if ($so->trade['force_hourly']) { echo $so->trade['force_hourly'] . ' * '; if ($so->trade['force_hourly_end']) echo ceil((date_create($so->trade['force_hourly_end'])->getTimestamp() - time()) / (60 * 60)); else echo '&amp;'; } ?></td>
          <td class="pct"><?php if ($so->trade['flag_force_instant_high']) echo '<img src="images/checkbox-checked-16x16.png">'; ?></td>
          <?php } ?>
        </tr>

<?php
}
?>