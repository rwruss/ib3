<?php

include('./slotFunctions.php');

$unitFile = fopen($gamePath.'/unitDat.dat', 'rb');
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
$taskFile = fopen($gamePath.'/tasks.tdt', 'rb');

//fseek($unitFile, $_SESSION['selectedItem']*$defaultBlockSize);
//$unitDat = unpack('i*', fread($unitFile, 200));

fseek($unitFile, $pGameID*$defaultBlockSize);
$playerDat = unpack('i*', fread($unitFile, 200));

//$plotList = new itemSlot($playerDat[20], $slotFile, 40);

//echo 'Show plots that char is in slot '.$unitDat[35];

if ($playerDat[20] > 0) {
  $plotList = new itemSlot($playerDat[20], $slotFile, 40);
  print_r($plotList->slotData);
  echo '<script>';
  for ($i=1; $i<=sizeof($plotList->slotData); $i++) {
    //echo 'Check '.$i.' which has a value of '.$plotList->slotData[$i].'<br>';
    if ($plotList->slotData[$i] > 0) {
      fseek($taskFile, $plotList->slotData[$i]*200);
    	$plotDat = unpack('i*', fread($taskFile, 200));
      echo 'unitList.newUnit({unitType:"plot", unitID:'.$plotList->slotData[$i].', actionPoints:'.($plotDat[6]+1000).', tResist:10, target:0, unitName:"Plot #'.$plotList->slotData[$i].'"});
      unitList.renderSum('.$plotList->slotData[$i].', document.getElementById("plotListContent"));
      ';
      /*
      echo 'var item = plotSummary({unitName: "plot #'.$plotList->slotData[$i].'", unitID:'.$plotList->slotData[$i].'}, document.getElementById("plotListContent"));
      ';*/
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
fclose($taskFile);

?>
