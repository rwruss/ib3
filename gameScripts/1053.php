<?php

echo 'Equipment options for unit '.$_SESSION['selectedUnit'];

// Get unit data for current items
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $_SESSION['selectedUnit']*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, $unitBlockSize));

// Get item descriptions
$itemDesc = explode('<->', file_get_contents($gamePath.'/units.desc'));
echo '<script>
var container = document.getElementById("equipContent");';

for ($i=1; $i<9; $i++) {
  echo 'addDiv("w'.$i.'", "eqBox eq'.$i.'", container);
  eqSpot = document.getElementById("w'.$i.'");
  eqSpot.innerHTML = "'.$unitDat[17+$i].'";';
}

for ($i=1; $i<9; $i++) {
  echo 'document.getElementById("w'.$i.'").addEventListener("click", function() {makeBox("eqItem", "1054,'.$i.'", 500, 500, 700, 50)});';
}
echo '</script>';

?>
