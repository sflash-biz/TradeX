<?php

require_once '../lib/global.php';
require_once 'utility.php';

// Indicate we are in the control panel
define('IN_CONTROL_PANEL', true);

// Directories
define('DIR_CP_INCLUDES', DIR_BASE . '/cp/includes');
define('DIR_CP_SESSION', DIR_DATA . '/cp_sessions');

// Files
define('FILE_CP_USER', DIR_DATA . '/cp_user');
define('FILE_CP_LOCK', DIR_DATA . '/cp_sessions/cp_login_lock');

// Misc defines
define('CP_USERNAME_FIELD', 'cp_username');
define('CP_PASSWORD_FIELD', 'cp_password');
define('NET_PASSWORD_FIELD', 'network_pass');
define('CP_SESSION_FIELD', 'cp_session');
define('CP_COOKIE_NAME', 'sftcp');
define('CP_COOKIE_PATH', preg_replace('~/cp/.*~', '/cp/', $_SERVER['REQUEST_URI']));
$parsed_url = parse_url($_SERVER['HTTP_HOST']);
define('CP_COOKIE_DOMAIN', $parsed_url['host']);
define('CP_SESSION_DURATION', 7776000);
define('CP_LOGINS_INTERVAL', 10);


// Setup include path
set_include_path(join(PATH_SEPARATOR, array(get_include_path(), DIR_CP_INCLUDES)));


// Check if it is time for an update
stats_check_update_time();




function cp_authenticate_network()
{
    //return true; // to disable authentication, disable authorisation

    if( string_is_empty($_REQUEST[CP_USERNAME_FIELD]) )
    {
        return 'The username field was left blank';
    }

    if( string_is_empty($_REQUEST[NET_PASSWORD_FIELD]) )
    {
        return 'The password field was left blank';
    }

    list($username, $password, $allowed_ips, $network_pass) = explode('|', file_first_line(FILE_CP_USER));

    if (empty($network_pass)) return sprintf('Network pass for <strong style="color: brown;">%s</strong> is empty. Is no way for net access.', CP_COOKIE_DOMAIN);

    if( $username == $_REQUEST[CP_USERNAME_FIELD] && $network_pass == $_REQUEST[NET_PASSWORD_FIELD] )
    {
        return true;
    }

    return "The supplied username/password combination is not valid";
}

function cp_authenticate($session = true)
{
    //return true; // to disable authentication, disable authorisation

    if( isset($_REQUEST[CP_USERNAME_FIELD]) )
    {
        if( string_is_empty($_REQUEST[CP_USERNAME_FIELD]) )
        {
            return 'The username field was left blank';
        }

        if( string_is_empty($_REQUEST[CP_PASSWORD_FIELD]) )
        {
            return 'The password field was left blank';
        }

        if (cp_login_locked(CP_LOGINS_INTERVAL)) return "Not so fast. Do not try login so often.";

        list($username, $password, $allowed_ips) = explode('|', file_first_line(FILE_CP_USER));

        if( $username == $_REQUEST[CP_USERNAME_FIELD]
            && $password == sha1($_REQUEST[CP_PASSWORD_FIELD])
            && array_has_substr(ips_to_array($allowed_ips), $_SERVER['REMOTE_ADDR'])
            && !cp_login_locked(CP_LOGINS_INTERVAL)
        )
        {
            if( $session )
            {
                delete_old_cp_sessions();
                cp_login_lock_delete();
                cp_session_create($username);
            }

            return true;
        }

        cp_login_lock_create();
        return sprintf('The supplied username/password combination is not valid or your IP: <strong style="color: brown;">%s</strong> is not valid for this user', $_SERVER['REMOTE_ADDR']);
    }
    else if( isset($_COOKIE[CP_COOKIE_NAME]) )
    {
        return cp_session_authenticate($_COOKIE[CP_COOKIE_NAME]);
    }
}

function cp_session_authenticate($cookie)
{
    parse_str($cookie, $cookie_data);
    $filename = DIR_CP_SESSION . '/' . file_sanitize($cookie_data[CP_SESSION_FIELD]);

    if( file_exists($filename) && is_file($filename) )
    {
        list($username, $session, $timestamp, $browser, $ip) = explode('|', file_first_line($filename));

        if( $username == $cookie_data[CP_USERNAME_FIELD] && $browser == sha1($_SERVER['HTTP_USER_AGENT']) && $ip == $_SERVER['REMOTE_ADDR'] )
        {
            define('CP_LOGGED_IN_USERNAME', $username);
            return true;
        }

        cp_logout();
        return 'Invalid control panel account';
    }
    else
    {
        cp_logout();
        return 'Your control panel session has expired';
    }
}

