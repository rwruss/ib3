<?php

session_start();

$host = 'localhost'; //host
$port = '9000'; //port
$null = NULL; //null var
$_GLOBALS['gameList'] = array();
/*
1-9 is piece rank
10 is spy
11 is bomb
12 is flag
*/
$rankList = [1, 2, 3, 3, 4, 4, 4, 5, 5, 5, 5, 6, 6, 6, 6, 7, 7, 7, 7, 8, 8, 8, 8, 8, 9, 9, 9, 9, 9, 9, 9, 9, 10, 11, 11, 11, 11, 11, 11, 12, 1, 2, 3, 3, 4, 4, 4, 5, 5, 5, 5, 6, 6, 6, 6, 7, 7, 7, 7, 8, 8, 8, 8, 8, 9, 9, 9, 9, 9, 9, 9, 9, 10, 11, 11, 11, 11, 11, 11, 12];
/*
1 - defender dies/attacker wins
2 - attacker dies/defender wins
3 - both die
4 - attacker wins game
*/
$results[1] = [3, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 4];
$results[2] = [2, 3, 1, 1, 1, 1, 1, 1, 1, 1, 2, 4];
$results[3] = [2, 2, 3, 1, 1, 1, 1, 1, 1, 1, 2, 4];
$results[4] = [2, 2, 2, 3, 1, 1, 1, 1, 1, 1, 2, 4];
$results[5] = [2, 2, 2, 2, 3, 1, 1, 1, 1, 1, 2, 4];
$results[6] = [2, 2, 2, 2, 2, 3, 1, 1, 1, 1, 2, 4];
$results[7] = [2, 2, 2, 2, 2, 2, 3, 1, 1, 1, 2, 4];
$results[8] = [2, 2, 2, 2, 2, 2, 2, 3, 1, 1, 1, 4];
$results[9] = [2, 2, 2, 2, 2, 2, 2, 2, 3, 1, 2, 4];
$results[10] = [1, 2, 2, 2, 2, 2, 2, 2, 2, 3, 2, 4];


//Create TCP/IP sream socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//reuseable port
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

//bind socket to specified host
socket_bind($socket, 0, $port);

//listen to port
socket_listen($socket);

//create & add listning socket to the list
$clients = array($socket);

// Load player auth File
$pFile = fopen("players.auth", "r");
$userChecks = array();

//start endless loop, so that our script doesn't stop
while (true) {
	//manage multipal connections
	$changed = $clients;
	//returns the socket resources in $changed array
	socket_select($changed, $null, $null, 0, 10);

	//check for new socket
	if (in_array($socket, $changed)) {
		$socket_new = socket_accept($socket); //accpet new socket
		$clients[] = $socket_new; //add socket to client array

		$header = socket_read($socket_new, 1024); //read data sent by the socket
		perform_handshaking($header, $socket_new, $host, $port); //perform websocket handshake

		socket_getpeername($socket_new, $ip); //get ip address of connected socket
		$response = mask(json_encode(array('type'=>'system', 'message'=>$ip.' connected'))); //prepare json data
		send_message($response); //notify all users about new connection

		//make room for new socket
		$found_socket = array_search($socket, $changed);
		unset($changed[$found_socket]);
	}

	//loop through all connected sockets
	foreach ($changed as $changed_socket) {

		//check for any incomming data
		while(socket_recv($changed_socket, $buf, 1024, 0) >= 1)
		{
			echo "Received message: ".$buf.'<br>';
			$received_text = unmask($buf); //unmask data
			$tst_msg = json_decode($received_text); //json decode

			print_r($tst_msg);
			echo "\n";
			if ($tst_msg != NULL) {
				// Verify user credintials
				if (isset($tst_msg->playerID) && isset($tst_msg->gameInf)) {
					if (isset($userChecks[$tst_msg->playerID])) {
						if ($tst_msg->gameInf == $userChecks[$tst_msg->playerID]) {
							handleMessage($tst_msg, $changed_socket);
					} else echo "invalid user: ".$tst_msg->gameInf. " vs ".$userChecks[$tst_msg->playerID]."\n";
				} else {
					// Need to load the player key to see if they are authorized
					fseek($pFile, $tst_msg->playerID*32);
					$userChecks[$tst_msg->playerID] = fread($pFile, 32);

					if ($tst_msg->gameInf == $userChecks[$tst_msg->playerID]) {
						handleMessage($tst_msg, $changed_socket);
					} else echo "invalid user: ".$tst_msg->gameInf. " vs ".$userChecks[$tst_msg->playerID]."\n";
				}
			} else echo "invalid message 104 ".$tst_msg->playerID." and ".$tst_msg->gameInf."\n";
		}
	}

		$buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
		if ($buf === false) { // check disconnected client
			// remove client for $clients array
			$found_socket = array_search($changed_socket, $clients);
			socket_getpeername($changed_socket, $ip);
			unset($clients[$found_socket]);

			//notify all users about disconnected connection
			$response = mask(json_encode(array('type'=>'system', 'message'=>$ip.' disconnected')));
			send_message($response);
		}
	}
}
// close the listening socket
socket_close($socket);
fclose($pFile);

