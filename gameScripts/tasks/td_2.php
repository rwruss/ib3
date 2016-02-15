<?php

if (isset($unitAssign)) {
  if ($unitAssign != 0) {
    echo 'Task type 2 Detail - unit assigned #'.$unitAssign.'<br>';
    print_r($taskDat);
  }
} else {
  echo 'Task type 2 Detail';
}
?>
