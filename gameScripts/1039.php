<?php
include("./slotFunctions.php");
include("./taskFunctions.php");
include('./unitClass.php');
echo 'Start production of the building.';

echo '<p>PostVals:<br>';
print_r($postVals);

// postvals 3 => bldg type to be started 1,2=>x,y coord

$cityID = $_SESSION['selectedItem'];
// Verify that the person giving the order has the proper credintials
$unitFile = fopen($gamePath.'/unitDat.dat' ,'r+b');
fseek($unitFile, $cityID*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));

$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
$approved = array_search($pGameID, $credList);

if ($approved != false) {
	$credLevel = $credList[$approved-1]*(-1);

	// Verify that enough resources are available for the building
	$buildingDat = explode('<->', file_get_contents($scnPath.'/rbuildings.desc'));
	print_r($buildingDat);
	$bldgTypeInfo = explode("<-->", $buildingDat[$postVals[3]]);
	$buildPts = explode(',', $bldgTypeInfo[2]);
	$rscList = explode('/', $bldgTypeInfo[4]);
	echo '<p>TypeInfo:<br>';
	print_r($bldgTypeInfo);
	echo '<p>RscList:<br>';
	print_r($rscList);
	$numReqd = sizeof($rscList);
	for ($i=0; $i<$numReqd; $i+=2) {
		$reqdRsc[$rscList[$i]] = $rscList[$i+1];
	}

	echo 'Resources required:<br>';
	print_r($reqdRsc);

	$cityRsc = array_fill(1, 100, 0);
	$rscDat = unpack("i*", readSlotData($slotFile, $cityDat[11], 40));
	$numHave = floor(sizeof($rscDat)/2);

	echo '<p>RSC DAT:<br>';
	print_r($rscDat);

	for ($i=0; $i<$numHave; $i++) {
		$cityRsc[$rscDat[$i*2+1]] = $rscDat[$i*2+2];
	}

	echo '<p>City Resources:<br>';
	print_r($cityRsc);

	$rscCheck = true;
	foreach ($reqdRsc as $key => $value) {
		if ($cityRsc[$key] - $value < 0) {
			$rscCheck = false;
		}
	}

	if ($rscCheck) {
		// Create a building object
		if (flock($unitFile, LOCK_EX)) {
			clearstatcache();
			$newID = max(1,filesize($gamePath.'/unitDat.dat')/$defaultBlockSize);

			fseek($unitFile, $newID*$defaultBlockSize+$unitBlockSize-4);
			fwrite($unitFile, pack('i', 0));

			flock($unitFile, LOCK_UN);

		} else {
			echo 'Major lock error';
		}
		// Record information for new building
		$newBldg = loadUnit($newID, $unitFile, 400);
		$newBldg->unitDat[1] = intval($postVals[1]/2)*2;
		$newBldg->unitDat[2] = intval($postVals[2]/2)*2;
		$newBldg->unitDat[4] = 2;
		$newBldg->unitDat[8] = 1;
		$newBldg->unitDat[9] = 1;
		$newBldg->unitDat[10] = $bldgTypeInfo[11];
		$newBldg->unitDat[15] = $cityID;
		$newBldg->unitDat[18] = 100;
		$newBldg->unitDat[19] = 1;
		$newBldg->unitDat[20] = 100;
		$newBldg->unitDat[22] = 10000;
		$newBldg->unitDat[23] = 10000;
		$newBldg->unitDat[24] = 100;
		$newBldg->unitDat[27] = time();

		$newBldg->saveAll($unitFile);
		//fseek($unitFile, $newID*$defaultBlockSize);
		//fwrite($unitFile, pack('i*', intval($postVals[1]/2)*2, intval($postVals[2]/2)*2, 0, 2, 0, 0, 0, 1, 1, $bldgTypeInfo[11], 0, 0, 0, 0, $cityID, 0, 0, 100, 1, 0, 0));

		// add the building to the town as an "in progress" building

		// Verify that slot exists and create one if needed. -> use resource point list for resource points
		echo 'City resource point is - '.$cityDat[10];
		if ($cityDat[10] == 0) { // Need to create a new slot
			$cityDat[10] = startASlot($slotFile, $gamePath.'/gameSlots.slt'); //startASlot($slot_file, $slot_handle)
			fseek($unitFile, $cityID*$defaultBlockSize+36);
			fwrite($unitFile, pack('i', $cityDat[10]));
			echo 'created new slot '.$cityDat[10];

			// Save new slot to city information
			fseek($unitFile, $cityID*$defaultBlockSize+36);
			fwrite($unitFile, pack('i', $cityDat[10]));
		}

		addDataToSlot($gamePath.'/gameSlots.slt', $cityDat[10], pack('i', $newID), $slotFile);
		echo '<p>New unit ID is '.$newID.'<br>';

		// add a task to the town as an "in progress" task
		// Create a new task to be processed.
		$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');

		$parameters = pack('i*', intval($postVals[1]/2)*2, intval($postVals[2]/2)*2,1,time(),$buildPts[0],0,2,$cityID,0, $cityID, $newID, $postVals[3]);
		$newTask = createTask($taskFile, $parameters); //createTask($taskFile, $taskIndex, $duration, $parameters, $gamePath, $slotFile)
		fclose($taskFile);

		echo '<p>Parameters:';
		print_r(unpack('i*', $parameters));

		addDataToSlot($gamePath.'/gameSlots.slt', $cityDat[21], pack('i', $newTask), $slotFile);
		// this is for adding to a map slot -> addtoSlotGen($gamePath.'/gameSlots.slt', $cityDat[21], pack('i', $taskIndex), $slot_file, 40) // function addtoSlotGen($slot_handle, $check_slot, $addData, $slot_file, $slotSize)

		// add the building to the map file at the specified location
		$mapSlot = floor($postVals[2]/120)*120+floor($postVals[1]/120);
		$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'r+b');

		//$mapSlot->addItem($mapSlotFile, pack('i*', $townID, 1), $mLoc); // file, bin value, loc
		$mSlotItem = new blockSlot($mapSlot, $mapSlotFile, 404);
		//$mSlotItem->addItem($newID, $mapSlotFile);

		$mLoc = sizeof($mSlotItem->slotData);
		for ($i=1; $i<=sizeof($mSlotItem->slotData); $i+=2) {
			if ($mSlotItem->slotData[$i] == 0) {
				$mLoc = $i;
				break;
			}
		}
		$mSlotItem->addItem($mapSlotFile, pack('i*', $newID, 1), $mLoc);

		//addtoSlotGen($gamePath.'/mapSlotFile.slt', $mapSlot, pack('i', $newID), $mapSlotFile, 404);
		fclose($mapSlotFile);
	} else {
		echo 'Not enough resources<br>';
		foreach($reqdRsc as $rscID => $rscQty) {
			echo 'Resource '.$rscID.': '.($cityRsc[$rscID] - $rscQty).'<br>';
		}
	}

} else {
	$credLevel = 0;
	echo 'You do not have the authority required to issue this order. ('.$pGameID.')';
}




?>
