<?php

class townBuilding {

	public $XLoc, $YLoc, $icon, $uType, $owner, $controller, $status, $culture, $religion, $bType, $currentSlot, $updateTime, $visionDistance, $currentTask;

	function __construct($id, $file) {
		$this->id = intval($id);

		fseek($file, $this->id *100);
		$bldgDat = unpack('i*', fread($file, 400));

		$this->XLoc = $bldgDat[1];
		$this->YLoc = $bldgDat[2];
		$this->icon = $bldgDat[3];
		$this->uType = $bldgDat[4];
		$this->owner = $bldgDat[5];
		$this->controller = $bldgDat[6];
		$this->status = $bldgDat[7];
		$this->culture = $bldgDat[8];
		$this->religion = $bldgDat[9];
		$this->bType = $bldgDat[10];
		$this->currentTask = $bldgDat[11];
		$this->currentSlot = $bldgDat[26];
		$this->updateTime = $bldgDat[27];
		$this->visionDistance = $bldgDat[28];

		var_dump(get_object_vars($this));
	}

	function saveBulding($file) {
		fseek($file, $this->id *100);
		fwrite(file, pack('i*', $this->XLoc, $this->YLoc, $this->icon, $this->uType, $this->owner, $this->controller, $this->status, $this->culture, $this->religion, $this->slotRSC, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, $this->currentSlot, $this-updateTime, $this->visionDistance));
	}
}

?>