function handleMessage($tst_msg, $userSocket) {
	switch ($tst_msg->type) {
		case "message" :
			$user_name = $tst_msg->name; //sender name
			$user_message = $tst_msg->message; //message text
			$user_color = $tst_msg->color; //color

			//prepare data to be sent to client
			$response_text = mask(json_encode(array('type'=>'usermsg', 'name'=>$user_name, 'message'=>$user_message, 'color'=>$user_color)));
			send_message($response_text); //send data
			echo "User: ".$userSocket. "Send message ".$response_text."<br>";
			break; //exist this loop

		case "move":
			makeMove($tst_msg->gameID, $tst_msg->playerID, $tst_msg->oldSpot, $tst_msg->newSpot);
			echo "process a move from user ".$userSocket."\n";
			break;

		case "start":
		startGame($tst_msg);
		$response_text = mask(json_encode(array('type'=>'script', 'name'=>'nada', 'message'=>'console.log("recveive a script message")', 'color'=>'')));
		send_message($response_text); //send data
		break;

		case "newGame":
		createGame($tst_msg->player1, $tst_msg->player2);
		break;
	}
}

function send_message($msg) {
	global $clients;
	foreach($clients as $changed_socket)
	{
		@socket_write($changed_socket,$msg,strlen($msg));
	}
	return true;
}


//Unmask incoming framed message
function unmask($text) {
	$length = ord($text[1]) & 127;
	if($length == 126) {
		$masks = substr($text, 4, 4);
		$data = substr($text, 8);
	}
	elseif($length == 127) {
		$masks = substr($text, 10, 4);
		$data = substr($text, 14);
	}
	else {
		$masks = substr($text, 2, 4);
		$data = substr($text, 6);
	}
	$text = "";
	for ($i = 0; $i < strlen($data); ++$i) {
		$text .= $data[$i] ^ $masks[$i%4];
	}
	return $text;
}

//Encode message for transfer to client.
function mask($text) {
	$b1 = 0x80 | (0x1 & 0x0f);
	$length = strlen($text);

	if($length <= 125)
		$header = pack('CC', $b1, $length);
	elseif($length > 125 && $length < 65536)
		$header = pack('CCn', $b1, 126, $length);
	elseif($length >= 65536)
		$header = pack('CCNN', $b1, 127, 0, $length); // changed from $header = pack('CCNN', $b1, 127, $length);
	return $header.$text;
}

//handshake new client.
function perform_handshaking($receved_header,$client_conn, $host, $port) {
	$headers = array();
	$lines = preg_split("/\r\n/", $receved_header);
	foreach($lines as $line)
	{
		$line = chop($line);
		if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
		{
			$headers[$matches[1]] = $matches[2];
		}
	}

	$secKey = $headers['Sec-WebSocket-Key'];
	$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
	//hand shaking header
	$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
	"Upgrade: websocket\r\n" .
	"Connection: Upgrade\r\n" .
	"WebSocket-Origin: $host\r\n" .
	"WebSocket-Location: ws://$host:$port/demo/shout.php\r\n".
	"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
	socket_write($client_conn,$upgrade,strlen($upgrade));
}

function createGame($player1, $player2) {
	global $gameList;
	$newID = sizeof($gameList)+1;
	$gameList[$newID] = new game($newID, 1, 2);
	$response_text = mask(json_encode(array('type'=>'script', 'name'=>'nada', 'message'=>'gameID = '.$newID, 'color'=>'')));
	send_message($response_text); //send data
}

function startGame($msg) {
	echo "\n\nSTART Player Side ".$msg->startSide." in game ".$msg->gameID."\n";
	//print_r($msg);
	/*
	$offset = (($msg->startSide-1)*80);
	$postions = $msg->startSpots;
	for ($i=0; $i<80; $i+=2) {
		$pieceTracking[$i+$offset] = $msg->startSpots[$i];
		$pieceTracking[$i+$offset+1] = $msg->startSpots[$i+1];
	}
	//print_r($pieceTracking);
	//echo $msg."\n";*/
	global $gameList;
	$postions = $msg->startSpots;
	$gameList[$msg->gameID]->loadSide($msg->startSpots, $msg->startSide);
}

