ncode_general = function(data) {
	eval(data);
	}

loadGame = function () {

	//create a new WebSocket object.
	var wsUri = "ws://localhost:9000/demo/stratego.php";
	websocket = new WebSocket(wsUri);

	websocket.onopen = function(ev) { // connection is open
		document.getElementById("message_box").innerHTML += "<div class=\"system_msg\">Connected!</div>"; //notify user
		var msg = {type: "newGame", player1: 1, player2: 2};
		//websocket.send(JSON.stringify(msg));
		sendToSocket(msg);
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
			document.getElementById("message_box").innerHTML += "<div class=\"system_msg\">"+umsg+"</div>";
			break;

		case 'gameMove':
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
	msg['gameInf'] = 1234;
	msg['playerID'] = 1;
	websocket.send(JSON.stringify(msg));
}
