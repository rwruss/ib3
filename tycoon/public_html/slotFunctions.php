<?php

class dataSlot {
	public $slotList = [];
	public $dataString;
	public $slotData = [];
	private $itemsPerSlot;
	private $slotSize, $useFile;
	public $start, $size;

	function __construct($start, $slotFile, $size) {
		$this->init($start, $slotFile, $size);
		$this->useFile = $slotFile;
	}

	function init($start, $slotFile, $size) {
		$this->start = $start;
		$this->size = $size;
		//echo 'Read slot '.$start.' with a size of '.$size.'<br>';
		$slotSize = $size;
		$nextSlot = $start;
		$this->itemsPerSlot = ($size-4)/4;
		while ($nextSlot > 0) {
			$seekto = $nextSlot*$size;

			$this->slotList[] = $nextSlot;
			fseek($slotFile, $nextSlot*$size);
			$tmpDat = fread($slotFile, $size);
			$this->dataString .= substr($tmpDat, 4);
			$tmpA = unpack("N", $tmpDat);
			$nextSlot = $tmpA[1];
			//echo 'seek to '.$seekto.' for next slot '.$nextSlot.'<br>';
		}
	}

	function save($file) {
		$numSlots = sizeof($slotList);
		$slotList[] = 0;  /// Add null reference so that last slot is not linked
		for ($i=0; $i<$numSlots; $i++) {
			fseek($file, $slotList[$i]*$slotSize);
			fwrite($file, pack("N", $slotList[$i+1]).substr($this->dataString, $i*($slotSize*4), $slotSize-4));
		}
	}

	function saveItem($file, $data, $location) {
		// Determine if the new item will fit in the slot spaces already allocated
		$available = $this->size*sizeof($this->slotList);
		if ($location*4+strlen($data) > $available) {
			// Get a new block for this slot
			if (flock($file, LOCK_EX)) {  // acquire an exclusive lock
				// verify that a new block hasn't already been added by another user
				do {
					fseek($file, end($this->slotList)*$this->size);
					$nextSlot = unpack('i', fread($file, 4));
					if ($nextSlot[1] > 0) $this->slotList[] = $nextSlot[1];
				} while ($nextSlot[1] > 0);

				fseek($file, $this->size-4, SEEK_END);

				$newBlock = (ftell($file)+4)/$this->size;

				fwrite($file, pack('i', 0));
				fflush($file);

		    flock($file, LOCK_UN);    // release the lock

				// Add new block to list of blocks for this slot
				$this->slotList[] = $newBlock;
			}
		}
	}

	function saveSlot() {
		if (flock($this->useFile, LOCK_EX)) {
			// Fill then end of the array as needed to get a round number of slots
			$numSlotItems = $this->size/4-1;
			while (sizeof($this->slotData)%$numSlotItems > 0) $this->slotData[] = 0;

			// Determind number of slots needed and number currently reserved
			$slotsNeeded = (sizeof($this->slotData)*4+4)/$this->size;

			fseek($this->useFile, 0, SEEK_END);
			$fileSlots = ftell($this->useFile)/$this->size;

			$addCount = 0;
			while ($slotsNeeded > sizeof($this->slotList)) {
				$slotList[] = $fileSlots + $count;
				$count++;
			}

			// Record the slots in the file
			for ($i=0; $i<$slotsNeeded; $i++) {
				$dat = pack('i', $this->slotList[$i]);
				for ($j=1; $j<=$numSlotItems; $j++) {
					$dat .= pack('i', $this->slotData[$i*$numSlotItems+$j]);
				}
				fseek($this->useFile, $this->size*$this->slotList[$i]);
				fwrite($this->useFile, $dat);
			}
		echo 'Saved';
		print_r($this->slotData);
		fflush($this->useFile);
		flock($this->useFile, LOCK_UN);
		}
	}
}

class itemSlot extends dataSlot {
	function __construct($start, $slotFile, $size) {
		parent::__construct($start, $slotFile, $size);
		$this->slotData = unpack('i*', $this->dataString);
	}

