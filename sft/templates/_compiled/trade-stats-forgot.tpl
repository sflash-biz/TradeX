<?php 
$this->vars['title'] = 'Stats Login Lost Password';
include(DIR_COMPILED . '/global-header.tpl');
 ?>

    <form action="trade-stats.php" method="post">

      <table align="center" width="900" cellspacing="0" cellpadding="4">
        <tr>
          <td colspan="2">
            <div class="ta-center">
              <h2>Stats Login Lost Password</h2>
            </div>
          </td>
        </tr>
        <?php if( $this->vars['g_errors'] ): ?>
        <tr>
          <td colspan="2">
            <div class="error">
            Password retrieval failed, please fix the following items:

            <ul>
            <?php 
if( is_array($this->vars['g_errors']) ):
    foreach( $this->vars['g_errors'] as $this->vars['error'] ):
 ?>
              <li><?php echo htmlspecialchars($this->vars['error']); ?></li>
            <?php 
    endforeach;
endif;
 ?>
            </ul>
            </div>
            <br>
          </td>
        </tr>
        <?php endif; ?>
        <tr>
          <td class="fw-bold ta-right va-top" width="40%">Domain</td>
          <td>
            <input type="text" size="25" name="domain" value="<?php echo htmlspecialchars($this->vars['g_request']['domain']); ?>">
          </td>
        </tr>
        <tr>
          <td class="fw-bold ta-right va-top">E-mail</td>
          <td>
            <input type="text" size="40" name="email" value="<?php echo htmlspecialchars($this->vars['g_request']['email']); ?>">
          </td>
        </tr>
        <tr>
          <td class="ta-center" colspan="2">
            <input type="hidden" name="r" value="remind">
            <input type="submit" value="Retrieve Password">
          </td>
        </tr>
      </table>

    </form>

<?php 
include(DIR_COMPILED . '/global-footer.tpl');
 ?>