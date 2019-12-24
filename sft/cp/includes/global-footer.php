
<script>
    var start_check = true;
    $("#trade-stats")
        .mousedown(function() {
            $("input").mousedown(function() {
                start_check = !$(this).prop("checked");
            });
            $(".ta-center input").mousemove(function () {
                $(this).prop("checked", start_check);
            })
        }).mouseup(function () {
        $(".ta-center input").unbind('mousemove');
    }).mouseleave(function () {
        $(".ta-center input").unbind('mousemove');
    });
</script>


<script>
    var latest = {};
    var installed = {timestamp: '<?php echo TIMESTAMP; ?>'};
    $.getScript('http://sflash.biz/sft-dist/version.php?' + Math.random() + '.js', function()
    {
        latest.timestamp = Math.floor(Date.parse(latest.released)/1000);
        $('#latest-version').text(latest.version);
        $('#latest-released').text(latest.released);

        if( latest.timestamp > installed.timestamp )
        {
            $('#new-version-message').show();
            $('#upd-icon').show();
        }
        else
        {
            $('#no-new-version-message').show();
        }
    });
</script>

</body>
</html>