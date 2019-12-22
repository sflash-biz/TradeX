<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

define('LIC_KEY', '===!!!JUST--USE--YOUR--OWN!!!===');
define('URL_DOWNLOAD', 'http://sflash.biz/sft-dist-pub/download-sft.php');
define('FILE_INSTALL', './install.dat');
define('DIR_LOGS', './logs');
define('FILE_LOG_INSTALL', DIR_LOGS . '/install.log');
define('FILE_UTILITY', 'lib/utility.php');
define('STRING_LF_UNIX', "\n");
define('STRING_LF_WINDOWS', "\r\n");
define('STRING_LF_MAC', "\r");
define('STRING_BLANK', '');

define('HTTP_SCHEME_HTTP', 'http');
define('HTTP_SCHEME_HTTPS', 'https');
define('HTTP_METHOD_POST', 'POST');
define('HTTP_METHOD_GET', 'GET');



echo $result = getInstaller();
echo '<br>';
if (stripos($result, 'successfully downloaded') !== false)
{
    echo $result = extractInstall();
    if (stripos($result, 'installation complete') !== false)
    {
        addLicKey();
        echo '<br>';
        echo '<br>';
        copy('utilities/reset-access.php', 'cp/reset-access.php');
        chmod('cp/reset-access.php', 0666);
        echo 'Go to <a href="cp/reset-access.php">cp/reset-access.php</a>';
    }
}



function getInstaller()
{

    if (substr(sprintf('%o', fileperms(__DIR__)), -4) !== '0777')
    {
        return retunError(__DIR__ . ' must be with 0777 permission');
    }

    $post_data = array(
        'install' => true,
        'version' => 'latest',
        'key' => LIC_KEY,
        'domain' => domain_from_url('http://' . $_SERVER['HTTP_HOST'])
    );

    $http = new HTTP();

    if( $http->POST(URL_DOWNLOAD, $post_data) )
    {
        if( preg_match('~X-SHA1: ([a-z0-9]+)~i', $http->response_headers, $matches) )
        {
            $installer_file = FILE_INSTALL;
            $sha1 = $matches[1];
            file_write($installer_file, $http->body);

            if( $sha1 != sha1_file($installer_file) )
            {
                file_delete($installer_file);
                return retunError('File hash does not match, possible corrupted data. Please try again.');
            }
            else
            {
                return retunSuccess('Install successfully downloaded');
            }
        }
        else if( preg_match('~X-Error: ([a-z0-9_]+)~i', $http->response_headers, $matches) )
        {
            return retunError('Unable to locate a license for this domain');
        }
        else
        {
            return retunError('Download install.dat failed, please try again');
        }
    }
    else
    {
        return retunError('Unable to connect to install rep: ' . $http->error);
    }
}

function extractInstall()
{

    define('ITEM_TYPE_DIR', 'DIR');
    define('ITEM_TYPE_FILE', 'FILE');

    $date = date('Y-m-d H:i:s');

    if (substr(sprintf('%o', fileperms(__DIR__)), -4) !== '0777')
    {
        return retunError(__DIR__ . ' must be with 0777 permission');
    }

    @mkdir(DIR_LOGS);
    @chmod(DIR_LOGS, 0777);
    file_delete(FILE_LOG_INSTALL);
    file_append(FILE_LOG_INSTALL, "[$date]: Install started...\n");

    $installer_file = FILE_INSTALL;
    if (!file_exists($installer_file))
    {
        return retunError('[Error]: Cant not find install.dat');
    }
    $fp = fopen($installer_file, 'r');
    while( !feof($fp) )
    {
        $arr_line = explode('|', trim(fgets($fp)));
        if (count($arr_line) < 8) continue;
        list($type, $name, $permissions, $su_permissions, $on_install, $on_patch, $chunk, $b64contents) = $arr_line;

        $permissions = (isset($_REQUEST['su']) && $_REQUEST['su']) ? octdec($su_permissions) : octdec($permissions);

        switch($type)
        {
            case ITEM_TYPE_DIR:
                file_append(FILE_LOG_INSTALL, "\tCreating directory $name - ");
                if (!file_exists($name))
                {
                    $cd = mkdir($name, $permissions, true);
                    if ($cd)
                    {
                        @chmod($name, $permissions);
                        file_append(FILE_LOG_INSTALL, "[Done]\n");
                    }
                    else
                    {
                        file_append(FILE_LOG_INSTALL, "[Error]: cant create\n");
                        return retunError("[Error]: cant create $name");
                    }
                }
                else
                {
                    file_append(FILE_LOG_INSTALL, "[Warning]: $name already exist\n");
                }
                break;

            case ITEM_TYPE_FILE:
                if( $on_install == '1' )
                {
                    file_append(FILE_LOG_INSTALL, "\tExtracting file $name - ");

                    $fp_out = fopen($name, 'w');
                    if ($fp_out)
                    {
                        $b64contents_encoded = base64_decode($b64contents);
                        if ($b64contents_encoded !== false)
                        {
                            fwrite($fp_out, $b64contents_encoded);
                            file_append(FILE_LOG_INSTALL, "[Done]\n");
                        } else
                        {
                            file_append(FILE_LOG_INSTALL, "[Error]: file base64_decode\n");
                            return retunError("[Error]: file base64_decode $name");
                        }
                        fclose($fp_out);
                        @chmod($name, $permissions);
                    }
                    else
                    {
                        file_append(FILE_LOG_INSTALL, "[Error]: cant read dir or create file\n");
                        return retunError("[Error]: cant read dir or create file $name");
                    }
                }
                break;
        }
    }
    fclose($fp);

    file_delete($installer_file);

    return retunSuccess('Installation complete!');
}

