<?php
include("./slotFunctions.php");
echo 'This is an army';

$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$armyUnits = new itemSlot($unitDat[14], $slotFile, 40);

print_r($armyUnits->slotData);
fclose($slotFile);
$actionPoints = min(1000, $unitDat[16] + floor((time()-$unitDat[27])/1));
echo '<script>
resetMove();

newUnitDetail('.$unitID.', "rtPnl");
newMoveBox('.$unitID.', '.$unitDat[1].', '.$unitDat[2].', "rtPnl");
document.getElementById("Udtl_'.$unitID.'_name").innerHTML = "unitName";
setUnitAction('.$unitID.', '.($actionPoints/1000).');
setUnitExp('.$unitID.', 0.5);
</script>';
?>
