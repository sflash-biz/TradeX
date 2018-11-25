<?php
include 'global-header.php';
include 'global-menu.php';

global $g_counter, $g_color;

?>

    <table width="900" align="center">
      <tr>
        <td valign="top">

    <div class="centered-header">
      Main Outlist
    </div>

      <?php _outlist_table_header(); ?>
      <tbody>
        <?php
        $outlist = load_outlist(FILE_OUTLIST_MAIN);
        $g_counter = 1;
        $g_color = '#ececec';
        $outlist_size = count($outlist['trades']);
        $outlist_weights = explode('|', file_get_contents(FILE_OUTLIST_WEIGHTS));
        $outlist_weights = array_slice($outlist_weights, 0, $outlist_size);
        $weights_sum = array_sum($outlist_weights);

        $weights_stamp = array();

        foreach ($outlist_weights as $point)
        {
            if ($weights_sum) $weights_stamp[] = $point * 100 / $weights_sum;
        }

        $trade_outlist_i = 0;

        foreach ( $outlist['trades'] as $trade )
        {
            _outlist_table_row_main(
                $trade,
                isset($weights_stamp[$trade_outlist_i]) ? number_format($weights_stamp[$trade_outlist_i], 2) : 0
            );
            $trade_outlist_i++;
        }
        ?>
      </tbody>
    </table>

        </td>
        <td><div style="width: 25px;"></div></td>
        <td valign="top">


    <div class="centered-header">
      Primary Bonus Outlist
    </div>

      <?php _outlist_table_header(); ?>
      <tbody>
        <?php
        $outlist = load_outlist(FILE_OUTLIST_PRIMARY);
        $g_counter = 1;
        $g_color = '#ececec';

        foreach( $outlist['trades'] as $trade )
        {
            _outlist_table_row($trade, $outlist['total']);
        }
        ?>
      </tbody>
    </table>

        </td>
      </tr>
      <tr>
        <td colspan="3"><div style="height: 10px"></div></td>
      </tr>
      <tr>
        <td valign="top">

    <div class="centered-header">
      Secondary Bonus Outlist
    </div>

      <?php _outlist_table_header(); ?>
      <tbody>
        <?php
        $outlist = load_outlist(FILE_OUTLIST_SECONDARY);
        $g_counter = 1;
        $g_color = '#ececec';

        foreach( $outlist['trades'] as $trade )
        {
            _outlist_table_row($trade, $outlist['total']);
        }
        ?>
      </tbody>
    </table>

        </td>
        <td><div style="width: 25px;"></div></td>
        <td valign="top">

    <div class="centered-header">
      Forces
    </div>

      <?php _outlist_table_header(); ?>
      <tbody>
      <?php
        $outlist = load_outlist(FILE_OUTLIST_FORCES);
        $g_counter = 1;
        $g_color = '#ececec';

        foreach( $outlist['trades'] as $trade )
        {
            _outlist_table_row($trade, $outlist['total']);
        }
        ?>
      </tbody>
    </table>

        </td>
      </tr>
    </table>

    <div id="toolbar-vspacer"></div>

<?php
include 'global-footer.php';

function _outlist_table_header()
{
?>
    <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 425px; min-width: 425px;">
      <thead>
        <tr>
          <td style="width: 30px;"></td>
          <td class="ta-center">Site</td>
          <td class="ta-center" style="width: 100px;">% of Total</td>
          <td class="ta-center" style="width: 70px;">Points</td>
        </tr>
      </thead>
<?php
}

function _outlist_table_row($trade, $total)
{
    global $g_color, $g_counter;

    $g_color = $g_color == '#ffffff' ? '#ececec' : '#ffffff';
    $percent = $total > 0 ? format_float_to_percent($trade[1]/$total, 1) : 0;
        ?>
        <tr bgcolor="<?php echo $g_color; ?>">
          <td class="ta-right" style="padding-right: 4px;">
            <?php echo $g_counter++; ?>
          </td>
          <td class="ta-right"><?php echo $trade[0]; ?></td>
          <?php if( $trade[5] ): ?>
          <td style="padding: 0px;" class="ta-center">
            FORCE
          </td>
          <?php else: ?>
          <td style="padding: 0px;" class="va-middle">
            <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
              <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
            </div>
            <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
          </td>
          <?php endif; ?>
          <td class="ta-right">
            <!--[<?php echo $trade[7]; ?>]-->
            <?php echo format_float_to_string($trade[1], 2); ?>
          </td>
        </tr>
<?php
}


function _outlist_table_row_main($trade, $percent)
{
    global $g_color, $g_counter;

    $g_color = $g_color == '#ffffff' ? '#ececec' : '#ffffff';

        ?>
        <tr bgcolor="<?php echo $g_color; ?>">
          <td class="ta-right" style="padding-right: 4px;">
            <?php echo $g_counter++; ?>
          </td>
          <td class="ta-right"><?php echo $trade[0]; ?></td>
          <?php if( $trade[5] ): ?>
          <td style="padding: 0px;" class="ta-center">
            FORCE
          </td>
          <?php else: ?>
          <td style="padding: 0px;" class="va-middle">
            <div class="percent-bar ta-right" style="width: <?php echo $percent; ?>%;">
              <?php if( $percent >= 60 ) echo "<span>$percent%</span>"; ?>
            </div>
            <span class="va-middle"><?php if( $percent < 60 ) echo "$percent%"; ?></span>
          </td>
          <?php endif; ?>
          <td class="ta-right">
            <!--[<?php echo $trade[7]; ?>]-->
            <?php echo format_float_to_string($trade[1], 2); ?>
          </td>
        </tr>
<?php
}

?>