function addLicKey()
{
    $utility = string_format_lf(file_get_contents(FILE_UTILITY));
    $utility = str_replace('/*#<LIC_KEY>*/', LIC_KEY, $utility);
    file_write(FILE_UTILITY, $utility, 0644);
}


function file_delete($filename)
{
    @unlink($filename);
}

function string_format_lf($string, $format = STRING_LF_UNIX)
{
    return is_array($string) ?
        array_map('string_format_lf', $string) :
        preg_replace('~' . STRING_LF_WINDOWS . '|' . STRING_LF_MAC . '|' . STRING_LF_UNIX . '~', $format, $string);
}

function file_write($filename, $data = STRING_BLANK, $permissions = 0666)
{
    $fh = fopen($filename, file_exists($filename) ? 'r+' : 'w');
    flock($fh, LOCK_EX);
    fseek($fh, 0);
    fwrite($fh, $data);
    ftruncate($fh, ftell($fh));
    flock($fh, LOCK_UN);
    fclose($fh);

    @chmod($filename, $permissions);
}

function file_append($filename, $data = STRING_BLANK, $permissions = 0666)
{
    $fh = fopen($filename, 'a');
    flock($fh, LOCK_EX);
    fwrite($fh, $data);
    flock($fh, LOCK_UN);
    fclose($fh);

    @chmod($filename, $permissions);
}

function domain_from_url($url)
{
    $parsed_url = parse_url($url);
    return strtolower(preg_replace('~^www\.~i', '', $parsed_url['host']));
}

function retunError($message = 'Error')
{
    return "<strong style='color: red'>$message</strong>\n";
}

function retunSuccess($message = 'Done')
{
    return "<strong>$message</strong>\n";
}







class HTTP
{

    const HTTP_SCHEME_HTTP = 'http';
    const HTTP_SCHEME_HTTPS = 'https';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_GET = 'GET';

    var $allow_redirection = true;

    var $max_redirects = 5;

    var $redirects = 0;

    var $max_http_code = 400;

    var $start_url = null;

    var $request_headers = null;

    var $post_data = null;

    var $response_headers = null;

    var $response_full_status = null;

    var $response_status = null;

    var $response_status_code = null;

    var $response_location = null;

    var $body;

    var $connect_timeout = 15;

    var $read_timeout = 30;

    var $error = null;

    function __construct()
    {
    }

    function GET($url, $referrer = null, $allow_redirect = true)
    {
        $this->_reset($url, $allow_redirect);
        return $this->_request($url, HTTP_METHOD_GET, $referrer);
    }

    function POST($url, $data = array(), $referrer = null, $allow_redirect = true)
    {
        $this->_reset($url, $allow_redirect);
        return $this->_request($url, HTTP_METHOD_POST, $referrer, $data);
    }

    function _request($url, $method = HTTP_METHOD_GET, $referrer = null, $data = array())
    {
        $result = false;

        if( ($parsed_url = parse_url($url)) !== false )
        {
            $scheme = strtolower($parsed_url['scheme']);
            $hostname = $parsed_url['host'];
            $port = (!empty($parsed_url['port'])) ? $parsed_url['port'] : null ;

            // Resolve hostname
            $ip_address = gethostbyname($hostname);
            if( $ip_address != $hostname )
            {
                // Set default HTTP port
                if( empty($port) )
                {
                    $port = $scheme == HTTP_SCHEME_HTTPS ? 443 : 80;
                }

                // SSL connection
                if( $scheme == HTTP_SCHEME_HTTPS )
                {
                    $ip_address = 'ssl://' . $ip_address;
                }

                // Open the connection
                if( ($socket = @fsockopen($ip_address, $port, $errno, $errstr, $this->connect_timeout)) !== false )
                {
                    // Send the request
                    fwrite($socket, $this->_generate_request($parsed_url, $method, $referrer, $data));

                    stream_set_timeout($socket, $this->read_timeout);

                    // Read the response
                    $response = null;
                    $read_success = true;
                    while( !feof($socket) )
                    {
                        $chunk = fread($socket, 65536);

                        if( $chunk === false )
                        {
                            $read_success = false;
                            break;
                        }

                        $response .= $chunk;
                    }
                    fclose($socket);

                    if( $read_success )
                    {
                        $this->_process_response($url, $response);

                        if( $this->redirects < $this->max_redirects )
                        {
                            if( $this->response_status_code < $this->max_http_code )
                            {
                                if( !empty($this->response_location) )
                                {
                                    return $this->_request($this->response_location, $method, $referrer, $data);
                                }
                                else
                                {
                                    $result = true;
                                }
                            }
                            else
                            {
                                $this->error = 'The URL returned HTTP status [' . $this->response_status . ']';
                            }
                        }
                        else
                        {
                            $this->error = 'URL generates too many redirects';
                        }
                    }
                    else
                    {
                        $this->error = 'Receive from remote server failed';
                    }
                }
                else
                {
                    $this->error = 'Could not connect to remote host [' . $errstr . ']';
                }
            }
            else
            {
                $this->error = 'Could not resolve hostname';
            }
        }
        else
        {
            $this->error = 'The URL is not properly formatted';
        }

        return $result;
    }

