<?php

// Standard item for message type

// Output generic message informaiton
echo 'You have been informed of a plot in progress.  This plot is now available for your characters to work on.  You will also be connected to this plot if it is discovered!  It is up to you 
whether to leave the plot, contribute to it, or pass along the secret!'.

// Output custom message information
if ($msgDat[6]<$msDat[7]) {
	$customFile = fopen($gamePath.'/customMsg.dat', 'rb');
	fseek($customFile, $msgDat[6]);
	echo '<hr>'.fread($custFile, $msgDat[7]-$msgDat[6]);
	fclose($customFile);
}

// Output message options

?>