<?php
echo 'Unit Details for unit '.$unitID.'<br>
  Type: '.$unitDat[4].'<br>
  Owner: '.$unitDat[5].'<br>
  Controller: '.$unitDat[6].'<br>
  Status: '.$unitDat[7].'<br>
  Space: '.$unitDat[8].'<br>
  Map Object ID '.$unitDat[23].'<br>
  <div style="position:absolute; bottom:100; left:0;" onclick="makeBox(\'cityProd\', 1022, 500, 500, 200, 50);">Manage Production</div>
  <div style="position:absolute; bottom:120; left:0;" onclick="makeBox(\'cityMan\', 1021, 500, 500, 200, 50);">Characters Present</div>
  <div style="position:absolute; bottom:140; left:0;" onclick="makeBox(\'cityMan\', \'1029,'.$unitID.'\', 500, 500, 200, 50);">City Projects</div>
  <div style="position:absolute; bottom:80; left:0;" onclick="makeBox(\'garrison\', 1042, 500, 500, 200, 50);">Garrison at town</div>
  <div style="position:absolute; bottom:60; left:0;" onclick="makeBox(\'unit\', 1020, 500, 500, 200, 50);">Run an external script</div>
  <div style="position:absolute; bottom:40; left:0;" onclick="setClick(['.$unitID.',1],\'progress\')">Move to Loc</div>
  <div style="position:absolute; bottom:20; left:0;" onclick="scrMod(\'1018,'.$unitID.'\', \'scrBox\');">show Move</div>
  <div style="position:absolute; bottom:160; left:0;" onclick="makeBox(\'unit\', 2001, 500, 500, 200, 50);">Add Resources</div>
  <div style="position:absolute; bottom:180; left:0;" onclick="makeBox(\'cityBuildings\', 1047, 500, 500, 200, 50);">City Buildings</div>
  <div style="position:absolute; bottom:0; left:0;">hideMove</div>';
?>