	function addItem($value, $file) {
		// Determine if there is a spot to add the item
		$emptySpot = array_search(0, $this->slotData);
		if (!$emptySpot) {
			echo 'None found - go to end<br>';
			$emptySpot = sizeof($this->slotData)+1;
		}

		// Determine if there is enough space in the slot
		$available = sizeof($this->slotList)*($this->size-4);
		$numSlotsStart = sizeof($this->slotList);
		echo 'Recording '.$value.' at Slot '.$this->slotList[0].' Need '.($emptySpot*4).', Have '.$available.'<p>';
		if ($emptySpot*4 > $available) {
			echo 'Add a slot<br>';
			// Need to get a new Slot
			if (flock($file, LOCK_EX)) {  // acquire an exclusive lock
				while($emptySpot*4 > $available) {
					fseek($file, $this->size-4, SEEK_END);
					fwrite($file, pack('i', 0));

					$newBlock = ftell($file)/$this->size;
					$this->slotList[] = $newBlock;
					$available+= $this->size-4;
				}
			flock($file, LOCK_UN);
			}

			// Record new slot links
			$this->slotList[] = 0; // Dead link at end of list
			for ($i=$numSlotsStart; $i<sizeof($this->slotList); $i++) {
				fseek($file, $this->slotList[$i-1]*$this->size);
				fwrite($file, pack('N', $this->slotList[$i]));
			}
		}

		// Determine which slot to write in
		$writeSlot = floor(($emptySpot*4-1)/($this->size-4));
		$writePos = 4*$emptySpot - $writeSlot*($this->size-4);

		echo 'Sub Slot #'.$writeSlot.' ('.$this->slotList[$writeSlot].').  Write Spot is '.$writePos.'<p>';
		$this->slotData[$emptySpot] = $value;

		fseek($file, $this->slotList[$writeSlot]*$this->size+$writePos);
		fwrite($file, pack('i', $value));
	}

	function deleteItem($position, $file) {
		$this->addItemAtSpot(0, $position, $file);
	}

	function addItemAtSpot($value, $position, $file) {
		// Position is the key in the array that should be deleted.  slotData array starts with key [1];

		// Determine which slot and position this is in
		echo 'WriteSlot = floor(('.$position.'-1)*4)/('.$this->size.'-4)<br>';
		$writeSlot = floor(($position-1)*4/($this->size-4));
		$writePos = 4*$position - $writeSlot*($this->size-4);

		fseek($file, $this->slotList[$writeSlot]*$this->size+$writePos);
		fwrite($file, pack('i', $value));

		$this->slotData[$position] = $value;
	}

	function deleteByValue($val, $file) {
		$matches = array_keys($this->slotData, $val);
		foreach ($matches as $keyNum) {
			$this->addItemAtSpot(0, $keyNum, $file);
		}
	}
}

class blockSlot extends dataSlot {
	public $version;
	function __construct($start, $slotFile, $size) {
		parent::__construct($start, $slotFile, $size);
		$this->slotData = unpack('i*', $this->dataString);
		//echo 'Read '.strlen($this->dataString).' bytes<br>';
		//print_r($this->slotData);
	}

