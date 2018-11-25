<?php
global $g_counter, $g_color;
?>
<div id="dialog-content">

    <div id="dialog-header">
        <img src="images/dialog-close-22x22.png" id="dialog-close">
        Outlist Settings
    </div>

    <form method="post" action="xhr.php" class="xhr-form">

        <div id="dialog-panel">
            <div style="padding-top: 2px;">

                <input type="button" id="dialog-button-default-1" value="Load Default 1" style="width: 23%; margin-bottom: 3px;">
                <input type="button" id="dialog-button-default-2" value="Load Default 2" style="width: 23%; margin-bottom: 3px;">
                <input type="button" id="dialog-button-default-3" value="Load Default 3" style="width: 23%; margin-bottom: 3px;">
                <input type="button" id="dialog-button-default-4" value="Load Default 4" style="width: 23%; margin-bottom: 3px;">

                <?php _outlist_table_header(); ?>
                <tbody>
                <?php

                $OUTLIST_WEIGHTS_FIELDS = 15;

                $main_outlist = load_outlist(FILE_OUTLIST_MAIN);
                $g_counter = 1;
                $g_color = '#ececec';
                $outlist_weights = explode('|', file_get_contents(FILE_OUTLIST_WEIGHTS));
                $weights_sum = array_sum($outlist_weights);

                $weights_stamp = array();

                for ($i = 0; $i < $OUTLIST_WEIGHTS_FIELDS; $i++)
                {
                    if ($weights_sum && !empty($outlist_weights[$i]))
                        $weights_stamp[] = $outlist_weights[$i] * 100 / $weights_sum;
                    else
                        $weights_stamp[] = 0;
                }

                for ($i = 0; $i < $OUTLIST_WEIGHTS_FIELDS; $i++)
                {
                    $trade = current($main_outlist['trades']);
                    _outlist_table_row_main(
                        $trade ? $trade : '',
                        isset($weights_stamp[$i]) ? number_format($weights_stamp[$i], 2) : 0,
                        $outlist_weights[$i]
                    );
                    next($main_outlist['trades']);
                }

                ?>
                </tbody>
                </table>

            </div>
        </div>

        <div id="dialog-help">
            <a href="docs/outlist-settings.html" target="_blank"><img src="images/help-22x22.png"></a>
        </div>

        <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working...">
            <input type="submit" id="button-save" value="Save">
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;">
        </div>

        <input type="hidden" name="r" value="_xOutListSettingsSave">
    </form>

</div>

<?php

function _outlist_table_header()
{
    ?>
    <table class="item-table no-wrap fsize-9pt" border="0" cellspacing="0" cellpadding="4" align="center" style="width: 465px; min-width: 465px;">
        <thead>
        <tr>
            <td style="width: 30px;"></td>
            <td class="ta-center" style="width: 60px;">Weight</td>
            <td class="ta-center" style="width: 160px;">% of Total</td>
            <td class="ta-center">Site</td>
        </tr>
        </thead>
    <?php
}

function _outlist_table_row_main($trade, $percent, $points)
{
    global $g_color, $g_counter;

    $g_color = $g_color == '#ffffff' ? '#ececec' : '#ffffff';

        ?>
        <tr bgcolor="<?php echo $g_color; ?>">
          <td class="ta-right" style="padding-right: 4px;">
            <?php echo $g_counter; ?>
          </td>
          <td class="ta-right" style="padding-right: 4px;">
             <span><input type="text" size="5" value="<?php echo $points; ?>" name="weight_<?php echo $g_counter; ?>" maxlength="5" pattern="[0-9]+"></span>
          </td>
          <td style="padding: 0px;" class="va-middle">
            <div class="percent-bar ta-left" style="width: <?php echo $percent; ?>%;" id="percent-bar-<?php echo $g_counter; ?>">
              <?php if( $percent >= 60 ) echo "<span id='percent-val-$g_counter'>$percent%</span>"; ?>
            </div>
            <span class="va-middle" id='percent-val-<?php echo $g_counter; ?>'><?php if( $percent < 60 ) echo "$percent%"; ?></span>
          </td>
          <td class="ta-right"><?php echo $trade[0]; ?></td>
        </tr>
<?php

    $g_counter++;
}

?>

<script>

    var fields_amount = 15;

    var out_points_default_1 = [    // Normal
        700,
        500,
        350,
        270,
        200,
        150,
        100,
        80,
        50,
        30
    ];

    var out_points_default_2 = [    // Dynamic
        1000,
        600,
        350,
        270,
        200,
        150,
        100,
        80,
        50,
        30
    ];

    var out_points_default_3 = [    // TE
        1000,
        650,
        423,
        275,
        179,
        116,
        75,
        49,
        32,
        21,
        14,
        9
    ];

    var out_points_default_4 = [    // ATX v2
        2920,
        1945,
        1300,
        865,
        575,
        385,
        255,
        170,
        115,
        75,
        50,
        33,
        23,
        15,
        10
    ];


    function format_float(original, after_dot) {
        var div_mult = 1;
        for (var i = 0; i < after_dot; i++) div_mult *= 10;
        return Math.round(original*div_mult)/div_mult;
    }

    var func_change_wight_fields = function() {
        var arr_fields_weights = [];
        var val;
        for (var i = 1; i <=fields_amount; i++)
        {
            val = parseInt( $('input[name="weight_' + i + '"]').val() );
            arr_fields_weights[i-1] = isNaN(val) ? 0 : val;
        }
        var out_points_sum = arr_fields_weights.reduce(function(a, b) { return a + b; }, 0);
        for (i = 1; i <=fields_amount; i++)
        {
            $('input[name="weight_' + i + '"]').attr('value', arr_fields_weights[i-1] ? arr_fields_weights[i-1] : '');
            var percent_val = arr_fields_weights[i-1] ? format_float(arr_fields_weights[i-1] * 100 / out_points_sum, 2) + '%' : '0.00%';
            $('#percent-bar-' + i).css('width', percent_val);
            $('#percent-val-' + i).text(percent_val);
        }
    };

    $('input[name^="weight_"]').change(
        func_change_wight_fields
    );



    $('#dialog-button-default-1').click(function() {
        for (var i = 1; i <=fields_amount; i++)
        {
            $('input[name="weight_' + i + '"]').attr('value', out_points_default_1[i-1] ? out_points_default_1[i-1] : '');
        }
        func_change_wight_fields();
    });

    $('#dialog-button-default-2').click(function() {
        for (var i = 1; i <=fields_amount; i++)
        {
            $('input[name="weight_' + i + '"]').attr('value', out_points_default_2[i-1] ? out_points_default_2[i-1] : '');
        }
        func_change_wight_fields();
    });

    $('#dialog-button-default-3').click(function() {
        for (var i = 1; i <=fields_amount; i++)
        {
            $('input[name="weight_' + i + '"]').attr('value', out_points_default_3[i-1] ? out_points_default_3[i-1] : '');
        }
        func_change_wight_fields();
    });

    $('#dialog-button-default-4').click(function() {
        for (var i = 1; i <=fields_amount; i++)
        {
            $('input[name="weight_' + i + '"]').attr('value', out_points_default_4[i-1] ? out_points_default_4[i-1] : '');
        }
        func_change_wight_fields();
    });

</script>