function ips_to_array($ips)
{
    $ips = preg_replace('~\s~', '', $ips);
    return explode(',', $ips);
}

function array_has_substr($substr_arr, $str)
{
    foreach ($substr_arr as $val) {
        if (strpos($str, $val) !== false) return true;
    }
    return false;
}

function cp_login_lock_create()
{
    $filename = FILE_CP_LOCK;
    file_write($filename, time());
}

function cp_login_lock_delete()
{
    $filename = FILE_CP_LOCK;
    if (file_exists($filename)) unlink($filename);
}

function cp_login_locked($lock_sec = 10)
{
    $filename = FILE_CP_LOCK;

    if (file_exists($filename)) {
        if (filemtime($filename) + $lock_sec - time() >= 0) {
            return true;
        } else {
            cp_login_lock_delete();
        }
    }

    return false;
}

function cp_session_create($username)
{
    $session = sha1(uniqid(rand(), true));
    $filename = DIR_CP_SESSION . '/' . $session;

    define('CP_LOGGED_IN_USERNAME', $username);

    file_write($filename, "$username|$session|" . time() . "|" . sha1($_SERVER['HTTP_USER_AGENT']) . "|{$_SERVER['REMOTE_ADDR']}");

    setcookie(CP_COOKIE_NAME,
        CP_USERNAME_FIELD . '=' . urlencode($username) . '&' . CP_SESSION_FIELD . '=' . urlencode($session),
        0,
        CP_COOKIE_PATH,
        CP_COOKIE_DOMAIN);
}

function cp_logout()
{
    parse_str($_COOKIE[CP_COOKIE_NAME], $cookie_data);
    $filename = DIR_CP_SESSION . '/' . file_sanitize($cookie_data[CP_SESSION_FIELD]);

    file_delete($filename);
    setcookie(CP_COOKIE_NAME, false, time() - CP_SESSION_DURATION, CP_COOKIE_PATH, CP_COOKIE_DOMAIN);
}

function cp_session_cleanup($clean_all = false)
{
    foreach( dir_read_files(DIR_CP_SESSION) as $file )
    {
        $file = DIR_CP_SESSION . '/' . $file;
        list($username, $session, $timestamp, $browser, $ip) = explode('|', file_first_line($file));

        if( $clean_all || $timestamp < time() - CP_SESSION_DURATION )
        {
            file_delete($file);
        }
    }
}

function merge_skim_scheme($scheme)
{
    require_once 'dirdb.php';

    $scheme_base = '-1|00|00|-1|00|00|' . file_get_contents(DIR_SKIM_SCHEMES_BASE . '/' . $scheme);

    $db = new SkimSchemeBaseDB();
    $data = $db->Retrieve($scheme);

    if( $data['dynamic'] )
    {
        $scheme_dynamic = file_get_contents(DIR_SKIM_SCHEMES_DYNAMIC . '/' . $scheme);
        file_write(DIR_SKIM_SCHEMES . '/' . $scheme, $scheme_dynamic . $scheme_base);
    }
    else
    {
        file_write(DIR_SKIM_SCHEMES . '/' . $scheme, $scheme_base);
    }
}

