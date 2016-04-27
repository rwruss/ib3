<?php
date_default_timezone_set('America/Chicago');
$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/1));

echo 'Object type 10 - Tribe unit<br><script>
resetMove();
newMoveBox('.$unitID.', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
newUnitDetail('.$unitID.', "rtPnl");

document.getElementById("Udtl_'.$unitID.'_name").innerHTML = "unitName";
setUnitAction('.$unitID.', '.($actionPoints/1000).');
setUnitExp('.$unitID.', 0.5);

var settle = addDiv("unitUpgrades", "stdFloatDiv", document.getElementById("rtPnl"));
settle.innerHTML = "Settle Here";
settle.addEventListener("click", function () {makeBox("settle", "1067,'.$postVals[1].'", 500, 500, 200, 50)});
</script>
';

?>
