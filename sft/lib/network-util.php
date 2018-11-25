<?php

define('NETWORK_SCRIPT', 'network.php');
define('NETWORK_SUCCESS', '!!@@SUCCESS@@!!');

// Network sync options
define('NETWORK_SYNC_TRADES', 'trades');
define('NETWORK_SYNC_SKIM_SCHEMES', 'skim-schemes');
define('NETWORK_SYNC_GROUPS', 'groups');
define('NETWORK_SYNC_CATEGORIES', 'categories');
define('NETWORK_SYNC_BLACKLIST', 'blacklist');
define('NETWORK_SYNC_COUNTRIES', 'countries');
define('NETWORK_SYNC_SEARCH_ENGINES', 'search-engines');
define('NETWORK_SYNC_TRADE_RULES', 'trade-rules');
define('NETWORK_SYNC_NETWORK_SITES', 'network-sites');
/////////////////////////////
define('NETWORK_DELETE_TRADES', 'trades-delete');

// Network functions
define('NETWORK_FNC_GET_STATS', '_xStatsGet');
define('NETWORK_FNC_SYNC', '_xSync');

function network_success($data)
{
    echo NETWORK_SUCCESS . serialize($data);
}



class NetworkRequest
{
    var $url;
    var $post_data;
    var $error;

    function NetworkRequest($site, $fnc, $extra_post = array())
    {
        $this->url = $site['url'] . NETWORK_SCRIPT;
        $this->post_data = array_merge(array('cp_username' => $site['username'],
                                             'cp_password' => $site['password'],
                                             'r' => $fnc),
                                       $extra_post);
    }

    function Execute()
    {
        require_once 'http.php';

        $http = new HTTP();

        if( $http->POST($this->url, $this->post_data) )
        {
            if( strpos($http->body, NETWORK_SUCCESS) === 0 )
            {
                return substr($http->body, strlen(NETWORK_SUCCESS));
            }
            else
            {
                $this->error = substr(strip_tags($http->body), 0, 100);
                return false;
            }
        }
        else
        {
            $this->error = $http->error;
            return false;
        }
    }
}


?>