	function addItem($file, $data, $location) {
		echo 'Slotlist:<br>';
		print_r($this->slotList);
		echo 'data block length '.strlen($data).'<br>';
		if (flock($file, LOCK_EX)) {  // acquire an exclusive lock
			// Read the last item that was saved in this slot and version
			fseek($file, $this->slotList[0]*$this->size+4);

			if (!$location) $location = sizeof($this->slotData);
			// Check if enough space is available for new items
			$available = sizeof($this->slotList)*($this->size-4);
			echo 'Available space: '.$available.', Need: '.($location*4+strlen($data)-4).'<br>';
			while ($available < $location*4+strlen($data)) {
				// Will need to get new slot
				$oldEnd = end($this->slotList);

				fseek($file, $this->size-4, SEEK_END);
				fwrite($file, pack('i', 0));
				$newLoc = ceil(ftell($file)/$this->size) - 1;
				$this->slotList[] = $newLoc;

				$testPack = pack('N', $newLoc);
				$testVal = unpack('N', $testPack);
				echo 'Add new slot: '.$newLoc.' ('.ftell($file).'/'.$this->size.') Record at old location '.$oldEnd.' = ('.$oldEnd*$this->size.') value is '.$testVal[1].'<p>';


				fseek($file, $oldEnd*$this->size);
				echo '<p> Write '.fwrite($file, pack('N', $newLoc)).'<p>';
				echo ftell($file).'<p>';

				$available = sizeof($this->slotList)*($this->size-4);
			}

			$startBlock = intval(($location-1)*4/($this->size-4));
			$endBlock = intval((($location+strlen($data)/4-1))*4/($this->size-4));

			if ($startBlock != $endBlock) {
				echo 'Loc is '.$location.' Split block --> '.$startBlock.' vs '.$endBlock.'<p>';
				// Need to split the string up
				$startOffset = ($location-$startBlock*($this->size-4)/4)*4;
				//$startOffset = ($location*4) - ($this->size-4)*$startBlock;
				$part1 = ($this->size) - $startOffset;
				$part2 = strlen($data)-$part1;

				echo 'Start offset = '.$startOffset.', Part 1  = '.$part1.', Part 2 = '.$part2.'<br>';

				$block1 = substr($data, 0, $part1);
				$block2 = substr($data, $part1);
				$seek1 = $this->slotList[$startBlock]*$this->size + $startOffset;
				$seek2 = $this->slotList[$endBlock]*$this->size+4;
				echo 'Write block 1 ('.strlen($block1).') at location '.$seek1.', and block 2 ('.strlen($block2).') at location '.$seek2.'<br>';

				fseek($file,$this->slotList[$startBlock]*$this->size + $startOffset);
				fwrite($file, $block1);

				fseek($file, $this->slotList[$endBlock]*$this->size+4);
				fwrite($file, $block2);
			} else {
				$startOffset = ($location-$startBlock*($this->size-4)/4)*4;
				$seekto = $this->slotList[$startBlock]*$this->size + $startOffset;
				echo 'Write Data ('.strlen($data).') at slot ('.$this->slotList[$startBlock].') at pos ('.$seekto.'):';
				print_r(unpack('i*', $data));

				fseek($file, $this->slotList[$startBlock]*$this->size + $startOffset);
				fwrite($file, $data);
			}

			fflush($file);
			flock($file, LOCK_UN);    // release the lock
		}
	}

	function deleteItem() {
	}

	function findLoc($trgVal, $blockSize) {
		$loc = sizeof($this->slotData);
		for ($i=1; $i<sizeof($this->slotData); $i+=$blockSize)  {
			if ($this->slotData[$i] == $trgVal) {
				$loc = $i;
				break;
			}
		}
		return $loc;
	}
}
/*
class resourceSlot extends blockSlot {
	function __construct($start, $slotFile, $size) {
		parent::__construct($start, $slotFile, $size);
	}
	
	function adjustRsc($rscID, $adjustVal);
}
*/
class mapEventSlot extends dataSlot {
	public $numEffects, $version;
	function __construct($start, $slotFile, $size) {
		parent::__construct($start, $slotFile, $size);
		$this->slotData = unpack('i*', $this->dataString);
		$this->numEffects = $this->slotData[1];
		$this->version = $this->slotData[2];

		$this->slotData = array_slice($this->slotData, 0, $this->numEffects*6+2);
		//$this->dataString = substr($this->dataString, 0, 4+$this->numEffects*24);
	}

