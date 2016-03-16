<?php

if (isset($unitAssign)) {
  if ($unitAssign != 0) {
    echo 'Task type 2 Detail - unit assigned #'.$unitAssign.'<br>';
    print_r($taskDat);
  }
} else {
  echo 'Task type 2 Detail';
}

echo 'This task is construction of a building.  The task was started at '.date('m/d/y', $taskDat[2]).'<br>
This building has '.$taskDat[4].' of '.$taskDat[3].' required progress points.';
?>
