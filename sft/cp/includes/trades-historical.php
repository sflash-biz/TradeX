<div id="dialog-content">

    <div id="dialog-header">
        <img src="images/dialog-close-22x22.png" id="dialog-close">
        Historical Stats for <?php echo $item['domain']; ?>
    </div>

    <div id="dialog-panel" dwidth="900px">
        <div>

            <iframe src="history.php?domain=<?php echo urlencode($item['domain']); ?>" width="100%" height="420px" frameborder="0" style="margin: 0 auto; display: block;"></iframe>

        </div>
    </div>

    <div id="dialog-buttons">
        <input type="button" id="dialog-button-cancel" value="Close" style="margin-left: 10px;">
    </div>

</div>
