<?php

//echo 'Information for building 1';

//print_r($bldgDat);

echo '
<div class="taskHeader" id="bldg_'.$postVals[1].'_header"></div>
<div class="centeredmenu" id="bldg_'.$postVals[1].'_tabs"><ul id="bldg_'.$postVals[1].'_tabs_ul"></ul></div>
<div class="taskOptions" id="bldg_'.$postVals[1].'_options"></div>';

echo '<script>
  newTabMenu("bldg_'.$postVals[1].'");
  newTab("bldg_'.$postVals[1].'", 1, "Building Info");
  newTab("bldg_'.$postVals[1].'", 2, "Building  Tasks");
  newTab("bldg_'.$postVals[1].'", 3, "Building  Upgrades")
  tabSelect("bldg_'.$postVals[1].'", 1);
  </script>';
  
// get list of things that this building can be upgraded to

$buildingInfo = explode('<-->', file_get_contents($gamePath.'/buildings.desc'));
$bldgTypeInfo = explode(',', $buildingInfo[$postVals[1]*7+1]);

for ($i=1; $i<sizeof($bldgTypeInfo); $i++) {
	echo 'newBldgOpt("'.$i.'", "bldg_'.$postVals[1].'_tab3", "'.$buildingInfo[$i*7].'");';
}

?>
