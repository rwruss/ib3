<?php

include('./slotFunctions.php');
//include('./bldgObjects.php');
include('./cityClass.php');
include('./unitClass.php');


//echo 'Specific building information for building '.$postVals[1].' <br>
//This might include specific options for the selected building and type.';

// Get building information
$unitFile = fopen($gamePath.'/unitDat.dat' ,'rb');
$targetBuilding = loadUnit($postVals[1], $unitFile, 400);
//fseek($unitFile, $postVals[1]*$defaultBlockSize);
//$bldgDat = unpack('i*', fread($unitFile, $defaultBlockSize));
$bldgInfo = explode('<->', file_get_contents($scnPath.'/buildings.desc'));
$typeInfo = explode('<-->', $bldgInfo[$targetBuilding->unitDat[10]]);

echo '<script>
useDeskTop.newPane("bldgInfo");
thisDiv = useDeskTop.getPane("bldgInfo");
thisDiv.innerHTML = "";
';
//print_r($targetBuilding);
switch ($targetBuilding->unitDat[7]) {
  case 0:
    echo 'textBlob("descriptiveBlob", thisDiv, "This building is still under construction.");
      textBlob("descriptiveBlob", thisDiv, "This building is still under construction.");';
    break;

  case 1:
	// Show upgrade options for this building
  echo '
  unitList.renderSum('.$postVals[1].', thisDiv);
	var bldg_'.$postVals[1].' = makeTabMenu("bldg_'.$postVals[1].'", thisDiv);
	bldg_'.$postVals[1].'.tab_1 = newTab("bldg_'.$postVals[1].'", 1, "Upgrade");
	bldg_'.$postVals[1].'.tab_2 = newTab("bldg_'.$postVals[1].'", 2, "Tasks");
	bldg_'.$postVals[1].'.tab_3 = newTab("bldg_'.$postVals[1].'", 3, "Production");
	textBlob("", bldg_'.$postVals[1].'.tab_1, "Upgrade options");';

	$upgrades = explode(',', $typeInfo[8]);
	for ($i=0; $i<sizeof($upgrades); $i++) {
		$upgradeInfo = explode('<-->', $bldgInfo[$upgrades[$i]]);
		echo 'newBldgOpt("'.$upgrades[$i].'", '.$postVals[1].', bldg_'.$postVals[1].'.tab_1, "'.$upgradeInfo[5].'");';
	}

	// Show task options
	$tasks = explode(',', $typeInfo[2]);
	if ($tasks > 1 ) {
    //print_r($tasks);
		echo 'textBlob("", bldg_'.$postVals[1].'.tab_2, "Building options");';
		for ($i=1; $i<sizeof($tasks); $i++) {
      $taskID = explode('.', $tasks[$i]);
      $unitInfo = explode('<->', file_get_contents($scnPath.'/units.desc'));
      switch ($taskID[0]) {
        case 5:
        $uTypeInfo = explode('<-->', $unitInfo[$taskID[1]]);
  			echo 'taskOpt('.$tasks[$i].', bldg_'.$postVals[1].'.tab_2, '.$postVals[1].', "'.trim($uTypeInfo[0]).' ('.$tasks[$i].')");';
        break;

        case 6:
        echo 'taskOpt('.$tasks[$i].', bldg_'.$postVals[1].'.tab_2, '.$postVals[1].', "Train character");';
        break;

        case 11:
        echo 'taskOpt('.$tasks[$i].', bldg_'.$postVals[1].'.tab_2, '.$postVals[1].', "Gather stuff");';
      }

		}
	}

	// Show type dependent items in progress at this locaiton - check training slots
	echo 'var bldgQueue = addDiv("", "stdContain", bldg_'.$postVals[1].'.tab_3);';
	if ($typeInfo[7] > 0) {
    //print_r($targetBuilding->unitDat);
  	for ($i=0; $i<$typeInfo[7]; $i++) {
  		if ($targetBuilding->unitDat[$i+18] != 0) {
  			// Get data on object being made
  			fseek($unitFile, $targetBuilding->unitDat[$i+18]*$defaultBlockSize);
  			$itemDat = unpack('i*', fread($unitFile, 400));
        /*
  			echo '
  			unitList.newUnit({unitType:"trainingUnit", unitID:'.$targetBuilding->unitDat[$i+18].', unitName:"Training", trainPts:'.$itemDat[18].', trainReq:'.$itemDat[19].'});
  			var objContain = addDiv("", "selectContain", bldgQueue);
  			unitList.renderSum('.$targetBuilding->unitDat[18+$i].', objContain);
  			var newButton = optionButton("", objContain, "25%");
  			newButton.objectID = "'.$postVals[1].','.$i.',1";
  			newButton.addEventListener("click", function () {scrMod("1092,"+this.objectID)});
  			var newButton = optionButton("", objContain, "50%");
  			newButton.objectID = "'.$postVals[1].','.$i.',2";
  			newButton.addEventListener("click", function () {scrMod("1092,"+this.objectID)});
  			var newButton = optionButton("", objContain, "100%");
  			newButton.objectID = "'.$postVals[1].','.$i.',3";
  			newButton.addEventListener("click", function () {scrMod("1092,"+this.objectID)});';*/

        echo '
        unitList.newUnit({unitType:"trainingUnit", unitID:'.$targetBuilding->unitDat[$i+18].', unitName:"Training", trainPts:'.$itemDat[18].', trainReq:'.$itemDat[19].'});
        var orderBox = actionBox(bldgQueue, "1092,'.$postVals[1].','.$i.'", '.$targetBuilding->actionPoints().');
        unitList.renderSum('.$targetBuilding->unitDat[18+$i].', orderBox.unitSpace);';
  		} else {
        echo 'var objContain = addDiv("", "selectContain", bldgQueue);
        objContain.innerHTML = "empty production slot";';
      }
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
