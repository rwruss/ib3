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
Show buildings in slot '.$cityDat[17].'<br>';

if ($approved) {
	$buildingsPresent = array_fill(0, 1000, 0);
	echo 'Options for construction of building type '.$postVals[1].' at location '.$_SESSION['selectedItem'];

	// Load building Names and Costs
	$buildingInfo = explode('<-->', file_get_contents($gamePath.'/buildings.desc'));
	print_r($buildingInfo);
	//$rscNames = explode('<-->', file_get_contents($gamePath.'/resources.desc'));

	if ($postVals[1] < 100) {
		// This is a community owned building

		$cityRsc = array_fill(0, 100, 0);

		// Load resources available in the city
		$rscDat = unpack("i*", readSlotData($slotFile, $cityDat[11], 40));
		print_r($rscDat);
		for ($i=1; $i<sizeof($rscDat); $i+=2) {
			echo $i.' - Resource '.$rscDat[$i].' qty is '.$rscDat[$i+1].'<br>';
			$cityRsc[$rscDat[$i]] += $rscDat[$i+1];
		}

		// Load constructed buildings present to check for prereqs
		fseek($unitFile, $_SESSION['selectedItem']);
		$cityDat = unpack('i*', fread($unitFile, $defaultBlockSize));

		$bldgList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[17], 40)));
		foreach ($bldgList as $bldgID) {
			fseek($unitFile, $bldgID*$defaultBlockSize);
			$bldgDat = unpack('i*', fread($unitFile, 100));

			if ($bldgDat[7] == 1) {
				// Building is complete - add to list
				$buildingsPresent[$bldgDat[10]]  = 1;
			}
		}

		// Compare the list of buildings in the $bldgNames list to what is here and can be constructed.
		echo 'Building Prerequsites for building tye '.$postVals[1].'<br>';
		$prereqs = explode(',', $buildingInfo[$postVals[1]*7+3]);
		foreach ($prereqs as $pVal) {
			echo 'Prereq: '.$pVal.'<br>';
		}

		echo 'Resource requirements for building type '.$postVals[1].'<br>';
		$rscReqs = explode('/', $buildingInfo[$postVals[1]*7+4]);
		for ($r=0; $r<sizeof($rscReqs); $r+=2) {
			echo 'Resource #'.$rscReqs[$r].' Needs '.$rscReqs[$r+1].'<br>';
		}

	} else {
		// This a a player controlled building (ID > 100)
		$playerRscSlot = 0;
		$playerRsc = array_fill(0, 100, 0);

		// Load constructed buildings present to check for prereqs
		fseek($unitFile, $_SESSION['selectedItem']);
		$cityDat = unpack('i*', fread($unitFile, $defaultBlockSize));

		$bldgList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[17], 40)));
		// Look for player controlled resource store building and load all other buildings
		foreach ($bldgList as $bldgID) {
			fseek($unitFile, $bldgID*$defaultBlockSize);
			$bldgDat = unpack('i*', fread($unitFile, 100));
			if ($$bldgDat[5] == $pGameID) {
				if ($bldgDat[7] == 1 ) {
					// Building is complete - add to list
					$buildingsPresent[$bldgDat[10]]  = 1;
					if ($bldgDat[10] == 1) $playerRscSlot = $bldgDat[11];
				}
			}
		}

		// Load resources available for the player
		$rscDat = unpack("i*", readSlotData($slotFile, $$playerRscSlot, 40));
		for ($i=0; $i<sizeof($rscDat); $i+=2) {
			$playerRsc[$rscDat[$i]] += $rscDat[$i+1];
		}

		// Compare the cost of the building versus the resources available.
		/*
		$neededRsc = [];
		$rscList = explode('/', $buildingDat[$postVals[3]*7+4]);
		$rscCheck = true;
		for ($i=0; $i<sizeof($rscList); $i++) {
			if ($playerRsc[$rscList[$i]] < $rscList[$i+2]);
			$rscCheck = false;
			$neededRsc[] = $rscList[$i];
		}
		if ($rscCheck) {
			// Give the option to Proceed with starting a task and construction of the building
			confirmBox("Confirm that you would like to construct this building", "1050,'.$_SESSION['selectedItem].','.$postVals[1].'");
		} else {
			echo 'Need more of the following resoruces';
			print_r($neededRsc);
		}

		*/

	}
} else {
	echo 'You are not approved to look at buildings in this city';
}

fclose($unitFile);

?>
