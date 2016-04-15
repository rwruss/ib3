<?php
if (isset($_GET['gameID'])) $gamePath = "../games/".$_GET['gameID'];
else exit();
$cultureList = explode(",", file_get_contents($gamePath."/culture.txt"));

echo "<script>
	function ncode_div(el_id) {
         var x = document.getElementById(el_id).getElementsByTagName('script');
         for(var i=0;i<x.length;i++) {
                 eval(x[i].text);
                 }
         }

	function passClick(val, trg) {
		params = 'val1='+val;
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open('POST', 'gameScr.php?gid=".$_GET['gameID']."', true);
		xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xmlhttp.setRequestHeader('Content-length', params.length);
		xmlhttp.setRequestHeader('Connection', 'close');

		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById(trg).innerHTML = xmlhttp.response;
				ncode_div(trg);
				}
			}

		xmlhttp.send(params);
		}

	window['readForm'] = function() {
		var subVal;
		subVal = [1004];
		for (var i=1; i<2; i++) {
			//subVal += document.getElementById('su0'+i).value +',';
			subVal.push(document.getElementById('su0'+i).value);
			}
		alert(subVal);
		passClick(subVal, 'scrBox');
		}

	window['changeCul'] = function() {
		document.getElementById('mapPane').innerHTML = document.getElementById('su01').value;
		}

	</script>
<div id='selPane' style='position:absolute; left:10; top:10; height:400; width:400; border:1px solid #000000;'>
	<table>
		<tr><td>Select a Culture:</td><td><select id='su01' onchange=changeCul()>";

for ($i=0; $i<sizeof($cultureList)/2; $i++) {
	echo "<option value=".$cultureList[$i*2].">".$cultureList[$i*2+1];
	}
	echo"</select></td></tr>
	<tr><td onclick=readForm()>Submit!</td></tr>

	</table>
</div>
<div id='mapPane' style='position:absolute; left:410; top:10; height:400; width:400; border:1px solid #000000;'></div>
<div id='dtlPane' style='position:absolute; left:10; top:410; height:300; width:800; border:1px solid #000000;'></div>
<div id='scrBox' style='position:absolute; left:0; top:0; height:0; width:0; overflow:hidden'>";



?>
