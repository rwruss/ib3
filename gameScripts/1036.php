<?php

echo 'Description of the task #'.$postVals[1];

// read the appropriate task file
if (file_exists($gamePath.'/') {
	$taskDesc = file_get_contents('../games/common/tasks/'.$postVals[1].'.desc');
} else {
	$taskDesc = 'No Description for this task';
}

?>