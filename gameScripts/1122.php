<?php

/*
Process gathering resources from a resource point
*/

include('./slotFunctions.php');
include('./unitClass.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

// Load the resource point
$trgPoint = loadUnit($postVals[1], $unitFile);

// Load the gathering unit
$trgUnit = loadUnit($postVals[2], $unitFile);

// Verify the unit is the right type and close enough and controlled by the right person
if ($trgUnit->get('uType') != 8) exit('Incorrect unit type');

$xDist = $trgPoint->get('xLoc') - $trgUnit->get('xLoc');
$yDist = $trgPoint->get('yLoc') - $trgUnit->get('yLoc');
if ($xDist*$xDist + $yDist*$yDist > 100) exit('This unit is too far away');

// Adjust for boosts and nerfs
/*
$buffFile = fopen($scnPath.'/type1buffs.bdf', 'rb');
fseek($buffFile, $trgUnit->get('uType')*($numTraits+$numBuildings)*2);
$buffDat = fread($buffFile, ($numTraits+$numBuildings)*2);
$charBuffMask = unpack('s*', substr($buffDat, 0, $numTraits*2);
$bldgBuffMask = unpack('s*', substr($buffDat, $numTraits*2);

$totalBuff = 0;
$totalBuff += $bldgBuffMask[$trgPoint->get('trait1')]+$bldgBuffMask[$trgPoint->get('trait2')]+$bldgBuffMask[$trgPoint->get('trait3')]+$bldgBuffMask[$trgPoint->get('trait4')]+$bldgBuffMask[$trgPoint->get('trait5')]+

// Check for army boosts
if ($trgUnit->get('armyID') > 0) {
	$army = loadUnit($trgUnit->get('armyID'), $unitFile, 40);
	if ($army->get('commander') > 0) {
		$commander = loadUnit($army->get('commander'), $unitFile, 40);
		$commandTraits = new itemSlot($commander->get('traitSlot'), $slotFile, 40);
		
		for ($i=1; $i<=sizeof($commandTraits->slotData); $i++) {
			$totalBuff += $charBuffMask[$commandTraits->slotData];
		}
	}
}

// Check for city boosts
*/

// Calculate actionpoints used in transporting to the city


// Calculate amount of resources produced

// Transfer the resoruces to the controlling city

// Add and affect for this resource spot

fclose($unitFile);
fclose($slotFile);

?>