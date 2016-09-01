<?php
session_start();
$_SESSION['userCount'] = 0;
echo '
<html>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<script>

function init() {
  console.log("hello");

  document.getElementById("talkSend").addEventListener("click", function () {
    sendMsg();
  })

  var source = new EventSource("phpDaemon2.php");
  console.log("Sources State:" + source.readyState);
  source.onopen = function () {
    source.onmessage = function(event) {
      console.log("loading stream..");
      console.log(event.data);
      //console.log("Sources State:" + source.readyState);
      target = document.getElementById("chatWindow");
      target.innerHTML += event.data + "<br>";
      target.scrollTop = target.scrollHeight;
    };
  }
}

function sendMsg () {
  var msgBox = document.getElementById("talkBox");
  console.log(msgBox.value);

  params = "msg="+msgBox.value;
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.open("POST", "recMsg.php", true);
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xmlhttp.onreadystatechange = function() {
    }

  xmlhttp.send(params);
  msgBox.value = "";
}

window.addEventListener("load", init);
</script>

<style>
.chatWindow {
  width:95%;
  height:50%;
  border:1px solid blue;
  overflow:scroll;
}

.talkContain {
  width:85%;
  height:70;
  border:1px solid red;
}

.talkSend {
  width:4%;
  height:70;
  border:1px solid green;
  float:left;
}
</style>

<body>
<div id="chatWindow" class="chatWindow">hello</div>
<div id="talkContain" class="talkContain">
  <input id="talkBox"></input>
</div>
<div id="talkSend" class="talkSend"></div>
</body>
</html>';

?>
