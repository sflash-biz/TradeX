<?php 
$this->vars['title'] = 'Stats Login Lost Password';
include(DIR_COMPILED . '/global-header.tpl');
 ?>

    <table align="center" width="900" cellspacing="0" cellpadding="4">
      <tr>
        <td>
          <div class="ta-center">
            <h2>Stats Login Lost Password</h2>
          </div>

          <?php if( $this->vars['g_invalid_confirm'] ): ?>

          
          <div class="error">
            The confirmation link you followed is either invalid or expired.  Confirmation links are good for 24 hours.
          </div>

          <?php else: ?>

          
          Your new account password is listed below!

          <br><br>

          <b>Stats Login:</b> <a href="<?php echo htmlspecialchars($this->vars['g_config']['base_url']); ?>/trade-stats.php" target="_blank"><?php echo htmlspecialchars($this->vars['g_config']['base_url']); ?>/trade-stats.php</a><br>
          <b>Your Account Password:</b> <?php echo htmlspecialchars($this->vars['g_trade']['password']); ?>

          <?php endif; ?>

        </td>
      </tr>
    </table>

<?php 
include(DIR_COMPILED . '/global-footer.tpl');
 ?>