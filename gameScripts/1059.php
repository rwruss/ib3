<?php
include("./slotFunctions.php");

date_default_timezone_set('America/Chicago');
// Load unit data
$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Process if unit has action points to spend
$divisor = max(1,$unitDat[17]);
$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/$divisor));

echo 'Points available: '.$actionPoints.'<br>';
echo 'Updated at '.date('d,m,y', $unitDat[27]).' = '.$unitDat[27];

// Load unit exeperience for doing tasks


// Load unit desc to determine what tasks this unit can do
$unitDesc = explode('<-->', file_get_contents($scnPath.'/units.desc'));
$unitTasks = explode(',', $unitDesc[$unitDat[10]*9+8]);

// Load task file to get list of tasks that can be done by this unit
$jobDesc = explode('<->', file_get_contents($scnPath.'/jobs.desc'));

// Check to see if the unit is in a city
$mapSlot = floor($unitDat[2]/120)*120+floor($unitDat[1]/120);
$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'rb');


// Look up list of tasks going on in city
$mapItems = new itemSlot($mapSlot, $mapSlotFile, 404); // start, file, slot size
$checkItems = array_filter($mapItems->slotData);

print_r($checkItems);
//for ($i=1; $i<=sizeof($checkItems); $i++) {
foreach ($checkItems as $checkID) {
	fseek($unitFile, $checkID*$defaultBlockSize);
	$checkDat = unpack('i*', fread($unitFile, 200));

	if ($checkDat[1] == $unitDat[1] && $checkDat[2] == $unitDat[2]) {
		if ($checkDat[4] == 1)	{
			echo 'In a city ('.$checkID.')';}

			$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
			// Look up city tasks
			echo 'Task slit is '.$checkDat[21].'<br>';
			$cityTasks = new itemSlot($checkDat[21], $slotFile, 40);
			echo 'Tasks found:';
			print_r($cityTasks->slotData);
			fclose($slotFile);
	}
}

echo '<script>';
for ($i=0; $i<sizeof($unitTasks); $i++) {
	echo 'var task = unitTaskOpt('.$unitTasks[$i].', "ordersContent", "'.$jobDesc[$unitTasks[$i]*4+2].'");';
}
echo '</script>';
?>
