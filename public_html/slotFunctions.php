<?php

class dataSlot {
	private $slotList = [];
	public $slotData = [];
	
	function __construct($start, $slotFile, $size) {
		$nextSlot = $start;
		while ($nextSlot > 0) {
			$slotList[] = $nextSlot;
			fseek($slotFile, $start*$size);
			$tmpDat = fread($slotFile, $size);
			$slotData .= substr($tmpDat, 4);
			$tmpA = unpack("N", $tmpDat);
			$nextSlot = $tmpA[1];
		}
	}
}

function startASlot($slot_file, $slot_handle)
	{
	echo "no slot established<br>";
	// Check for abandoned slots first
	$slot_list_dat = fread($slot_file, 40);
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
/*
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
function removeFromSlot($file, $startSlot, $slot_size, $targetVal) {
}
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
}
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
