[<?php echo htmlspecialchars($this->vars['g_config']['site_name']); ?>] New Trade Registered
A new trade has been registered at <?php echo htmlspecialchars($this->vars['g_config']['site_name']); ?>.  
The following information was submitted for this trade:

Traffic URL: <?php echo htmlspecialchars($this->vars['g_trade']['return_url']); ?>

Site Name: <?php echo htmlspecialchars($this->vars['g_trade']['site_name']); ?>


You can access your SFTrade control panel to view additional details:
<?php echo htmlspecialchars($this->vars['g_config']['base_url']); ?>/cp/index.php