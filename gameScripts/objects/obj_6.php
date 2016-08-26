<?php
date_default_timezone_set('America/Chicago');
//$actionPoints = min(1000, $workUnit->unitDat[16] + floor((time()-$workUnit->unitDat[27])*$workUnit->unitDat[17]/360000));
$actionPoints = min(1000, $thisUnit->get('energy') + floor((time()-$thisUnit->get('updateTime')])*$thisUnit->get('enRegen')/360000));

//print_R($unitDat);

switch ($thisUnit->get('status')) {
	case 1: // Normal
		echo '<script>
		resetMove();

		newUnitDetail('.$postVals[1].', "rtPnl");
		newMoveBox('.$postVals[1].', '.$thisUnit->get('xLoc').', '.$thisUnit->get('yLoc').', "rtPnl");
		document.getElementById("Udtl_'.$postVals[1].'_name").innerHTML = "unitName";
		setUnitAction('.$postVals[1].', '.($actionPoints/1000).');
		setUnitExp('.$postVals[1].', 0.5);
		var equip = addDiv("unitEquip", "stdFloatDiv", document.getElementById("rtPnl"));
		equip.innerHTML = "Equip this unit";
		equip.addEventListener("click", function () {makeBox("equip", "1053,'.$postVals[1].'", 500, 500, 200, 50)} );

		var orders = addDiv("unitOrders", "stdFloatDiv", document.getElementById("rtPnl"));
		orders.innerHTML = "Unit Orders";
		orders.addEventListener("click", function () {scrMod("1059,'.$postVals[1].'")});

		var upgrades = addDiv("unitUpgrades", "stdFloatDiv", document.getElementById("rtPnl"));
		upgrades.innerHTML = "Upgrade";
		upgrades.addEventListener("click", function () {makeBox("upgrades", "1069,'.$postVals[1].'", 500, 500, 200, 50)});';
		if ($thisUnit->get('armyID') > 0) {
			echo '
			var armyDtl = addDiv("unitUpgrades", "stdFloatDiv", document.getElementById("rtPnl"));
			armyDtl.innerHTML = "Army Detail";
			armyDtl.addEventListener("click", passClick("1027,'.$thisUnit->get('armyID').'", rtPnl););';
		} else {
			echo '
			var armyDtl = addDiv("unitUpgrades", "stdFloatDiv", document.getElementById("rtPnl"));
			armyDtl.innerHTML = "Army Detail";
			armyDtl.addEventListener("click", passClick("1113,'.$unitID.'", rtPnl););';
		}
		
		echo '
		var controlOpt = addDiv("unitUpgrades", "stdFloatDiv", document.getElementById("rtPnl"));
		controlOpt.innerHTML = "Upgrade";
		controlOpt.addEventListener("click", function () {passClick("1115,'.$postVals[1].'")});';
		
		break;

	case 2: // Involved in a battle

	default:
	 echo 'Undefined status';
	 break;
}

echo '</script>';

?>
