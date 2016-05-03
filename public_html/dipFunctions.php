<?php

// Diplomacy functions
function compareOrgs($player1, $player2, $unitFile) {
	$tree_1 = parentTree($player1, $unitFile);
	$tree_2 = parentTree($player2, $unitFile);
	
	$returnVal = false;
	for ($i=0; $i<sizeof($tree_1); $i++) {
		$match = array_search($tree_1[$i], $tree_2);
		if ($match) {
			$returnVal = tree_1[$i];
			break;
		} 
	}
	return $returnVal;
}

function parentTree($player, $unitFile) {
	global $defaultBlockSize;
	
	$checkID = $player;
	$parentList = [];
	while ($checkID > 0) {
		$parentList[] = $checkID;
		fseek($unitFile, $checkID[1]*$defaultBlockSize);
		$tmpDat = unpack('i*', fread($unitFile, 100));
		
		$checkID = $tmpDat[15];
	}
	
	return $parentList;
}

class intel {
	function __construct($id, $file) {
		
	}
}

?>