<?php

class unit {
	global $defaultBlockSize;
	private $linkFile, $unitBin, $id, $attrList;
	
	$attrList['xLoc'] = 1;
	$attrList['yLoc'] = 2;
	$attrList['icon'] = 3;
	$attrList['uType'] = 4;
	$attrList['owner'] = 5;
	$attrList['controller'] = 6;
	$attrList['status'] = 7;
	$attrList['culture'] = 8;
	$attrList['religion'] = 9;
	
	function __construct($id, $file, $size) {
		$linkFile = $file;
		fseek($linkFile, $id*$defaultBlockSize);
		$unitBin = fread($linkFile, $size);
		$unitDat = unpack('i*', $unitBin);
	}
	
	function get($desc) {
		if (array_key_exists($desc, $attrList) {
			return $attrList[$desc];
		} else {
			return false;
		}
	}
	
	function save($desc, $val) {
		if (array_key_exists($desc, $attrList) {
			fseek($this->linkFile, $this->$id*$defaultBlockSize + $attrList[$desc]*4);
			fwrite($this->linkFile, pack('i', $val));
			
			$attrList[$desc] = $val;
		} else {
			return false;
		}
	}
}

class warband extends unit {
	function __construct($id, $file, $size) {
		parent::__construct($id, $file, $size);
		
		$attrList['troopType'] = 10;
		$attrList['currentTask'] = 11;
		$attrList['currentLoc'] = 12;
		$attrList['timeStarted'] = 13;
		$attrList['expSlot'] = 14;
		$attrList['armyID'] = 15;
		$attrList['energy'] = 16;
		$attrList['enRegen'] = 17;
		$attrList['item1'] = 18;
		$attrList['item2'] = 19;
		$attrList['item3'] = 20;
		$attrList['item4'] = 21;
		$attrList['item5'] = 22;
		$attrList['item6'] = 23;
		$attrList['item7'] = 24;
		$attrList['item8'] = 25;
		$attrList['currentSlot'] = 26;
		$attrList['updateTime'] = 27;
		$attrList['visionDist'] = 28;		
		$attrList['carryCap'] = 29;
		$attrList['carrySlot'] = 30;
		$attrList['battleID'] = 31;
		}
}

?>