<?php

echo "conPane
	<script>
	window['readForm'] = function() {
		
		if (document.getElementById('su02').value != document.getElementById('su03').value) {
			alert('Verify Email');
			}
		else if (document.getElementById('su04').value != document.getElementById('su05').value) {
			alert('Verify Password');
			}
		else {
			var subVal;
			var send = 1;
			subVal = '1006,';
			for (var i=1; i<6; i++) {
				if (document.getElementById('su0'+i).value == '') {send=1;}
				subVal += document.getElementById('su0'+i).value +',';
				}
			if (send) {//alert(subVal);
				passClick(subVal);}
			else alert('incomplete');
			
			}
		}
	</script>

	<table>
		<tr><td>User Name:</td><td><input id='su01'></td></tr>
		<tr><td>Email:</td><td><input id='su02'></td></tr>
		<tr><td>Repeat Email:</td><td><input id='su03'></td></tr>
		<tr><td>Password:</td><td><input type='password' id='su04'></td></tr>
		<tr><td>Password:</td><td><input type='password' id='su05'></td></tr>
		<tr><td onclick=readForm()>Submit!</td><td>yay</td></tr>
		
	</table>";

?>