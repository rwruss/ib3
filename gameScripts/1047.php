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
	echo '
	<div class="taskHeader" id="bldg_header"></div>
	<div class="centeredmenu" id="bldg_tabs"><ul id="bldg_tabs_ul"></ul></div>
	<div class="taskOptions" id="bldg_options"></div>';

	echo '<script>
		newTabMenu("bldg");
		newTab("bldg", 1);
		newTab("bldg", 2);
		tabSelect("bldg", 1);
		</script>';
	echo '<div id="bldgHolder"></div><script>';
	$bldgList = array_filter(unpack("i*", readSlotData($slotFile, $cityDat[17], 40)));
	foreach ($bldgList as $bldgID) {
		fseek($unitFile, $bldgID*$defaultBlockSize);
		$bldgDat = unpack('i*', fread($unitFile, $defaultBlockSize));
		echo 'newBldgSum("'.$bldgID.'", "bldg_tab1", .5);';
	}

	// Generate a list of buildings that can be built at this location
	for ($i=0; $i<10; $i++) {
		echo 'newBldgSum("'.$i.'", "bldg_tab2", .5);';
	}
	echo '</script>';
} else {
  echo 'Not approved';
}

fclose($unitFile);
fclose($slotFile);

?>
