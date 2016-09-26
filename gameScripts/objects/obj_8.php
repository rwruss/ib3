<?php
date_default_timezone_set('America/Chicago');
//$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/1));
$actionPoints = min(1000, $thisUnit->unitDat[16] + floor((time()-$thisUnit->unitDat[27])*$thisUnit->unitDat[17]/360000));

$_SESSION['selectedUnit'] = $unitID;
//print_r($thisUnit->unitDat);
echo '
	<script>
	resetMove();
	unitList.newUnit({unitType:"warband", unitID:'.$unitID.', unitName:"unit name", actionPoints:'.$actionPoints.', strength:75, tNum:'.$thisUnit->unitDat[4].'});
	unitList.renderSum('.$unitID.', "rtPnl");
	//newUnitDetail('.$unitID.', "rtPnl");
	newMoveBox('.$unitID.', '.$thisUnit->unitDat[1].', '.$thisUnit->unitDat[2].', "rtPnl");
	//document.getElementById("Udtl_'.$unitID.'_name").innerHTML = "unitName";
	//setUnitAction('.$unitID.', '.($actionPoints/1000).');
	//setUnitExp('.$unitID.', 0.5);

	var equip = addDiv("unitEquip", "stdFloatDiv", document.getElementById("rtPnl"));
	equip.innerHTML = "Equip this unit";
	equip.addEventListener("click", function () {makeBox("equip", "1053,'.$unitID.'", 500, 500, 200, 50)} );

	var orders = addDiv("unitOrders", "stdFloatDiv", document.getElementById("rtPnl"));
	orders.innerHTML = "Unit Orders";
	orders.addEventListener("click", function () {passClick("1059,'.$unitID.'", rtPnl)});
	';

	if ($thisUnit->get('armyID') > 0) {
		echo 'var armyDtl = addDiv("unitUpgrades", "stdFloatDiv", document.getElementById("rtPnl"));
		armyDtl.innerHTML = "Army Detail";
		armyDtl.addEventListener("click", function () {passClick("1027,'.$thisUnit->get('armyID').'", rtPnl);});';
	} else {
		echo 'var armyDtl = addDiv("unitUpgrades", "stdFloatDiv", document.getElementById("rtPnl"));
		armyDtl.innerHTML = "Army Detail";
		armyDtl.addEventListener("click", function () {passClick("1113,'.$unitID.'", rtPnl)});';
	}
	echo '</script>';
/*
echo '<script>
resetMove();

newUnitDetail('.$unitID.', "rtPnl");
newMoveBox('.$unitID.', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
document.getElementById("Udtl_'.$unitID.'_name").innerHTML = "unitName";
setUnitAction('.$unitID.', '.($actionPoints/1000).');
setUnitExp('.$unitID.', 0.5);


var equip = addDiv("unitEquip", "stdFloatDiv", document.getElementById("rtPnl"));
equip.innerHTML = "Equip this unit";
equip.addEventListener("click", function () {makeBox("equip", "1053,'.$unitID.'", 500, 500, 200, 50)} );

var orders = addDiv("unitOrders", "stdFloatDiv", document.getElementById("rtPnl"));
orders.innerHTML = "Unit Orders";
orders.addEventListener("click", function () {makeBox("orders", "1059,'.$unitID.'", 500, 500, 200, 50)})
</script>';
*/
?>
