<?php

echo '
<script>
function ncode_div(el_id) {
         var x = document.getElementById(el_id).getElementsByTagName("script");
         for(var i=0;i<x.length;i++) {
                 eval(x[i].text);
                 }
         }

	function ncode_general(data) {
         var x = data.getElementsByTagName("script");
         for(var i=0;i<x.length;i++) {
                 eval(x[i].text);
                 }
         }

function passClick(val) {
	params = "val1="+val;
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open("POST", "director.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById(xmlhttp.response.substr(0,7)).innerHTML = xmlhttp.response.substr(7);
			ncode_div(xmlhttp.response.substr(0,7));
			}
		}

	xmlhttp.send(params);
	}
</script>
<html>
<title>IB3</title>
<div id="navPane" style="position:absolute; top:30; left:0; border:1px solid #000000; width:200; height:700;">
<table>
	<tr><td>About</td></tr>
	<tr><td><a href="javascript:void(0)" onclick=passClick(1001)>Signup</a></td></tr>
	<tr><td><a href="javascript:void(0)" onclick=passClick(1002)>Login</a></td></tr>
	<tr><td><a href="javascript:void(0)" onclick=passClick(1003)>My Stats</a></td></tr>
	<tr><td><a href="javascript:void(0)" onclick=passClick(1004)>My Games</a></td></tr>
	<tr><td><a href="javascript:void(0)" onclick=passClick(1005)>Logout</a></td></tr>

</table>
</div>
<div id="plrPane" style="position:absolute; top:0; left:0; border:1px solid #000000; width:800; height:30;">Content</div>
<div id="conPane" style="position:absolute; top:30; left:200; border:1px solid #000000; width:600; height:700; overflow:scroll;">Content</div>
<div id="scrPane" style="position:absolute; left:0; width:0; height:0; overflow:hidden"></div>
</html>';

?>
