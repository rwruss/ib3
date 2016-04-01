<?php

include("./slotFunctions.php");

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
Show buildings in slot '.$cityDat[17].'<br>
Tasks in slot '.$cityDat[21].'<br>';

if ($approved) {
	$buildingsPresent = array_fill(0, 1000, 0);
	$buildingProgress = array_fill(0,1000,0);
	echo 'Options for construction of building type '.$postVals[1].' at location '.$_SESSION['selectedItem'];

	// Load building Names and Costs
	$buildingInfo = explode('<-->', file_get_contents($gamePath.'/buildings.desc'));
	//print_r($buildingInfo);
	//$rscNames = explode('<-->', file_get_contents($gamePath.'/resources.desc'));

	if ($postVals[1] < 100) {
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
		print_r($bldgList);
		foreach ($bldgList as $bldgID) {
			echo 'Check '.$bldgID.'<br>';
			fseek($unitFile, $bldgID*$defaultBlockSize);
			$bldgDat = unpack('i*', fread($unitFile, 100));

			if ($bldgDat[7] == 1) {
				echo 'FINISHED ('.$bldgDat[10].')';
				// Building is complete - add to list
				$buildingsPresent[$bldgDat[10]]++;
			} else {
				echo 'INPROGRESS ('.$bldgDat[10].')';
				$buildingProgress[$bldgDat[10]]++;
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
			if ($$bldgDat[5] == $pGameID) {
				if ($bldgDat[7] == 1 ) {
					// Building is complete - add to list
					$buildingsPresent[$bldgDat[10]]++;
					if ($bldgDat[10] == 1) $playerRscSlot = $bldgDat[11];
				} else {
					$buildingProgress[$bldgDat[10]]++;
				}
			}
		}

		// Load resources available for the player
		$rscDat = unpack("i*", readSlotData($slotFile, $$playerRscSlot, 40));
		for ($i=0; $i<sizeof($rscDat); $i+=2) {
			$cityRsc[$rscDat[$i]] += $rscDat[$i+1];
		}
	}


	// Compare the list of buildings in the $bldgNames list to what is here and can be constructed.
	echo '<script>
	addDiv("test", "reqHolder", document.getElementById("bldgStartContent"));';
	$prereqs = explode(',', $buildingInfo[$postVals[1]*7+3]);
	//echo 'Explode '.$buildingInfo[$postVals[1]*7+3].' ('.strlen($buildingInfo[$postVals[1]*7+3]).')';
	//print_r($prereqs);
	$preCheck = true;
	$buildingsNeeded = [];
	if (strlen($buildingInfo[$postVals[1]*7+3]) > 0) {
		for ($i=0; $i<sizeof($prereqs); $i+=2) {
			echo 'reqBox("Pre '.$prereqs[$i].'", "test", '.$buildingsPresent[$prereqs[$i]].','.$prereqs[$i+1].');';
			//echo 'reqBox(1, "test", '.$buildingsPresent[$prereqs[$i]].', '.$prereqs[$i+1].')';
			//echo 'Prereq: '.$prereqs[$i].' needs '.$prereqs[$i+1].'<br>';
			//echo $prereqs[$i+1].' X '.$prereqs[$i].' ('.$buildingsPresent[$prereqs[$i]].')<br>';
			if ($buildingsPresent[$prereqs[$i]] < $prereqs[$i+1]) {
				$preCheck = false;
				$buildingsNeeded[$prereqs[$i]] = $prereqs[$i+1]-$buildingsPresent[$prereqs[$i]];
			}
		}
	}

	// Check for buildings in progress
	$neededRsc = [];
	$rscList = explode('/', $buildingInfo[$postVals[1]*7+4]);
	$rscCheck = true;
	for ($i=0; $i<sizeof($rscList); $i+=2) {
		//echo 'Check for '.$rscList[$i+1].' of resource '.$rscList[$i].'. Have '.$cityRsc[$rscList[$i]];
		//echo $rscList[$i+1].' X '.$rscList[$i].' ('.$cityRsc[$rscList[$i]].')<br>';
		echo 'reqBox("RSC '.$rscList[$i].'", "test", '.$cityRsc[$rscList[$i]].', '.$rscList[$i+1].');';
		if ($cityRsc[$rscList[$i]] < $rscList[$i+1]) {
			$rscCheck = false;
			$neededRsc[] = $rscList[$i];
		}
	}
	
	// Check if building is an upgrade item
	if ($postVals[2] > 0) {
		echo 'This is an updgrade.'
		
		// Confirm that this project is in the upgrade path for the building ID.
	}
	
	if ($preCheck && $rscCheck) {
		if ($buildingProgress[$postVals[1]]+$buildingsPresent[$postVals[1]] < $buildingInfo[$postVals[1]*7+6]) {
			// Give the option to Proceed with starting a task and construction of the building
			echo 'confirmButtons("Confirm that you would like to construct this building", "1050,'.$postVals[1].','.$postVals[2].'", "bldgStartContent", 2, "Build It!");</script>';
			//echo 'confirmBox("Confirm that you would like to construct this building", "1050,'.$_SESSION['selectedItem].', '.$postVals[1].'");';
		} else {
			echo 'reqBox("Allowed:", "test", '.$buildingInfo[$postVals[1]*7+6].', '.($buildingProgress[$postVals[1]]+$buildingsPresent[$postVals[1]]).');
			confirmButtons("You cannot build anymore of the building", "", "bldgStartContent", 1, "", "OK :(");';
		}
	} else {
		echo 'confirmButtons("PreRequisites not met.", "", "bldgStartContent", 1, "", "OK :(");';
	}
	echo '</script>';

} else {
	echo 'You are not approved to look at buildings in this city';
}

fclose($unitFile);

?>
