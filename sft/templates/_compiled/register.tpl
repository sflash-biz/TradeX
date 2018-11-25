<?php 
$this->vars['title'] = 'Register Site';
include(DIR_COMPILED . '/global-header.tpl');
 ?>

    <script language="JavaScript" type="text/javascript">
    $(function()
    {
        // Reload CAPTCHA image
        $('#captcha-reload').click(function()
        {
            $(this)
            .siblings('.captcha-image')
            .attr('src', 'code.php?' + Math.random());
        });
    });
    </script>

    <form method="post" action="register.php">

      <table align="center" width="900" cellspacing="0" cellpadding="4">
        <tr>
          <td colspan="2">
            <div class="ta-center">
              <h2>Register Site</h2>
            </div>

            <div class="fw-bold">Trade Rules</div>
            <div><?php echo nl2br($this->vars['g_trade_rules']); ?></div>

            <br>

            <?php if( $this->vars['g_trade_defaults']['start_raws'] || $this->vars['g_trade_defaults']['start_clicks'] || $this->vars['g_trade_defaults']['start_prod'] ): ?>
            <div class="fw-bold">To Start Trading:</div>
            <ul>
              <?php if( $this->vars['g_trade_defaults']['start_raws'] ): ?><li>Send at least <?php echo htmlspecialchars($this->vars['g_trade_defaults']['start_raws']); ?> incoming click(s)</li><?php endif; ?>
              <?php if( $this->vars['g_trade_defaults']['start_clicks'] ): ?><li>Referred surfers generate at least <?php echo htmlspecialchars($this->vars['g_trade_defaults']['start_clicks']); ?> click(s)</li><?php endif; ?>
              <?php if( $this->vars['g_trade_defaults']['start_prod'] ): ?><li>Productivity of at least <?php echo htmlspecialchars($this->vars['g_trade_defaults']['start_prod']); ?>%</li><?php endif; ?>
            </ul>
            <?php endif; ?>

            <div class="fw-bold"><span class="required">*</span> - Required field</div>
            <br>
          </td>
        </tr>
        <?php if( $this->vars['g_errors'] ): ?>
        <tr>
          <td colspan="2">
            <div class="error">
            Registration failed, please fix the following items:

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
          <td class="fw-bold ta-right va-top"><span class="required">*</span> URL to Send Traffic</td>
          <td><input type="text" size="80" name="return_url" value="<?php echo htmlspecialchars($this->vars['g_request']['return_url']); ?>"></td>
        </tr>
        <tr>
          <td class="fw-bold ta-right va-top"><?php if( $this->vars['g_config']['flag_req_email'] ): ?><span class="required">*</span> <?php endif; ?>E-mail Address</td>
          <td><input type="text" size="40" name="email" value="<?php echo htmlspecialchars($this->vars['g_request']['email']); ?>"></td>
        </tr>
        <tr>
          <td class="fw-bold ta-right va-top"><?php if( $this->vars['g_config']['flag_req_site_name'] ): ?><span class="required">*</span> <?php endif; ?>Site Name</td>
          <td>
            <input type="text" size="60" name="site_name" value="<?php echo htmlspecialchars($this->vars['g_request']['site_name']); ?>">
            <div class="fs-9pt">Your site name must be between <?php echo htmlspecialchars($this->vars['g_config']['site_name_min']); ?> and <?php echo htmlspecialchars($this->vars['g_config']['site_name_max']); ?> characters</div>
          </td>
        </tr>
        <tr>
          <td class="fw-bold ta-right va-top"><?php if( $this->vars['g_config']['flag_req_site_description'] ): ?><span class="required">*</span> <?php endif; ?>Site Description</td>
          <td>
            <input type="text" size="80" name="site_description" value="<?php echo htmlspecialchars($this->vars['g_request']['site_description']); ?>">
            <div class="fs-9pt">Your site description must be between <?php echo htmlspecialchars($this->vars['g_config']['site_description_min']); ?> and <?php echo htmlspecialchars($this->vars['g_config']['site_description_max']); ?> characters</div>
          </td>
        </tr>

        
        <?php if( $this->vars['g_config']['flag_allow_select_category'] && count($this->vars['g_categories']) ): ?>
        <tr>
          <td class="fw-bold ta-right va-top"><span class="required">*</span> Category</td>
          <td>
            <select name="category">
              <?php foreach( $this->vars['g_categories'] as $x_key => $x_value )
{
echo "<option value=\"" . htmlspecialchars($x_value) . "\"" . 
($x_value == $this->vars['g_request']['category'] ? " selected=\"selected\"" : "") . 
">" . htmlspecialchars($x_value) . "</option>";
}
 ?>
            </select>
          </td>
        </tr>
        <?php endif; ?>

        <tr>
          <td class="fw-bold ta-right va-top"><?php if( $this->vars['g_config']['flag_req_icq'] ): ?><span class="required">*</span> <?php endif; ?>ICQ Number</td>
          <td><input type="text" size="15" name="icq" value="<?php echo htmlspecialchars($this->vars['g_request']['icq']); ?>"></td>
        </tr>
        <tr>
          <td class="fw-bold ta-right va-top"><?php if( $this->vars['g_config']['flag_req_nickname'] ): ?><span class="required">*</span> <?php endif; ?>Name/Nickname</td>
          <td><input type="text" size="40" name="nickname" value="<?php echo htmlspecialchars($this->vars['g_request']['nickname']); ?>"></td>
        </tr>
        <tr>
          <td class="fw-bold ta-right va-top"><?php if( $this->vars['g_config']['flag_req_banner'] ): ?><span class="required">*</span> <?php endif; ?>Banner URL</td>
          <td><input type="text" size="80" name="banner" value="<?php echo htmlspecialchars($this->vars['g_request']['banner']); ?>"></td>
        </tr>

        
        <?php if( $this->vars['g_config']['flag_captcha_register'] ): ?>
        <tr>
          <td class="fw-bold ta-right va-top"><span class="required">*</span> Verification</td>
          <td>
            <img src="code.php" class="captcha-image">
            <span id="captcha-reload">&#8634;</span>
            <br>
            <input type="text" name="captcha" size="20">
          </td>
        </tr>
        <?php endif; ?>

        <tr>
          <td class="ta-center" colspan="2">
            <input type="hidden" name="r" value="register">
            <input type="submit" value="Submit">
          </td>
        </tr>

      </table>

    </form>

<?php 
include(DIR_COMPILED . '/global-footer.tpl');
 ?>