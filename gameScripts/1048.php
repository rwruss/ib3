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
	for ($i=4; $i<sizeof($upgrades); $i++) {
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
	
	// Show type dependent items in progress at this locaiton
	echo 'var bldgQueue = addDiv("", "stdContain", thisDiv);';
	if ($typeInfo[7] > 0)
	for ($i=0; $i<$typeInfo[7]; $i++) {
		if ($bldgDat[$i] != 0) {
			// Get data on object being made
			fseek($unitFile, $bldgDat[$i]*$defaultBlockSize);
			$itemDat = unpack('i*', fread($unitFile, 400));
			echo '
			unitList.newUnit({unitType:"warband", unitID:'.$bldgDat[$i].', unitName:"Training", actionPoints:'.$itemDat[16].', strength:'.$itemDat[17].'});
			var objContain = addDiv("", "selectContain", bldgQueue);
			unitList.renderSum('.$bldgDat[18+$i].', objContain);
			var newButton = optionButton("", objContain, "25%");
			newButton.objectID = "'.$postVals[1].','.$i.',1";
			newButton.addEventListener("click", function () {scrMod("1092,"+this.objectID)});
			var newButton = optionButton("", objContain, "50%");
			newButton.objectID = "'.$postVals[1].','.$i.',2";
			newButton.addEventListener("click", function () {scrMod("1092,"+this.objectID)});
			var newButton = optionButton("", objContain, "100%");
			newButton.objectID = "'.$postVals[1].','.$i.',3";
			newButton.addEventListener("click", function () {scrMod("1092,"+this.objectID)});';
		}
	}
	break;
}
echo '</script>';

//include('../gameScripts/objects/bldg_'.$bldgDat[10].'.php');
fclose($unitFile);

/*
var bldg_'.$postVals[1].' = makeTabMenu("bldg_'.$postVals[1].'", thisDiv);
bldg_'.$postVals[1].'.tab_1 = newTab("bldg_'.$postVals[1].'", 1, "My Chars");
bldg_'.$postVals[1].'.tab_2 = newTab("bldg_'.$postVals[1].'", 2, "New");
*/
?>


