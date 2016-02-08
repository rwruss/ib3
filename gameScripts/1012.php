<?php
if ($postVals[1] == 0) {
echo "<div onclick=\x22passClick('1012,1' , 'infoBar')\x22; style='z-index:1; border:1px solid black; position:absolute; left:0; width:50; height:50;'>Gathering</div>
	<div style='border:1px solid black; position:absolute; left:60; width:50; height:50;'>Group 2</div>
	<div style='border:1px solid black; position:absolute; left:120; width:50; height:50;'>Group 3</div>
	<div style='border:1px solid black; position:absolute; left:180; width:50; height:50;'>Group 4</div>
	<div style='border:1px solid black; position:absolute; left:240; width:50; height:50;'>Group 5</div>
	<div style='border:1px solid black; position:absolute; left:300; width:50; height:50;'>Group 6</div>
	<div style='border:1px solid black; position:absolute; left:360; width:50; height:50;'>Group 7</div>
	<div style='border:1px solid black; position:absolute; left:420; width:50; height:50;'>Group 8</div>
	<div style='border:1px solid black; position:absolute; left:480; width:50; height:50;'>Group 9</div>
	<div style='border:1px solid black; position:absolute; left:540; width:50; height:50;'>Group 10</div>";
}
else if($postVals[1] == 1)
	{
	echo "<div style='border:1px solid black; position:absolute; left:0; width:50; height:50;'>Food</div>
		<div style='border:1px solid black; position:absolute; left:60; width:50; height:50;'>Building Material</div>
		<div style='border:1px solid black; position:absolute; left:120; width:50; height:50;'>Trade Goods</div>";
	}


?> 