function makeMove($gameID, $playerNum, $from, $to) {
	global $gameList;
	$gameList[$gameID]->movePiece($playerNum, $from, $to);
}

class game {
	public $unitLocs, $unitStatus, $playerStatus, $turn, $boardSpots;
	function __construct($id, $player1, $player2) {
		$this->unitLocs = array_fill(0, 160, 0);
		$this->unitStatus = array_fill(0, 80, 1);
		$this->playerStatus = [0, 0, 0];
		$this->boardSpots = array_fill(0,100,81);
		$this->turn = 1;
		$this->opponentSwitch = [0, 2, 1];
		echo "New game created (".$id.")\n";
	}


	function loadSide($locList, $side) {
		$offset = (($side-1)*80);
		for ($i=0; $i<40; $i++) {
			$boardIndex = $locList[$i*2]+$locList[$i*2+1]*10;
			$this->unitLocs[$i*2+$offset] = $locList[$i*2];
			$this->unitLocs[$i*2+$offset+1] = $locList[$i*2+1];
			$this->playerStatus[$side] = 1;

			$this->boardSpots[$boardIndex] = $i;
		}

		// Check if both players are ready and send a start message
		if ($this->playerStatus[1] ==1 && $this->playerStatus[2] == 1) {
			echo "Both sides loaded - start the game!\n";
			$response_text = mask(json_encode(array('type'=>'script', 'name'=>'nada', 'message'=>'gameStatus=1;', 'color'=>'')));
			send_message($response_text); //send data
		}
	}

	function movePiece($playerNum, $from, $to) {
		// Verify player controls the piece
		echo "Player ".$playerNum." making a move ".$from." --> ".$to."\n";
		print_r($this->boardSpots);

		$fromIndex = $from[0] + $from[1]*10;
		$toIndex = $to[0] + $to[1]*10;

		$movedPiece = $this->boardSpots[$from];
		$trgPiece = $this->boardSpots[$to];
		if (floor($movedPiece/40)+1 == $playerNum) {
			// Check target location to see if it is a valid move
			if (abs($from-$to)==10 || abs($from-$to) == 1) {
				// This is a valid one space move -> now verify that it is a move to able spot
				echo "valid move\n";
				$spotCheck = floor($trgPiece/40)+1;
				switch($spotCheck) {
					case $playerNum:
						echo "Can't move onto your own piece(".$movedPiece." vs ".$trgPiece.")\n";
						break;
					case $this->opponentSwitch[$playerNum]:
						echo "Move onto an opponents piece (".$trgPiece." vs ".$this->opponentSwitch[$playerNum].")\n";
						// Review outcome of piece collision
						$outCome = resolveCollision($attacker, $defender);
						switch($outCome) {
							case 1:
								$this->kill($toIndex, $trgPiece);
								$this->processMove($from, $to, $movedPiece);
								$response_text = mask(json_encode(array('type'=>'script', 'message'=>'console.log("result 1");')));
								break;

							case 2:
								$this->kill($fromIndex, $movedPiece);
								$response_text = mask(json_encode(array('type'=>'script', 'message'=>'console.log("result 2");')));
								break;

							case 3:
								$this->kill($toIndex, $trgPiece);
								$this->kill($fromIndex, $movedPiece);
								$response_text = mask(json_encode(array('type'=>'script', 'message'=>'console.log("result 3");')));
								break;
						}
						break;
					case 3:
						echo "move to an empty spot\n";
						break;
					case 4:
						echo "move to a closed tile\n";
						break;
				}

			} else echo "invalid move (".($from-$to).")\n";
		}
	}

	function processMove($fromIndex, $toIndex, $pieceID) {
		$this->boardSpots[$fromIndex] = 100;
		$this->boardSpots[$toIndex] = $pieceID;
		$this->unitLocs[$pieceID*2] = $toIndex - floor($toIndex/10);
		$this->unitLocs[$pieceID*2+1] = floor($toIndex/10);
	}

	function kill($index, $pieceID) {
		$this->boardSpots[$index] = 100;
		$this->unitLocs[$pieceID*2] = -1;
		$this->unitLocs[$pieceID*2+1] = -1;
	}
}

function resolveCollision($attacker, $defender) {
	global $rankList;
	global $results;

	$aRank = $rankList[$attacker];
	$dRank = $rankList[$defender];

	$outCome = $results[$aRank][$dRank];
	return $outCome;
}

?>
