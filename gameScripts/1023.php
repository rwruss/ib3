<?php

include("./slotFunctions.php");

//$postVals: 1,2,3,4 => r,g,b,a coord 5,6 => base Tile x,y 7 => zoom Level

$tileX = [-3,-2,-1,0,1,2];
$tileY = [-3,-2,-1,0,1,2];

$colorX = $postVals[7]*120*($postVals[2]/255);
$colorY = $postVals[7]*120*($postVals[3]/255);

$tileNumY = floor($postVals[1]/6);
$tileNumX = $postVals[1]%6;

$baseX = floor(0+120*$postVals[7]*($tileX[$tileNumX]+$postVals[5])+$colorX);
$baseY = floor(0+120*$postVals[7]*($tileY[$tileNumY]+$postVals[6])+$colorY);

echo 'Base X: '.$baseX.' ('.$tileNumX.'), Base Y: '.$baseY.' ('.$tileNumY.')<br>';
echo 'X Loc = 120*'.$postVals[7].'*('.$tileX[$tileNumX].' + '.$postVals[5].')+'.$colorX.'<br>';
echo 'Y Loc = 120*'.$postVals[7].'*('.$tileY[$tileNumY].' + '.$postVals[6].')+'.$colorY.'<br>';
$cityID = $_SESSION['selectedItem'];
print_r($postVals);
// Get city data
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $cityID*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Verify credintials
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
$approved = array_search($pGameID, $credList);
echo '<p>City Dat cred slot is '.$cityDat[19].':<br>';
print_r($credList);
if ($approved != false) {
	$credLevel = $credList[$approved-1]*(-1);
} else {
	$credLevel = 0;
}

if ($approved) {
	// Review city to see if it is allowed to construct this new building type

	/// Load list of buildings already at the city
	$buildingDat = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[17], 40)));
	$numBuildings = sizeof($buildingDat);

	for ($i=0; $i<$numBuildings; $i++) {
		fseek($unitFile, $bldgID*$defaultBlockSize);
		$buildingDat = unpack('i*', fread($unitFile, $unitBlockSize));
		$typeList[$buildingDat[10]][] = $i;
		$buildingDatList[] = $buildingDat;
	}
	echo 'Select what you want to build at this location:<br>
	<select id="newBldgType">
		<option value="1">Farm</option>
		<option value="2">Mine</option>
	</select><div onclick="sendValue(\'newBldgType\', [1039,'.$baseX.','.$baseY.'])">Give the Order!</div>
	Buildings already present here:<br>';
	if ($numBuildings > 0) {
		foreach ($typeList as $typeID) {
			echo 'Building type '.$typeID.'<br>';
			foreach ($typeList[$typeID] as $bldgIndex) {
				echo '<div onclick="makeBox(\'bldgDtl\', 1037, 500, 500, 200, 50)">Building #'.$bldgIndex.' with a condition of '.$buildingDatList[$bldgIndex][20].'</div>>';
			}
		}
	// create a pointer at the location on the map
	echo '<script></script>';
	} else {
		echo 'No buildings at this location';
	}
} else {
	echo 'You do not have the authority required to issue this order. ('.$pGameID.')';
}

?>
