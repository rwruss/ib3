<?php

include("./slotFunctions.php");
include("./cityClass.php");

// verify that user is ok to view this info
$cityID = $_SESSION['selectedItem'];
//echo 'Show projects for city '.$cityID.'<br>';
// Verify that the person giving the order has the proper credintials
$unitFile = fopen($gamePath.'/unitDat.dat' ,'rb');
fseek($unitFile, $cityID*$defaultBlockSize);
$cityDat = unpack('i*', fread($unitFile, $unitBlockSize));

//print_r($cityDat);

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$credList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[19], 40)));
//$approved = array_search($pGameID, $credList);
$approved = checkCred($pGameID, $credList);
//echo 'Approved level '.$approved.'<br>
//Show buildings in slot '.$cityDat[17].'<br>';

if ($approved) {
	$buildingTypes = explode('<->', file_get_contents($scnPath.'/buildings.desc'));

	/*
	echo '
	<div class="taskHeader" id="bldg_header"></div>
	<div class="centeredmenu" id="bldg_tabs"><ul id="bldg_tabs_ul"></ul></div>
	<div class="taskOptions" id="bldg_options"></div>';

	echo '<script>
		newTabMenu("bldg");
		newTab("bldg", 1, "Buildings Present");
		newTab("bldg", 2, "Common Buildings");
		newTab("bldg", 3, "Player Buildings");
		tabSelect("bldg", 1);
		';*/
	echo '<script>
		useDeskTop.newPane("cityBldg");
		thisDiv = useDeskTop.getPane("cityBldg");
		var bldgTabs = makeTabMenu("bldgMenu", thisDiv);
		var bldgTabs_1 = newTab("bldgMenu", 1, "Buildings");
		var bldgTabs_2 = newTab("bldgMenu", 2, "Construct");
		var bldgTabs_3 = newTab("bldgMenu", 3, "Town Buildings");';

	$bldgList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[17], 40)));
	foreach ($bldgList as $bldgID) {

		fseek($unitFile, $bldgID*$defaultBlockSize);
		$bldgDat = unpack('i*', fread($unitFile, 400));
		$buildingInfo = explode('<-->', $buildingTypes[$bldgDat[10]]);
		//print_r($bldgDat);
		$actionPoints = min(1000, $bldgDat[16] + floor((time()-$bldgDat[27])*4167/360000));
		//$actionPoints = min(1000, $bldgDat[16] + floor((time()-$bldgDat[27])/$bldgDat[17]));
		echo 'unitList.newUnit({unitType:"building", unitID:'.$bldgID.', unitName:"'.$buildingInfo[0].'", actionPoints:'.$actionPoints.'});
		unitList.renderSum('.$bldgID.', bldgTabs_1);';
		//echo 'newBldgSum("'.$bldgID.'", "bldg_tab1", .5, '.$bldgDat[7].');';
	}

	// Generate a list of common buildings that can be built at this location
	for ($i=1; $i<sizeof($buildingTypes); $i++) {
		$bldgTypeInfo = explode('<-->', $buildingTypes[$i]);
		$bldgClass = explode(',', $bldgTypeInfo[1]);

		if ($bldgClass[2] == 1)	{
			$cultureList = explode(',', $bldgTypeInfo[9]);
			for ($j=0; $j<sizeof($bldgTypeInfo[9]); $j++) {
				if ($_SESSION['game_'.$gameID]['culture'] == $cultureList[$j]) {
					echo 'unitList.newUnit({unitType:"building", unitID:"b'.$i.'", unitName:"'.$bldgTypeInfo[0].' - '.$bldgClass[2].'"});
					unitList["unit_b'.$i.'"].buildOpt(bldgTabs_2, 0);';
					//echo 'newBldgOpt("'.$i.'", 0, "bldg_tab2", "'.$bldgTypeInfo[0].'");';
				}
			}
		}
	}

	// Generate a list of player buildings that can be built at this locaiton
	echo '</script>';
} else {
  echo 'Not approved';
}

fclose($unitFile);
fclose($slotFile);

?>
