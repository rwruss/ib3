<?php

$playerFile = fopen("../users/userDat.dat", "r+b");

if (flock($playerFile, LOCK_EX)) {  // acquire an exclusive lock
	$newID = max(1,filesize("../users/userDat.dat")/500);
	
	fseek($nameFile, 100+$numNames*40);
	fwrite($nameFile, $testName);
	fseek($nameFile, 100+$numNames*40+36);
	fwrite($nameFile, pack("N", $newID));
	
	fseek($playerFile, $newID*500+499);
	fwrite($playerFile, pack("C", 0));
	
	$pFile = fopen("../users/userCheck.dat", "r+b");
	
	fseek($pFile, $newID*255);
	fwrite($pFile, md5($postVals[4]));	
	fclose($pFile);
	
	$pNameFile = fopen("../users/playerNames.dat", "r+b");
	fseek($pNameFile, $newID*36);
	fwrite($pNameFile, substr($postVals[1], 0, 30));
	fclose($pNameFile);
	
    fflush($playerFile);            // flush output before releasing the lock
    flock($playerFile, LOCK_UN);    // release the lock
	
	session_start();
	$_SESSION['playerId'] = $newID;
	$_SESSION['pHandle'] = substr($postVals[1], 0, 30);
	
	echo "conPane<script>
		document.getElementById('plrPane').innerHTML = '".substr($postVals[1], 0, 30)." - ".$newID."';
		passClick(1014);
		</script>";
} else {
    echo "Error 7001-1";
}

?>