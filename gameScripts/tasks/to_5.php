<?php

$sentVal = explode('.', $postVals[1]);
$unitType = $sentVal[1];
$unitDesc = explode('<-->', file_get_contents($gamePath.'/units.desc'));

echo 'Produce unit type '.$unitType.'<br>
'.$unitDesc[$unitType*8+5].'
<script>
confirmButtons("Confirm that you would like to train '.$unitDesc[$unitType*8].'", "1052,'.$postVals[1].','.$unitType.'", "taskDtlContent", 2, "Train");
</script>
';

?>
