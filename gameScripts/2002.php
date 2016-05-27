<?php

echo 'Updating Resources...';
print_r($postVals);

include("./slotFunctions.php");
//Load city info
$cityID = $_SESSION['selectedItem'];
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $cityID*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));
fclose($unitFile);
echo 'City Data: ';
print_r($cityDat);

if ($cityDat[11] > 0) {
	// Load resource Data
	$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
	$cityRsc = array_fill(1, 100, 0);
	$rscDat = unpack("i*", readSlotData($slotFile, $cityDat[11], 40));
	$numHave = sizeof($rscDat)/2;

	echo '<p>City Resources in slot '.$cityDat[11].': ';
	for ($i=1; $i<$numHave; $i++) {
		$cityRsc[$rscDat[$i*2-1]] = $rscDat[$i*2];
	}

	// Update for resources to add...
	$cityRsc[$postVals[1]] += $postVals[2];

	// clear out empties
	$writeRsc = array_filter($cityRsc);

	$writeDat = '';
	foreach($writeRsc as $rscID => $rscAmt) {
		$writeDat .= pack('i*', $rscID, $rscAmt);
		echo 'Save rsc '.$rscID.'<br>';
	}

	print_r($writeRsc);

	writeBlocktoSlot($gamePath.'/gameSlots.slt', $cityDat[11], $writeDat, $slotFile, 40); //writeBlocktoSlot($slotHandle, $checkSlot, $addData, $slotFile, $slotSize)
	

	echo '<p>Pack check:<br>
		size: '.strlen($writeDat).'<br>';
	print_r(unpack('i*', $writeDat));
} else {
	echo 'Resource slot error';
}
fclose($slotFile);
?>
