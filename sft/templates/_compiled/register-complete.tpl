<?php 
$this->vars['title'] = 'Registration Complete';
include(DIR_COMPILED . '/global-header.tpl');
 ?>

    <table align="center" width="900" cellspacing="0" cellpadding="4">
      <tr>
        <td>
          <div class="ta-center">
            <h2>Registration Complete</h2>
          </div>

          <?php if( $this->vars['g_trade']['status'] == STATUS_UNCONFIRMED ): ?>

          
          Your account information has been recorded and a confirmation e-mail has been sent to <b><?php echo htmlspecialchars($this->vars['g_trade']['email']); ?></b>.
          This e-mail message contains a link that you must visit to confirm your account.  Please visit that link
          within 24 hours.

          <?php else: ?>

          
          Your trade account has been successfully setup and you can start sending traffic at any time!

          <br><br>

          <b>Send Traffic To:</b> <a href="<?php echo htmlspecialchars($this->vars['g_config']['traffic_url']); ?>" target="_blank"><?php echo htmlspecialchars($this->vars['g_config']['traffic_url']); ?></a><br>
          <?php if( $this->vars['g_config']['flag_allow_login'] ): ?>
          <b>Stats Login:</b> <a href="<?php echo htmlspecialchars($this->vars['g_config']['base_url']); ?>/trade-stats.php" target="_blank"><?php echo htmlspecialchars($this->vars['g_config']['base_url']); ?>/trade-stats.php</a><br>
          <b>Your Account Password:</b> <?php echo htmlspecialchars($this->vars['g_trade']['password']); ?>
          <?php endif; ?>

          <?php endif; ?>

        </td>
      </tr>
    </table>

<?php 
include(DIR_COMPILED . '/global-footer.tpl');
 ?>