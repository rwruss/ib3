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
  tabSelect("bldg_'.$postVals[1].'", 1);
  </script>';

?>
