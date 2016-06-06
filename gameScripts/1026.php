<?php

//echo 'Task description for task type '.$postVals[1].'<br>';
$taskNum = explode('.', $postVals[1]);

include('../gameScripts/tasks/to_'.intval($taskNum[0]).'.php');

?>