	function addItem($file, $data, $location) {
		echo 'Slotlist:<br>';
		print_r($this->slotList);
		echo 'data block length '.strlen($data).'<br>';
		if (flock($file, LOCK_EX)) {  // acquire an exclusive lock
			// Read the last item that was saved in this slot and version
			fseek($file, $this->slotList[0]*$this->size+4);
			$header = unpack('i*', fread($file, 8));
			echo 'VERSION '.$header[2].'<p>';
			if ($this->version != $header[2]) {
				/*
				// Reload the information so that you are working with the most current slot list
				$this->slotList = array();
				$nextIndex[1] = $this->start;
				while ($nextIndex[1] > 0) {
					$this->slotList[] = $nextIndex[1];
					fseek($file, $nextIndex*$this->size);
					$nextIndex = unpack('N', fread($file, 4));
				}
				*/
			init($this->slotList[0], $file, $this->size);
			}

			if (!$location) $location = sizeof($this->slotData);
			// Check if enough space is available for new items
			$available = sizeof($this->slotList)*($this->size-4);
			echo 'Available space: '.$available.', Need: '.($location*4+24).'<br>';
			while ($available < $location*4+24) {
				// Will need to get new slot
				$oldEnd = end($this->slotList);

				fseek($file, $this->size-4, SEEK_END);
				fwrite($file, pack('i', 0));
				$newLoc = ftell($file)/$this->size - 1;
				$this->slotList[] = $newLoc;

				echo 'Add new slot: '.$newLoc.'<br>';

				fseek($file, $oldEnd*$this->size);
				fwrite($file, pack('N', $newLoc));

				$available = sizeof($this->slotList)*($this->size-4);
			}

			$startBlock = intval(($location*4)/($this->size-4));
			$endBlock = intval(($location*4+strlen($data)-1)/($this->size-4));

			if ($startBlock != $endBlock) {
				echo 'Loc '.$location.' Split block -- '.$startBlock.' vs '.$endBlock.'<p>';
				// Need to split the string up
				$startOffset = ($location*4)%($this->size-4);
				$part1 = ($this->size - 4) - $startOffset;
				$part2 = strlen($data)-$part1;

				echo ' is Start offset = '.$startOffset.', Part 1  = '.$part1.', Part 2 = '.$part2.'<br>';

				$block1 = substr($data, 0, $part1);
				$block2 = substr($data, $part1);
				$seek1 = $this->slotList[$startBlock]*$this->size + $startOffset+4;
				$seek2 = $this->slotList[$endBlock]*$this->size+4;
				echo 'Write block 1 ('.strlen($block1).') at location '.$seek1.', and block 2 ('.strlen($block2).') at location '.$seek2.'<br>';

				fseek($file,$this->slotList[$startBlock]*$this->size + $startOffset+4);
				fwrite($file, $block1);

				fseek($file, $this->slotList[$endBlock]*$this->size+4);
				fwrite($file, $block2);
			} else {
				$startOffset = ($location*4)%($this->size-4);
				$seekto = $this->slotList[$startBlock]*$this->size + $startOffset+4;
				echo 'Write Data ('.strlen($data).') at slot ('.$this->slotList[$startBlock].') at pos ('.$seekto.'):';
				print_r(unpack('i*', $data));

				fseek($file, $this->slotList[$startBlock]*$this->size + $startOffset+4);
				fwrite($file, $data);
			}

			// increment the update and count
			$seekto = $this->slotList[0]*$this->size+4;
			echo '<br>Go to '.$seekto.' and write header';
			fseek($file, $this->slotList[0]*$this->size+4);
			fwrite($file, pack('i*', $header[1]+1, $header[2]+1));

			fflush($file);
			flock($file, LOCK_UN);    // release the lock
		}
	}

	function deleteItem() {

	}
}

