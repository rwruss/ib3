<?php

//$sentVal = explode('.', $postVals[1]);
$unitType = $taskNum[1];
$unitDesc = explode('<->', file_get_contents($gamePath.'/units.desc'));
$typeDesc = explode('<-->', $unitDesc[$unitType]);

// Verify if prerequsites are met

echo 'Produce unit type '.$unitType.'<br>
'.$typeDesc[0].'
<script>
confirmButtons("Confirm that you would like to train '.$typeDesc[0].'", "1052,'.$taskNum[0].','.$unitType.'", "taskDtlContent", 2, "Train");
</script>
';

?>
