<?php

session_start();
if (isset($_SESSION['playerId'])) {
	echo "conPane
	<script>
	window['readForm'] = function() {
		var subVal;
		subVal = '1011,';
		for (var i=1; i<3; i++) {
			subVal += document.getElementById('su0'+i).value +',';
			}
		alert(subVal);
		passClick(subVal);
		}
	</script>
			
	Create a Game<p>
	<table>
		<tr><td>Scenario</td><td><select id='su01'>
			<option value='1'>High King of Ireland
			</select>
			</td></tr>
		<tr><td>Your Race:</td><td><select id='su02'>
			<option value='1'>Irish
			</select>
			</td></tr>
		<tr><td>Max Players:</td><td><select id='su02'>
			<option value='1'>20
			</select>
			</td></tr>
	</table>
	<a href='javascript:void(0)' onclick=readForm(1011)>Create!</a>";
	}
else include("../scripts/1002.php");

?>