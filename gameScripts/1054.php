<?php

$itemDesc = explode('<->', file_get_contents($gamePath.'/equip.desc'));
//print_r($itemDesc);

for ($i=1; $i<sizeof($itemDesc); $i++) {
  $itemDtl = explode('<-->', $itemDesc[$i]);
  if ($itemDtl[0] == $postVals[1]) echo $itemDtl[1].'<br>';
}

?>
