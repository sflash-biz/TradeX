<?php
include 'global-header.php';
include 'global-menu.php';

require_once 'stats.php';
?>

    <div class="centered-header">
      Historical Trade Stats
    </div>


    <iframe src="history.php" width="98%" height="420px" frameborder="0" style="margin: 0 auto; display: block;"></iframe>
    <iframe src="history.php?domain=search-engine" width="98%" height="420px" frameborder="0" style="margin: 0 auto; display: block;"></iframe>

<?php
include 'global-footer.php';
?>

