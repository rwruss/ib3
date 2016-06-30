<?php
date_default_timezone_set('America/Chicago');
//$actionPoints = min(1000, $workUnit->unitDat[16] + floor((time()-$workUnit->unitDat[27])*$workUnit->unitDat[17]/360000));
$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])*$unitDat[17]/360000));

//print_R($unitDat);

switch ($unitDat[7]) {
	case 1: // Normal
		echo '<script>
		resetMove();
		unitList.newUnit({unitType:"warband", unitID:'.$unitID.', unitName:"unit name", actionPoints:'.$actionPoints.', strength:75, tNum:'.$unitDat[4].'});
		unitList.renderSum('.$unitID.', "rtPnl");
		newMoveBox('.$unitID.', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");

		var orders = addDiv("unitOrders", "stdFloatDiv", document.getElementById("rtPnl"));
		orders.innerHTML = "Unit Orders";
		orders.addEventListener("click", function () {scrMod("1059,'.$unitID.'")});
		</script>';
		break;

	case 2: // Involved in a battle

	default:
	 echo 'Undefined status';
	 break;
}

?>
