[<?php echo htmlspecialchars($this->vars['g_config']['site_name']); ?>] Password Reset Confirmation
Someone has recently requested that your account password be reset at <?php echo htmlspecialchars($this->vars['g_config']['site_name']); ?>.
If you did not make this request, you can ignore this e-mail message.

To reset your account password, please visit this confirmation URL:
<?php echo htmlspecialchars($this->vars['g_config']['base_url']); ?>/trade-stats.php?r=confirm&id=<?php echo htmlspecialchars($this->vars['g_trade']['confirm_id']); ?>