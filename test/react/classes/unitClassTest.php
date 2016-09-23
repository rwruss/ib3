<?php

include('c:/websites/ib3/public_html/unitClass.php');

$testList['xLoc'] = 1;
$testList['yLoc'] = 2;
$testList['icon'] = 3;
$testList['uType'] = 4;
$testList['owner'] = 5;
$testList['controller'] = 6;
$testList['status'] = 7;
$testList['culture'] = 8;
$testList['religion'] = 9;
$testList['troopType'] = 10;
$testList['currentTask'] = 11;
$testList['currentLoc'] = 12;
$testList['timeStarted'] = 13;
$testList['expSlot'] = 14;
$testList['armyID'] = 15;
$testList['energy'] = 16;
$testList['enRegen'] = 17;
$testList['item1'] = 18;
$testList['item2'] = 19;
$testList['item3'] = 20;
$testList['item4'] = 21;
$testList['item5'] = 22;
$testList['item6'] = 23;
$testList['item7'] = 24;
$testList['item8'] = 25;
$testList['currentSlot'] = 26;
$testList['updateTime'] = 27;
$testList['visionDist'] = 28;
$testList['carryCap'] = 29;
$testList['carrySlot'] = 30;
$testList['battleID'] = 31;

$defaultBlockSize = 100;

$binDat = '';
for ($i=1; $i<=100; $i++ ) {
	$binDat .= pack('i', $i);
}

$testFile = fopen('./unitClasstest.dat', 'w+b');
fseek($testFile, 100);
fwrite($testFile, $binDat);

$testUnit = new warband(1, $testFile, 400);

$count = 0;
foreach($testList as $key=>$value) {
	echo $key.' has a start value of '.$testUnit->get($key);
	$testUnit->save($key, 100+$count);
	++$count;
	echo ' and a new value of '.$testUnit->get($key).'<br>';
}

echo '<p>Reload and recheck.<p>';

$nextTest = new warband(1, $testFile, 400);
foreach($testList as $key=>$value) {
	echo $key.' has a start value of '.$testUnit->get($key).'<br>';
}


?>
