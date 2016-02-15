<?php

// Confirm that player controls unit
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $postVals[1]*400);
$unitDat = unpack('i*', fread($unitFile, 400));

$control = false;
if ($unitDat[5] == $pGameID || $unitDat[6] == $pGameID) $control = true;

if ($control) {
  // Confirm that resource point is in the appropriate town
    fseek($unitFile, $postVals[2]*400);
    $pointDat = unpack('i*', fread($unitFile, 400));

    if ($pointDat[15] == $unitDat[12]) {
      echo '<script>alert(\'Start work on resource point '.$postVals[2].' with unit '.$postVals[1].'\');</script>';
    } else {
      echo '<script>alert(\'Not able to work at this location '.$pointDat[15].' <==> '.$unitDat[12].'\');</script>';
    }

} else {
  echo '<script>alert(\'Control Error\');</script>';
}

fclose($unitFile);



?>
