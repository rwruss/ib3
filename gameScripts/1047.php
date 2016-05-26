<?php

include("./slotFunctions.php");
include("./cityClass.php");

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
//$approved = array_search($pGameID, $credList);
$approved = checkCred($pGameID, $credList);
echo 'Approved level '.$approved.'<br>
Show buildings in slot '.$cityDat[17].'<br>';

if ($approved) {
	$buildingInfo = explode('<->', file_get_contents($scnPath.'/buildings.desc'));
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
		</script>';
	echo '<div id="bldgHolder"></div><script>';
	$bldgList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[17], 40)));
	foreach ($bldgList as $bldgID) {
		fseek($unitFile, $bldgID*$defaultBlockSize);
		$bldgDat = unpack('i*', fread($unitFile, $defaultBlockSize));
		echo 'newBldgSum("'.$bldgID.'", "bldg_tab1", .5, '.$bldgDat[7].');';
	}

	// Generate a list of common buildings that can be built at this location
	//print_r($buildingInfo);
	for ($i=1; $i<sizeof($buildingInfo); $i++) {
		$bldgTypeInfo = explode('<-->', $buildingInfo[$i]);
		$bldgClass = explode(',', $bldgTypeInfo[1]);
		//print_r($bldgClass);
		if ($bldgClass[2] == 1)		echo 'newBldgOpt("'.$i.'", 0, "bldg_tab2", "'.$bldgTypeInfo[0].'");';
	}

	// Generate a list of player buildings that can be built at this locaiton
	for ($i=101; $i<110; $i++) {
		//echo 'newBldgOpt("'.$i.'", "bldg_tab3", "'.$buildingInfo[$i*7].'");';
	}
	echo '</script>';
} else {
  echo 'Not approved';
}

fclose($unitFile);
fclose($slotFile);

?>
