<?php

require_once 'lib/global.php';
require_once 'utility.php';
require_once 'captcha.php';

headers_no_cache();

$captcha = new Captcha();
$captcha->GenerateAndDisplay();

