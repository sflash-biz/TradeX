<?php

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

@ini_set('memory_limit', -1);
@set_time_limit(0);
@session_cache_limiter('nocache');


/*#<CONFIG>*/
$C = array(
'domain' => 'localhost',
'dir_base' => '/var/www/html/sft',
'keyphrase' => '2dfc30bc18736ede5a82474dfd4f9bde',
'distrib_forces' => '30',
'distrib_main' => '50',
'distrib_primary' => '10',
'distrib_secondary' => '10',
'count_clicks' => '10',
'fast_click' => '1.25',
'trades_satisfied_url' => 'http://www.maturesladies.com/',
'flag_filter_no_image' => '',
'cookie_tdxsess' => 'sftse',
'cookie_tdxsig' => 'sftsi',
'storage_method' => 'File system',);
/*#</CONFIG>*/


// Globals
$g_force_type = null;


// Session variables
$now = time();
$session_length = 3600;
$unique = false;
$g_external_info = false;
$tdxsess = $C['cookie_tdxsess'];
$tdxsig = $C['cookie_tdxsig'];
$send_to_trade = false;

if ($C['storage_method'] == 'Redis')
{
    try
    {
        $redis = new Redis();
        $redis->connect($C['redis_host'], $C['redis_port']=='sock'?null:$C['redis_port']);
    }
    catch(RedisException $e)
    {
        echo 'Cant connect to Redis';
    }
    $session_key = "host:{$C['domain']}:session:{$_SERVER['REMOTE_ADDR']}";
    //$redis_data = $redis->get($session_key);
    $session_data = $redis->get($session_key);
}
else
{
    $ip_hash = md5($_SERVER['REMOTE_ADDR']);
    $session_file = "{$C['dir_base']}/data/sessions/{$ip_hash[0]}/{$ip_hash[1]}/{$_SERVER['REMOTE_ADDR']}";
    $session_data = file_get_contents($session_file);
}


// Session defaults
$g_session = array(
    't' => 'unknown',   // trade type
    'sys' => true,      // is trade system
    'l' => null,        // system language
    'p' => false,       // proxy
    'd' => null,        // ref domain
    'se' => null,       // SE data
    'st' => null,
    'c'   => 246,
    'cq'  => 1,
    'cl'  => 0,         // click number
    'ei' => array(),
    'ca'  => null,
    'v' => array(),
    'ni' => false
);


