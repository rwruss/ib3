<?php

//echo 'Information for building 1';

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

for ($i=1; $i<sizeof($bldgTypeInfo); $i++) {
	echo 'newBldgOpt("'.$bldgTypeInfo[$i].', '.$postVals[1].'", "bldg_'.$postVals[1].'_tab3", "'.$buildingInfo[$bldgTypeInfo[$i]*7].'");';
}

// Produce task options for this buildingInfo
if ($targetBuilding->currentTask == 0) {
  echo 'textBlob("descriptiveBlob", "bldg_'.$postVals[1].'_tab2", "This building is idle");';
} else {
  echo 'textBlob("descriptiveBlob", "bldg_'.$postVals[1].'_tab2", "You have a task in progress with this building.");';
}
echo 'taskOpt(1, "bldg_'.$postVals[1].'_tab2");';

echo 'textBlob("descriptiveBlob", "bldg_'.$postVals[1].'_tab1", "I want to quote somebody whot said \"blah blah blah\"");';
echo '</script>';
?>
