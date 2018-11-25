<!DOCTYPE html>
<html>
  <head>
    <title>SFTrade - <?php echo htmlspecialchars($this->vars['g_config']['site_name']); ?> - <?php echo htmlspecialchars($this->vars['title']); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script type="text/javascript" src="js/jquery.js"></script>
  </head>
  <style>
  body {font-family:'Trebuchet MS', Arial, Tahoma;font-size:10pt}
  h2 {font-size:14pt;font-weight:bold;margin-top:0}
  .required {color:#ff0000;font-weight:bold}
  .ta-center {text-align:center}
  .ta-right {text-align:right}
  .va-top {vertical-align:top}
  .fw-bold {font-weight:bold}
  .fs-9pt {font-size:9pt}
  .fs-8pt {font-size:8pt}
  .captcha-image {border:1px solid black;margin-bottom:4px}
  #captcha-reload {margin-left:8px;vertical-align:top;cursor:pointer;font-size:32px}
  .error {border:1px solid #D52727;color:#D52727;background-color:#FEE7E8;padding:8px;font-weight:bold;-moz-border-radius:5px;-webkit-border-radius:5px;-webkit-box-shadow:0px 0px 6px #999;-moz-box-shadow:0px 0px 6px #999}
  .powered-by {font-size:9pt;text-align:center;margin-top:20px}
  .item-table {}
  .item-table thead tr {background-color:#333;color:#fff;font-weight:bold;font-size:105%}
  .item-table thead td {border-left:1px solid #999;border-bottom:1px solid #999}
  .item-table thead td:first-child {border-left:none}
  .item-table tbody td {border-left:1px dotted #afafaf;border-bottom:1px dotted #afafaf}
  .item-table tbody td:first-child {border-left:none}
  .item-table tr.odd {background-color:#ececec}
  </style>
  <body>