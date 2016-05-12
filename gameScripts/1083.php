<?php

include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');

fseek($unitFile, $_SESSION['selectedItem']*$defaultBlockSize);
$unitDat = unpack('i*', fread($unitFile, 200));

echo 'Show plots that char is in slot '.$unitDat[35];

if ($unitDat[35] > 0) {
  $plotList = new itemSlot($unitDat[35], $slotFile, 40);
  print_r($plotList->slotData);
  echo '<script>';
  for ($i=1; $i<=sizeof($plotList->slotData); $i++) {
    //echo 'Check '.$i.' which has a value of '.$plotList->slotData[$i].'<br>';
    if ($plotList->slotData[$i] > 0) {
      echo 'var item = plotSummary({desc: "plot #'.$plotList->slotData[$i].'", id:'.$plotList->slotData[$i].'}, document.getElementById("plotListContent"));
      //item.addEventListener("click", function() {makeBox("plotDetail", "1081,'.$plotList->slotData[$i].'", 500, 500, 200, 50)});
      ';
      //echo 'Plot '.$plotList->slotData[$i].'<br>';
    } else {
      //echo 'Failed on '.$plotList->slotData[$i].'<br>';
    }
  }
} else {
  echo 'No plots';
}
echo '</script>';
//print_r($plotList->slotData);

fclose($slotFile);
fclose($unitFile);

?>
