<?php

include("./slotFunctions.php");
include("./bldgObjects.php");


echo 'Specific building information for building '.$postVals[1].' <br>
This might include specific options for the selected building and type.';

// Get building information
$unitFile = fopen($gamePath.'/unitDat.dat' ,'rb');
fseek($unitFile, $postVals[1]*$defaultBlockSize);
$bldgDat = unpack('i*', fread($unitFile, $defaultBlockSize));
$bldgInfo = explode('<->', file_get_contents($scnPath.'/buildings.desc'));
$typeInfo = explode('<-->', $bldgInfo[$bldgDat[10]]);

echo '<script>
useDeskTop.newPane("addChars");
thisDiv = useDeskTop.getPane("addChars");
thisDiv.innerHTML = "";
';

switch ($targetBuilding->status) {
  case 0:
    echo 'textBlob("descriptiveBlob", thisDiv, "This building is still under construction.");
      newTaskSummary("'.$targetBuilding->currentTask.'", thisDiv, 0.50);
      textBlob("descriptiveBlob", thisDiv, "This building is still under construction.");';
    break;

  case 1:
	echo 'var bldg_'.$postVals[1].' = makeTabMenu("bldg_'.$postVals[1].'", thisDiv);
	bldg_'.$postVals[1].'.tab_1 = newTab("bldg_'.$postVals[1].'", 1, "Upgrade");
	bldg_'.$postVals[1].'.tab_2 = newTab("bldg_'.$postVals[1].'", 2, "Tasks");
	textBlob("", bldg_'.$postVals[1].'.tab_1, "Upgrade options");'
	$upgrades = explode(',', $typeInfo[1]);
	for ($i=3; $i<sizeof($upgrades); $i++) {
		$upgradeInfo = explode('<-->', $bldgInfo[$upgrades[$i]]);
		echo 'newBldgOpt("'.$upgrades[$i].'", '.$postVals[1].', bldg_'.$postVals[1].'.tab_1, "'.$upgradeInfo[5].'");';
	}
	  
	// Show task options
	$tasks = explode(',', $typeinfo[2]);
	if ($tasks > 1 ) {
		echo 'textBlob("", bldg_'.$postVals[1].'.tab_2, "Building options");'
		for ($i=1; $i<sizeof($tasks); $i++) {
			echo 'taskOpt('.$tasks[$i].', bldg_'.$postVals[1].'.tab_2, '.$postVals[1].', "Task '.$tasks[$i].'")';
		}
	}
	
	// Show tasks in progress at this locaiton

echo '</script>';

//include('../gameScripts/objects/bldg_'.$bldgDat[10].'.php');
fclose($unitFile);

/*
var bldg_'.$postVals[1].' = makeTabMenu("bldg_'.$postVals[1].'", thisDiv);
bldg_'.$postVals[1].'.tab_1 = newTab("bldg_'.$postVals[1].'", 1, "My Chars");
bldg_'.$postVals[1].'.tab_2 = newTab("bldg_'.$postVals[1].'", 2, "New");
*/
?>


