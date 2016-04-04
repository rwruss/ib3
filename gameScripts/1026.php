<?php

echo 'Task description for task type '.$postVals[1].'<br>';

include('../gameScripts/tasks/to_'.intval($postVals[1]).'.php');

?>
