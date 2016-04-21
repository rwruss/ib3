<?php

include("./slotFunctions.php");
include("./taskFunctions.php");

// verify that user is ok to view this info
$cityID = $_SESSION['selectedItem'];
echo 'Construct a building in '.$cityID.' - Building Type is '.$postVals[1].'<br>';
// Verify that the person giving the order has the proper credintials
$unitFile = fopen($gamePath.'/unitDat.dat' ,'rb');
fseek($unitFile, $cityID*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));

//print_r($cityDat);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
$approved = array_search($pGameID, $credList);
echo 'Approved level '.$approved.'<br>
Show buildings in slot '.$cityDat[17].'
Show tasks in slot '.$cityDat[21].'<br>';

if ($approved) {
	$buildingsPresent = array_fill(0, 1000, 0);
	echo 'Options for construction of building type '.$postVals[1].' at location '.$_SESSION['selectedItem'];

	// Load building Names and Costs
	$buildingInfo = explode('<-->', file_get_contents($scnPath.'/buildings.desc'));
	$buildingCat = explode(',', $buildingInfo[$postVals[1]*7+1]);// Need to determine if this is a city building or a player building
	//print_r($buildingInfo);
	//$rscNames = explode('<-->', file_get_contents($scnPath.'/resources.desc'));
print_r($buildingInfo);
echo 'Building cat '.$buildingCat[1].'<br>';
	
$cityRsc = array_fill(0, 100, 0);



// Load resources available in the city
$rscDat = unpack("i*", readSlotData($slotFile, $cityDat[11], 40));
for ($i=1; $i<sizeof($rscDat); $i+=2) {
	$cityRsc[$rscDat[$i]] += $rscDat[$i+1];
}

// Load constructed buildings present to check for prereqs

if ($buildingCat[1] == 1) {
	$bldgList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[17], 40)));
	foreach ($bldgList as $bldgID) {
		fseek($unitFile, $bldgID*$defaultBlockSize);
		$bldgDat = unpack('i*', fread($unitFile, 100));

		if ($bldgDat[7] == 1) {
			$buildingsPresent[$bldgDat[10]]++;
		}
	}
} else {
	$bldgList = [];
	// check if a parent city exists
	if ($cityDat[29] > 0) {
		fseek($unitFile, $cityDat[29]*$defaultBlockSize);
		$parentDat = unpack('i*', fread($unitFile, 400));
		
		$bldgList = array_filter(unpack('i*', readSlotData($slotFile, $parentDat[17], 40));
	}
	foreach ($bldgList as $bldgID) {
		fseek($unitFile, $bldgID*$defaultBlockSize);
		$bldgDat = unpack('i*', fread($unitFile, 100));

		if ($bldgDat[7] == 1) {
			$buildingsPresent[$bldgDat[10]]++;
		}
	}
}
	/*
	if ($buildingCat[1] == 1) {
		// This is a community owned building

		$cityRsc = array_fill(0, 100, 0);

		// Load resources available in the city
		$rscDat = unpack("i*", readSlotData($slotFile, $cityDat[11], 40));
		//print_r($rscDat);
		for ($i=1; $i<sizeof($rscDat); $i+=2) {
			//echo $i.' - Resource '.$rscDat[$i].' qty is '.$rscDat[$i+1].'<br>';
			$cityRsc[$rscDat[$i]] += $rscDat[$i+1];
		}

		// Load constructed buildings present to check for prereqs
		//fseek($unitFile, $_SESSION['selectedItem']);
		//$cityDat = unpack('i*', fread($unitFile, $defaultBlockSize));

		$bldgList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[17], 40)));
		foreach ($bldgList as $bldgID) {
			fseek($unitFile, $bldgID*$defaultBlockSize);
			$bldgDat = unpack('i*', fread($unitFile, 100));

			if ($bldgDat[7] == 1) {
				// Building is complete - add to list
				$buildingsPresent[$bldgDat[10]]++;
			}
		}
	} else {
		// This a a player controlled building (ID > 100)
		$playerRscSlot = 0;
		$cityRsc = array_fill(0, 100, 0);

		// Load constructed buildings present to check for prereqs
		//fseek($unitFile, $_SESSION['selectedItem']);
		//$cityDat = unpack('i*', fread($unitFile, $defaultBlockSize));

		$bldgList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[17], 40)));
		// Look for player controlled resource store building and load all other buildings
		foreach ($bldgList as $bldgID) {
			fseek($unitFile, $bldgID*$defaultBlockSize);
			$bldgDat = unpack('i*', fread($unitFile, 100));
			if ($bldgDat[5] == $pGameID) {
				if ($bldgDat[7] == 1 ) {
					// Building is complete - add to list
					$buildingsPresent[$bldgDat[10]]++;
					if ($bldgDat[10] == 1) $playerRscSlot = $bldgDat[11];
				}
			}
		}

		// Load resources available for the player
		$rscDat = unpack("i*", readSlotData($slotFile, $playerRscSlot, 40));
		for ($i=0; $i<sizeof($rscDat); $i+=2) {
			$cityRsc[$rscDat[$i]] += $rscDat[$i+1];
		}
	}
*/

	// Compare the list of buildings in the $bldgNames list to what is here and can be constructed.

	$prereqs = explode(',', $buildingInfo[$postVals[1]*7+3]);
	$preCheck = true;
	$buildingsNeeded = [];
	echo 'Buildings Required ('.sizeof($prereqs).')<br>';
	print_r($prereqs);
	if (sizeof($prereqs) > 1) {
		for ($i=0; $i<sizeof($prereqs); $i+=2) {
			//echo 'Prereq: '.$prereqs[$i].' needs '.$prereqs[$i+1].'<br>';
			echo $prereqs[$i+1].' X '.$prereqs[$i].' ('.$buildingsPresent[$prereqs[$i]].')<br>';
			if ($buildingsPresent[$prereqs[$i]] < $prereqs[$i+1]) {
				$preCheck = false;
				$buildingsNeeded[$prereqs[$i]] = $prereqs[$i+1]-$buildingsPresent[$prereqs[$i]];
			}
		}
	}

	echo 'Resources required<br>';

	$neededRsc = [];
	$rscList = explode('/', $buildingInfo[$postVals[1]*7+4]);
	$rscCheck = true;
	for ($i=0; $i<sizeof($rscList); $i+=2) {
		//echo 'Check for '.$rscList[$i+1].' of resource '.$rscList[$i].'. Have '.$cityRsc[$rscList[$i]];
		echo $rscList[$i+1].' X '.$rscList[$i].' ('.$cityRsc[$rscList[$i]].')<br>';
		if ($cityRsc[$rscList[$i]] < $rscList[$i+1]) {
			$rscCheck = false;
			$neededRsc[] = $rscList[$i];
		}
	}

	if ($preCheck && $rscCheck) {
		// Give the option to Proceed with starting a task and construction of the building
		echo 'APPROVED - PROCESS THIS BUILDING';
		echo '<script>confirmBox("Construction Started", 0, 1, "bldgStart", "", "GREAT!")</script>'; // confirmBox = function (msg, prm, type, trg, aSrc, dSrc)

		// Create a new building data space for this objects
		if ($postVals[2] > 0) { // This is an upgrade to an existing building
			$buildingPoints = explode(',', $buildingInfo[$postVals[1]*8+2]);
			// Create a new task to be processed.
			$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
			$taskIndex = fopen($gamePath.'/tasks.tix', 'rb');
			$parameters = pack('i*', $cityDat[1], $cityDat[2],1,time(),$buildingPoints[0],0,5,$cityID,0, $cityID, $postVals[2], $postVals[1]);
			$newTask = createTask($taskFile, $taskIndex, 24*60, $parameters, $gamePath, $slotFile); //createTask($taskFile, $taskIndex, $duration, $parameters, $gamePath, $slotFile)
			fclose($taskFile);
			fclose($taskIndex);

			// Update existing building status to show that it is being upgraded
			fseek($unitFile, $postVals[2]*$defaultBlockSize+24);
			fwrite($unitFile, pack('i', 2));

			// Record the new task in the building upgrade task spot
			fseek($unitFile, $postVals[2]*$defaultBlockSize+48);
			fwrite($unitFile, pack('i', $newTask));

			// Record task in city task list
			// Verify that slot exists and create one if needed.
			if ($cityDat[21] == 0) { // Need to create a new slot
				echo 'Making a new slot';
				$cityDat[21] = startASlot($slotFile, $gamePath.'/gameSlots.slt'); //startASlot($slot_file, $slot_handle)
				fseek($unitFile, $cityID*$defaultBlockSize+80);
				fwrite($unitFile, pack('i', $cityDat[21]));
			}

			addDataToSlot($gamePath.'/gameSlots.slt', $cityDat[21], pack('i', $newTask), $slotFile);
			// Does this need to be adjust to add to player task list as well??

		} else {

			if (flock($unitFile, LOCK_EX)) {

				echo 'GOT LOCK<br>';
				clearstatcache();
				$newID = max(1,filesize($gamePath.'/unitDat.dat')/$defaultBlockSize);

				fseek($unitFile, $newID*$defaultBlockSize+$unitBlockSize-4);
				fwrite($unitFile, pack('i', 0));

				flock($unitFile, LOCK_UN);

			} else {
				echo 'Major lock error';
			}

			fseek($unitFile, $newID*$defaultBlockSize);
			fwrite($unitFile, pack('i*', $cityDat[1], $cityDat[2], 0, 9, $cityID, $cityID, 0, 1, 1, $postVals[1], 0, 0, 0, 0, $cityID, 0, 0, 0, 1, 0, 0));

			// Verify that city building slot exists and create one if needed.
			if ($cityDat[17] == 0) { // Need to create a new slot
				echo 'Making a new slot';
				$cityDat[17] = startASlot($slotFile, $gamePath.'/gameSlots.slt'); //startASlot($slot_file, $slot_handle)
				fseek($unitFile, $cityID*$defaultBlockSize+64);
				fwrite($unitFile, pack('i', $cityDat[17]));
			}

			// Record new building in city buildign slot
			addDataToSlot($gamePath.'/gameSlots.slt', $cityDat[17], pack('i', $newID), $slotFile);
			//writeBlocktoSlot($gamePath.'/gameSlots.slt', $cityDat[17], pack('i*', 0, $newID), $slotFile, 40); // function writeBlocktoSlot($slotHandle, $checkSlot, $addData, $slotFile, $slotSize)

			echo '<p>New unit ID is '.$newID.'<br>';

			// add a task to the town as an "in progress" task
			// Create a new task to be processed.
			$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');
			$taskIndex = fopen($gamePath.'/tasks.tix', 'rb');
			$parameters = pack('i*', $cityDat[1], $cityDat[2],1,time(),1000,0,2,$cityID,0, $cityID, $newID, $postVals[1]);
			$newTask = createTask($taskFile, $taskIndex, 24*60, $parameters, $gamePath, $slotFile); //createTask($taskFile, $taskIndex, $duration, $parameters, $gamePath, $slotFile)
			fclose($taskFile);
			fclose($taskIndex);

			echo '<p>Parameters:';
			print_r(unpack('i*', $parameters));

			// Record task in city task list
			// Verify that slot exists and create one if needed.
			if ($cityDat[21] == 0) { // Need to create a new slot
				echo 'Making a new slot';
				$cityDat[21] = startASlot($slotFile, $gamePath.'/gameSlots.slt'); //startASlot($slot_file, $slot_handle)
				fseek($unitFile, $cityID*$defaultBlockSize+80);
				fwrite($unitFile, pack('i', $cityDat[21]));
			}

			addDataToSlot($gamePath.'/gameSlots.slt', $cityDat[21], pack('i', $newTask), $slotFile);
			// this is for adding to a map slot -> addtoSlotGen($gamePath.'/gameSlots.slt', $cityDat[21], pack('i', $taskIndex), $slot_file, 40) // function addtoSlotGen($slot_handle, $check_slot, $addData, $slot_file, $slotSize)
		}
	} else {
		if (sizeof($buildingsNeeded) > 0) {
			echo "Need more of the following buildings.";
			print_r($buildingsNeeded);
		}
		if (sizeof($neededRsc) > 0) {
			echo 'Need more of the following resoruces (City)';
			print_r($neededRsc);
		}
	}
	// gusbo, thoegen, wehrfen

} else {
	echo 'You are not approved to look at buildings in this city';
}

fclose($unitFile);
fclose($slotFile);

?>
