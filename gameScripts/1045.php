<?php
include("./slotFunctions.php");
echo 'Receive a move order<br>';
print_r($postVals);

$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'r+b');
$datSlotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');

$xDir = [0,-1,0,1,-1,0,1,-1,0,1];
$yDir = [0,1,1,1,0,0,0,-1,-1,-1];

$showHierarchy = [0, 1, 0, 2, 4, 0, 3, 0, 3];

fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, $unitBlockSize));

if ($playerDat[32] > 0) {
	$myWarList = array_filter(unpack("i*", readSlotData($datSlotFile, $playerDat[32], 40)));
} else {
	$myWarList = [];
}

// Determine if move is allowed
if ($unitDat[5] == $pGameID || $unitDat[6] == $pGameID) {
  $moveList = str_split($postVals[2]);
  //  Calculate current slot & new Slot
  $oldSlot = floor($unitDat[2]/120)*120+floor($unitDat[1]/120);
  $newLoc[1] = $unitDat[1];
  $newLoc[2] = $unitDat[2];

  $startCol = floor($unitDat[1]/2)-51;
  $startRow = floor($unitDat[2]/2)-51;
  // Load terrain data for the affected area:  Each degree has 60x60 tiles
  $terMoveFile = fopen('../scenarios/common/terMoveFile.dat', 'rb');
  $terDat = '';
  for ($row=0; $row<101; $row++) {
	  fseek($terMoveFile, ($startRow+$row)*60*120+$startCol);
	  $terDat .= fread($terMoveFile, 101);
  }
  fclose($terMoveFile);
  $terInfo = unpack("C*", $terDat);

  // Load elevation data for the affected area: Each degree has 60 x 60 tiles
  $elMoveFile = fopen('../scenarios/common/elMoveFile.dat', 'rb');
  $elDat = '';
  for ($row=0; $row<101; $row++) {
	  fseek($elMoveFile, ($startRow+$row)*60*120+$startCol);
	  $elDat .= fread($elMoveFile, 101);
  }
  fclose($elMoveFile);
  $elInfo = unpack("c*", $elDat);

  //Temp override
  $terInfo = array_fill(1,101*101,20);
  $elInfo = array_fill(1,101*101,0);

  // Load rivers for this area

  // Adjust x/y coordinates for unit.
  $moves = sizeof($moveList);
  $terIndex = 5101;
  $riverCheck = true;
	$showOnMap = 1;
  $loadedSlots = [];
  $unitList = [];
  $diplomacyList = [];
  $collisionList = [];
	$riverDat = [];
  $actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/1));
  $seenBy = [];
	// Check for collision at end location - if there is one - confirm move order

  for ($i=0; $i<$moves; $i++) {
  	$oldElevation = $elInfo[$terIndex];
  	$terIndex += $xDir[$moveList[$i]] + $yDir[$moveList[$i]]*101;
  	$moveCost = $terInfo[$terIndex] + abs(floor($oldElevation - $elInfo[$terIndex])/100);

	$tmpLoc[1] = $newLoc[1] + $xDir[$moveList[$i]]*2;
	$tmpLoc[2] = $newLoc[2] + $yDir[$moveList[$i]]*2;
	$startSlot = floor($newLoc[2]/120)*120+floor($newLoc[1]/120);
	$endSlot = floor($tmpLoc[2]/120)*120+floor($tmpLoc[1]/120);
  	if ($actionPoints >= $moveCost) {
		// Check rivers

		if (!array_key_exists(intval($endSlot), $riverDat)) {
			// River data for this area not loaded yet
			$riverDat[$endSlot] = unpack("v*", file_get_contents("./rivers/riverFiles/1/".$endSlot.".pf2"));
			echo 'River file '.$endSlot.' size is '.filesize("./rivers/riverFiles/1/".$endSlot.".pf2").'<br>';
		}


		if ($startSlot != $endSlot) {
			// Need to check the start slot separate from the end slot
			echo 'Check Rivers in '.$endSlot.'<br>';
			$segCount = 0;
			for ($rNum=1; $rNum<sizeof($riverDat[$startSlot])-2; $rNum+=2) {
				$segCount++;
				// If there is a break in the lines, skip this segment and increment an extra space to start of next segment
				if ($rNum+2 == 0 && $rNum+3 == 0) {
					$rNum+=2;
				} else {
					//echo 'Check spin for<br>';
					$spin1 = calcSpin([$newLoc[1], $newLoc[2]], [$tmpLoc[1], $tmpLoc[2]], [$riverDat[$startSlot][$rNum], $riverDat[$startSlot][$rNum+1]]);
					$spin2 = calcSpin([$newLoc[1], $newLoc[2]], [$tmpLoc[1], $tmpLoc[2]], [$riverDat[$startSlot][$rNum+2], $riverDat[$startSlot][$rNum+3]]);
					$spin3 = calcSpin([$riverDat[$startSlot][$rNum], $riverDat[$startSlot][$rNum+1]], [$riverDat[$startSlot][$rNum+2], $riverDat[$startSlot][$rNum+3]], [$newLoc[1], $newLoc[2]]);
					$spin4 = calcSpin([$riverDat[$startSlot][$rNum], $riverDat[$startSlot][$rNum+1]], [$riverDat[$startSlot][$rNum+2], $riverDat[$startSlot][$rNum+3]], [$tmpLoc[1], $tmpLoc[2]]);
					if ($spin1*$spin2 <= 0 && $spin3*$spin4 <=0) {
						// May need to add a check against $terInfo[$terIndex] to see if there is a bridge along this path.  This could be indicated by a terrain value greater than a certain
						// threshold.  The terrain affects would then be calculate using a mod of this value.

						echo 'Can\'t cross a river! Segment '.$riverDat[$startSlot][$rNum].', '.$riverDat[$startSlot][$rNum+1].' to '.$riverDat[$startSlot][$rNum+2].', '.$riverDat[$startSlot][$rNum+3].'<br>';
						goto endMove;
					}
				}
			}
			echo 'Checked '.$segCount.' segments<Br>';
		}

		// Check the rivers in the ending slot
		echo 'Check Rivers in '.$startSlot.'<br>';
		$segCount = 0;
		for ($rNum=1; $rNum<sizeof($riverDat[$startSlot])-2; $rNum+=2) {
			// If there is a break in the lines, skip this segment and increment an extra space to start of next segment
			$segCount++;

			if ($rNum+2 == 0 && $rNum+3 == 0) {
				$rNum+=2;
			} else {
				//echo 'Check spin for<br>';
				$spin1 = calcSpin([$newLoc[1], $newLoc[2]], [$tmpLoc[1], $tmpLoc[2]], [$riverDat[$startSlot][$rNum], $riverDat[$startSlot][$rNum+1]]);
				$spin2 = calcSpin([$newLoc[1], $newLoc[2]], [$tmpLoc[1], $tmpLoc[2]], [$riverDat[$startSlot][$rNum+2], $riverDat[$startSlot][$rNum+3]]);
				$spin3 = calcSpin([$riverDat[$startSlot][$rNum], $riverDat[$startSlot][$rNum+1]], [$riverDat[$startSlot][$rNum+2], $riverDat[$startSlot][$rNum+3]], [$newLoc[1], $newLoc[2]]);
				$spin4 = calcSpin([$riverDat[$startSlot][$rNum], $riverDat[$startSlot][$rNum+1]], [$riverDat[$startSlot][$rNum+2], $riverDat[$startSlot][$rNum+3]], [$tmpLoc[1], $tmpLoc[2]]);
				if ($spin1*$spin2 <= 0 && $spin3*$spin4 <=0) {
					// May need to add a check against $terInfo[$terIndex] to see if there is a bridge along this path.  This could be indicated by a terrain value greater than a certain
					// threshold.  The terrain affects would then be calculate using a mod of this value.

					echo 'Can\'t cross a river! Segment '.$riverDat[$startSlot][$rNum].', '.$riverDat[$startSlot][$rNum+1].' to '.$riverDat[$startSlot][$rNum+2].', '.$riverDat[$startSlot][$rNum+3].' Results: ';
					echo 'Spin1: '.$spin1;
					$riverCheck = false;
					goto endMove;
				}
			}
		}
		echo 'Checked '.$segCount.' segments<Br>';

  	if ($riverCheck == true) {
    	echo 'process move<br>';
  		$newLoc[1] = $tmpLoc[1];
  		$newLoc[2] = $tmpLoc[2];
  		$actionPoints -= $moveCost;

  		// Check for collisions at the new location
  		// Load slot data for the new location if it is not already loaded

  		$currentSlot = floor($newLoc[2]/120)*120+floor($newLoc[1]/120);

		// Load surrounding slots as needed for view checks

        echo 'Look for slot '.$currentSlot.'<br>';
  		if (!array_key_exists(intval($currentSlot), $loadedSlots)) {
			echo 'Load units in slot '.$currentSlot.'<br>';
			$loadedSlots[$currentSlot] = [];
			$mapList = new itemSlot($currentSlot, $mapSlotFile, 404);
			$unitList = array_filter($mapList->slotData);
			$checkList = [];
			for ($listNum = 1; $listNum<=sizeof($mapList->slotData); $listNum+=2) {
				if ($mapList->slotData[$listNum] > 0) $checkList[] = $mapList->slotData[$listNum];
			}
  			foreach ($checkList as $unitNumber) {
				echo 'Load unit '.$unitNumber.'<br>';
  				fseek($unitFile, $unitNumber*$defaultBlockSize);
  				$tmpUnit = unpack('i*', fread($unitFile, $unitBlockSize));
  				//array_push($loadedSlots[$currentSlot], $unitNumber);
				$loadedSlots[$currentSlot][] = $unitNumber;
				$foundList[$unitNumber] = [];
				array_push($foundList[$unitNumber], $tmpUnit[1], $tmpUnit[2], $tmpUnit[6], $tmpUnit[4], $tmpUnit[28]); // X Loc, Y Loc, Controller, Type, View Range
  			}
  		} else {
          echo 'That key already exists!';
        }
		$turnCollisions = checkCollisions($currentSlot, $newLoc, $loadedSlots, $foundList);
		foreach ($turnCollisions as $collisionID) {
			// Check if diplomacy has already been loaded for the unit that you are colliding with
			echo 'Collision with type '.$foundList[$collisionID][3].'<br>';
			if (!array_key_exists($collisionID, $collisionList)) {
				if ($foundList[$collisionID][2] != $pGameID) {
					if ($moves - $i == 1) {
						if ($foundList[$collisionID][3] <= $showHierarchy[$unitDat[4]]) $showOnMap = 0;
					}
					switch ($foundList[$collisionID][3]) {
					// Determine action based on unit type
						case 1: // A village
							if ($moves - $i == 1) {
								echo 'End in city '.$collisionID.'<br>';
							} else {
								echo 'Collision with city '.$collisionID.'<br>';}
							break; // end case 1

						case 2: // a resource building
							if ($moves - $i == 1) {
								echo 'End on resource Building '.$collisionID.' Player '.$pGameID.', Controller '.$foundList[$collisionID][2].'<br>';
							} else {
								echo 'Collision with resource Building '.$collisionID.' Player '.$pGameID.', Controller '.$foundList[$collisionID][2].'<br>';
							}
							break; // end case 2

						case 3: // An army
							echo 'Collision with army '.$collisionID.'<br>';
							break; // end case 3

						case 4: // A character
							echo 'Collision with character '.$collisionID.'<br>';
							break; // end case 4

						case 5: // An agent
							echo 'Collision with agent '.$collisionID.'<br>';
							break; // end case 5

						case 6:  // A warband
							echo 'Collision with warband '.$collisionID.'<br>';
							// Load the war list for the unit
							fseek($unitFile, $foundList[$collisionID][2]*$defaultBlockSize);
							$trgPlayerData = unpack('i*', fread($unitFile, $unitBlockSize));

							$trgWarList = array_filter(unpack("i*", readSlotData($datSlotFile, $trgPlayerData[32], 40)));
							$collisionList[$collisionID] = 1;

							if(sizeof($trgWarList) > sizeof($myWarList)) {
								$foundWars = compareWarLists($myWarList, $trgWarList);
							} else {
								$foundWars = compareWarLists($trgWarList, $myWarList);
							}

							if (sizeof($foundWars) > 0) {
								// Start a battle at this location.  This will affect all of the common wars found.
								$battleID = startNewBattle($postVals[1], $collisionID, $unitFile);

								// Record battle ID for each unit
								fseek($unitFile, $postVals[1]*$defaultBlockSize+120);
								fwrite($unitFile, pack('i', $battleID));

								fseek($unitFile, $collisionID*$defaultBlockSize+120);
								fwrite($unitFile, pack('i', $battleID));

								goto endMove;
							}
							break; // break case 6

						case 12:  // a battle
							// Create a dialoge box for joining the battle
							echo '<script>scrMod("1071,'.$collisionID.'")</script>';

							break; // end case 12
						}
				} else {
					// Collision is with a player controlled object
					echo 'Collision with something you own <br>';

					switch ($foundList[$collisionID][3]) {
					// Determine action based on unit type
						case 1: // A village
							if ($moves - $i == 1) {
								echo 'End in city '.$collisionID.'<br>';
								$showOnMap = 0;
							} else {
								echo 'Collision with city '.$collisionID.'<br>';}
							break; // end case 1
						}
					}
				} else {
					echo 'This one was already checked';
				}
  			}

  			echo 'Step to ('.$newLoc[1].', '.$newLoc[2].').  '.$moveCost.' Move points used and '.$unitDat[16].' Action Points Remaining<br>';
  		}
  	} else {
      echo 'Not enough move points ('.$actionPoints.' vs '.$moveCost.')<br>';
  		break;
  	}
  }
 endMove:
  $newSlot = floor($newLoc[2]/120)*120+floor($newLoc[1]/120);

  // if slot has changed, make adjustment
  echo 'Old Slot: '.$oldSlot.', New Slot: '.$newSlot;

	if ($oldSlot != $newSlot) {
		// Remove from old slot file and add to new slot file

		$oldSlotItem = new blockSlot($oldSlot, $mapSlotFile, 404);
		$loc = $oldSlotItem->findLoc($postVals[1],2);
		$oldSlotItem->addItem($mapSlotFile, pack('i*', 0, 0), $loc);

		$newSlotItem = new blockSlot($newSlot, $mapSlotFile, 404);
		$loc = $newSlotItem->findloc(0,2);
		$newSlotItem->addItem($mapSlotFile, pack('i*', $postVals[1], $showOnMap), $loc);

		fseek($unitFile, $postVals[1]*$defaultBlockSize+100);
		fwrite($unitFile, pack('i', $newSlot));

		echo 'Old Slot:<br>';
		print_r($oldSlotItem->slotData);

		echo '<p>New Slot<br>';
		print_r($newSlotItem->slotData);

	} else {
		// Adjust to show or not show the unit depending upon the showOnMap value
		$newSlotItem = new blockSlot($newSlot, $mapSlotFile, 404);
		$loc = $newSlotItem->findloc(0,2);
		$newSlotItem->addItem($mapSlotFile, pack('i*', $postVals[1], $showOnMap), $loc);
	}


	// Record new location of unit
	fseek($unitFile, $postVals[1]*$defaultBlockSize);
	fwrite($unitFile, pack('i*', $newLoc[1], $newLoc[2]));

	// Record new energy level of unit
	fseek($unitFile, $postVals[1]*$defaultBlockSize+60);
	fwrite($unitFile, pack('i', $actionPoints));

	// Record Current Slot and last update time for unit
	fseek($unitFile, $postVals[1]*$defaultBlockSize+100);
	fwrite($unitFile, pack('i*', $newSlot, time()));

	// Output results to browser
	echo '<script>
	setUnitAction('.$postVals[1].',  '.($actionPoints/1000).');
	updateUnitPosition('.$postVals[1].', '.$newLoc[1].', '.$newLoc[2].');
	resetMove('.$newLoc[1].', '.$newLoc[2].');
	</script>';

} else {
	// Move is not allowed
  echo 'Not allowed to make this order';
}
fclose($datSlotFile);
fclose($mapSlotFile);
fclose($unitFile);

