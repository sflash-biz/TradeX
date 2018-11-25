[<?php echo htmlspecialchars($this->vars['g_config']['site_name']); ?>] Confirm Trade Submission
Thank you for registering a trade account at <?php echo htmlspecialchars($this->vars['g_config']['site_name']); ?>.
Your trade account needs to be confirmed by visiting the link listed below.

To confirm your trade account submission, please visit this confirmation URL:
<?php echo htmlspecialchars($this->vars['g_config']['base_url']); ?>/register.php?r=confirm&id=<?php echo htmlspecialchars($this->vars['g_trade']['confirm_id']); ?>