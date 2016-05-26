<?php

class city {
	public $cityData, $rscSlot, $slotFile;
	private $cityID, $cityDatStr;

	function __construct($id, $file) {
		//$this->init($start, $slotFile, $size);
		$this->init($id, $file);
		$this->cityID = $id;
		//$this->slotFile = $file;
	}

	function slotFile($slotFile) {
		$this->slotFile = $slotFile;
	}

	function init($id, $file) {
		fseek($file, $id*100);

		$this->cityDatStr = fread($file, 400);
		$this->cityData = unpack('i*', $this->cityDatStr);
		echo 'Created a new city<br>';
		print_r($this->cityData);
	}

	function addRsc($id, $amount) {
		if ($this->cityData[11] == 0) {
			// Need to generate a new slot
			$this->cityData[11] == newSlot($this->slotFile);
			fseek($file, $this->cityID*100+40);
			fwrite($file, pack('i', $this->cityData[11]));
		}

		if (!get_class($this->rscSlot)) $this->rscSlot = new blockSlot($this->cityData[11], $this->slotFile, 40);
		$loc = sizeof($rscSlot->slotData);
		$newAmt = $amount;

		for ($i=1; $i<sizeof($this->rscSlot->slotData); $i+=2) {
			if ($this->rscSlot->slotData[$i] == $id) {
				$newAmt = $this->rscSlot->slotData[$i+1] + $amount;
				$loc = $i+1;
				break;
			}
		}
		$this->addItem($this->slotFile, pack('i*', $id, $newAmt), $loc);
	}

	function loadRsc($slotFile) {
		if ($this->cityData[11] == 0) {
			// Need to generate a new slot
			$this->cityData[11] == newSlot($slotFile);
			fseek($file, $this->cityID*100+40);
			fwrite($file, pack('i', $this->cityData[11]));
		}
		$this->rscSlot = new blockSlot($this->cityData[11], $slotFile, 40);
	}

	function rscAmt($id) {
		if (!get_class($this->rscSlot)) $this->rscSlot = new blockSlot($this->cityData[11], $this->slotFile, 40);

		$qty = 0;
		for ($i=0; $i<sizeof($this->rscSlot->slotData); $i+=2) {
			if ($this->rscSlot->slotData[$i] == $id) $qty = $this->rscSlot->slotData[$i+1];
			break;
		}

		return qty;
	}
}

class building {
	public $bldgData, $rscSlot, $slotFile;
	private $bldgID, $bldgDatStr;
	
	function __construct($id, $file) {
		//$this->init($start, $slotFile, $size);
		$this->init($id, $file);
		$this->bldgID = $id;
		//$this->slotFile = $file;
	}
	
	function init($id, $file) {
		fseek($file, $id*100);

		$this->bldgDatStr = fread($file, 400);
		$this->bldgData = unpack('i*', $this->bldgDatStr);
		echo 'Loaded a new building<br>';
		print_r($this->bldgData);
	}
	
	function saveAll($file) {
		// Pack the bldg data
		$packStr = '';
		foreach ($this->bldgData as $value) {
			$packStr.=pack('i', $value);
		}
		fseek($file, $this->bldgID*100);
		fwrite($file, $packStr);
	}
}

function newTown($id, $townFile, $slotFile, $townDtls) {
	global $defaultBlockSize, $gameSlot, $pGameID, $startLocation, $gamePath;

	$townData = array_fill(1, 100, 0);
	$townData[1] = $townDtls[0]; // X loc
	$townData[2] = $townDtls[1]; // Y Loc
	$townData[3] = 1;
	$townData[4] = 1;
	$townData[5] = $townDtls[2]; // pgameId
	$townData[6] = $townDtls[2]; // pgameID
	$townData[7] = 1;
	$townData[8] = $townDtls[3]; // Culture



	// Create a credential list for the town and record this player as having full cred.
	$credListSlot = startASlot($slotFile, $gamePath."/gameSlots.slt");
	$townData[19] = $credListSlot;

	echo 'credintial slot:'.$credListSlot.'<br>';
	writeBlocktoSlot($gamePath."/gameSlots.slt", $credListSlot, pack('i*', -9, $pGameID), $slotFile, 40); // ($slotHandle, $checkSlot, $addData, $slotFile, $slotSize)

	// Make a chars slot for the new town and record the player's faction leader as the town's leader
	$townCharSlot = startASlot($slotFile, $gamePath."/gameSlots.slt");
	echo 'Town Char Slot is '.$townCharSlot.'<br>';
	$townData[13] = $townCharSlot;

	// Make a units slot for the new town
	$townUnitSlot = startASlot($slotFile, $gamePath."/gameSlots.slt");
	echo 'Town Unit Slot is '.$townUnitSlot.'<br>';
	$townData[18] = $townUnitSlot;

	// Make a resource slot for the new town
	$rscSlot = startASlot($slotFile, $gamePath."/gameSlots.slt");
	echo 'Town RSC Slot is '.$rscSlot.'<br>';
	$towndata[11] = $rscSlot;

	// Make a task slot for the new town
	$taskSlot = startASlot($slotFile, $gamePath."/gameSlots.slt");
	echo 'Town task Slot is '.$taskSlot.'<br>';
	$townData[21] = $taskSlot;

	fseek($townFile, $id*$defaultBlockSize);
	for ($i=1; $i<=sizeof($townData); $i++) {
		fwrite($townFile, pack('i', $townData[$i]));
	}

	return $townData;
}

function checkCred($trg, $credSlotDat) {
	$approved = false;
	for ($i=1; $i<=sizeof($credSlotDat); $i++) {
		if ($credSlotDat[$i+1] == $trg) {
			$approved = $credSlotDat[$i];
			break;
		}
	}
	return $approved;
}

?>