function calcSpin($p1, $p2, $p3) {
	/*
	echo 'P1: '.$p1[0].', '.$p1[1].'<br>';
	echo 'P2: '.$p2[0].', '.$p2[1].'<br>';
	echo 'P3: '.$p3[0].', '.$p3[1].'<br>';*/
	return ($p2[0]-$p1[0])*($p3[1]-$p1[1])-($p3[0]-$p1[0])*($p2[1]-$p1[1]);
}

function checkCollisions($slotNumber, $location, &$loadedSlots, &$unitList) {
	$returnList = [];
  print_r($loadedSlots);
	for ($j=0; $j<sizeof($loadedSlots[$slotNumber]); $j++) {
    echo 'Check '.$loadedSlots[$slotNumber][$j].'<br>';
		if ($location[1] == $unitList[$loadedSlots[$slotNumber][$j]][0]) { /// X Values Match
			if ($location[2] == $unitList[$loadedSlots[$slotNumber][$j]][1]) { // Y Values Match
        echo 'HIT!!!!!!!!!!!!!!!!!!!!!!';
				$returnList[] = $loadedSlots[$slotNumber][$j];
			}
		}
	}

	return $returnList;
}

function compareWarLists(&$shortList, &$longList) {
	echo 'Coparing war lists<br>';
	$returnList = [];

	// List format is war #, side #
	for($w=1; $w<=sizeof($shortList); $w+=2) {
		$matchKey = array_search($shortList[$w], $longList);
		if ($matchKey) {
			if ($shortList[$w+1] != $longList[$matchKey+1]) {
				$returnList[] = $shortList[$w];
			}
		}
	}
	return $returnList;
}