// Cleanup variables
$_SERVER['HTTP_USER_AGENT'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
$_SERVER['HTTP_REFERER'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
$_GET['l'] = (!empty($_GET['l'])) ? $_GET['l'] : null;
$_SERVER['HTTP_USER_AGENT'] = str_replace('|', '', $_SERVER['HTTP_USER_AGENT']);
$_SERVER['HTTP_REFERER'] = str_replace('|', '', $_SERVER['HTTP_REFERER']);
$_GET['l'] = str_replace('|', '', $_GET['l']);


// See is this a spider from UA again
if( !empty($_SERVER['HTTP_USER_AGENT']) )
{
    $spiders_uas = array(/*#<SPIDERS>*/'bot','spider','crawl','search','AddThis','SCJchecker','Yahoo','Yandex','Bing','Baidu','Google','libwww','python'/*#</SPIDERS>*/);
    foreach ($spiders_uas as $spider_ua)
    {
        if (stripos($_SERVER['HTTP_USER_AGENT'], $spider_ua) !== false)
        {
            $g_session['t'] = 'spiders';
            break;
        }
    }
}

// See is this a spider already by existing session
if (isset($session_data)) {
    $session = unserialize($session_data);
    if (isset($session['t']) && $session['t'] == 'spiders')
        $g_session['t'] = 'spiders';
}


if ($g_session['t'] != 'spiders')
{
    // Session cookie is set
    if (isset($_COOKIE[$tdxsess]))
    {
        $cookie_data = base64_decode($_COOKIE[$tdxsess]);
        $cookie_signature = sha1($C['keyphrase'] . $cookie_data);
        $g_session = unserialize($cookie_data);

        // Debug logging
//        $fp = fopen('logs/debug.log', 'a');
//        flock($fp, LOCK_EX);
//        fwrite($fp, "OUT|$now|{$_SERVER['REMOTE_ADDR']}|{$_SERVER['HTTP_REFERER']}|{$_SERVER['REQUEST_URI']}|{$_COOKIE[$tdxsess]}|{$_GET['u']}|".var_export($g_session,true)."| * $session_data| ** $cookie_data| ++ {$_COOKIE[$tdxsig]} != $cookie_signature\n");
//        flock($fp, LOCK_UN);
//        fclose($fp);

        // Bad signature
        if (!isset($_COOKIE[$tdxsig]) || isset($_COOKIE[$tdxsig]) && $_COOKIE[$tdxsig] != $cookie_signature)
        {

            // Debug logging
//        $fp = fopen('logs/debug.log', 'a');
//        flock($fp, LOCK_EX);
//        fwrite($fp, "Bad signature\n");
//        flock($fp, LOCK_UN);
//        fclose($fp);

            $g_session['t'] = 'unknown';
        }

        // Bad signature #2
        if (isset($_COOKIE[$tdxsig]) && $_COOKIE[$tdxsig] != $cookie_signature)
        {

            // Debug logging
//        $fp = fopen('logs/debug.log', 'a');
//        flock($fp, LOCK_EX);
//        fwrite($fp, "Bad signature #2\n");
//        flock($fp, LOCK_UN);
//        fclose($fp);

            $g_session['t'] = 'unknown';
        }

        // Cookie data does not match server-side stored data
        if (isset($_COOKIE[$tdxsig]) && $_COOKIE[$tdxsig] == $cookie_signature)
        {
            if ($cookie_data != $session_data)
            {

                // Debug logging
//                $fp = fopen('logs/debug.log', 'a');
//                flock($fp, LOCK_EX);
//                fwrite($fp, "Cookie data does not match server-side stored data\n");
//                fwrite($fp, "---cookie_data----\n");
//                fwrite($fp, var_export($cookie_data, true)."\n");
//                fwrite($fp, "---session_data---\n");
//                fwrite($fp, var_export($session_data, true)."\n");
//                fwrite($fp, "==================\n");
//                flock($fp, LOCK_UN);
//                fclose($fp);

                $g_session['t'] = 'unknown';
            }
        }
    }
    else
    {
        // Existing session
        if (!empty($session_data))
        {
            $g_session = unserialize($session_data);
            $g_session['t'] = 'no-cookie';
        }
    }
}


// Filter no-image
if( $C['flag_filter_no_image'] && $g_session['ni'] === true )
{
    $g_session['t'] = 'no-image';
    $g_session['sys'] = true;
}


// Set to not-counted if click amount is too high and trader not system
if( $g_session['cl'] + 1 > $C['count_clicks'] && !$g_session['sys'] )
{
    $g_session['t'] = 'not-counted';
    $g_session['sys'] = true;
}


$i_skim_scheme = $g_session['sys'] ? 2 : 23;
$trade = explode('|', file_get_contents($g_session['sys'] ? "{$C['dir_base']}/data/system/{$g_session['t']}" : "{$C['dir_base']}/data/trades/{$g_session['d']}"));
$trade['domain'] = $g_session['d'];


// Skim percent set in URL (top priority)
if( !empty($_GET['s']) )
{
    $skim = intval($_GET['s']);
}
// Skim scheme set in URL (second priority)
else if( !empty($_GET['ss']) )
{
    $skim = skim_from_scheme($_GET['ss'], $g_session['cl']);
}
// Use skim scheme of trade (low priority)
else
{
    $skim = skim_from_scheme($trade[$i_skim_scheme], $g_session['cl']);
}


// Flag to indicate if this click is going to a trade
$to_trade = false;
$is_first_click = !empty($_GET['fc']) && $g_session['cl'] == 0;
if( !$is_first_click && $skim < 100 && mt_rand(1,100) >= $skim )
{
    $to_trade = true;
}


// System trade, determine send method
if( $g_session['sys'] )
{
    switch($trade[4])
    {
        case '0': // Normal
            break;

        case '1': // Only to trades
            $to_trade = true;
            break;

        case '2': // To content if specified, otherwise traffic URL
            if( !isset($_GET['u']) )
            {
                $_GET['u'] = $trade[5];
            }
            $to_trade = false;
            break;

        case '3': // Only to traffic URL
            $_GET['u'] = $trade[5];
            $to_trade = false;
            break;
    }
}


// Sending to trade if no parameters specified
if( !$to_trade && !isset($_GET['u']) && !isset($_GET['t']) )
{
    $to_trade = true;
}


// Sending to a specific trade
if( isset($_GET['t']) && file_exists("{$C['dir_base']}/data/trades/{$_GET['t']}") )
{
    $to_trade = true;
    $send_to_trade = basename($_GET['t']);
    $send_to_data = explode('|', file_get_contents("{$C['dir_base']}/data/trades/$send_to_trade"));
    $_GET['u'] = $send_to_data[0];
    $g_external_info = $send_to_data[37];
}
// Select trade
else if( $to_trade )
{
    $send_to_trade = select_trade($trade);

    if( empty($send_to_trade) )
    {
        // Go to url or trades_satisfied_url
        if (empty($_GET['u'])) $_GET['u'] = $C['trades_satisfied_url'];
        $to_trade = false;
    }
}


// Stats files sizes and times
$record_items = 22;
$record_size = $record_items * 4;
$date_now = date('YmdHi', $now);
$hour_now = substr($date_now, 8, 2);
$minute_now = substr($date_now, 10, 2);


// Sending to trade, so get return URL and update out stats
if( $to_trade && file_exists("{$C['dir_base']}/data/trade_stats/$send_to_trade") )
{
    // Update visited trades
    $unique = !in_array($send_to_trade, $g_session['v']);
    $g_session['v'][] = $send_to_trade;
    $g_session['ei'][] = substr(crc32($send_to_trade), -4);


    // Update out stats
    $out_items = 8;
    $pack_arg = 'L' . $out_items;
    $out_size = $out_items * 4;
    $hour_offset = ($hour_now * $record_size) + (14 * 4);
    $minute_offset = ((($hour_now * 60) + $minute_now) * $record_size + (24 * $record_size)) + (14 * 4);
    $fp = fopen("{$C['dir_base']}/data/trade_stats/$send_to_trade", 'r+');
    flock($fp, LOCK_EX);

    // Seek to hour, read, update
    fseek($fp, $hour_offset, SEEK_SET);
    $r = unpack($pack_arg, fread($fp, $out_size));
    $r[1]++;
    if( $unique ) $r[2]++;
    if( $g_session['p'] ) $r[3]++;
    $r[4 + $g_session['cq']]++;
    if( $g_force_type == 'I' ) $r[7]++;
    if( $g_force_type == 'H' ) $r[8]++;
    fseek($fp, -$out_size, SEEK_CUR);
    fwrite($fp, pack($pack_arg, $r[1], $r[2], $r[3], $r[4], $r[5], $r[6], $r[7], $r[8]), $out_size);

    // Seek to minute, read, update
    fseek($fp, $minute_offset, SEEK_SET);
    $r = unpack($pack_arg, fread($fp, $out_size));
    $r[1]++;
    if( $unique ) $r[2]++;
    if( $g_session['p'] ) $r[3]++;
    $r[4 + $g_session['cq']]++;
    if( $g_force_type == 'I' ) $r[7]++;
    if( $g_force_type == 'H' ) $r[8]++;
    fseek($fp, -$out_size, SEEK_CUR);
    fwrite($fp, pack($pack_arg, $r[1], $r[2], $r[3], $r[4], $r[5], $r[6], $r[7], $r[8]), $out_size);

    flock($fp, LOCK_UN);
    fclose($fp);


    // Update out log
    $fp = fopen("{$C['dir_base']}/data/trade_stats/$send_to_trade-out", 'a');
    flock($fp, LOCK_EX);
    fwrite($fp, "$now|{$_SERVER['REMOTE_ADDR']}|{$g_session['p']}|{$_SERVER['HTTP_USER_AGENT']}|{$g_session['c']}|{$_SERVER['HTTP_REFERER']}|{$_GET['l']}|{$g_session['l']}|0|0\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}

// Update user click count
$unique = $g_session['cl'] == 0;
$g_session['cl']++;


// Update click stats
$click_items = 6;
$pack_arg = 'L' . $click_items;
$click_size = $click_items * 4;
$hour_offset = ($hour_now * $record_size) + (6 * 4);
$minute_offset = ((($hour_now * 60) + $minute_now) * $record_size + (24 * $record_size)) + (6 * 4);
$statsfile = $g_session['sys'] ? "{$C['dir_base']}/data/system_stats/{$g_session['t']}" : "{$C['dir_base']}/data/trade_stats/{$g_session['d']}";

if( file_exists($statsfile) )
{
    $fp = fopen($statsfile, 'r+');
    flock($fp, LOCK_EX);

    // Seek to hour, read, update
    fseek($fp, $hour_offset, SEEK_SET);
    $r = unpack($pack_arg, fread($fp, $click_size));
    $r[1]++;
    if( $unique ) $r[2]++;
    if( $g_session['p'] ) $r[3]++;
    if( $to_trade ) $r[4]++;
    $r[6 + $g_session['cq']]++;
    fseek($fp, -$click_size, SEEK_CUR);
    fwrite($fp, pack($pack_arg, $r[1], $r[2], $r[3], $r[4], $r[5], $r[6]), $click_size);

    // Seek to minute, read, update
    fseek($fp, $minute_offset, SEEK_SET);
    $r = unpack($pack_arg, fread($fp, $click_size));
    $r[1]++;
    if( $unique ) $r[2]++;
    if( $g_session['p'] ) $r[3]++;
    $r[6 + $g_session['cq']]++;
    fseek($fp, -$click_size, SEEK_CUR);
    fwrite($fp, pack($pack_arg, $r[1], $r[2], $r[3], $r[4], $r[5], $r[6]), $click_size);

    flock($fp, LOCK_UN);
    fclose($fp);
}

// Update click again stats
if( !empty($g_session['ca']) )
{
    $g_session['ca'] = basename($g_session['ca']);

    if( file_exists("{$C['dir_base']}/data/trade_stats/{$g_session['ca']}") )
    {
        $ca_items = 1;
        $pack_arg = 'L' . $ca_items;
        $ca_size = $ca_items * 4;
        $hour_offset = ($hour_now * $record_size) + (10 * 4);
        $minute_offset = ((($hour_now * 60) + $minute_now) * $record_size + (24 * $record_size)) + (10 * 4);
        $fp = fopen("{$C['dir_base']}/data/trade_stats/{$g_session['ca']}", 'r+');
        flock($fp, LOCK_EX);

        // Seek to hour, read, update
        fseek($fp, $hour_offset, SEEK_SET);
        $r = unpack($pack_arg, fread($fp, $ca_size));
        $r[1]++;
        fseek($fp, -$ca_size, SEEK_CUR);
        fwrite($fp, pack($pack_arg, $r[1]), $ca_size);

        // Seek to minute, read, update
        fseek($fp, $minute_offset, SEEK_SET);
        $r = unpack($pack_arg, fread($fp, $ca_size));
        $r[1]++;
        fseek($fp, -$ca_size, SEEK_CUR);
        fwrite($fp, pack($pack_arg, $r[1]), $ca_size);

        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
$g_session['ca'] = $to_trade ? $send_to_trade : null;


// Update click log
$fast = (microtime_float() - $g_session['ct']) <= $C['fast_click'];
$logfile = $g_session['sys'] ? "{$C['dir_base']}/data/system_stats/{$g_session['t']}-clicks" : "{$C['dir_base']}/data/trade_stats/{$g_session['d']}-clicks";
if( file_exists($logfile) )
{
    $fp = fopen($logfile, 'a');
    flock($fp, LOCK_EX);
    fwrite($fp, "$now|{$_SERVER['REMOTE_ADDR']}|{$g_session['p']}|{$_SERVER['HTTP_USER_AGENT']}|{$g_session['c']}|{$_SERVER['HTTP_REFERER']}|{$_GET['l']}|{$g_session['l']}|$fast|{$g_session['ni']}\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}


// Update cookie
$g_session['ei'] = array_unique($g_session['ei']);
$g_session['ct'] = microtime_float();
$serialized = serialize($g_session);
setcookie($tdxsess, base64_encode($serialized), $now + $session_length, '/', $C['domain']);
setcookie($tdxsig, sha1($C['keyphrase'] . $serialized), $now + $session_length, '/', $C['domain']);


if ($C['storage_method'] == 'Redis')
{
    // Update session key->value
    $redis->set($session_key, $serialized, $session_length);
}
else
{
    // Update session file
    $session_dir = dirname($session_file);
    if (!is_dir($session_dir))
        mkdir($session_dir, 0755, true);
    $fp = fopen($session_file, 'w');
    flock($fp, LOCK_EX);
    fwrite($fp, $serialized);
    flock($fp, LOCK_UN);
    fclose($fp);
}


// Send to URL
if (
    !empty($_GET['b6'])
    ||
    (strpos($_GET['u'], 'http://') !== 0 && strpos($_GET['u'], 'https://') !== 0 && strpos($_GET['u'], '/') !== 0)
)
{
    $_GET['u'] = base64_decode($_GET['u']);
}

$_GET['u'] = urldecode($_GET['u']);

if ( !preg_match('~^http://|^https://|^//~', $_GET['u']) )
{
    $host = $_SERVER['HTTP_HOST'];
    $_GET['u'] = ltrim($_GET['u'], '/\\');
    $_GET['u'] = "http://$host/" . $_GET['u'];
}

if ($g_external_info)
{
    $_GET['u'] .= (strpos($_GET['u'], '?') === false ? '?' : '&') . 'x=' . join('.', $g_session['ei']);
}

header("Location: {$_GET['u']}", false, 302);

// Debug logging
//$fp = fopen('logs/debug.log', 'a');
//flock($fp, LOCK_EX);
//fwrite($fp, "OUT|$now|{$_SERVER['REMOTE_ADDR']}|{$_SERVER['HTTP_REFERER']}|{$_SERVER['REQUEST_URI']}|{$_COOKIE[$tdxsess]}|$serialized|$to_trade|{$_GET['u']}\n");
//flock($fp, LOCK_UN);
//fclose($fp);

exit;












function base74_encode($str) {
    $str = base64_encode($str);
    $eq_sign_amount = substr_count($str, '=');
    $str = trim($str, '=');
    $str = strrev($str);
    $eq_sign = '';
    if ($eq_sign_amount === 2)
    {
        $eq_sign = '__';
    }
    elseif ($eq_sign_amount === 1)
    {
        $eq_sign = '_';
    }
    return '_'.$str.$eq_sign;
}

function select_trade($trade)
{
    global $C, $g_force_type, $g_session, $g_external_info;

    if( filesize("{$C['dir_base']}/data/outlist_forces") < 10 )
    {
        $C['distrib_main'] += $C['distrib_forces'];
        $C['distrib_forces'] = 0;
    }

    $percent = mt_rand(1,100);
    $select_order = null;

    if( $percent < $C['distrib_forces'] )
    {
        //$select_order = array('outlist_forces', 'outlist_main');
        $select_order = array('outlist_forces');
    }
    else if( $percent > $C['distrib_forces'] && $percent < $C['distrib_forces'] + $C['distrib_main'] )
    {
        $select_order = array('outlist_main');
    }
    else if( $percent > $C['distrib_forces'] + $C['distrib_main'] && $percent < $C['distrib_forces'] + $C['distrib_main'] + $C['distrib_primary'] )
    {
        $select_order = array('outlist_primary');
    }
    else
    {
        $select_order = array('outlist_secondary');
    }


    $i_excludes = $g_session['sys'] ? 3 : 36;
    $outlist_file = null;
    $trades = array();

    foreach( $select_order as $outlist_file )
    {
        $fp = fopen("{$C['dir_base']}/data/$outlist_file", 'r');

        while( !feof($fp) )
        {
            $ints = fread($fp, 8);

            if( feof($fp) )
            {
                break;
            }

            $ints = unpack('Lowe/Lsize', $ints);
            $stt = explode('|', fread($fp, $ints['size']));
            //$stt[] = $ints['owe'];

            if ( is_allowed_trade($trade, $stt, $i_excludes) )
            {
                $trades[] = $stt;
                if ($outlist_file !== 'outlist_main') break;
            }
        }

        fclose($fp);
    }


    if ($outlist_file === 'outlist_main')
    {
        $outlist_size = count($trades);
        if ($outlist_size == 0) return null;
        $outlist_weights = array(/*#<OUTLIST_POINTS>*/1200,500,350,270,200,150,100,80,50,30/*#</OUTLIST_POINTS>*/);
        $outlist_weights = array_slice($outlist_weights, 0, $outlist_size);
        $weights_sum = array_sum($outlist_weights);

        $weights_stamp = array();
        foreach ($outlist_weights as $weight)
        {
            $weights_stamp[] = $weight * 100 / $weights_sum;
        }

        $trade_luck = mt_rand(0, 99);  // 99 - useful, 100 - never reached

        $trade_outlist_i = 0;
        $sum_percent = 0;

        foreach ($weights_stamp as $i => $line_percent)
        {
            $sum_percent += $line_percent;
            if ($trade_luck < $sum_percent)
            {
                $trade_outlist_i = $i;
                break;
            }
        }

        $send_to_trade = isset($trades[$trade_outlist_i]) ? $trades[$trade_outlist_i] : null;
    }
    else
    {
        $send_to_trade = $trades[0];
    }

    if( !empty($send_to_trade[0]) )
    {
        $g_external_info = $send_to_trade[7];
        $_GET['u'] = $send_to_trade[4];

        if( $outlist_file == 'outlist_main' && $send_to_trade[5] )
        {
            $g_force_type = 'I';
        }
        else if( $outlist_file == 'outlist_forces' )
        {
            $g_force_type = $send_to_trade[6];
        }
    }

    return empty($send_to_trade[0]) ? null : $send_to_trade[0];
}

function is_allowed_trade(&$trade, &$send_to_trade, &$i_excludes)
{
    global $g_session;

    // Don't send back to self
    if( !empty($trade['domain']) && $trade['domain'] == $send_to_trade[0] )
    {
        return false;
    }

    // Check category
    if( isset($_GET['c']) && !empty($send_to_trade[2]) && strpos($send_to_trade[2], ",{$_GET['c']},") === false )
    {
        return false;
    }

    // Check group
    if( isset($_GET['g']) && !empty($send_to_trade[3]) && strpos($send_to_trade[3], ",{$_GET['g']},") === false )
    {
        return false;
    }

    // Check excludes
    if( !empty($trade[$i_excludes]) && strpos(",{$trade[$i_excludes]},", ",{$send_to_trade[0]},") !== false )
    {
        return false;
    }

    // Don't send to already visited
    if(
        isset($send_to_trade[0])
        && isset($g_session['v'])
        && is_array($g_session['v'])
        && in_array($send_to_trade[0], $g_session['v'])
    )
    {
        return false;
    }

    // Don't send to already visited (external info)
    if( isset($send_to_trade[0])
        && isset($g_session['ei'])
        && is_array($g_session['ei'])
        && in_array(substr(crc32($send_to_trade[0]), -4), $g_session['ei'])
    )
    {
        return false;
    }

    return true;
}

function skim_from_scheme($scheme_name, $click)
{

    global $C;

    // Sanitize
    $scheme_name = preg_replace('~[^a-z0-9\-_]~i', '', $scheme_name);

    if( file_exists("{$C['dir_base']}/data/skim_schemes/$scheme_name") )
    {
        $scheme = explode('|', file_get_contents("{$C['dir_base']}/data/skim_schemes/$scheme_name"));
        return $scheme[$click % 50];
    }

    // Return default skim
    return 70;
}


// Get microtime as float
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}



