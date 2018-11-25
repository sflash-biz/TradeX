var data =
[ // data

[ // data[0]
<?php for( $i = 0; $i < HOURS_PER_DAY; $i++ ) echo '['.strtotime(date("d F Y {$i}:00")).'000,'.$stats->i_raw[$i].'],'; ?>
],

[ // data[1]

],

[ // data[2]
<?php for( $i = 0; $i < HOURS_PER_DAY; $i++ ) echo '['.strtotime(date("d F Y {$i}:00")).'000,'.$stats->o_raw[$i].'],'; ?>
],

[ // data[3]
<?php for( $i = 0; $i < HOURS_PER_DAY; $i++ ) echo '['.strtotime(date("d F Y {$i}:00")).'000,'.$stats->c_raw[$i].'],'; ?>
],

[ // data[4]
<?php for( $i = 0; $i < HOURS_PER_DAY; $i++ ) echo '['.strtotime(date("d F Y {$i}:00")).'000,'.$stats->prod[$i].'],'; ?>
],

[ // data[5]
<?php for( $i = 0; $i < HOURS_PER_DAY; $i++ ) echo '['.strtotime(date("d F Y {$i}:00")).'000,'.$stats->return[$i].'],'; ?>
],

];
