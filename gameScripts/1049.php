<?php

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
echo 'Approved level '.$approved.'<br>
Show buildings in slot '.$cityDat[17].'<br>';

if ($approved) {
	$buildingsPresent = array_fill(0, 1000, 0);
	echo 'Options for construction of building type '.$postVals[1].' at location '.$_SESSION['selectedItem'];

	// Load building Names and Costs
	$buildingDat = explode('<-->', file_get_contents($gamePath.'/buildings.desc'));
	$rscNames = explode('<-->', file_get_contents($gamePath.'/resources.desc'));
	
	if ($postVals[1] < 100) {	
		// This is a community owned building
		
		$cityRsc = array_fill(0, 100, 0);
		
		// Load resources available in the city
		$rscDat = unpack("i*", readSlotData($slotFile, $cityDat[19], 40));
		for ($i=0; $i<sizeof($rscDat); $i+=2) {
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
		for ($i=0; $i<sizeof($bldgNames), $i+=7) {
		// If building ID is < 100 it is a common building
		}
		
	} else {
		// This a a player controlled building
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