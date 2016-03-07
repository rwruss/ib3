<?php
include("./slotFunctions.php");
echo 'Receive a move order<br>';
print_r($postVals);

$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

$mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'rb');
$datSlotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

$xDir = [0,-1,0,1,-1,0,1,-1,0,1];
$yDir = [0,1,1,1,0,0,0,-1,-1,-1];

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
  $loadedSlots = [];
  $unitList = [];
  $diplomacyList = [];
  $collisionList = [];
  $actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/1));
  for ($i=0; $i<$moves; $i++) {
  	$oldElevation = $elInfo[$terIndex];
  	$terIndex += $xDir[$moveList[$i]] + $yDir[$moveList[$i]]*101;
  	$moveCost = $terInfo[$terIndex] + abs(floor($oldElevation - $elInfo[$terIndex])/100);

  	if ($actionPoints >= $moveCost) {
  		if ($riverCheck == true) {
        echo 'process move<br>';
  			$newLoc[1] += $xDir[$moveList[$i]]*2;
  			$newLoc[2] += $yDir[$moveList[$i]]*2;
  			$actionPoints -= $moveCost;

  			// Check for collisions at the new location
  			// Load slot data for the new location if it is not already loaded

  			$currentSlot = floor($newLoc[2]/120)*120+floor($newLoc[1]/120);
        echo 'Look for slot '.$currentSlot.'<br>';
  			if (!array_key_exists(intval($currentSlot), $loadedSlots)) {
          echo 'Load units in slot '.$currentSlot.'<br>';
          $loadedSlots[$currentSlot] = [];
  				$unitList = array_filter(unpack('i*', readSlotDataEndKey($mapSlotFile, $currentSlot, 404)));
  				foreach ($unitList as $unitNumber) {
            echo 'Load unit '.$unitNumber.'<br>';
  					fseek($unitFile, $unitNumber*$defaultBlockSize);
  					$tmpUnit = unpack('i*', fread($unitFile, $unitBlockSize));
  					//array_push($loadedSlots[$currentSlot], $unitNumber);
						$loadedSlots[$currentSlot][] = $unitNumber;
						$unitList[$unitNumber] = [];
						array_push($unitList[$unitNumber], $tmpUnit[1], $tmpUnit[2], $tmpUnit[6]); // X Loc, Y Loc, Controller
  				}
  			} else {
          echo 'That key already exists!';
        }
  			$turnCollisions = checkCollisions($currentSlot, $newLoc, $loadedSlots, $unitList);
  			foreach ($turnCollisions as $collisionID) {
  				// Check if diplomacy has already been loaded for the unit that you are colliding with
					if (!array_key_exists($collisionID, $collisionList)) {
						echo 'Collision with unit '.$collisionID.'<br>';
						// Load the war list for the unit
						fseek($unitFile, $unitList[$collisionID][2]*$defaultBlockSize);
						$trgPlayerData = unpack('i*', fread($unitFile, $unitBlockSize));

						$trgWarList = array_filter(unpack("i*", readSlotData($datSlotFile, $trgPlayerData[32], 40)));
						$collisionList[$collisionID] = 1;

						if(sizeof($trgWarList) > sizeof($myWarList)) {
							$foundWars = compareWarLists($myWarList, $trgWarList);
						} else {
							$foundWars = compareWarLists($trgWarList, $myWarList);
						}

						if (sizeof($foundWars) > 0) {
							goto endMove;
						}
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

  if ($oldSlot != $newSlot && $unitDat[26] != 0) {
    // Remove from old slot file and add to new slot file
    $mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'r+b');
    $oldDat = array_filter(unpack('i*', readSlotDataEndKey($mapSlotFile, $oldSlot, 404)));
    $newDat = array_filter(unpack('i*', readSlotDataEndKey($mapSlotFile, $newSlot, 404)));

    echo 'Old Slot:<br>';
    print_r($oldDat);

    echo '<p>New Slot<br>';
    print_r($newDat);

    removeFromEndSlot($mapSlotFile, $oldSlot, 404, $postVals[1]); //removeFromEndSlot($slotFile, $startSlot, $slot_size, $targetVal)
    addtoSlotGen($gamePath.'/mapSlotFile.slt', $newSlot, pack('i', $postVals[1]), $mapSlotFile, 404); //addtoSlotGen($gamePath.'/mapSlotFile.slt', $mapSlot, pack('i', $newID), $mapSlotFile, 404);
  }
  else if ($unitDat[26] == 0) {
    // Record this unit in the slot file
    $mapSlotFile = fopen($gamePath.'/mapSlotFile.slt', 'r+b');
    addtoSlotGen($gamePath.'/mapSlotFile.slt', $newSlot, pack('i', $postVals[1]), $mapSlotFile, 404);
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
  resetMove();
  </script>';

} else {
	// Move is not allowed
  echo 'Not allowed to make this order';
}
fclose($datSlotFile);
fclose($mapSlotFile);
fclose($unitFile);

function compareWarLists(&$shortList, &$longList) {
	echo 'Coparing war lists<br>';
	$returnList = [];
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
/*
function loadNewSlot($, $targetSlot) {
	return readSlotDataEndKey($, $targetSlot, 404); //function readSlotDataEndKey($file, $slot_num, $slot_size)
}*/
?>