function newSlot($slotFile) {
	echo "make a new slot<br>";
	// Check for abandoned slots first
	fseek($slotFile, 0);
	$slot_list_dat = fread($slotFile, 40);
	echo 'Size of read dat:'.strlen($slot_list_dat);
	$check_slot = unpack("N", substr($slot_list_dat, 0, 4));
	if ($check_slot[1] == 0) // Need to create a new slot
		{
		if (flock($slotFile, LOCK_EX)) {
			echo "create new slot<br>";
			clearstatcache();
			fseek($slotFile, 0, SEEK_END);
			$use_slot = max(1, ceil((ftell($slotFile))/40));
			fseek($slotFile, $use_slot*40 +39);
			fwrite($slotFile, pack("C", 0));
			flock($slotFile, LOCK_UN); // release the lock
		} else {echo 'flocks issue';}
	}
	else // need to remove this slot from the list
		{
		echo 'something else ('.$check_slot[1].')';
		}
	echo 'Create slot #'.$use_slot;
	return $use_slot;
}

function startASlot($slot_file, $slot_handle)
	{
	echo "no slot established<br>";
	// Check for abandoned slots first
	fseek($slot_file, 0);
	$slot_list_dat = fread($slot_file, 40);
	echo 'Size of read dat:'.strlen($slot_list_dat);
	$check_slot = unpack("N", substr($slot_list_dat, 0, 4));
	if ($check_slot[1] == 0) // Need to create a new slot
		{
		if (flock($slot_file, LOCK_EX)) {
			echo "create new slot<br>";
			clearstatcache();
			$use_slot = max(1, (filesize($slot_handle))/40);
			fseek($slot_file, $use_slot*40 +39);
			fwrite($slot_file, pack("C", 0));
			flock($slot_file, LOCK_UN); // release the lock
		} else {echo 'flocks issue';}
	}
	else // need to remove this slot from the list
		{
		echo 'something else ('.$check_slot[1].')';
		}
	return $use_slot;
	}

