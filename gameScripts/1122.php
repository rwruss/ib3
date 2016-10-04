<?php

/*
Process gathering resources from a resource point
*/

include('./slotFunctions.php');
include('./unitClass.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'r+b');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'r+b');

// Load the resource point
$trgPoint = loadUnit($postVals[1], $unitFile, 400);

// Load the gathering unit
$trgUnit = loadUnit($postVals[2], $unitFile, 400);

// Verify the unit is the right type and close enough and controlled by the right person
if ($trgUnit->get('uType') != 8) exit('Incorrect unit type');
echo 'Point at ('.$trgPoint->get('xLoc').', '.$trgPoint->get('yLoc').')<br>
	Unit at ('.$trgUnit->get('xLoc').', '.$trgUnit->get('yLoc').')';

$xDist = $trgPoint->get('xLoc') - $trgUnit->get('xLoc');
$yDist = $trgPoint->get('yLoc') - $trgUnit->get('yLoc');
if ($xDist*$xDist + $yDist*$yDist > 400) exit('This unit is too far away - '.$xDist.', '.$yDist);
$trgDst = floor(sqrt($xDist*$xDist + $yDist*$yDist));

$totalBuff = 1.0;
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

$parentCity = loadUnit($trgPoint->get('parentCity'), $unitFile, 400);
$xDist = $trgPoint->get('xLoc') - $parentCity->get('xLoc');
$yDist = $trgPoint->get('yLoc') - $parentCity->get('yLoc');
$parentDst = floor(sqrt($xDist*$xDist + $yDist*$yDist));

// Calculate amount of resources produced
$usedPoints = min($trgUnit->actionPoints(), $postVals[3]);
$productionPoints = max(0, $usedPoints - $trgDst - $parentDst);
echo 'Production Points = min('.$trgUnit->actionPoints().', '.$postVals[3].') - '.$trgDst.' - '.$parentDst;

$useTime = time();
$currentCondition = min($trgPoint->get('maxCondition'), floor($trgPoint->get('conditionPoints')+$trgPoint->get('recoveryRate')*($useTime-$trgPoint->get('updateTime'))/3600));

$maxCond = max($trgPoint->get('maxCondition'),1);
$rscProd = $totalBuff*$productionPoints*$trgPoint->get('baseProd')*($currentCondition)/($maxCond*100);
echo 'Production = '.$totalBuff.' * '.$productionPoints.' * '.$trgPoint->get('baseProd').' * '.$currentCondition.' / ('.$maxCond.' * 100)';

$currentCondition -= $productionPoints;


// Record the new condition for the site
$trgPoint->save('updateTime', $useTime);
$trgPoint->save('conditionPoints', $currentCondition);

// Record the new energy points for the unit
$trgUnit->save('updateTime', $useTime);
$trgUnit->save('energy', $trgUnit->actionPoints()-$usedPoints);

// Transfer the resoruces to the controlling city
echo 'Add'.$rscProd.' of resource '.$trgPoint->get('rscType').' to parent city';
$parentCity->adjustRsc($trgPoint->get('rscType'), $rscProd, $slotFile);

// Add and affect for this resource spot

fclose($unitFile);
fclose($slotFile);

?>
