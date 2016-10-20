<?php

class object {
	protected $linkFile, $unitBin, $id, $attrList;
	
	function __construct($id, $dat, $file) {
		$this->linkFile = $file;
		
		if (sizeof($dat) == 0) {
			echo 'Start a blank unit';
			$this->objDat = array_fill(1, 100, 0);
		} else {
			$this->objDat = $dat;
		}
		//echo 'Set as type '.gettype($this->objDat);
		$this->unitID = $id;

		$this->attrList = [];
		$this->attrList['xLoc'] = 1;
		$this->attrList['yLoc'] = 2;
		$this->attrList['icon'] = 3;
		$this->attrList['oType'] = 4;
		$this->attrList['owner'] = 5;
		$this->attrList['lastUpdate'] = 10;
		/*
		
		
		$this->attrList['controller'] = 6;
		$this->attrList['status'] = 7;
		$this->attrList['culture'] = 8;
		$this->attrList['religion'] = 9;
		$this->attrList['troopType'] = 10;
		$this->attrList['currentTask'] = 11;
		$this->attrList['updateTime'] = 27;
		*/
	}
	
	function get($desc) {
		if (array_key_exists($desc, $this->attrList)) {
			return $this->objDat[$this->attrList[$desc]];
		} else {
			echo 'Not found in type '.$this->objDat[4].' ('.$desc.')';
			return false;
		}
	}

	function set($desc, $val) {
		if (array_key_exists($desc, $this->attrList)) {
			echo 'Found '.$desc.' use spot '.$this->attrList[$desc].'.  Type: '.gettype ($this->objDat);
			$this->objDat[$this->attrList[$desc]] = $val;
		}
	}
	
	function save($desc, $val) {
		global $defaultBlockSize;

		if (array_key_exists($desc, $this->attrList)) {
			fseek($this->linkFile, $this->unitID*$defaultBlockSize + $this->attrList[$desc]*4-4);
			fwrite($this->linkFile, pack('i', $val));
			echo 'ID: '.$this->unitID;
			echo 'Save '.$val.' at spot '.($this->unitID*$defaultBlockSize + $this->attrList[$desc]*4-4);
			$this->objDat[$this->attrList[$desc]] = $val;
		} else {
			return false;
		}
	}


	function saveAll($file) {
		// Pack the char data
		$packStr = '';
		foreach ($this->objDat as $value) {
			$packStr.=pack('i', $value);
		}
		fseek($file, $this->unitID*100);
		$saveLen = fwrite($file, $packStr);
	}
}

class bunsiness extends object {
	function __construct($id, $dat, $file) {
		parent::__construct($id, $dat, $file);
		
		$this->attrList['ownedObjects'] = 11;
	}
}

class factory extends object {
	private $inputIndex, $inputInventoryIndex, $productIndex, $productInventoryIndex
	
	$inputIndex = ;
	$inputInventoryIndex = 61;
	$productIndex = ;
	
	function __construct($id, $dat, $file) {
		parent::__construct($id, $dat, $file);
		
		$this->attrList['currentProd'] = 19; // which inventory item is being produced
		$this->attrList['currentRate'] = 20;
		$this->attrList['prodOpt1'] = 31;
		$this->attrList['prodOpt2'] = 32;
		$this->attrList['prodOpt3'] = 33;
		$this->attrList['prodOpt4'] = 34;
		$this->attrList['prodOpt5'] = 35;
		$this->attrList['inputInv1'] = 36;
		$this->attrList['inputInv2'] = 37;
		$this->attrList['inputInv3'] = 38;
		$this->attrList['inputInv4'] = 39;
		$this->attrList['inputInv5'] = 40;
		$this->attrList['inputInv6'] = 41;
		$this->attrList['inputInv7'] = 42;
		$this->attrList['inputInv8'] = 43;
		$this->attrList['inputInv9'] = 44;
		$this->attrList['inputInv10'] = 45;
		$this->attrList['inputInv11'] = 46;
		$this->attrList['inputInv12'] = 47;
		$this->attrList['inputInv13'] = 48;
		$this->attrList['inputInv14'] = 49;
		$this->attrList['inputInv15'] = 50;
		$this->attrList['inputInv16'] = 51;
		$this->attrList['inputInv17'] = 52;
		$this->attrList['inputInv18'] = 53;
		$this->attrList['inputInv19'] = 54;
		$this->attrList['inputInv20'] = 55;
		$this->attrList['invItem1'] = 61;
		$this->attrList['invItem2'] = 62;
		$this->attrList['invItem3'] = 63;
		$this->attrList['invItem4'] = 64;
		$this->attrList['invItem5'] = 65;
		$this->attrList['invItem6'] = 66;
		$this->attrList['invItem7'] = 67;
		$this->attrList['invItem8'] = 68;
		$this->attrList['invItem9'] = 69;
		$this->attrList['invItem10'] = 70;
		$this->attrList['invItem11'] = 71;
		$this->attrList['invItem12'] = 72;
		$this->attrList['invItem13'] = 73;
		$this->attrList['invItem14'] = 74;
		$this->attrList['invItem15'] = 75;
		$this->attrList['invItem16'] = 76;
		$this->attrList['invItem17'] = 77;
		$this->attrList['invItem18'] = 78;
		$this->attrList['invItem19'] = 79;
		$this->attrList['invItem20'] = 80;
	}
	
	updateStocks() {
		$elapsed = time() - $this->get('lastUpdate');
		$this->objDat[]
	}
}

class labor extends object {
	function __construct($id, $dat, $file) {
		parent::__construct($id, $dat, $file);
	}
}

funciton loadObject($id, $file, $size) {
	global $defaultBlockSize;
	
	fseek($file, $id*$defaultBlockSize);
	fseek($file, $id*$defaultBlockSize);
	$dat = unpack('i*', fread($file, $size));
	switch($dat[4]) {
		case 1:
			new business($id, $dat, $file);
		break;
		
		case 2:
			new labor($id, $dat, $file);
		break;
		
		case 3:
			new factory($id, $dat, $file);
		break;
	}
	
}

?>