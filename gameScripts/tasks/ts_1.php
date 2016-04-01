<?php

$scriptCheck = FALSE;
if (isset($postVals[1]) && isset($postVals[2])) $scriptCheck = TRUE;

if (!$scriptCheck) {
	echo 'An error has occured';
	exit;
}

echo '<script>confirmBox("Foraging Started!", 0, 1, "taskDtlContent", "", "GREAT!")</script>'; // confirmBox = function (msg, prm, type, trg, aSrc, dSrc)';

// Process the start of the foraging activity at this location.

// Load unit information
$unitID = $postVals[2];
$unitFile = fopen();



?>
