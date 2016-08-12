<?php

class unit {
	protected $linkFile, $unitBin, $id, $attrList;
	public $unitDat;

	function __construct($id, $file, $size) {
		global $defaultBlockSize;

		$this->linkFile = $file;
		fseek($this->linkFile, $id*$defaultBlockSize);
		$unitBin = fread($this->linkFile, $size);
		if (strlen($unitBin) < $size) {
			echo 'Start a blank unit';
			$this->unitDat = array_fill(1, 100, 0);
		} else {
			$this->unitDat = unpack('i*', $unitBin);
		}
		$this->unitID = $id;

		$this->attrList = [];
		$this->attrList['xLoc'] = 1;
		$this->attrList['yLoc'] = 2;
		$this->attrList['icon'] = 3;
		$this->attrList['uType'] = 4;
		$this->attrList['owner'] = 5;
		$this->attrList['controller'] = 6;
		$this->attrList['status'] = 7;
		$this->attrList['culture'] = 8;
		$this->attrList['religion'] = 9;
		$this->attrList['troopType'] = 10;
		$this->attrList['currentTask'] = 11;
		$this->attrList['updateTime'] = 27;
	}

	function get($desc) {
		if (array_key_exists($desc, $this->attrList)) {
			return $this->unitDat[$this->attrList[$desc]];
		} else {
			return false;
		}
	}

	function set($desc, $val) {
		if (array_key_exists($desc, $this->attrList)) {
			$this->unitDat[$this->attrList[$desc]] = $val;
		}
	}

	function changeID($newID) {
		$this->unitID = $newID;
	}

	function save($desc, $val) {
		global $defaultBlockSize;

		if (array_key_exists($desc, $this->attrList)) {
			fseek($this->linkFile, $this->id*$defaultBlockSize + $this->attrList[$desc]*4);
			fwrite($this->linkFile, pack('i', $val));

			$this->attrList[$desc] = $val;
		} else {
			return false;
		}
	}

	function saveAll($file) {
		// Pack the char data
		$packStr = '';
		foreach ($this->unitDat as $value) {
			$packStr.=pack('i', $value);
		}
		fseek($file, $this->unitID*100);
		$saveLen = fwrite($file, $packStr);
	}
}

class player extends unit {
	function __construct($id, $file, $size) {
		parent::__construct($id, $file, $size);

		$this->attrList['unitSlot'] = 22;
	}
}

class battle extends unit {
	function __construct($id, $file, $size) {
		parent::__construct($id, $file, $size);

		$this->attrList['battleType'] = 10;
		$this->attrList['baseUnit_1'] = 11;
		$this->attrList['baseUnit_2'] = 12;
		$this->attrList['timeStarted'] = 13;
		$this->attrList['resolveTime'] = 14;
		$this->attrList['sideList_1'] = 15;
		$this->attrList['sideList_2'] = 16;
	}
}

class char extends unit {
		function __construct($id, $file, $size) {
			parent::__construct($id, $file, $size);

			$this->attrList['subType'] = 10;
			$this->attrList['currentTask'] = 11;
			$this->attrList['currentLoc'] = 12;
			$this->attrList['positionSlot'] = 13;
			$this->attrList['titleSlot'] = 14;
			$this->attrList['traitSlot'] = 15;
			$this->attrList['energy'] = 16;
			$this->attrList['regen'] = 17;
			$this->attrList['publicReligion'] = 18;
			$this->attrList['experience'] = 18;
		}
}

class warband extends unit {
	function __construct($id, $file, $size) {
		parent::__construct($id, $file, $size);

		$this->attrList['troopType'] = 10;
		$this->attrList['currentTask'] = 11;
		$this->attrList['currentLoc'] = 12;
		$this->attrList['timeStarted'] = 13;
		$this->attrList['expSlot'] = 14;
		$this->attrList['armyID'] = 15;
		$this->attrList['energy'] = 16;
		$this->attrList['enRegen'] = 17;
		$this->attrList['item1'] = 18;
		$this->attrList['item2'] = 19;
		$this->attrList['item3'] = 20;
		$this->attrList['item4'] = 21;
		$this->attrList['item5'] = 22;
		$this->attrList['item6'] = 23;
		$this->attrList['item7'] = 24;
		$this->attrList['item8'] = 25;
		$this->attrList['currentSlot'] = 26;
		$this->attrList['updateTime'] = 27;
		$this->attrList['visionDist'] = 28;
		$this->attrList['carryCap'] = 29;
		$this->attrList['carrySlot'] = 30;
		$this->attrList['battleID'] = 31;

		//echo 'MAKE WARBAND ('.sizeof($this->attrList).')';
		//print_r($this->attrList);
		}
}

class army extends unit {
	function __construct($id, $file, $size) {
		parent::__construct($id, $file, $size);
		
		$this->attrList['carryCap'] = 29;
		$this->attrList['carrySlot'] = 30;
}

class task {
	protected $linkFile, $unitBin, $id, $attrList;

	function __construct($id, $file) {

		$this->linkFile = $file;
		fseek($this->linkFile, $id*200);
		$unitBin = fread($this->linkFile, 200);
		$this->taskDat = unpack('i*', $unitBin);
		$this->taskID = $id;
	}

	function saveAll($file) {
		// Pack the char data
		$packStr = '';
		foreach ($this->taskDat as $value) {
			$packStr.=pack('i', $value);
		}
		fseek($file, $this->taskID*200);
		fwrite($file, $packStr);
	}
}

?>
