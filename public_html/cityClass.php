<?php

class city {
	public $cityData, $rscSlot, $slotFile;
	private $cityID;

	function __construct($id, $file) {
		$this->init($start, $slotFile, $size);
		$this->cityID = $id;
		//$this->slotFile = $file;
	}

	function slotFile($slotFile) {
		$this->slotFile = $slotFile;
	}

	function init($id, $file) {
		fseek($file, $id*100);
		$cityDatStr = fread($file, 400);
		$cityData = unpack('i*', $this->$cityDatStr);
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

function newTown($id, $townFile, $slotFile) {
	global $defaultBlockSize, $gameSlot, $pGameID, $startLocation, $gamePath, $postVals;

	$townData = array_fill(1, 100, 0);
	$townData[1] = $startLocation[0];
	$townData[2] = $startLocation[1];
	$townData[3] = 1;
	$townData[4] = 1;
	$townData[5] = $pGameID;
	$townData[6] = $pGameID;
	$townData[7] = 1;
	$townData[8] = $postVals[1];

	//fseek($unitFile, $townID*$defaultBlockSize);
	//fwrite($unitFile, pack("i*", $startLocation[0],$startLocation[1],1,1,$pGameID, $pGameID,1,$postVals[1],0));
	//fseek($unitFile, $townID*$defaultBlockSize+$unitBlockSize-4);
	//fwrite($unitFile, pack("i", 9990));

	// Create a credential list for the town and record this player as having full cred.
	$credListSlot = startASlot($slotFile, $gamePath."/gameSlots.slt");
	//fseek($unitFile, $townID*$defaultBlockSize+72);
	//fwrite($unitFile, pack('i', $credListSlot));
	$townData[19] = $credListSlot;

	echo 'credintial slot:'.$credListSlot.'<br>';
	writeBlocktoSlot($gamePath."/gameSlots.slt", $credListSlot, pack('i*', -9, $pGameID), $slotFile, 40); // ($slotHandle, $checkSlot, $addData, $slotFile, $slotSize)

	// Make a chars slot for the new town and record the player's faction leader as the town's leader
	$townCharSlot = startASlot($slotFile, $gamePath."/gameSlots.slt");
	//fseek($unitFile, $townID*$defaultBlockSize+48);
	//fwrite($unitFile, pack('i', $townCharSlot));
	echo 'Town Char Slot is '.$townCharSlot.'<br>';
	$townData[13] = $townCharSlot;

	// Make a units slot for the new town
	$townUnitSlot = startASlot($slotFile, $gamePath."/gameSlots.slt");
	//fseek($unitFile, $townID*$defaultBlockSize+68);
	//fwrite($unitFile, pack('i', $townUnitSlot));
	echo 'Town Unit Slot is '.$townUnitSlot.'<br>';
	$townData[18] = $townUnitSlot;

	// Make a resource slot for the new town
	$rscSlot = startASlot($slotFile, $gamePath."/gameSlots.slt");
	//fseek($unitFile, $townID*$defaultBlockSize+40);
	//fwrite($unitFile, pack('i', $rscSlot));
	echo 'Town RSC Slot is '.$rscSlot.'<br>';
	$towndata[11] = $rscSlot;

	// Make a task slot for the new town
	$taskSlot = startASlot($slotFile, $gamePath."/gameSlots.slt");
	//fseek($unitFile, $townID*$defaultBlockSize+80);
	//fwrite($unitFile, pack('i', $taskSlot));
	echo 'Town task Slot is '.$taskSlot.'<br>';
	$townData[21] = $taskSlot;

	fseek($townFile, $id*$defaultBlockSize);
	for ($i=1; $i<=sizeof($townData); $i++) {
		fwrite($townFile, pack('i', $townData[$i]));
	}

	return $townData;
}

?>
