<?php

include("./slotFunctions.php");
include("./taskFunctions.php");

// verify that user is ok to view this info
$cityID = $_SESSION['selectedItem'];
echo 'Construct a building in '.$cityID.' - Building Type is '.$postVals[1].'<br>';
// Verify that the person giving the order has the proper credintials
$unitFile = fopen($gamePath.'/unitDat.dat' ,'r+b');
fseek($unitFile, $cityID*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));

//print_r($cityDat);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
$approved = array_search($pGameID, $credList);
echo 'Approved level '.$approved.'<br>
Show buildings in slot '.$cityDat[17].'
Show tasks in slot '.$cityDat[21].'<br>';

if ($approved) {
	$buildingsPresent = array_fill(0, 1000, 0);
	echo 'Options for construction of building type '.$postVals[1].' at location '.$_SESSION['selectedItem'];

	// Load building Names and Costs
	$buildingInfo = explode('<->', file_get_contents($scnPath.'/buildings.desc'));
	$bldgType = explode('<-->', $buildingInfo[$postVals[1]]);
	$buildingCat = explode(',', $bldgType[1]);// Need to determine if this is a city building or a player building
	//print_r($buildingInfo);
	//$rscNames = explode('<-->', file_get_contents($scnPath.'/resources.desc'));
//print_r($buildingInfo);
echo 'Building cat '.$buildingCat[1].'<br>';

$cityRsc = array_fill(0, 100, 0);



// Load resources available in the city
$rscDat = unpack("i*", readSlotData($slotFile, $cityDat[11], 40));
for ($i=1; $i<sizeof($rscDat); $i+=2) {
	$cityRsc[$rscDat[$i]] += $rscDat[$i+1];
}

// Load constructed buildings present to check for prereqs

if ($buildingCat[1] == 1 || 1) {
	$bldgList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[17], 40)));
	echo 'building list for slot ('.$cityDat[17].'):';
	print_r($bldgList);
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

		$bldgList = array_filter(unpack('i*', readSlotData($slotFile, $parentDat[17], 40)));
	}
	foreach ($bldgList as $bldgID) {
		fseek($unitFile, $bldgID*$defaultBlockSize);
		$bldgDat = unpack('i*', fread($unitFile, 100));

		if ($bldgDat[7] == 1) {
			$buildingsPresent[$bldgDat[10]]++;
		}
	}
}

	$prereqs = explode(',', $bldgType[3]);
	$preCheck = true;
	$buildingsNeeded = [];
	echo 'Buildings Required ('.(sizeof($prereqs)/2).')<br>';
	print_r($prereqs);
	echo 'Buildings present:';
	print_r($buildingsPresent);;
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
	$rscList = explode('/', $bldgType[4]);
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
			$buildingPoints = explode(',', $bldgType[2]);
			// Create a new task to be processed.
			$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');
			$parameters = pack('i*', $cityDat[1], $cityDat[2],1,time(),$buildingPoints[0],0,4,$cityID,0, $cityID, $postVals[2], $postVals[1]);
			$newTask = createTask($taskFile, $parameters);
			fclose($taskFile);

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
			// This is not an upgrade to an existing building
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
			fwrite($unitFile, pack('i*', $cityDat[1], $cityDat[2], 0, 9, $cityID, $cityID, 0, 1, 1, $postVals[1], 0, 0, 0, 0, $cityID, 0, 0, 0, 1, 0, 0, 0, $bldgType[11]));

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
			$buildingPoints = explode(',', $bldgType[2]);
			$taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');
			$parameters = pack('i*', $cityDat[1], $cityDat[2],1,time(),$buildingPoints[0],0,2,$cityID,0, $cityID, $newID, $postVals[1]);
			$newTask = createTask($taskFile, $parameters); //createTask($taskFile, $taskIndex, $duration, $parameters, $gamePath, $slotFile)
			fclose($taskFile);

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
			echo 'Record task ('.$newTask.') in slot '.$cityDat[21].'<br>';
			$taskSlot = new itemSlot($cityDat[21], $slotFile, 40);
			$taskSlot->addItem($newTask, $slotFile);
			//addDataToSlot($gamePath.'/gameSlots.slt', $cityDat[21], pack('i', $newTask), $slotFile);
			$checkSlot = new itemSlot($cityDat[21], $slotFile, 40);
			echo 'Result is:<p>';
			print_r($checkSlot->slotData);
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
