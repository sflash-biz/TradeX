<link href="jqvmap/jqvmap.css" media="screen" rel="stylesheet" type="text/css"/>
<script src="jqvmap/jquery.vmap.min.js"></script>
<script src="jqvmap/jquery.vmap.world.js"></script>

      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close">
          Country Stats for <?php echo $item['domain']; ?>
        </div>

        <div id="dialog-panel" dwidth="850px">
          <div>

            <div class="block-center fw-bold ta-center" style="width: 800px; font-size: 110%; margin-bottom: 10px;">
              <span class="option option-selected" style="width: 32%;">In</span>
              <span class="option" style="width: 32%;">Out</span>
              <span class="option" style="width: 32%;">Clicks</span>
            </div>

            <div id="vmap" style="width: 790px; height: 400px; margin: 0 auto;"></div>
            <!--div class="block-center" id="ammap" style="width: 800px; height: 400px; border: 1px solid #666; background: transparent url(images/activity-32x32.gif) no-repeat 50% 50%;"></div-->

          </div>
        </div>

        <div id="dialog-buttons">
          <input type="button" id="dialog-button-cancel" value="Close" style="margin-left: 10px;">
        </div>

      </div>

<script>

  function reloadData(stat, src)
  {
    var url = 'index.php?r=_xTradesCountriesData&stat=' + stat + '&domain=<?php echo $item['domain'] . '&' . rand(100, 99999) . '.js'; ?>';
    $.getScript( url, function() {
      jQuery('#vmap').html('');
      jQuery('#vmap').vectorMap({
        map: 'world_en',
        backgroundColor: '#333333',
        color: '#ffffff',
        hoverOpacity: 0.7,
        selectedColor: '#666666',
        enableZoom: true,
        showTooltip: true,
        scaleColors: ['#C8EEFF', '#006491'],
        values: map_data,
        normalizeFunction: 'polynomial'
      });
    });
    $(src).addClass('option-selected').siblings().removeClass('option-selected');
  }

  $('span.option')
      .click(function()
      {
        reloadData($(this).text(), this);
      });

  $( document ).ready(function() {
    reloadData('In', 'dialog-panel span.option');
  });
</script>