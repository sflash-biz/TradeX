var data =
[ // data

[ // data[0]
<?php for( $i = 0; $i < HOURS_PER_DAY; $i++ ) echo '['.strtotime(date("d F Y {$i}:00")).'000,'.$stats->prod[$i].'],'; ?>
],

[ // data[1]
<?php for( $i = 0; $i < HOURS_PER_DAY; $i++ ) echo '['.strtotime(date("d F Y {$i}:00")).'000,'.$stats->return[$i].'],'; ?>
],

];