<?php

$chatFile = fopen('chat.dat', 'a');
if (flock($chatFile, LOCK_EX)) {

  fwrite($chatFile, $_POST['msg']);
  flock($chatFile, LOCK_UN);
}

?>
