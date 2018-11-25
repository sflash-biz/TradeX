var another_data =
[ // data

[ // data[0]
<?php foreach( $history->stats as $date => $stats ) echo '['.strtotime($date).'000,'.($stats[0] > 0 ? format_float_to_percent($stats[6] / $stats[0]) : 0).'],'; ?>
],

[ // data[1]
<?php foreach( $history->stats as $date => $stats ) echo '['.strtotime($date).'000,'.($stats[0] > 0 ? format_float_to_percent($stats[14] / $stats[0]) : 0).'],'; ?>
],

];