function write_config($settings)
{
    global $C;

    if( !file_exists(FILE_HISTORY) )
    {
        file_create(FILE_HISTORY);
    }

    unset($settings['r']);

    //$settings['domain'] = preg_replace('~^www\.~i', '', $_SERVER['HTTP_HOST']);
    $settings['domain'] = domain_from_url('http://' . $_SERVER['HTTP_HOST']);

    $C = array_merge($C, $settings);

    check_image_resizer();

    $C['base_url'] = preg_replace('~/$~', '', $C['base_url']);

    $fp = fopen(DIR_LIB . '/config.php', 'r+');
    flock($fp, LOCK_EX);
    fwrite($fp, "<?php\nglobal \$C;\n\$C = array();\n");

    foreach( $C as $key => $val )
    {
        $val = str_replace(array('\"', '\.'), array('"', '.'), addslashes($val));
        fwrite($fp, "\$C['$key'] = '$val';\n");
    }

    fwrite($fp, "?>");
    ftruncate($fp, ftell($fp));
    flock($fp, LOCK_UN);
    fclose($fp);


    $in_settings = "\$C = array(\n" .
        "'domain' => '{$C['domain']}',\n" .
        "'keyphrase' => '{$C['keyphrase']}',\n" .
        "'flag_filter_no_image' => '{$C['flag_filter_no_image']}',\n".
        "'cookie_tdxsess' => '{$C['cookie_tdxsess']}',\n".
        "'cookie_tdxsig' => '{$C['cookie_tdxsig']}',\n".
        "'cookie_tdxbookmark' => '{$C['cookie_tdxbookmark']}',\n".
        "'storage_method' => '{$C['storage_method']}',";
    if ($C['storage_method'] == 'Redis') $in_settings .= "\n'redis_host' => '{$C['redis_host']}',\n".
        "'redis_port' => '{$C['redis_port']}'";
    $in_settings .= ');';

    //$root_dir = DIR_SITE_ROOT;
    $out_settings = "\$C = array(\n" .
        "'domain' => '{$C['domain']}',\n" .
        "'dir_base' => '" . DIR_BASE . "',\n" .
        "'keyphrase' => '{$C['keyphrase']}',\n" .
        "'distrib_forces' => '{$C['distrib_forces']}',\n" .
        "'distrib_main' => '{$C['distrib_main']}',\n" .
        "'distrib_primary' => '{$C['distrib_primary']}',\n" .
        "'distrib_secondary' => '{$C['distrib_secondary']}',\n" .
        "'count_clicks' => '{$C['count_clicks']}',\n" .
        "'fast_click' => '{$C['fast_click']}',\n" .
        "'trades_satisfied_url' => '{$C['trades_satisfied_url']}',\n" .
        "'flag_filter_no_image' => '{$C['flag_filter_no_image']}',\n".
        "'cookie_tdxsess' => '{$C['cookie_tdxsess']}',\n".
        "'cookie_tdxsig' => '{$C['cookie_tdxsig']}',\n".
        "'storage_method' => '{$C['storage_method']}',";
    if ($C['storage_method'] == 'Redis') $out_settings .= "\n'redis_host' => '{$C['redis_host']}',\n".
        "'redis_port' => '{$C['redis_port']}'";
    $out_settings .= ');';

    $img_settings = "\$C = array(\n" .
        "'dir_base' => '" . DIR_BASE . "',\n" .
        "'domain' => '{$C['domain']}',\n" .
        "'keyphrase' => '{$C['keyphrase']}',\n" .
        "'cookie_tdxsess' => '{$C['cookie_tdxsess']}',\n".
        "'cookie_tdxsig' => '{$C['cookie_tdxsig']}',\n".
        "'storage_method' => '{$C['storage_method']}',";
    if ($C['storage_method'] == 'Redis') $img_settings .= "\n'redis_host' => '{$C['redis_host']}',\n".
        "'redis_port' => '{$C['redis_port']}'";
    $img_settings .= ');';


    // Write settings to in.php
    $in = string_format_lf(file_get_contents(FILE_IN_PHP));
    $in = preg_replace('~/\*#<CONFIG>\*/(.*?)/\*#</CONFIG>\*/~msi', "/*#<CONFIG>*/\n" . $in_settings . "\n/*#</CONFIG>*/", $in);
    if( version_compare(PHP_VERSION, '5.1.0', '>=') )
    {
        $timezone = date_default_timezone_get();
        $in = preg_replace('~/?/?date_default_timezone_set\(\'.*?\'\);~', "date_default_timezone_set('$timezone');", $in);
        $in = str_replace('//date_default_timezone_set($timezone);', 'date_default_timezone_set($timezone);', $in);
        $in = str_replace('//$timezone = date_default_timezone_get();', '$timezone = date_default_timezone_get();', $in);
    }
    file_write(FILE_IN_PHP, $in, 0666);

    // Write settings to out.php
    $out = string_format_lf(file_get_contents(FILE_OUT_PHP));
    //$out = string_format_lf(file_get_contents($C['out_path'])); // TIP, to get path faster
    $out = preg_replace('~/\*#<CONFIG>\*/(.*?)/\*#</CONFIG>\*/~msi', "/*#<CONFIG>*/\n" . $out_settings . "\n/*#</CONFIG>*/", $out);
    file_write(FILE_OUT_PHP, $out, 0666);

    // Write settings to image.php
    $img = string_format_lf(file_get_contents(FILE_IMAGE_PHP));
    $img = preg_replace('~/\*#<CONFIG>\*/(.*?)/\*#</CONFIG>\*/~msi', "/*#<CONFIG>*/\n" . $img_settings . "\n/*#</CONFIG>*/", $img);
    file_write(FILE_IMAGE_PHP, $img, 0666);
}

