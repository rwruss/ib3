<?php
date_default_timezone_set('America/Chicago');
//$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/1));
$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])*$unitDat[17]/360000));

if ($unitDat[5] == $pGameID || $unitDat[6] == $pGameID) {
	echo 'Object type 10 - Tribe unit<br>
	'.$unitDat[5].' - '.$unitDat[6].' vs '.$pGameID.'<script>
	resetMove();

	resetMove();
		unitList.newUnit({unitType:"warband", unitID:'.$unitID.', unitName:"unit name", actionPoints:'.$actionPoints.', strength:75, tNum:'.$unitDat[4].'});
		unitList.renderSum('.$unitID.', "rtPnl");
		//newUnitDetail('.$unitID.', "rtPnl");
		newMoveBox('.$unitID.', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
		//document.getElementById("Udtl_'.$unitID.'_name").innerHTML = "unitName";
		//setUnitAction('.$unitID.', '.($actionPoints/1000).');
		//setUnitExp('.$unitID.', 0.5);

		var settle = addDiv("unitUpgrades", "stdFloatDiv", document.getElementById("rtPnl"));
		settle.innerHTML = "Settle Here";
		settle.addEventListener("click", function () {makeBox("settle", "1067,'.$postVals[1].'", 500, 500, 200, 50)});
		</script>';}
	else {include('../gameScripts/1096.php');}

/*
newMoveBox('.$unitID.', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
newUnitDetail('.$unitID.', "rtPnl");

document.getElementById("Udtl_'.$unitID.'_name").innerHTML = "unitName";
setUnitAction('.$unitID.', '.($actionPoints/1000).');
setUnitExp('.$unitID.', 0.5);

var settle = addDiv("unitUpgrades", "stdFloatDiv", document.getElementById("rtPnl"));
settle.innerHTML = "Settle Here";
settle.addEventListener("click", function () {makeBox("settle", "1067,'.$postVals[1].'", 500, 500, 200, 50)});
</script>
';*/

?>
