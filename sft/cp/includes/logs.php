<?php
include 'global-header.php';
include 'global-menu.php';

$_REQUEST['sort_by'] = isset($_REQUEST['sort_by']) ? $_REQUEST['sort_by'] : 'name';
?>

    <script type="text/javascript" src="js/logs.js"></script>

    <div class="centered-header">
      Logs
    </div>

    <table align="center" width="90%" cellspacing="0" class="item-table">
      <thead>
        <tr>
          <td class="ta-center" style="width: 25px;">
            <input type="checkbox" class="check-all">
          </td>
          <td class="<?php if ($_REQUEST['sort_by'] == 'name') echo 'sort-by'; ?>" style="width: 90px;">
            <a href="index.php?r=_xLogsShow&sort_by=name">Name</a>
          </td>
          <td class="<?php if ($_REQUEST['sort_by'] == 'size') echo 'sort-by'; ?>" style="width: 90px;">
             <a href="index.php?r=_xLogsShow&sort_by=size">Size</a>
          </td>
          <td>
            Content
          </td>
        </tr>
      </thead>
      <tbody>
        <?php

        $log_files = glob(DIR_BASE . '/logs/*.log');

        if (count($log_files) > 0)
        {

            foreach ($log_files as $item)
            {
                $log_fileName = str_replace(DIR_BASE . '/logs/', '', $item);
                $log_fileSize = filesize($item);
                $log_contents = file_get_contents($item);
                $arr_logs_size[] = array(
                    'name' => $log_fileName,
                    'size' => $log_fileSize,
                    'contents' => $log_contents
                );
            }

            foreach ($arr_logs_size as $key => $row)
            {
                $name[$key] = $row['name'];
                $size[$key] = $row['size'];
            }

            if ($_REQUEST['sort_by'] == 'size')
            {
                array_multisort($size, SORT_DESC, $name, SORT_ASC, $arr_logs_size);
            } else
            {
                array_multisort($name, SORT_ASC, $arr_logs_size);
            }

            foreach ($arr_logs_size as $item)
            {
                $log_fileSize = number_format($item['size'] / 1024, 3, '.', ' ');
                $str_log = <<<LOG
        <tr id="item-{$item['name']}">
          <td class="ta-center" style="width: 25px;">
            <input type="checkbox" value="{$item['name']}">
          </td>
          <td style="font-weight:bold;">{$item['name']}</td>
          <td>$log_fileSize Kb</td>
          <td><textarea style="width: 100%; height: 100px;">{$item['contents']}</textarea></td>
        </tr>
LOG;
                echo $str_log;

            }

        }
        ?>
      </tbody>
    </table>


    <div id="toolbar">
      <div id="toolbar-content">
        <div id="toolbar-icons">
          <img src="images/delete-32x32.png" class="action" title="Delete">
          <img src="images/toolbar-separator-2x32.png">
          <a href="docs/logs.html" title="Documentation" target="_blank"><img src="images/help-32x32.png" border="0"></a>
        </div>
      </div>
    </div>

    <div id="toolbar-vspacer"></div>

<?php
include 'global-footer.php';
?>