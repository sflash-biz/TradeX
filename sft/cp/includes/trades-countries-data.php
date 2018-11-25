<?php

require_once 'geoip-utility.php';

// Load stats from the log file
list($total, $countries) = get_trade_countries($domain, file_sanitize(strtolower($stat)));

// Default to 0 for all un-represented countries
foreach( $geoip_country_codes as $i => $cc )
{
    if( !isset($countries[$i]) )
    {
        $countries[$i] = 0;
    }
}

echo "var map_data = {\n";
foreach( $geoip_country_codes as $i => $cc ) {
    $cc = strtolower($cc);
    echo "\"$cc\": {$countries[$i]},\n";
}
echo "};\n";

?>
