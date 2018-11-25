var data =
[ // data

[ // data[0]
<?php foreach( $history->stats as $date => $stats ) echo '['.strtotime($date).'000,'.$stats[0].'],'; ?>
],

[ // data[1]
<?php foreach( $history->stats as $date => $stats ) echo '['.strtotime($date).'000,'.$stats[1].'],'; ?>
],

[ // data[2]
<?php foreach( $history->stats as $date => $stats ) echo '['.strtotime($date).'000,'.$stats[6].'],'; ?>
],

[ // data[3]
<?php foreach( $history->stats as $date => $stats ) echo '['.strtotime($date).'000,'.$stats[14].'],'; ?>
],

[ // data[4]
<?php foreach( $history->stats as $date => $stats ) echo '['.strtotime($date).'000,'.($stats[0] > 0 ? format_float_to_percent($stats[6] / $stats[0]) : 0).'],'; ?>
],

[ // data[5]
<?php foreach( $history->stats as $date => $stats ) echo '['.strtotime($date).'000,'.($stats[0] > 0 ? format_float_to_percent($stats[14] / $stats[0]) : 0).'],'; ?>
],

];
