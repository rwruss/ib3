<?php
// Show current tasks/projects in the city
//print_r($postVals);
include("./slotFunctions.php");

// verify that user is ok to view this info
$cityID = $_SESSION['selectedItem'];
echo 'Show projects for city '.$cityID.'<br>';
// Verify that the person giving the order has the proper credintials
$unitFile = fopen($gamePath.'/unitDat.dat' ,'rb');
fseek($unitFile, $cityID*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));

//print_r($cityDat);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
$approved = array_search($pGameID, $credList);
echo 'Approved level '.$approved.'<br>';
if ($approved) {
	// Read the block data for the projects in progress
	if ($cityDat[21] > 0) {
		$taskDat = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[21], 40)));
		$taskSize = sizeof($taskDat);
		$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
		print_r($taskDat);
		for ($i=1; $i<=$taskSize; $i++) {
			fseek($taskFile, $taskDat[$i]*$defaultBlockSize);
			$taskDtl = unpack('i*', fread($taskFile, $jobBlockSize));
			//print_r($taskDtl);
			echo '<div onclick="makeBox(\'taskDtl\', \'1040,'.$taskDat[$i].'\', 500, 500, 200, 50);">'.$i.' - '.$taskDat[$i].')Task Type '.$taskDtl[5].' is '.$taskDtl[4].'/'.$taskDtl[3].' Complete</div>';
		}
		fclose($taskFile);
	} else {
		echo 'No tasks right now';
	}
} else {
	echo 'You are not authorized to view this information.';
}

/*
// Load player Dat
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $pGameID*400);
$playerDat = unpack('i*', fread($unitFile, 400));

// Load city Dat
fseek($unitFile, $postVals[1]*400);
$cityDat = unpack('i*', fread($unitFile, 400));
echo 'City '.$postVals[1].'<br>';
print_r($cityDat);
// Read buildings & upgrades slot
$bldgHere = [];
$bldgDat = [];
if ($cityDat[17] > 0) {
	$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
	$bldgDat = array_filter(unpack("N*", readSlotData($slotFile, $cityDat[17], 40))); // Bldg ID, % Complete
	fclose($slotFile);
}
$bldgDatSize = sizeof($bldgDat)/2;
$bldgProgress = array();
$bldgDmg = array();
for ($i=0; $i<$bldgDatSize; $i++) {
	if ($bldgDat[$i*2+1] == 100) $bldgHere[] = $bldgDat[$i*2];
	else if ($bldgDat[$i*2+1] < 100) $bldgProgress[] = $bldgDat[$i*2];
	else $bldgDmg[] = $bldgDat[$i*2];
}

// load and show current build options
$buildTree = file_get_contents($gamePath.'/buildings.desc');
$bldgList = explode('<-->', $buildTree);
$listSize = sizeof($bldgList)/7;

$bldgStatus = []; // a list of buildings present.  Key is building ID, valueis amount present
$bldgStatus = array_fill(1, $listSize, 0);
//fclose($buildTreeFile);

// Current Buildings Override
$bldgHere = [1,2,3];

// Show a list of buildings present
echo 'Buildings Constructed -<br>';
if (sizeof($bldgHere) > 0) {
	//echo 'showlist'.sizeof($bldgHere);
	foreach ($bldgHere as $bldgID) {
		$bldgStatus[$bldgID]++;
		echo $bldgList[$bldgID*7].'<br>';
	}
} else {
	echo 'No buildings constructed yet';
}



//echo '<hr>Buildings Available ('.$listSize.')<br>';
for ($i=1;$i<$listSize; $i++) {
	// Check prereqs
	//echo $bldgList[$i*7];


	if ($bldgList[$i*7+3] == 'None') {
		//echo 'Can Build - no prereq';
		$canBuild[] = $i;
	}	else  {

		$preReqs = explode(',', $bldgList[$i*7+3]);
		//print_r($preReqs);
		$pScore = 1;
		for ($p=0; $p<sizeof($preReqs); $p++) {
			//echo 'Pre: '.$preReqs[$p].'<br>';
			$pScore *= min(1,$bldgStatus[$preReqs[$p]]);
		}
		if ($pScore > 0) {
			//echo 'Can Build - pscore of '.$pScore;
			$canBuild[] = $i;
		}
		else {
			//echo 'Not ready';
			$noBuild[] = $i;
		}
	}

	//echo '<br>';

}
echo 'Buildings in progress:<br>';
if (sizeof($bldgProgress) > 0) {
	foreach ($bldgProgress as $bldgID) {
		echo '<div onclick="makeBox(\'cityMan\', \'1031,'.$bldgID.','.$postVals[1].'\', 500, 500, 200, 50)">'.$bldgList[$bldgID*7].' ('.$bldgID.')</div>';
	}
} else echo 'No buildings in progress';
if (sizeof($bldgDmg > 0)) {
	echo '<hr>Damaged Buildings<br>';
	foreach($bldgDmg as $bldgID) {
		echo '<div>'.$bldgList[$bldgID*7].' ('.$bldgID.')</div>';
	}
}
echo '<hr>Final tally:<hr>Can Build:<br>';
foreach($canBuild as $bldgID) {
	echo '<div onclick="makeBox(\'cityMan\', \'1030,'.$bldgID.','.$postVals[1].'\', 500, 500, 200, 50)">'.$bldgList[$bldgID*7].' ('.$bldgID.')</div>';
}
echo '<hr>Can\'t Build<br>';
foreach($noBuild as $bldgID) {
	echo $bldgList[$bldgID*7].' ('.$bldgID.')<br>';
}
print_r($bldgStatus);
*/
?>