function load_countries()
{
    global $geoip_country_codes, $geoip_country_names;

    require_once 'geoip-utility.php';

    $fp = fopen(FILE_COUNTRIES, 'r');
    $weights = explode('|', trim(fread($fp, RECORD_SIZE_COUNTRY_WEIGHT)));

    $countries = array(0 => array(),
        1 => array(),
        2 => array());

    asort($geoip_country_codes);
    foreach( $geoip_country_codes as $i => $code )
    {
        if( string_is_empty($code) )
        {
            continue;
        }

        fseek($fp, RECORD_SIZE_COUNTRY_WEIGHT + $i * RECORD_SIZE_COUNTRY);
        $quality = fread($fp, RECORD_SIZE_COUNTRY);
        $countries[$quality][]
            = array($code,
            $geoip_country_names[$i]);
    }
    fclose($fp);

    return array($weights, $countries);
}

function recompile_templates()
{
    require_once 'compiler.php';

    $compiler = new Compiler();
    $files = dir_read_files(DIR_TEMPLATES);

    foreach( $files as $file )
    {
        $compiled = DIR_COMPILED . '/' . $file;

        if( ($code = $compiler->CompileFile($file, DIR_TEMPLATES)) === false )
        {
            return array(JSON_KEY_MESSAGE => 'Template ' . $file . ' contains errors',
                JSON_KEY_WARNINGS => $compiler->GetErrors());
        }

        file_write($compiled, $code);
    }

    return true;
}

function cp_exec($function, $default = '_xIndexShow')
{
    if( empty($function) )
    {
        call_user_func($default);
        return;
    }
    else if( preg_match('~^(_x[a-zA-Z0-9_]+)(\((.*?)\))?~', $function, $matches) )
    {
        $function = $matches[1];
        $arguments = isset($matches[3]) ? explode(',', $matches[3]) : array();

        if( function_exists($function) )
        {
            call_user_func_array($function, $arguments);
            return;
        }
    }

    trigger_error('Not a valid SFTrade function', E_USER_ERROR);
}

function check_image_resizer()
{
    global $C;

    $C['have_magick'] = 0;
    $C['have_gd'] = 0;

    // Check ImageMagick
    if( !string_is_empty($C['magick_mogrify_path']) )
    {
        set_error_handler('shell_exec_error_handler');
        $output = shell_exec($C['magick_mogrify_path'] . ' -resize "90x120^" 2>&1');
        restore_error_handler();

        if( empty($output) && empty($GLOBALS['shell_exec_errors']) )
        {
            $C['have_magick'] = 1;
        }
    }


    // Check GD
    if( extension_loaded('gd') )
    {
        $gdinfo = gd_info();
        if( $gdinfo['JPG Support'] )
        {
            $C['have_gd'] = 1;
        }
    }
}

////////////////////////////////
function check_redis()
{

    error_reporting(0);

    global $C;

    $C['redis_work'] = 1;
    $C['redis_port']=='sock'?$C['redis_work'] = 2:$C['redis_work'] = 1;

    // Check Redis key add\read\delete
    if( !string_is_empty($C['redis_host']) && !string_is_empty($C['redis_port']) )
    {

        if ($C['redis_work'] == 2 && ( $C['redis_host'] == 'localhost' || $C['redis_host'] == '127.0.0.1'))
            $C['redis_work'] = 3;

        try {
            $redis = new Redis();
        } catch (RedisException $e) {
            $C['redis_work'] = 4;
            return;
        }

        try {
            $redis->connect($C['redis_host'], $C['redis_port']=='sock'?null:$C['redis_port']);
            $redis->set("test:{$C['domain']}", 'test');
            $test_val = $redis->get("test:{$C['domain']}");
            $redis->del("test:{$C['domain']}", 1);
        } catch (RedisException $e) {
            $C['redis_work'] = 5;
            return;
        }

        if( $test_val != 'test' )
        {
            $C['redis_work'] = 5;
        }

    }

}
////////////////////////////////

function shell_exec_error_handler($errno, $errstr)
{
    if( !isset($GLOBALS['shell_exec_errors']) || !is_array($GLOBALS['shell_exec_errors']) )
    {
        $GLOBALS['shell_exec_errors'] = array();
    }

    $GLOBALS['shell_exec_errors'][] = $errstr;
}

