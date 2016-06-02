<?php

session_start();
$gameID = $_SESSION['instance'];
//echo 'Game #'.$gameID;
$tileList = explode(",", $_POST['val1']);
$tileList = array(8,5,5,0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35);
include('c:/websites/ib3/public_html/slotFunctions.php');
$zoomLevel = $tileList[0];

switch($tileList[0]) {
	case 1:
	$rowSize = 120;
	break;

	case 2:
	$rowSize = 60;
	break;

	case 4:
	$rowSize = 30;
	break;

	case 8:
	$rowSize = 15;
	break;
	}

//$baseIndex = $tileList[2]*$rowSize+$tileList[1];
$xMove = array(-3, -2, -1, 0, 1, 2,
		-3, -2, -1, 0, 1, 2,
		-3, -2, -1, 0, 1, 2,
		-3, -2, -1, 0, 1, 2,
		-3, -2, -1, 0, 1, 2,
		-3, -2, -1, 0, 1, 2);
$yMove = array(-3,-3,-3,-3,-3,-3,
		  -2, -2, -2, -2,-2,-2,
		  -1, -1, -1, -1,-1,-1,
		0, 0, 0, 0,0,0,
		1, 1, 1, 1,1,1,
		2,2,2,2,2,2);

$dat = "";
$head = "";
for ($i=3; $i<sizeof($tileList); $i++) {
	$tileX = $tileList[1] + $xMove[$tileList[$i]];
	$tileY = $tileList[2] + $yMove[$tileList[$i]];
	$tileIndex = $tileY*$rowSize+$tileX;
	//$tileIndex = 95;


	$head = $head.pack("v", filesize("./riverFiles/".$zoomLevel."/".$tileIndex.".pf2"));
	$dat = $dat.file_get_contents("./riverFiles/".$zoomLevel."/".$tileIndex.".pf2");
}

//echo $head.$dat;

$drawHead = '';
$drawDat = '';
// Read data about map items to display in the map.
$slotFile = fopen('c:/websites/ib3/games/'.$gameID.'/mapSlotFile.slt', 'rb');
$unitFile = fopen('c:/websites/ib3/games/'.$gameID.'/unitDat.dat', 'rb');
$totalUnits = 0;
$maxUnitID = 0;
$maxCount = 0;
/*
$xArray = array_fill(0, 1001, 0);
$yArray = array_fill(0, 1001, 0);
$tiArray = array_fill(0, 1001, 0);
*/
$repeat = 0;
$thousand = pack('i', 1000);
//echo 'TILE LIST:';
//print_r($tileList);
for ($i=3; $i<sizeof($tileList); $i++) {
	$numUnits = 0;
	for ($rows=0; $rows<$tileList[0]; $rows++) {
		$tileY = ($tileList[2] + $yMove[$tileList[$i]])*$tileList[0]+$rows;
		for ($cols = 0; $cols<$tileList[0]; $cols++) {
			//echo 'Check tile list #'.$i.' = '.$tileList[$i];
			$tileX = ($tileList[1] + $xMove[$tileList[$i]])*$tileList[0]+$cols;

			$tileIndex = $tileY*120+$tileX;

			//$listDat = readSlotDataEndKey($slotFile, $tileIndex, 404);
			$mapDat = new itemSlot($tileIndex, $slotFile, 404); // start, file, size
			$unitList = unpack("i*", $mapDat->dataString);
			$count = 0;
			//print_r($unitList);

			for ($j=1; $j<sizeof($unitList); $j+=2) {
				if ($unitList[$j] > 0) echo 'Index '.$j.' is unit '.$unitList[$j].' -> '.$unitList[$j+1];
				if ($unitList[$j+1] == 1) {
					//echo 'Draw unit '.$unitList[$j+1];
					fseek($unitFile, $unitList[$j]*100);
					$drawDat = $drawDat.fread($unitFile, 12).substr($mapDat->dataString, $count*8, 4); // X Loc, Y Loc, Unit ID
					//$drawDat = $drawDat.fread($unitFile, 8).$thousand;
					//$drawDat = $drawDat.fread($unitFile, 12);
					$numUnits++;
				}
			}

			/*
			foreach ($unitList as $unitID) {
				$maxUnitID = max($maxUnitID, $unitID);
				if ($unitID > 0 ) {
					if ($trackArray[$unitID] > 0) {
						//echo "Repeat unit ".$unitID;
						$repeat++;
					}
					//echo "read unit".($unitID)." for Row: ".$rows.", Col ".$cols." (".$tileIndex.") in game ".$gameID." - count ".$count."<br>";
					fseek($unitFile, $unitID*100);

					$drawDat = $drawDat.fread($unitFile, 12).substr($mapDat->dataString, $count*8, 4); // X Loc, Y Loc, Unit ID
					//$drawDat = $drawDat.fread($unitFile, 8).$thousand;
					//$drawDat = $drawDat.fread($unitFile, 12);
					$numUnits++;
					$trackArray[$unitID]++;
				}
			$count++;
			}
			*/
		$maxCount = max($maxCount, $count);
		}
	}
	//echo 'lenght is '.strlen($drawDat).'<br>';
	$drawHead = $drawHead.pack("i", $numUnits);
	$totalUnits += $numUnits;
}

fclose($slotFile);
fclose($unitFile);

for ($i=0; $i<=1000; $i++) {
	//echo $i.','.$xArray[$i].','.$yArray[$i].','.$tiArray;
}

//echo $drawHead;
echo $drawHead.$drawDat;
//echo 'drawHead is '.strlen($drawHead).' drawDat is :'.strlen($drawDat).' for '.$totalUnits.' max unit ID is '.$maxUnitID.' max in a slot is '.$maxCount.' repeated '.$repeat;
//print_r(unpack("v*", $head));
//echo "Total Length: ".strlen($head.$dat);
//sort($checkList);
//print_r(unpack('i*', $drawDat));
?>
