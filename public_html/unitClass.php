<?php

class unit {
	protected $linkFile, $unitBin, $id, $attrList;
	public $unitDat, $mercApproved;

	function __construct($id, $dat, $file) {
		$this->linkFile = $file;
		$this->mercApproved = false;
		/*
		global $defaultBlockSize;


		fseek($this->linkFile, $id*$defaultBlockSize);
		$unitBin = fread($this->linkFile, $size);
		*/
		if (sizeof($dat) == 0) {
			echo 'Start a blank unit';
			$this->unitDat = array_fill(1, 100, 0);
		} else {
			$this->unitDat = $dat;
		}
		//echo 'Set as type '.gettype($this->unitDat);
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
			echo 'Not found in type '.$this->unitDat[4].' ('.$desc.')';
			return false;
		}
	}

	function set($desc, $val) {
		if (array_key_exists($desc, $this->attrList)) {
			echo 'Found '.$desc.' use spot '.$this->attrList[$desc].'.  Type: '.gettype ($this->unitDat);
			$this->unitDat[$this->attrList[$desc]] = $val;
		}
	}

	function actionPoints() {
		return min(1000, $this->get('energy') + floor((time()-$this->get('updateTime'))*4167/360000))+500;
	}

	function changeID($newID) {
		$this->unitID = $newID;
	}

	function save($desc, $val) {
		global $defaultBlockSize;

		if (array_key_exists($desc, $this->attrList)) {
			fseek($this->linkFile, $this->unitID*$defaultBlockSize + $this->attrList[$desc]*4-4);
			fwrite($this->linkFile, pack('i', $val));
			echo 'ID: '.$this->unitID;
			echo 'Save '.$val.' at spot '.($this->unitID*$defaultBlockSize + $this->attrList[$desc]*4-4);
			$this->unitDat[$this->attrList[$desc]] = $val;
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

	function adjustEnergy($delta) {
		$this->save('energy', max(0, $this->get('energy')+$delta));
		$this->save('updateTime', time());
	}
}

class army extends unit {
	function __construct($id, $dat, $file) {
		parent::__construct($id, $dat, $file);
		//echo 'Load an army';
		$this->attrList['commander'] = 10;
		$this->attrList['unitListSlot'] = 14;
		$this->attrList['carryCap'] = 29;
		$this->attrList['carrySlot'] = 30;
		$this->attrList['energy'] = 99; // Undefined for this type
	}
	
	function objectTarget() {
		return $this->get('controller');
	}
}

class battle extends unit {
	function __construct($id, $dat, $file) {
		parent::__construct($id, $dat, $file);

		$this->attrList['battleType'] = 10;
		$this->attrList['baseUnit_1'] = 11;
		$this->attrList['baseUnit_2'] = 12;
		$this->attrList['timeStarted'] = 13;
		$this->attrList['resolveTime'] = 14;
		$this->attrList['sideList_1'] = 15;
		$this->attrList['sideList_2'] = 16;
	}
}

class building extends unit {
	function __construct($id, $dat, $file) {
		parent::__construct($id, $dat, $file);
		$this->attrList['parentCity'] = 15;
		$this->attrList['energy'] = 16;
		$this->attrList['renownGen'] = 23;

		$this->attrList['trait1'] = 29;
		$this->attrList['trait2'] = 30;
		$this->attrList['trait3'] = 31;
		$this->attrList['trait4'] = 32;
		$this->attrList['trait5'] = 33;
	}
	
	function objectTarget() {
		return $this->get('controller');
	}
}

class resourcePoint extends unit {
	function __construct($id, $dat, $file) {
		parent::__construct($id, $dat, $file);

		$this->attrList['rscType'] = 10;
		$this->attrList['parentCity'] = 15;
		$this->attrList['energy'] = 16;
		$this->attrList['baseProd'] = 18;
		$this->attrList['warPrize'] = 34;

		$this->attrList['conditionPoints'] = 22;
		$this->attrList['maxCondition'] = 23;
		$this->attrList['recoveryRate'] = 24;

		$this->attrList['trait1'] = 29;
		$this->attrList['trait2'] = 30;
		$this->attrList['trait3'] = 31;
		$this->attrList['trait4'] = 32;
		$this->attrList['trait5'] = 33;
	}
	
	function objectTarget() {
		return $this->get('controller');
	}
}

class char extends unit {
		public $buffs;

		function __construct($id, $dat, $file) {
			parent::__construct($id, $dat, $file);

			$this->attrList['subType'] = 10;
			$this->attrList['currentTask'] = 11;
			$this->attrList['currentLoc'] = 12;
			$this->attrList['positionSlot'] = 13;
			$this->attrList['titleSlot'] = 14;
			$this->attrList['traitSlot'] = 15;
			$this->attrList['energy'] = 16;
			$this->attrList['enRegen'] = 17;
			$this->attrList['publicReligion'] = 18;
			$this->attrList['experience'] = 18;
			$this->attrList['skillSlot'] = 20;
			$this->attrList['renown'] = 22;
			$this->attrList['renownRate'] = 23;
			$this->attrList['warPrize'] = 24;
			$this->attrList['trait1'] = 47;
			$this->attrList['trait2'] = 48;
			$this->attrList['trait3'] = 49;
			$this->attrList['trait4'] = 50;
			$this->attrList['trait5'] = 51;
			$this->attrList['trait6'] = 52;
			$this->attrList['trait7'] = 53;
			$this->attrList['trait8'] = 54;
			$this->attrList['trait9'] = 55;
			$this->attrList['trait10'] = 56;

			$buffs = array();
		}

	function renownPoints() {
		return $this->get('renown')+$this->get('renownRate')*(time() - $this->updateTime)/3600;
	}

	function calcBuffs($traitList, &$traitDescs) {
		$buffs = array();
		for ($i=1; $i<=sizeof($rawList); $i+=3) {
			if (isset($buffs['t'.$rawList[$i].'-'.$rawList[$i+1]])) {
				$buffs['t'.$rawList[$i].'-'.$rawList[$i+1]] += $rawList[$i+2];
			} else {
				$buffs['t'.$rawList[$i].'-'.$rawList[$i+1]] = $rawList[$i+2];
			}
		}
	}
	
	function objectTarget() {
		return $this->get('controller');
	}

	function getBuff($id) {
		if (isset($boosts[$id])) {
			return $boosts[$id];
		} else return 0;
	}
}

class settlement extends unit {
	function __construct($id, $dat, $file) {
		parent::__construct($id, $dat, $file);

		$this->attrList['carrySlot'] = 11;
		$this->attrList['townLeaders'] = 13;
		$this->attrList['buildingSlot'] = 17;
		$this->attrList['parentCity'] = 29;
		$this->attrList['carryCap'] = 33;
		$this->attrList['renownGen'] = 35;
		$this->attrList['warPrize'] = 36;
		$this->attrList['energy'] = 99; // undefined for this type

	}

	function adjustRsc($rscID, $qty, $slotFile) {
		$location = 0;
		$townRsc = new blockSlot($this->get('carrySlot'), $slotFile, 40);
		$data = pack('i*', $rscID, $qty);
		for ($i=1; $i<=sizeof($townRsc->slotData); $i+=2) {
			if ($townRsc->slotData[$i] == $rscID) {
				$location = $i;
				$data = pack('i*', $rscID, $townRsc->slotData[$i+1]+$qty);
				break;
			}
			else if ($townRsc->slotData[$i] == 0) $location = $i;
		}

		if ($location > 0) {
			$townRsc->addItem($slotFile, $data, $location);
		} else {
			$townRsc->addItem($slotFile, $data);
		}
	}
	
	function objectTarget() {
		return $this->get('controller');
	}
}

class player extends unit {
	function __construct($id, $dat, $file) {
		parent::__construct($id, $dat, $file);

		$this->attrList['homeCity'] = 11;
		$this->attrList['lordID'] = 15;
		$this->attrList['unitSlot'] = 22;
		$this->attrList['dipSlot'] = 23;
		$this->attrList['warList'] = 32;
		$this->attrList['intelSlot'] = 24;
	}
}

class warband extends unit {
	function __construct($id, $dat, $file) {
		parent::__construct($id, $dat, $file);
		//echo 'Load an warband';
		$this->mercApproved = true;

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
		$this->attrList['warPrize'] = 43;
		}

	function actionPoints() {
		return min(1000, $this->unitDat[16] + floor((time()-$this->unitDat[27])*4167/360000))+500;
	}
	
	function objectTarget() {
		return $this->get('controller');
	}
}

class tribeObject extends unit {
	function __construct($id, $dat, $file) {
		parent::__construct($id, $dat, $file);

		$this->attrList['energy'] = 16;
	}

	function objectTarget() {
		return $this->get('controller');
	}
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

function loadPlayer($id, $file, $size) {
	global $defaultBlockSize;
	fseek($file, $id*$defaultBlockSize);
	$dat = unpack('i*', fread($file, $size));

	return new player($id, $dat, $file);
}

function loadUnit($id, $file, $size) {
	global $defaultBlockSize;
	fseek($file, $id*$defaultBlockSize);
	$dat = unpack('i*', fread($file, $size));
	switch($dat[4]) {
		case 1:
			return new settlement($id, $dat, $file);
			break;

		case 2:
			return new resourcePoint($id, $dat, $file);
			break;

		case 3:
			return new army($id, $dat, $file);
			break;

		case 4:
			return new char($id, $dat, $file);
			break;

		case 6:
			return new warband($id, $dat, $file);
			break;

		case 8:
			return new warband($id, $dat, $file);
			break;

		case 9:
			//echo 'Load a building object';
			return new building($id, $dat, $file);
			break;

		case 10:
			return new tribeObject($id, $dat, $file);
			break;

		default:
		 	return new unit($id, $dat, $file);
	}
}

function newUnit($type, $file) {
	global $defaultBlockSize;

	if (flock($file, LOCK_EX)) {
		fseek($file, 0, SEEK_END);
		$endPos = ftell($file);
		$newID = ceil($endPos/$defaultBlockSize);

		fseek($file, $newID*$defaultBlockSize+396);
		fwrite($file, pack('i', 0));

		$tmpDat = array_fill(1, 100, 0);
		$tmpDat[4] = $type;

		flock($file, LOCK_UN);

		return loadUnit($newID, $file, 400);
	}
}
?>
