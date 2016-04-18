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
		for ($i=0; $i<sizeof($this->rscSlot->slotData)); $i+=2) {
			if ($this->rscSlot->slotData[$i] == $id) $qty = $this->rscSlot->slotData[$i+1];
			break;
		}
		
		return qty;
	}
}

?>