function joinBattle() {

}

function startNewBattle($unit1, $unit2, $unitFile) {
	global $defaultBlockSize;

	// Create a battle slot with location, start time, affected wars, etc.
	if (flock($unitFile, LOCK_EX)) {
		// Get new id for battle
		fseek($unitFile, 0, SEEK_END);
		$size = ftell($unitFile);
		$battleID = $size/$defaultBlockSize;

		fseek($unitFile, $battleID*$defaultBlockSize+96);
		fwrite($unitFile, pack('i', 0));

		flock($unitFile, LOCK_UN); // release dat lock
	}

	// Add the two starting units to the war list
	fseek($unitFile, $battleID*$defaultBlockSize+40);
	fwrite($unitFile, pack('i*', $unit1, $unit2, time(), time()+86400));

	return $battleID;
	/*
	// Remove the two units from the map slot file and add a battle icon
	$mapSlotItem = new itemSlot(, $mapSlotFile, 404);

	$u1_pos = array_search($unit1, $mapSlotItem->slotData);
	$mapSlotItem->deleteItem($u1_pos, $mapSlotFile);

	$u2_pos = array_search($unit2, $mapSlotItem->slotData);
	$mapSlotItem->deleteItem($u2_pos, $mapSlotFile);

	$mapSlotItem->addItem($newID, $mapSlotFile);

	// Adjust the status and battle ID for each unit to set it for battle
	fseek($unitFile, $unit1*$defaultBlockSize + 24);
	fwrite($unitFile, pack('i', 2));

	fseek($unitFile, $unit1*$defaultBlockSize + 120);
	fwrite($unitFile, pack('i', $newID));

	fseek($unitFile, $unit2*$defaultBlockSize + 24);
	fwrite($unitFile, pack('i', 2));

	fseek($unitFile, $unit2*$defaultBlockSize + 120);
	fwrite($unitFile, pack('i', $newID));
	*/
}

?>
