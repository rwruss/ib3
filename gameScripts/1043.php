<?php

// Confirm that player controls unit
$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
fseek($unitFile, $postVals[1]*400);
$unitDat = unpack('i*', fread($unitFile, 400));

$control = false;
if ($unitDat[5] == $pGameID || $unitDat[6] == $pGameID) $control = true;

if ($control) {
  // Confirm that task can be done by this unit
  $taskFile = fopen($gamePath.'/tasks.tdt', 'r+b');
  fseek($taskFile, $postVals[2]*200);
  $taskDat = unpack('i*', fread($taskFile, 200));

  if ($taskDat[8] == $unitDat[12]) {

    echo '<script>alert(\'Start work on task '.$postVals[2].'\');</script>';
  }
} else {
  echo '<script>alert(\'Control Error\');</script>';
}

fclose($unitFile);



?>
