<?php

require_once 'includes/functions.php';
require_once 'global-header.php';


if (isset($_REQUEST['delete']) && $_REQUEST['delete'] == 'self')
{
    if (unlink(__FILE__))
        echo '<br><div style="text-align: center">reset-access.php file was deleted from server</div>';
    else
        echo '<br><div style="text-align: center; color: red;">Cant delete reset-access.php, try do it manually</div>';
}
else
{
    $password = get_random_password();
    file_write(FILE_CP_USER, 'admin|' . sha1($password) . '||');
    cp_session_cleanup(true);
}

if (empty($password)) {
    if (!empty($_REQUEST['pass']))
        $password = $_REQUEST['pass'];
    else
        $password = '';
}

?>

    <div class="block-center margin-top-bottom-10px" style="width: 550px;">
      Your SFTrade control panel login information has been set and is listed below.
      Please bookmark the control panel and write down both the username and password
      for safe keeping!

      <br><br>

      <a href="../cp/index.php" target="_blank">SFTrade Control Panel</a><br>
      <b>Username:</b> admin<br>
      <b>Password:</b> <input type="text" value="<?php echo $password; ?>" onclick="this.select();" style="border:none; outline: none; vertical-align: baseline;">

      <br><br>

      <form method="POST" action="reset-access.php">
          <input type="hidden" name="delete" value="self">
          <input type="hidden" name="pass" value="<?php echo $password; ?>">
          <input type="submit" value="Delete reset-access.php">
      </form>
    </div>

<?php
require_once 'global-footer.php';
?>

