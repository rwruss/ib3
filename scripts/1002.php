<?php

echo "conPane
	<script>
	window['readForm'] = function() {
		var subVal;
		var send = 1;
		subVal = '1008,';
		for (var i=1; i<3; i++) {
			subVal += document.getElementById('su0'+i).value +',';
			}
		passClick(subVal);
		}

		window['checkKey'] = function() {
			alert('key')
		}
	</script>
	<form action ='#' method='get' onsubmit=readForm()>
	<table>

		<tr><td>User Name:</td><td><input id='su01'></td></tr>
		<tr><td>Password:</td><td><input type='password' id='su02'></td></tr>
		<tr><td onclick=readForm()>Submit!</td><td>yay</td></tr>
		<div class='yourCustomDiv'/>
    <input type='submit' style='display:none'/>

	</table>	</form>";

?>
