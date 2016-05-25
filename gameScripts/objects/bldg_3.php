<?php

// Building detail items for building class 3
echo 'var bldgQueue = addDiv("", "stdContain", thisDiv);';
for ($i=18; $i<23; $i++) {
		if ($bldgDat[$i] != 0) {
			// Get data on object being made
			fseek($unitFile, $bldgDat[$i]*$defaultBlockSize);
			$itemDat = unpack('i*', fread($unitFile, 400));
			echo '
			unitList.newUnit({unitType:"warband", unitID:'.$bldgDat[$i].', unitName:"Training", actionPoints:'.$itemDat[16].', strength:'.$itemDat[17].'});
			var objContain = addDiv("", "selectContain", bldgQueue);
			unitList.renderSum('.$bldgDat[$i].', objContain);
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
/*
//print_r($bldgDat);
$targetBuilding = new townBuilding($postVals[1], $unitFile);


echo '
<div class="taskHeader" id="bldg_'.$postVals[1].'_header"></div>
<div class="centeredmenu" id="bldg_'.$postVals[1].'_tabs"><ul id="bldg_'.$postVals[1].'_tabs_ul"></ul></div>
<div class="taskOptions" id="bldg_'.$postVals[1].'_options"></div>';

echo '<script>
  newTabMenu("bldg_'.$postVals[1].'");
  newTab("bldg_'.$postVals[1].'", 1, "Building Info");
  newTab("bldg_'.$postVals[1].'", 2, "Building Tasks");
  newTab("bldg_'.$postVals[1].'", 3, "Building Upgrades");
  tabSelect("bldg_'.$postVals[1].'", 1);';

// get list of things that this building can be upgraded to

$buildingInfo = explode('<-->', file_get_contents($gamePath.'/buildings.desc'));
$bldgTypeInfo = explode(',', $buildingInfo[$targetBuilding->bType*7+1]);

//print_r($bldgTypeInfo);
switch ($targetBuilding->status) {
  case 0:
    echo 'textBlob("descriptiveBlob", "bldg_'.$postVals[1].'_tab2", "This building is still under construction.");
      newTaskSummary("'.$targetBuilding->currentTask.'", "bldg_'.$postVals[1].'_tab2", 0.50);
      textBlob("descriptiveBlob", "bldg_'.$postVals[1].'_tab3", "This building is still under construction.");';
    break;

  case 1:
  for ($i=3; $i<sizeof($bldgTypeInfo); $i++) {
  	echo 'newBldgOpt("'.$bldgTypeInfo[$i].'", '.$postVals[1].', "bldg_'.$postVals[1].'_tab3", "'.$buildingInfo[$bldgTypeInfo[$i]*7].'");';
  }

// Load unit descriptions
$unitDesc = explode('<-->', file_get_contents($gamePath.'/units.desc'));

  // Produce task options for this buildingInfo
  if ($targetBuilding->currentTask == 0) {
    $buildingPoints = explode(',', $buildingInfo[$targetBuilding->bType*7+2]);
    echo 'textBlob("descriptiveBlob", "bldg_'.$postVals[1].'_tab2", "This building is idle");';
    for ($i=1; $i<sizeof($buildingPoints); $i++) {
      $taskOpt = explode('.', $buildingPoints[$i]);
      echo 'taskOpt('.floatval($buildingPoints[$i]).', "bldg_'.$postVals[1].'_tab2", '.$postVals[1].', "'.$unitDesc[$taskOpt[1]*8].'");';
    }
  } else {
    echo 'textBlob("descriptiveBlob", "bldg_'.$postVals[1].'_tab2", "You have a task in progress with this building.");';
  }
  break;
}

echo 'textBlob("descriptiveBlob", "bldg_'.$postVals[1].'_tab1", "I want to quote somebody whot said \"blah blah blah\"");';
echo '</script>';
*/
?>
