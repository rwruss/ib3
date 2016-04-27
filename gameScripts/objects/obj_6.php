<?php
date_default_timezone_set('America/Chicago');
$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/1));

switch ($unitdat[7]) {
	case 1: // Normal
		echo '<script>
		resetMove();

		newUnitDetail('.$unitID.', "rtPnl");
		newMoveBox('.$unitID.', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
		document.getElementById("Udtl_'.$unitID.'_name").innerHTML = "unitName";
		setUnitAction('.$unitID.', '.($actionPoints/1000).');
		setUnitExp('.$unitID.', 0.5);
		</script>Full and true unit information for this unit. <br>
		Last Update Time: '.date('d/m/y h:i:s', $unitDat[27]).'<br>'.
		time().' - '.$unitDat[27].' = '.(floor((time()-$unitDat[27])/1)).'
		Now: '.date('d/m/y h:i:s', time());
		break;

	case 2: // Involved in a battle
}

?>