    function _reset($url, $allow_redirect)
    {
        $this->request_headers = null;
        $this->response_headers = null;
        $this->start_url = $url;
        $this->error = null;
        $this->allow_redirection = $allow_redirect;
        $this->max_http_code = $this->allow_redirection ? 400 : 300;
        $this->body = null;
        $this->redirects = 0;
        $this->response_full_status = null;
        $this->response_status = null;
        $this->response_status_code = null;
        $this->response_location = null;
    }

    function _process_response($url, $response)
    {
        $crlfx2 = "\r\n\r\n";
        $first_crlfx2 = strpos($response, $crlfx2);
        $headers = substr($response, 0, $first_crlfx2 + strlen($crlfx2));
        $this->body = substr($response, $first_crlfx2 + strlen($crlfx2));

        $this->response_headers .= $headers;

        if( preg_match('~HTTP/\d\.\d ((\d+).*)~mi', $headers, $matches) )
        {
            $this->response_full_status = trim($matches[0]);
            $this->response_status = trim($matches[1]);
            $this->response_status_code = trim($matches[2]);
        }

        $this->response_location = null;
        if( preg_match('~Location:\s+(.*)~mi', $headers, $matches) )
        {
            $this->redirects++;
            $this->response_location = $this->_relative_to_absolute($url, trim($matches[1]));
        }
    }

    function _generate_request($parsed_url, $method = HTTP_METHOD_GET, $referrer = null, $data = array())
    {
        $crlf = "\r\n";

        $uri = (isset($parsed_url['path']) ?  str_replace(' ', '%20', $parsed_url['path']) : '/' ) .
            (isset($parsed_url['query']) ? "?{$parsed_url['query']}" : '');

        // Generate POST data
        if( $method == HTTP_METHOD_POST )
        {
            $post_parts = array();
            foreach( $data as $key => $val )
            {
                $post_parts[] = "$key=" . urlencode($val);
            }

            $this->post_data = join('&', $post_parts);
        }

        // Generate request headers
        $request = "$method $uri HTTP/1.0$crlf" .
            "Host: {$parsed_url['host']}$crlf" .
            "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.0.249.43 Safari/532.5$crlf" .
            "Accept: application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5$crlf" .
            "Accept-Language: en-US,en;q=0.8$crlf" .
            "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3$crlf" .
            (!empty($referrer) ? "Referer: $referrer$crlf" : '') .
            ($method == HTTP_METHOD_POST ? "Content-Length: " . strlen($this->post_data) . "$crlf" . "Content-Type: application/x-www-form-urlencoded$crlf" : '') .
            "Connection: close$crlf$crlf";

        // Store request headers
        $this->request_headers .= $request;

        // Add post data to the request
        if( $method == HTTP_METHOD_POST )
        {
            $request .= $this->post_data;
        }

        return $request;
    }

    function _relative_to_absolute($start_url, $relative_url)
    {
        if( empty($relative_url) )
        {
            return $start_url;
        }
        else if( preg_match('~^https?://~i', $relative_url) )
        {
            return $relative_url;
        }

        $parsed = parse_url($start_url);
        $base_url = $parsed['scheme'] . '://' . $parsed['host'] . (isset($parsed['port']) ? ':' . $parsed['port'] : '');

        if( $relative_url[0] == '/' )
        {
            return $base_url . $this->_resolve_path($relative_url);
        }
        else
        {
            // Strip filename from path
            $parsed['path'] = preg_replace('~[^/]+$~', '', $parsed['path']);

            return $base_url . $this->_resolve_path($parsed['path'] . $relative_url);
        }
    }

    function _resolve_path($path)
    {
        $parts = explode('/', $path);
        $absolutes = array();

        foreach( $parts as $part )
        {
            switch($part)
            {
                case '.':
                    break;

                case '..':
                    array_pop($absolutes);
                    break;

                default:
                    $absolutes[] = $part;
                    break;
            }
        }

        return preg_replace('~/+~', '/', implode('/', $absolutes));
    }
}
