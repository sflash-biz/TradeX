<?php

/*#<CONFIG>*/
$C = array(
'dir_base' => '/var/www/html/sft',
'domain' => 'localhost',
'keyphrase' => '2dfc30bc18736ede5a82474dfd4f9bde',
'cookie_tdxsess' => 'sftse',
'cookie_tdxsig' => 'sftsi',
'storage_method' => 'File system',);
/*#</CONFIG>*/


// Session variables
$now = time();
$g_session = null;
$session_length = 3600;
$tdxsess = $C['cookie_tdxsess'];
$tdxsig = $C['cookie_tdxsig'];

if ($C['storage_method'] == 'Redis') {
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
}
else
{
    $ip_hash = md5($_SERVER['REMOTE_ADDR']);
    $session_file = "data/sessions/{$ip_hash[0]}/{$ip_hash[1]}/{$_SERVER['REMOTE_ADDR']}";
}


// Session cookie is set
if( isset($_COOKIE[$tdxsess]) )
{
    $cookie_data = base64_decode($_COOKIE[$tdxsess]);
    $cookie_signature = sha1($C['keyphrase'] . $cookie_data);
    $g_session = unserialize($cookie_data);

    // Good signature
    if( isset($_COOKIE[$tdxsig]) && $_COOKIE[$tdxsig] == $cookie_signature )
    {
        $g_session['ni'] = false;

        // Update cookie
        $serialized = serialize($g_session);
        setcookie($tdxsess, base64_encode($serialized), $now + $session_length, '/', $C['domain']);
        setcookie($tdxsig, sha1($C['keyphrase'] . $serialized), $now + $session_length, '/', $C['domain']);

        if ($C['storage_method'] == 'Redis')
        {
            // Update session key->value
            $redis->set($session_key, serialize($session), $session_length);
            //var_dump(unserialize($redis->get($session_key)));
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
    }
}

$images = array('actions-16x16.png',
                'add-16x16.png',
                'build-16x16.png',
                'calendar-16x16.png',
                'detailed-16x16.png');

header('Content-type: image/png');
readfile($C['dir_base'] . '/cp/images/' . $images[array_rand($images)]);

// Debug logging
//$fp = fopen('logs/debug.log', 'a');
//flock($fp, LOCK_EX);
//fwrite($fp, "IMAGE|$now|{$_SERVER['REMOTE_ADDR']}|{$_SERVER['HTTP_REFERER']}|{$_SERVER['REQUEST_URI']}|{$_COOKIE[$tdxsess]}|$serialized\n");
//flock($fp, LOCK_UN);
//fclose($fp);