function writeBlocktoSlot($slotHandle, $checkSlot, $addData, $slotFile, $slotSize) {
	// Build slot tree
	$slotList[] = $checkSlot;
	echo 'Write a blocks starting at '.$checkSlot.'<br>';
	do {
		fseek($slotFile, $slotSize*$checkSlot);
		$nextSlot = unpack('N', fread($slotFile, 4));
		$slotList[] = $nextSlot[1];
		$checkSlot = $nextSlot[1];
	}
	while ($checkSlot > 0) ;
	$numSlotsNeeded = ceil(strlen($addData)/($slotSize-4));
	if ($numSlotsNeeded > sizeof($slotList)) {
		// Need to get some more slotSize
		$neededSlots = $numSlotsNeeded - sizeof($slotList);
		echo 'Getting more slots ('.$neededSlots.')<br>';
		if (flock($slotFile, LOCK_EX)) {
			clearstatcache();
			$eof = filesize($slotHandle);
			fseek($slotFile, $eof+$neededSlots*$slotSize+$slotSize-4);
			fwrite($slotFile, pack('N', 0));
		}
		for ($i=0; $i<$neededSlots; $i++) {
			$slotList[] = $eof/$slotSize+$i;
			echo 'Add slot '.($eof/$slotSize+$i).'<br>';
		}
	}
	else if ($numSlotsNeeded > sizeof($slotList)) {
		// Release the amount of slots not needed
		for ($i=$numSlotsNeeded; $i<sizeof($slotList); $i++) {
			slotRelease($slotList[$i-1]);
		}
	}
	$chunkSize = $slotSize-4;
	$slotList[]= 0;
	// make and record an empty chunk at the end of the tree
	$emptyChunk = '';
	for ($i=0; $i<$slotSize/4; $i++) {
		$emptyChunk .= pack('i', 0);
	}
	fseek($slotFile, end($slotList)*$slotSize);
	echo 'Empty Chunk: '.fwrite($slotFile, $emptyChunk);
	for ($i=0; $i<$numSlotsNeeded; $i++) {
		echo 'Record block at '.$slotList[$i].'<br>';
		fseek($slotFile, $slotList[$i]*$slotSize);
		fwrite($slotFile, pack('N', $slotList[$i+1]).substr($addData, $i*$chunkSize, $chunkSize));
	}
	echo 'Block done';
}
// link to next slot at end
/*
function addtoSlotGen($slot_handle, $check_slot, $addData, $slot_file, $slotSize) {
	// function where pointer to next slot is at the END of the string
	// Loop over slots until a space if found in a slot
	echo "Adding to slot ".$check_slot;
	$success = FALSE;
	while ($check_slot)
		{
		fseek($slot_file, $check_slot*$slotSize);
		$check_dat_bin = fread($slot_file, $slotSize);
		$check_dat_a = unpack("N*", substr($check_dat_bin, 0, $slotSize-4));
		//print_r($check_dat_a);
		$open_spot = array_search(0, $check_dat_a);
		if ($open_spot)
			{
			fseek($slot_file, $check_slot*$slotSize + $open_spot*4-4);
			$seek = $check_slot*$slotSize + $open_spot*4-4;
			echo 'Write '.fwrite($slot_file, $addData);
			$success = TRUE;
			$check_slot = 0;
			echo ' =>Found open spot '.$open_spot.' at '.$seek.'<br>';
			}
		else
			{
			$prev_slot = $check_slot;
			$next_slot_a = unpack("N", substr($check_dat_bin, $slotSize-4));
			$check_slot = $next_slot_a[1];
			echo "<br>Check Next Slot: ".$check_slot.", Read size of ".strlen($check_dat_bin);
			}
		}
	// If no slot is found add a new one
	if (!$success)
		{
		$new_slot_id = max(1, (filesize($slot_handle))/$slotSize);
		// Write new item ID to the new slot
		fseek($slot_file, $new_slot_id*$slotSize);
		fwrite($slot_file, $addData);
		// Fill the entire slot
		fseek($slot_file, $new_slot_id*$slotSize + $slotSize - 1);
		fwrite($slot_file, pack("C", 0));
		// Write new slot pointer to last slot
		fseek($slot_file, $prev_slot*$slotSize+$slotSize-4);
		fwrite($slot_file, $addData);
		//echo "case 3";
		echo "<br>Need a new slot : ".$new_slot_id." Write to slot ".$prev_slot."<br>";
		}
	//fclose($slot_file);
	return TRUE;
}
*/

	function addDataToSlot($slot_handle, $check_slot, $addData, $slot_file)
		{
		// Loop over slots until a space if found in a slot
		echo "Adding to slot ".$check_slot;
		$success = FALSE;
		while ($check_slot)
			{
			fseek($slot_file, $check_slot*40);
			$check_dat_bin = fread($slot_file, 40);
			$check_dat_a = unpack("N*", substr($check_dat_bin, 4));
			//print_r($check_dat_a);
			$open_spot = array_search(0, $check_dat_a);
			if ($open_spot)
				{
				fseek($slot_file, $check_slot*40 + $open_spot*4);
				$seek = $check_slot*40 + $open_spot*4;
				fwrite($slot_file, $addData);
				$success = TRUE;
				$check_slot = 0;
				echo "Found open spot<br>";
				}
			else
				{
				$prev_slot = $check_slot;
				$next_slot_a = unpack("N", $check_dat_bin);
				$check_slot = $next_slot_a[1];
				echo "<br>Check Next Slot: ".$check_slot;
				}
			}
		// If no slot is found add a new one
		if (!$success)
			{
			$new_slot_id = max(1, (filesize($slot_handle))/40);
			// Write new item ID to the new slot
			fseek($slot_file, $new_slot_id*40 + 4);
			fwrite($slot_file, $addData);
			// Fill the entire slot
			fseek($slot_file, $new_slot_id*40 + 4 + 35);
			fwrite($slot_file, pack("C", 0));

			// Write new slot pointer to last slot
			fseek($slot_file, $prev_slot*40);
			fwrite($slot_file, pack('N', $new_slot_id));
			//echo "case 3";
			echo "<br>Need a new slot : ".$new_slot_id." Write to slot ".$prev_slot."<br>";
			}
		//fclose($slot_file);

		return TRUE;
	}

