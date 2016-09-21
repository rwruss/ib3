
addDiv = function(id, useClassName, target) {
	var trg;
	if (typeof(target) == "string") trg = document.getElementById(target);
	else trg = target;

	var newDiv = document.createElement("div");
	newDiv.className = useClassName;
	newDiv.id = id;
	trg.appendChild(newDiv);
	return newDiv;
}

ncode_general = function(data) {
	eval(data);
	}

gameMenu = function () {
	menuContain = addDiv("createGameMenu", "createGameMenu", "container");
	sendButton = addDiv("createButton", "createButton", menuContain);
	sendButton.addEventListener("click", function () {createGame();})
	openGames = addDiv("openGames", "openGames", menuContain);
	var msg = {type: "showOpenGames"};
	sendToSocket(msg);
}

joinGame = function(id) {
	var msg = {type:"join", gameID: id};
	console.log("join game with message " + id + ", " + msg);
	sendToSocket(msg);
}

loadPieces = function () {
	console.log("Load pieces");
		rankList = [1, 2, 3, 3, 4, 4, 4, 5, 5, 5, 5, 6, 6, 6, 6, 7, 7, 7, 7, 8, 8, 8, 8, 8, 9, 9, 9, 9, 9, 9, 9, 9, 10, 11, 11, 11, 11, 11, 11, 12];
		useRanks = [];
		var placeNum;
		// randomize the rank list
		while (rankList.length>0) {
			placeNum = Math.floor(Math.random()*rankList.length);
			useRanks.push(rankList[placeNum]);
			rankList.splice(placeNum, 1);
		}
		var count = 0;
		for (side=1; side<3; side++) {
			for (rank=0; rank<useRanks.length; rank++) {
				if (side == playerSide) pieceList.push(new piece(count, useRanks[rank], side));
				else pieceList.push(new piece(count, 0, side));
				count++;
			}
		}
	}

showGames = function (games) {
	console.log("showGames");
	for (var i=0; i<games.length; i++) {
		console.log("SHow game " + games[i])
	gameContain = addDiv("", "gameContain", "openGames");
	gameContain.gameID = games[i];
	gameContain.innerHTML = "Game "+ games[i];
	gameContain.addEventListener("click", function () {joinGame(this.gameID)});}
}

setSide = function (player, newside) {
	if (playerID == player) {playerSide = newside;
		document.getElementById("createGameMenu").remove();
		loadPieces();
		var pieceOffset = 0;
		if (playerSide == 2) pieceOffset = 40;
		for (var i=0+pieceOffset; i<40+pieceOffset; i++) {
			thisPiece = addDiv("piece_"+i, "pieceStyle_0", "rightPane");
			thisPiece.pieceID = i;
			thisPiece.addEventListener("click", selectPiece);
			thisPiece.innerHTML = i;
		}
	}
}

gameMessage = function(msg) {
	document.getElementById("gameMessageBox").innerHTML += "<div><span class=\"user_name\" style=\"color:#000000\">System:</span> : <span class=\"user_message\">"+msg+"</span></div>"
}

createGame = function() {
	var msg = {type: "newGame", player1: playerSide};
	//websocket.send(JSON.stringify(msg));
	sendToSocket(msg);
}

loadSocket = function () {
	//create a new WebSocket object.
	var wsUri = "ws://localhost:9000/demo/stratego.php";
	websocket = new WebSocket(wsUri);

	websocket.onopen = function(ev) { // connection is open
		document.getElementById("message_box").innerHTML += "<div class=\"system_msg\">Connected!</div>"; //notify user
	}

	document.getElementById("send-btn").addEventListener("click", function(){ //use clicks message send button
		console.log("click send");
		var mymessage = document.getElementById("message").value; //get message text
		var myname = document.getElementById("name").value; //get user name

		if(myname == ""){ //empty name?
			alert("Enter your Name please!");
			return;
		}
		if(mymessage == ""){ //emtpy message?
			alert("Enter Some message Please!");
			return;
		}

		//prepare json data
		var msg = {
		type: "message",
		message: mymessage,
		name: myname,
		color : '<?php echo $colours[$user_colour]; ?>'
		};
		//convert and send data to server
		console.log("Send " + msg);
		//websocket.send(JSON.stringify(msg));
		sendToSocket(msg);
	});

	//#### Message received from server?
	websocket.onmessage = function(ev) {
		console.log(ev.data);
		var msg = JSON.parse(ev.data); //PHP sends Json data
		var type = msg.type; //message type

		switch (type) {
		case 'usermsg':
			var umsg = msg.message; //message text
			var uname = msg.name; //user name
			var ucolor = msg.color; //color
			document.getElementById("message_box").innerHTML += "<div><span class=\"user_name\" style=\"color:#"+ucolor+"\">"+uname+"</span> : <span class=\"user_message\">"+umsg+"</span></div>";
			break;
		case 'system':
			console.log("System message " + msg.message);
			document.getElementById("message_box").innerHTML += "<div class=\"system_msg\">"+msg.message+"</div>";
			break;

		case 'gameMove':
			break;

		case 'gameMessage':
			gameMessage(msg.message);
			break;

		case 'script':
			console.log("run script message " + msg.message)
			ncode_general(msg.message);
			break;
		}

		document.getElementById("message").value = ""; //reset text
	};

	websocket.onerror	= function(ev){document.getElementById("message_box").innerHTML += "<div class=\"system_error\">Error Occurred - "+ev.data+"</div>";};
	websocket.onclose 	= function(ev){document.getElementById("message_box").innerHTML += "<div class=\"system_msg\">Connection Closed</div>";};
}

sendToSocket = function(msg) {
	msg['gameInf'] = '12340000000000000000000000000000';
	msg['playerID'] = playerID;
	console.log("SEND " + JSON.stringify(msg));
	websocket.send(JSON.stringify(msg));
}
