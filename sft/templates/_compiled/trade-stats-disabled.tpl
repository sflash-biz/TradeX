<?php 
$this->vars['title'] = 'Stats Login Disabled';
include(DIR_COMPILED . '/global-header.tpl');
 ?>

    <table align="center" width="900" cellspacing="0" cellpadding="4">
      <tr>
        <td>
          <div class="ta-center">
            <h2>Stats Login</h2>
          </div>

          <div class="error ta-center">
            Sorry, logging in to view stats is not currently available
          </div>
        </td>
      </tr>
    </table>

<?php 
include(DIR_COMPILED . '/global-footer.tpl');
 ?>