function readSlotData($file, $slot_num, $slot_size)
	{
	$next_slot = $slot_num;
	$slotData = "";
	//echo "Next slot = ".$next_slot."<br>";
	while ($next_slot > 0)
		{
		$seekTo = $next_slot*$slot_size;
		fseek($file, $next_slot*$slot_size);
		$slot_dat = fread($file, $slot_size);
		$slotData .= substr($slot_dat, 4);
		//$slotData .= $slot_dat;
		$slot_check = unpack("N", $slot_dat);
		$next_slot = $slot_check[1];
		//echo "Seek Location: ".$seekTo." at slot ".$next_slot."<br>";
		}
	return $slotData;
	}
function readSingleSlot($file, $slot_num, $slot_size) {
	$next_slot = $slot_num;
	$slotData = "";
	//echo "Next slot = ".$next_slot."<br>";
		$seekTo = $next_slot*$slot_size;
		fseek($file, $next_slot*$slot_size);
		$slot_dat = fread($file, $slot_size);
		$slotData .= substr($slot_dat, 0, $slot_size-4);
		//echo 'Slot size is '.strlen($slot_dat).' <br>';
		$slot_check = unpack("i", substr($slot_dat, $slot_size-4));
		$next_slot = $slot_check[1];
		//echo "Seek Slot: ".$seekTo."<br>";
	//echo "Return ".strlen($slotData)."<br>";
	return $slotData;
}
/*
function readSlotDataEndKey($file, $slot_num, $slot_size)
	{
	$next_slot = $slot_num;
	$slotData = "";
	//echo "Next slot = ".$next_slot."<br>";
	while ($next_slot > 0)
		{
		$seekTo = $next_slot*$slot_size;
		fseek($file, $next_slot*$slot_size);
		$slot_dat = fread($file, $slot_size);
		$slotData .= substr($slot_dat, 0, $slot_size-4);
		//echo 'Slot size is '.strlen($slot_dat).' <br>';
		$slot_check = unpack("i*", substr($slot_dat, $slot_size-4));
		$next_slot = $slot_check[1];
		//echo "Seek Slot: ".$slot_check[1]."<br>";
		}
	//echo "Return ".strlen($slotData)."<br>";
	return $slotData;
	}
*/
/*
function removeFromEndSlot($file, $startSlot, $slot_size, $targetVal) {
	$next_slot = $startSlot;
	//$slotData = "";
	$looking = true;
	//echo "Next slot = ".$next_slot."<br>";
	$returnVal = 0;
	while ($next_slot > 0 && $looking)		{
		$seekTo = $next_slot*$slot_size;
		fseek($file, $next_slot*$slot_size);
		$slot_dat = fread($file, $slot_size);
		$checkVals = unpack('i*', substr($slot_dat, 0, $slot_size-4));
		echo '<p>Look for '.$targetVal.' in<br>';
		print_r($checkVals);
		$foundLoc = array_search($targetVal, $checkVals);
		if ($foundLoc) {
			echo 'Found at location '.$foundLoc;
			// overwrite the value at that position
			fseek($file, $next_slot*$slot_size+$foundLoc*4-4);
			fwrite($file, pack('i', 0));
			$returnVal = $foundLoc;
			$looking = false;
		} else {
			//$slotData .= substr($slot_dat, 0, $slot_size-4);
			//echo 'Slot size is '.strlen($slot_dat).' <br>';
			$slot_check = unpack("i*", substr($slot_dat, $slot_size-4));
			$next_slot = $slot_check[1];
			//echo "Seek Slot: ".$slot_check[1]."<br>";
		 	}
		}
return $returnVal;
}*/
function writeSlotPoint($slotFile, $startSlot, $targetPoint, $data, $slotSize) {
		$blockNum = floor($targetPoint/($slotSize-4));
		for ($i=0; $i<=$blockNum; $i++) {
			fseek($slotFile, $startSlot*$slotSize);
			$nextSlot = unpack('N', fread($slotFile, 4));
			$startSlot = $nextSlot[1];
		}
		fseek($startSlot*$slotSize+$targetPoint*4);
		fwrite($data);
	}
?>
