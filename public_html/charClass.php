<?php

class character {
	public $charData, $rscSlot, $slotFile;
	private $charID, $charDatStr;

	function __construct($id, $file) {
		//$this->init($start, $slotFile, $size);
		$this->init($id, $file);
		$this->charID = $id;
		//$this->slotFile = $file;
	}

	function slotFile($slotFile) {
		$this->slotFile = $slotFile;
	}

	function init($id, $file) {
		fseek($file, $id*100);

		$this->charDatStr = fread($file, 400);
		$this->charData = unpack('i*', $this->charDatStr);
		//echo 'Char Data:';
		//print_r($this->charData);
	}

	function changeID($newID) {
		$this->charID = $newID;
		echo 'New ID is '.$this->charID.'<br>';
	}

	function save($file) {
		// Pack the char data
		$packStr = '';
		foreach ($this->charData as $value) {
			$packStr.=pack('i', $value);
		}
		fseek($file, $this->charID*100);
		fwrite($file, $packStr);
	}
}



?>
