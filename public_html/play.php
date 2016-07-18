<?php

session_start();
if (!isset($_SESSION["playerId"])) echo "<script>window.location.replace(./index.php)</script>";
if (!isset($_GET["gameID"])) echo "<script>window.location.replace(./index.php)</script>";

// Read game file to determine player number and status.

$playerList = unpack("i*", file_get_contents("../games/".$_GET["gameID"]."/players.dat"));
$playerListLoc = array_search($_SESSION["playerId"], $playerList);
$pGameID = $playerList[$playerListLoc+1]*-1;
$_SESSION["instance"] = $_GET["gameID"];
//echo "PLAYER GAME ID IS ".$pGameID;
if ($pGameID == FALSE) {
	echo "<p><p><p><p>Not alrady in game(".$_SESSION["playerId"].")";
	print_r($playerList);
	include("../gameScripts/1003.php");

	exit;}

$_SESSION["gameIDs"][$_GET["gameID"]] = $pGameID;


$gamePath = "../games/".$_GET["gameID"];
$gameID = $_GET["gameID"];
// Read game parameters
$paramDat = file_get_contents($gamePath."/params.ini");
$mapBounds = unpack("S*", substr($paramDat, 100, 8));
$gameTimes = unpack("N*", substr($paramDat, 0, 8));

// Read player info
//$playerDat = file_get_contents($gamePath."/unitDat.dat", NULL, NULL, $pGameID*400, 400);

$playerDat = unpack("i*", file_get_contents($gamePath."/unitDat.dat", NULL, NULL, $pGameID*100, 400));

/*
$pStatus = unpack("C*", substr($playerDat, 0, 5));
$playerOther = unpack("s*", substr($playerDat, 24, 42));
$playerSlots = unpack("N*", substr($playerDat, 66, 100));
$startTile = unpack("S", substr($playerDat, 24, 2));
*/

// Read faction leader info
/*
$leaderID = unpack("N", substr($playerDat, 66, 4));
$leaderDat = file_get_contents($gamePath."/chars.dat", NULL, NULL, $leaderID[1]*200, 200);
$leader_C = unpack("C*", substr($leaderDat, 0, 4));
$leaderNameKeys = unpack("S*", substr($leaderDat, 20, 6));
*/


if ($playerDat[1] == 0) {include("../gameScripts/1003.php"); exit;}
/*
$nameFile = fopen("../games/common/names_".$leader_C[3].".dat", "rb");
fseek($nameFile, $leaderNameKeys[1]*20);
$charName[0] = trim(fread($nameFile, 20));
fseek($nameFile, $leaderNameKeys[2]*20);
$charName[1] = trim(fread($nameFile, 20));
if ($leaderNameKeys[3] > 0) {
	fseek($nameFile, $leaderNameKeys[3]*20);
	$charName[2] = trim(fread($nameFile, 20));
	}
else $charName[2] = "";
*/

echo '
<link rel="stylesheet" type="text/css" href="ib3styles.css">
<script type="text/javascript" src="glMatrix-0.9.5.min.js"></script>
<script type="text/javascript" src="webgl-utils.js"></script>
<script type="text/javascript" src="templates.js"></script>

<script id="shader-fs" type="x-shader/x-fragment">
    precision mediump float;

	varying float ptElevation;
	varying float ptTerrain;
	varying vec2 vTextureCoord;
	varying vec2 vVertexPosition;
	varying vec3 vVertexNormal;
	//vec3 vMapScale
	varying vec4 oceanColor;
	varying vec4 screenPos;
	varying float fOffset;
	vec4 texColor;
	vec4 texColorRt;
	vec4 texColorDn;
	vec4 texColorDnRt;

	vec4 terColor;
	vec4 terColorDn;
	vec4 terColorRt;
	vec4 terColorDnRt;
	vec4 borderMask;
	vec4 terIndexDn;
	vec4 terIndexRt;
	vec4 terIndexDnRt;
	//uniform vec3 uMapScale;
	uniform sampler2D uSampler;
	uniform sampler2D uBSampler;
	uniform sampler2D uTSampler;
	uniform sampler2D uOSampler;
	uniform sampler2D uAreaSampler;
	uniform sampler2D uHexPSampler;
	uniform sampler2D uRoadSampler;
	uniform sampler2D uHexMap;
	uniform sampler2D uBumpSampler;
	uniform sampler2D uGrassSampler;
	uniform sampler2D uPlainsSampler;
	uniform sampler2D ufBumpSampler;
	uniform sampler2D uMaskSampler;
	uniform float uHexOn;
	uniform float uUseColor;
	vec4 maskColor;
	vec4 hexMap;
	vec4 hexPattern;
	varying vec4 flatPos;
	vec4 flatColor;
	//varying float vMapScale;
	varying float vTileNum;
	varying float directionalLightWeighting;
	varying float timeVal;
	varying vec4 vPosition;
	varying vec3 vTransformedNormal;
	varying vec4 worldLightDirection;
	float xPos;
	float yPos;
	float offset;

    void main(void) {
		xPos = vVertexPosition.x*12.0;
		yPos = vVertexPosition.y*12.0;
		offset = floor(mod(xPos, 2.0));
		vec3 normalWeight = max(-1.0, dot(vVertexNormal, vec3(-0.57735, 0.57735, -0.57735)))*vec3(0.25, 0.25, 0.25);
		hexPattern = texture2D(uHexPSampler, vec2(fract(xPos)/2.0+offset*0.5, fract(yPos)/2.0));
		hexMap = texture2D(uHexMap, vec2(fract(xPos)/2.0+offset*0.5, fract(yPos)/2.0));

		flatColor = texture2D(uAreaSampler, vec2(flatPos.x, flatPos.y));

		vec4 ter = texture2D(uSampler, vec2(vTextureCoord.s+1.0/128.0, vTextureCoord.t+1.0/128.0))*255.;
		vec4 terRt = texture2D(uSampler, vec2(vTextureCoord.s+2.0/128.0, vTextureCoord.t+1.0/128.0))*255.;
		vec4 terRtDn = texture2D(uSampler, vec2(vTextureCoord.s+2.0/128.0, vTextureCoord.t+2.0/128.0))*255.;
		vec4 terDn = texture2D(uSampler, vec2(vTextureCoord.s+1.0/128.0, vTextureCoord.t+2.0/128.0))*255.;
		vec4 screenVec = vec4(0.);


		if (ter.b == 0.) screenVec.r = 0.;
		else if (ter.b >= 1. && ter.b < 7.)	{screenVec.r=1.;}
		else if (ter.b <12.) screenVec.r=2.;
		else screenVec.r = 3.;

		if (terRt.b == 0.) screenVec.g = 0.;
		else if (terRt.b > 0. && terRt.b < 7.)	{screenVec.g=1.;}
		else if (terRt.b <12.) screenVec.g=2.;
		else screenVec.g = 3.;

		if (terRtDn.b == 0.) screenVec.b = 0.;
		else if (terRtDn.b > 0. && terRtDn.b < 7.)	{screenVec.b=1.;}
		else if (terRtDn.b <12.) screenVec.b=2.;
		else screenVec.b = 3.;

		if (terDn.b == 0.) screenVec.a == 0.;
		else if (terDn.b > 0. && terDn.b < 7.)	{screenVec.a=1.;}
		else if (terDn.b <12.) screenVec.a=2.;
		else screenVec.a = 3.;

		float refNum = screenVec.r*8.*8.*8.+screenVec.g*8.*8.+screenVec.b*8.+screenVec.a;
		float baseY = floor(refNum/256.);
		float baseX = (refNum-baseY*256.)/255.;

		vec4 baseRef = texture2D(uMaskSampler, vec2(baseX, baseY/255.));
		vec4 useMask = texture2D(uMaskSampler, vec2(255.*baseRef.g*0.125+mod(vVertexPosition.x*12.0,1.0)*0.125, 0.125*baseRef.r*255.+0.125+mod(vVertexPosition.y*12.0,1.0)*0.125));

		if (ptElevation <= 0.002) {
			vec4 bumpColor = texture2D(uBumpSampler, vec2(vVertexPosition.x/2., vVertexPosition.y/2.));
			vec3 lightDirection = normalize(worldLightDirection.xyz - vPosition.xyz);
			vec3 normal = normalize(vTransformedNormal+5.*bumpColor.r*vec3(0.,1.,0.));

			vec3 eyeDirection = normalize(-vPosition.xyz);
			vec3 reflectionDirection = reflect(-lightDirection, normal);

			float specularLightWeighting = pow(max(dot(reflectionDirection, eyeDirection), 0.0), 250.0);
			float diffuseLightWeighting = max(dot(normal, lightDirection), 0.0);

			//vec3 lightWeighting = vec3(0.20) + vec3(1.5) * 50.*specularLightWeighting + diffuseLightWeighting*vec3(0.9, 0.9, 0.9);
			vec3 lightWeighting = vec3(0.20) + vec3(1.0) * 50.*specularLightWeighting + 1.*vec3(0.9, 0.9, 0.9);

			gl_FragColor = vec4(vec3(0.1, bumpColor.r, 0.7)*lightWeighting, 1.0)*(1.0-flatColor.a) + flatColor;
			if (uUseColor == 1.) gl_FragColor = vec4(ter.b/12., 0.,0.,1.);
			}
		else {
			if (ptElevation > 0.25) {
				gl_FragColor = ((2.0 - ptElevation*4.0)*texture2D(uTSampler, vec2(0.75+fract(vVertexPosition.x*12.0)*0.25, 0.75+fract(vVertexPosition.y*12.0)*0.25)) + vec4(0.9, 0.9, 0.85, 1.0)*(1.0 - 2.0 + ptElevation*4.0)+vec4(normalWeight, 1.0))*(1.0-flatColor.a)+flatColor;
				}
			else if (vVertexNormal.y < 0.0) {
				// make stone face
				gl_FragColor = (texture2D(uTSampler, vec2(0.75+fract(vVertexPosition.x*12.0)*0.25, 0.75+fract(vVertexPosition.y*12.0)*0.25))+vec4(normalWeight, 1.0))*(1.0-flatColor.a)+flatColor;
				}
			else {
				vec4 addMarks = vec4(0.,0.,0.,0.);
				if (uUseColor == 1.) addMarks = max(vec4(0.),vec4(10.*(0.1 - fract(vVertexPosition.x*1.5)), 10.*(0.1 - fract(vVertexPosition.y*1.5)), 0., 1.)) ;
				//else vec4 addMarks = vec4(0.,0.,0.,0.);
				float xMod = mod(vVertexPosition.x,1.0);
				float yMod = mod(vVertexPosition.y,1.0);

				texColor = texture2D(uSampler, vec2(vTextureCoord.s+1.0/128.0, vTextureCoord.t+1.0/128.0));


				terColor = texture2D(uTSampler, vec2(mod(ter.b, 4.0)*0.25+xMod*0.25, floor(ter.b/4.0)*0.25+yMod*0.25));
				terColorDn = texture2D(uTSampler, vec2(mod(terDn.b*255.0, 4.0)*0.25+xMod*0.25, floor(terDn.b*255.0/4.0)*0.25+yMod*0.25));
				terColorDnRt = texture2D(uTSampler, vec2(mod(texColorDnRt.b*255.0, 4.0)*0.25+xMod*0.25, floor(texColorDnRt.b*255.0/4.0)*0.25+yMod*0.25));
				terColorRt = texture2D(uTSampler, vec2(mod(texColorRt.b*255.0, 4.0)*0.25+xMod*0.25, floor(texColorRt.b*255.0/4.0)*0.25+yMod*0.25));
				vec4 terColorGrass = texture2D(uTSampler, vec2(mod(8.0, 4.0)*0.25+xMod*0.25, floor(8.0/4.0)*0.25+yMod*0.25));
				float xFract = fract(vVertexPosition.x*3.0);
				float yFract = fract(vVertexPosition.y*3.0);

				//gl_FragColor = ((1.0 - borderMask.r - borderMask.g - borderMask.b)*terColor + borderMask.r*terColor + borderMask.g*terColor + borderMask.b*terColor)*0.65+vec4(normalWeight, 1.0);

				float useColor = ((ter.b*(1.-useMask.r)*(1.-useMask.g)*(1.-useMask.b))+terRt.b*useMask.r+terRtDn.b*useMask.g + terDn.b*useMask.b);
				terColor = texture2D(uTSampler, vec2(mod(useColor, 4.0)*0.25+xFract*0.25, floor(useColor/4.0)*0.25+yFract*0.25));
				//terColor = texture2D(uTSampler, vec2(mod(ter.b, 4.0)*0.25+xFract*0.25, floor(ter.b/4.0)*0.25+yFract*0.25));
					if (useColor == 0.) {
						vec4 bumpColor = texture2D(uBumpSampler, vec2(vVertexPosition.x/2., vVertexPosition.y/2.));
						vec3 lightDirection = normalize(worldLightDirection.xyz - vPosition.xyz);
						vec3 normal = normalize(vTransformedNormal+5.*bumpColor.r*vec3(0.,1.,0.));

						vec3 eyeDirection = normalize(-vPosition.xyz);
						vec3 reflectionDirection = reflect(-lightDirection, normal);

						float specularLightWeighting = pow(max(dot(reflectionDirection, eyeDirection), 0.0), 250.0);
						float diffuseLightWeighting = max(dot(normal, lightDirection), 0.0);

						//vec3 lightWeighting = vec3(0.20) + vec3(1.5) * 50.*specularLightWeighting + diffuseLightWeighting*vec3(0.9, 0.9, 0.9);
						vec3 lightWeighting = vec3(0.20) + vec3(1.0) * 50.*specularLightWeighting + 1.*vec3(0.9, 0.9, 0.9);

						gl_FragColor = vec4(vec3(0.1, bumpColor.r, 0.7)*lightWeighting, 1.0)*(1.0-flatColor.a)+flatColor+useMask*0.5*uHexOn+ addMarks;
						}
					else if (useColor >0. && useColor <7.) {
						float xFract = fract(vVertexPosition.x*6.0);
						float yFract = fract(vVertexPosition.y*6.0);
						vec4 grassColor = texture2D(uTSampler, vec2(0.75+xMod*0.25, 0.25+yMod*0.25));
						vec4 screenColor = texture2D(ufBumpSampler, vec2(vVertexPosition));

						gl_FragColor = vec4((terColor.rgb*(1.-screenColor.r) + terColorGrass.rgb*screenColor.r+normalWeight),(1.0-flatColor.a))*(1.0-flatColor.a)+flatColor+useMask*0.5*uHexOn + addMarks;
						}
					else {
						gl_FragColor = vec4(terColor.rgb+normalWeight, (1.0-flatColor.a))*(1.0-flatColor.a)+flatColor+useMask*0.5*uHexOn + addMarks;
						}
				//if (uUseColor == 1.) gl_FragColor = vec4(useColor/5., 0.,0.,1.)*uUseColor;
				//gl_FragColor = vec4(ter.b, 0.,0.,1.);
				}

			}

		}
</script>

<script id="shader-vs" type="x-shader/x-vertex">
    attribute vec2 aVertexPosition;
	attribute vec2 aTextureCoord;
	attribute vec3 aVertexNormal;

    uniform mat4 uMVMatrix;
    uniform mat4 uPMatrix;
    uniform mat3 uNMatrix;


	uniform float uTileNum;
	uniform float uTime;
	uniform vec3 uMapScale;
	uniform vec3 uMapOffset;
	uniform sampler2D uSampler;
	uniform sampler2D uTSampler;
	uniform sampler2D uOSampler;
	uniform sampler2D uBSampler;
	uniform sampler2D uHexPSampler;
	uniform sampler2D uRoadSampler;
	uniform sampler2D uHexMap;
	uniform sampler2D uBumpSampler;
	uniform sampler2D uNoiseSampler;
	uniform sampler2D uMaskSampler;

	uniform float uOffset;

	varying float ptElevation;
	varying float ptTerrain;
	varying float fOffset;
	varying float timeVal;

	varying vec4 texColor;
	varying vec4 screenPos;
	varying vec4 oceanColor;
	varying vec4 texColorDn;
	varying vec4 texColorRt;
	varying vec4 texColorDnRt;
	varying vec4 vPosition;
	varying vec4 worldLightDirection;
	varying vec2 vTextureCoord;
	varying vec3 vVertexNormal;
	varying vec3 vTransformedNormal;
	varying vec2 vVertexPosition;
	varying float vTileNum;
	//varying float vMapScale;
	varying float directionalLightWeighting;

	vec4 locRough;
	varying vec4 flatPos;
	vec4 flatCoord;

    void main(void) {
		oceanColor = texture2D(uOSampler, vec2(aVertexPosition.x/10., aVertexPosition.y/10.));
		texColor = texture2D(uSampler, vec2(aTextureCoord.s+1.0/128.0, aTextureCoord.t+1.0/128.0));


		if (texColor.r*texColor.b == 0.) {
			float offset = 1./256.;
			timeVal = uTime/600.;
			//vec4 hmC =  texture2D(uBumpSampler, vec2(vVertexPosition.x+timeVal, vVertexPosition.y)) - texture2D(uBumpSampler, vec2(vVertexPosition.x, vVertexPosition.y+timeVal));
			//vec4 hmL =  texture2D(uBumpSampler, vec2(vVertexPosition.x-offset+timeVal, vVertexPosition.y)) - texture2D(uBumpSampler, vec2(vVertexPosition.x-offset, vVertexPosition.y+timeVal));
			//vec4 hmU =  texture2D(uBumpSampler, vec2(vVertexPosition.x+timeVal, vVertexPosition.y+offset)) - texture2D(uBumpSampler, vec2(vVertexPosition.x, vVertexPosition.y+timeVal+offset));
			//vec4 hmR =  texture2D(uBumpSampler, vec2(vVertexPosition.x+offset+timeVal, vVertexPosition.y)) - texture2D(uBumpSampler, vec2(vVertexPosition.x+offset, vVertexPosition.y+timeVal));
			//vec4 hmD =  texture2D(uBumpSampler, vec2(vVertexPosition.x+timeVal, vVertexPosition.y-offset)) - texture2D(uBumpSampler, vec2(vVertexPosition.x, vVertexPosition.y-offset+timeVal));
			vVertexPosition = (aVertexPosition)/5.;
			vec4 hmC =  texture2D(uBumpSampler, vec2(vVertexPosition.x+timeVal, vVertexPosition.y));
			vec4 hmL =  texture2D(uBumpSampler, vec2(vVertexPosition.x-offset+timeVal, vVertexPosition.y));
			vec4 hmU =  texture2D(uBumpSampler, vec2(vVertexPosition.x+timeVal, vVertexPosition.y+offset));
			vec4 hmR =  texture2D(uBumpSampler, vec2(vVertexPosition.x+offset+timeVal, vVertexPosition.y));
			vec4 hmD =  texture2D(uBumpSampler, vec2(vVertexPosition.x+timeVal, vVertexPosition.y-offset));
			vec3 calcNorm = normalize (vec3(1.*(-hmR.r+hmL.r), 0.0157, 1.*(hmU.r-hmD.r)));

			//vPosition = uMVMatrix * vec4(aVertexPosition.x, (hmC.r-0.5)/100., aVertexPosition.y,  1.0);
			vPosition = uMVMatrix * vec4(aVertexPosition.x+uMapScale.y-uMapOffset.x, 0.0, aVertexPosition.y+uMapScale.z-uMapOffset.z,  1.0);

			worldLightDirection = uMVMatrix*vec4(1.0,10.0,-10.0,1.0);
			vec4 tmpPos = uPMatrix*vPosition;
			tmpPos.x *= uMapScale.x;
			tmpPos.y *= uMapScale.x;
			gl_Position = tmpPos;

			vTransformedNormal = uNMatrix * calcNorm;
			flatCoord = uPMatrix * uMVMatrix * vec4((aVertexPosition.x+uMapScale.y-uMapOffset.x), 0.0, (aVertexPosition.y+uMapScale.z-uMapOffset.z), 1.0);

			flatCoord.x *= uMapScale.x;
			flatCoord.y *= uMapScale.x;
			flatPos = vec4((flatCoord.x/flatCoord.w)*0.5+0.5, (flatCoord.y/flatCoord.w)*0.5+0.5, 0.0, 1.0);
			}
		else {
		locRough = uPMatrix * uMVMatrix * vec4((aVertexPosition.x+uMapScale.y-uMapOffset.x), texColor.r/2.0, (aVertexPosition.y+uMapScale.z-uMapOffset.z), 1.0);
        flatCoord = uPMatrix * uMVMatrix * vec4((aVertexPosition.x+uMapScale.y-uMapOffset.x), 0.0, (aVertexPosition.y+uMapScale.z-uMapOffset.z), 1.0);

		locRough.x *= uMapScale.x;
		locRough.y *= uMapScale.x;
		flatCoord.x *= uMapScale.x;
		flatCoord.y *= uMapScale.x;
		flatPos = vec4((flatCoord.x/flatCoord.w)*0.5+0.5, (flatCoord.y/flatCoord.w)*0.5+0.5, 0.0, 1.0);

		gl_Position = locRough;
		ptElevation = texColor.r;
		ptTerrain = texColor.b;
		fOffset = uOffset;

		vVertexNormal = aVertexNormal;

		vTileNum = uTileNum;}
		vTextureCoord = vec2(aTextureCoord.x, aTextureCoord.y);
		vVertexPosition = aVertexPosition;
		//vMapScale = uMapScale.x;
		}
</script>

<script id="buffer-fs" type="x-shader/x-fragment">
	precision mediump float;
	varying float vTileNum;
	varying vec2 vVertexPosition;

	void main(void) {
		gl_FragColor = vec4(vTileNum, vVertexPosition.x, vVertexPosition.y, 1.0);
		}
</script>
<script id="buffer-vs" type="x-shader/x-vertex">
	attribute vec2 aVertexPosition;
	attribute vec2 aTextureCoord;

    uniform mat4 uMVMatrix;
    uniform mat4 uPMatrix;

	uniform float uTileNum;
	uniform vec3 uMapScale;
	uniform vec3 uMapOffset;
	uniform sampler2D uSampler;


	varying float ptElevation;

	varying vec4 texColor;
	varying vec2 vVertexPosition;
	varying float vTileNum;

	vec4 locRough;

    void main(void) {
		texColor = texture2D(uSampler, vec2(aTextureCoord.s+1.0/128.0, aTextureCoord.t+1.0/128.0));
        locRough = uPMatrix * uMVMatrix * vec4((aVertexPosition.x+uMapScale.y-uMapOffset.x), texColor.r/2.0, (aVertexPosition.y+uMapScale.z-uMapOffset.z), 1.0);
		locRough.x *= uMapScale.x;
		locRough.y *= uMapScale.x;
		//gl_Position = (locRough.x, locRough.y, locRough.z, 1.0);
		gl_Position = locRough;
		ptElevation = texColor.r;
		vTileNum = uTileNum/255.0;
		vVertexPosition = aVertexPosition/10.0;
		}
</script>

<script id="riverFS" type="x-shader/x-fragment">
	precision mediump float;

	varying vec3 vVertexShade;
	varying float vRiverWidth;

    void main(void) {

		if (vVertexShade.y < vVertexShade.x) gl_FragColor = vec4(0.0, 0.0, 1.0, 1.0);
		else {
			float xDiff = vVertexShade.x-vVertexShade.y;
			if ((xDiff*xDiff+vVertexShade.z*vVertexShade.z) < vRiverWidth*vRiverWidth) gl_FragColor = vec4(0.0, 0.0, 1.0, 1.0);
			else discard;
		}

	//gl_FragColor = vec4(0.0, 0.0, 1.0, 1.0);
	}

</script>
<script id="riverVS" type="x-shader/x-vertex">
	attribute vec2 aVertexPosition;
    attribute vec3 aVertexShade;
    attribute vec4 aVertexColor;

    uniform mat4 uMVMatrix;
    uniform mat4 uPMatrix;
	uniform vec4 uMapOffset;
	uniform vec3 uMapScale;
	uniform float uRiverWidth;

    varying vec4 vColor;
    varying vec3 vVertexShade;
	vec4 locRough;
	varying float vRiverWidth;

    void main(void) {
		locRough = uPMatrix * uMVMatrix * vec4((aVertexPosition.x-uMapOffset.y)/(12.0*uMapScale.y), 0.0, (aVertexPosition.y-uMapOffset.w)/(12.0*uMapScale.y), 1.0);
		locRough.x *= uMapScale.x;
		locRough.y *= uMapScale.x;
		gl_Position = locRough;
		vVertexShade = aVertexShade;
		vRiverWidth = uRiverWidth;
    }
</script>
<script id="colorFS" type="x-shader/x-fragment">
precision mediump float;

	void main(void) {
		gl_FragColor = vec4(1.0,1.0,0.0,1.0);
		}
</script>
<script id="colorVS" type="x-shader/x-vertex">
	attribute vec3 aVertexPosition;
	uniform vec3 uMapScale;
	uniform vec3 uMapOffset;
	uniform mat4 uMVMatrix;
    uniform mat4 uPMatrix;

	vec4 locRough;
	void main(void) {

		locRough = uPMatrix * uMVMatrix * vec4((aVertexPosition.x), aVertexPosition.y, (aVertexPosition.z), 1.0);
		locRough.x *= uMapScale.x;
		locRough.y *= uMapScale.x;
		gl_Position = locRough;
		//gl_Position = vec4(aVertexPosition, 1.0);
		}
</script>

<script id="unitFS" type="x-shader/x-fragment">
precision mediump float;

	varying vec3 vPosition;
	varying vec3 uColor;

	void main(void) {
		gl_FragColor = vec4(uColor,1.0);
		}
</script>
<script id="unitVS" type="x-shader/x-vertex">
	attribute vec3 aVertexPosition;
	attribute vec3 aUnitLoc;
	uniform vec3 uMapScale;
	uniform vec4 uMapOffset;
	uniform mat4 uMVMatrix;
    uniform mat4 uPMatrix;

	varying vec3 vPosition;
	varying vec3 uColor;

	vec4 locRough;

	void main(void) {
		//locRough = uPMatrix * uMVMatrix * vec4((aVertexPosition.x+(aUnitLoc.x-uMapOffset.y)/(120.0*uMapScale.y)), aVertexPosition.y, (aVertexPosition.z+(aUnitLoc.y-uMapOffset.w)/(120.0*uMapScale.y)), 1.0);
		locRough = uPMatrix * uMVMatrix * vec4(aVertexPosition.x/uMapScale.y+(aUnitLoc.x-uMapOffset.y)/(12.0*uMapScale.y), aVertexPosition.y, aVertexPosition.z/uMapScale.y+(aUnitLoc.y-uMapOffset.w)/(12.0*uMapScale.y), 1.0);
		//locRough = uPMatrix * uMVMatrix * vec4((aVertexPosition.x+1.), aVertexPosition.y, (aVertexPosition.z), 1.0);
		locRough.x *= uMapScale.x;
		locRough.y *= uMapScale.x;
		gl_Position = locRough;
		//gl_Position = vec4(aVertexPosition, 1.0);
		vPosition = aVertexPosition;
		//uColor = vec3(mod(aUnitLoc.z,1000.)/1000., mod(aUnitLoc.z,100.)/100., mod(aUnitLoc.z,10.)/10.);
		uColor = vec3(1., floor(aUnitLoc.z/255.)/255., mod(aUnitLoc.z,255.)/255.);
		}
</script>

<script id="areaFS" type="x-shader/x-fragment">
precision mediump float;

    varying vec4 vColor;
    varying vec2 vCircleCenter;
	varying vec3 vVertexPosition;

	float rSq;
	varying float mag;

    void main(void) {
		rSq = 10000.0/mag;
		float xDiff = (vVertexPosition.x - vCircleCenter.x);
		float yDiff = (vVertexPosition.y - vCircleCenter.y);
		float dSq = xDiff*xDiff+yDiff*yDiff;
		if (dSq > rSq - 0.0) gl_FragColor = vec4(1.0, 1.0, 0.0/3.0, 0.5 - (dSq/rSq));
		else gl_FragColor = vec4(vColor.r, vColor.g, vColor.b, 0.5+0.15*floor(dSq*1.25/rSq));
		//else gl_FragColor = vec4(vColor.r, vColor.g, vColor.b, 1.0);
		//gl_FragColor = vec4(1.0, 0.0, 0.0, 1.0);
		}
</script>
<script id="areaVS" type="x-shader/x-vertex">
	attribute vec3 aVertexPosition;
    attribute vec3 aCircleColor;
    attribute vec2 aCircleCenter;

    uniform mat4 uMVMatrix;
    uniform mat4 uPMatrix;
	uniform vec4 uMapOffset;
	uniform vec3 uMapScale;

    varying vec4 vColor;
    varying vec3 vVertexPosition;
    varying vec2 vCircleCenter;
	vec4 locRough;
	vec2 centerScaled;

	varying float mag;

    void main(void) {
		locRough = uPMatrix * uMVMatrix * vec4((aVertexPosition.x-uMapOffset.y)/(12.0*uMapScale.y), 0.0, (aVertexPosition.y-uMapOffset.w)/(12.0*uMapScale.y), 1.0);
		locRough.x *= uMapScale.x;
		locRough.y *= uMapScale.x;
		gl_Position = locRough;
        vColor = vec4(aCircleColor, 1.0);
        //vColor = vec4(0.0, 1.0, 0.0, 1.0);
		vCircleCenter = aCircleCenter;
		vVertexPosition = aVertexPosition;
		mag = uMapScale.y;
		mag = 1.0;
    }
</script>

<script id="treeFS" type="x-shader/x-fragment">
precision mediump float;
	varying float vHeight;
	varying vec2 texPos;
	uniform sampler2D uTSampler;
	vec4 texColor;
	void main(void) {
		texColor = texture2D(uTSampler, texPos);
		if (texColor.a > 0.75)	gl_FragColor = texColor;
		else discard;
		//gl_FragColor = texColor;
		}
</script>
<script id="treeVS" type="x-shader/x-vertex">
	attribute vec3 aVertexPosition;
	attribute float aTreeOffset;

    uniform mat4 uMVMatrix;
    uniform mat4 uPMatrix;

	uniform vec3 uMapScale;
	uniform vec3 uMapOffset;
	uniform sampler2D uSampler;
	uniform sampler2D uTSampler;
	uniform float uOffset;

	varying float vTileNum;
	//varting vec3 vVertexPos;
	vec4 color1;
	vec4 color2;
	vec4 color3;

	vec4 locRough;
	vec4 tmpPos;
	float height;
	varying float vHeight;
	varying vec2 texPos;

    void main(void) {
		float xFract = fract(aVertexPosition.x*12.0);
		float zFract = fract(aVertexPosition.z*12.0);
		color1 = texture2D(uSampler, vec2(aVertexPosition.x*0.09375+1.0/128.0, aVertexPosition.z*0.09375+1.0/128.0));
		height = color1.r;
		tmpPos = uMVMatrix * vec4((aVertexPosition.x+uMapScale.y-uMapOffset.x), aVertexPosition.y+height/2.0, (aVertexPosition.z+uMapScale.z-uMapOffset.z), 1.0);
		locRough = uPMatrix * vec4(tmpPos.x+aTreeOffset, tmpPos.y, tmpPos.z, 1.0);

		locRough.x *= uMapScale.x;
		locRough.y *= uMapScale.x;
		gl_Position = locRough;
		//vVertexPos = aVertexPos;
		vHeight = height;
		texPos = vec2((aTreeOffset+0.02)/0.04, 1.0-aVertexPosition.y/0.1);
		}
</script>

<script id="oceanFS" type="x-shader/x-fragment">
	 precision mediump float;

	uniform sampler2D uSampler;

	varying float directionalLightWeighting;
	varying vec3 vNorm;
	varying vec4 vColor;
	varying vec2 vPos;

    void main(void) {
		float reflection = dot(vNorm, vec3(0.0, -0.707, -0.707));
		gl_FragColor = vColor;
		//gl_FragColor = vec4(vPos.y, 0., 0.0, 1.0);
		}
</script>
<script id="oceanVS" type="x-shader/x-vertex">
	attribute vec2 aVertexPosition;

	uniform sampler2D uSampler;
	//uniform sampler2D uSampler2;
	uniform float uTime;

	varying float directionalLightWeighting;
	varying vec3 vNorm;
	varying vec4 vColor;
	varying vec2 vPos;

    void main(void) {
        float timeOff = uTime/600.;
        //float timeOff = 0.;
		float imgSize = 256.0;
		vec2 tPos = vec2(aVertexPosition.x, aVertexPosition.y);
	    //vec4 hmUp = texture2D(uSampler, vec2(tPos.x+timeOff, tPos.y-1./imgSize)) - texture2D(uSampler2, vec2(tPos.x, tPos.y-1./imgSize));
	    //vec4 hmDn = texture2D(uSampler, vec2(tPos.x+timeOff, tPos.y+1./imgSize)) - texture2D(uSampler2, vec2(tPos.x, tPos.y+1./imgSize));
	    //vec4 hmLt = texture2D(uSampler, vec2(tPos.x+timeOff-1./imgSize, tPos.y)) - texture2D(uSampler2, vec2(tPos.x-1./imgSize, tPos.y));
	    //vec4 hmRt = texture2D(uSampler, vec2(tPos.x+timeOff+1./imgSize, tPos.y)) - texture2D(uSampler2, vec2(tPos.x+1./imgSize, tPos.y));
	    //vec4 hmC = texture2D(uSampler, vec2(tPos.x+timeOff, tPos.y)) - texture2D(uSampler2, vec2(tPos.x, tPos.y));

			vec4 hmUp = texture2D(uSampler, vec2(tPos.x+timeOff, tPos.y-1./imgSize));
	    vec4 hmDn = texture2D(uSampler, vec2(tPos.x+timeOff, tPos.y+1./imgSize));
	    vec4 hmLt = texture2D(uSampler, vec2(tPos.x+timeOff-1./imgSize, tPos.y));
	    vec4 hmRt = texture2D(uSampler, vec2(tPos.x+timeOff+1./imgSize, tPos.y));
	    vec4 hmC = texture2D(uSampler, vec2(tPos.x+timeOff, tPos.y));

		vNorm = normalize(vec3(2.*(hmRt.r-hmLt.r), 0.01569, 2.*(hmUp.r-hmDn.r)));

		float finalHeight = hmC.r;
		gl_Position = vec4(tPos.x*2.-1., tPos.y*2.-1., 0., 1.0);
		vColor = vec4(finalHeight, vNorm);
		vPos = tPos;
		//vColor = vec4(1.0, 1.0, 0.0, 1.0);
    }
</script>

<script type="text/javascript">

	function ncode_div(el_id) {
				if (typeof(el_id) == "string") trg = document.getElementById(el_id);
				else trg = el_id;
         var x = trg.getElementsByTagName("script");
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

	var groupList = new Array();
	function groupSelect(selNum) {
		//alert(selNum);
		dupe = false;
		for (i=0; i<groupList.length; i++) {
			if (groupList[i] == selNum) {
				dupe = true;
				groupList.splice(i, 1);
				document.getElementById("selOpt_"+selNum).className="unselected";
				break;
			}
		}
		if (!dupe) {
			groupList.push(selNum);
			document.getElementById("selOpt_"+selNum).className="selected";
		}
	}

	var moveString = new Array();
	var umList = [];
	var umFauxVerts = [];
	var drawLoc = [];

	function resetMove(x, y) {
		moveString.splice(1, moveString.length+1);
		umList = [];
		umFauxVerts = [];
		drawLoc = [x,y,x,y];
		moveLength = 0;

	}

	xMoves = [0, -1, 0, 1, -1, 0, 1, -1, 0, 1];
	yMoves = [0, 1, 1, 1, 0, 0, 0, -1, -1, -1];
	function move(val) {
		lineWidth = 0.75;
		if (val < 10) {
			moveString.push(val);
			//alert(moveString);
			mag = Math.sqrt(xMoves[val]*xMoves[val]+yMoves[val]*yMoves[val]);

			normx = xMoves[val]/mag;
			normy = yMoves[val]/mag;

			umList.push(drawLoc[0]-normy*lineWidth, drawLoc[1]+normx*lineWidth,
			drawLoc[0]-normy*lineWidth, drawLoc[1]+normx*lineWidth,
			drawLoc[0]+normy*lineWidth, drawLoc[1]-normx*lineWidth);

			drawLoc[0] += xMoves[val]*2;
			drawLoc[1] += yMoves[val]*2;

			umList.push(drawLoc[0]-normy*lineWidth+normx*lineWidth, drawLoc[1]+normx*lineWidth+normy*lineWidth,
			drawLoc[0]+normy*lineWidth+normx*lineWidth, drawLoc[1]-normx*lineWidth+normy*lineWidth,
			drawLoc[0]+normy*lineWidth+normx*lineWidth, drawLoc[1]-normx*lineWidth+normy*lineWidth);

			umFauxVerts.push(0,0,0,
			mag,0,lineWidth,
			mag,0,-lineWidth,
			mag,mag+lineWidth,lineWidth,
			mag,mag+lineWidth,-lineWidth,
			0,0,0);
		} else {
			//alert(val + ", " + moveString.length + "/" + moveString);
			if (val == 10 && moveString.length > 2) {
				lastMove = moveString.pop();
				//moveString = moveString.slice(0,-1);
				umList = umList.slice(0,-12);
				umFauxVerts = umFauxVerts.slice(0,-18);

				drawLoc[0] -= xMoves[lastMove]*2;
				drawLoc[1] -= yMoves[lastMove]*2;

			} else {
				moveString.splice(1, moveString.length+1);
				umList = [];
				umFauxVerts = [];
				drawLoc = [drawLoc[2], drawLoc[3], drawLoc[2], drawLoc[3]];
				moveLength = 0;
				}
		}

		moveLength = umList.length/2.0;
		gl.bindBuffer(gl.ARRAY_BUFFER, moveLine);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(umList), gl.STATIC_DRAW);

		gl.bindBuffer(gl.ARRAY_BUFFER, moveVerts);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(umFauxVerts), gl.STATIC_DRAW);
	}

	function loadMove(startA, steps, times) {
		lineWidth = 0.25;

		//rpList = new Array();
		//umFauxVerts = new Array();
		//resetCount=0;
		//xMoves = [0, -1, 0, 1, -1, 0, 1, -1, 0, 1];
		//yMoves = [0, 1, 1, 1, 0, 0, 0, -1, -1, -1];

		for (var j=0; j<steps.length; j++) {
			mag = Math.sqrt(xMoves[steps[j]]*xMoves[steps[j]]+yMoves[steps[j]]*yMoves[steps[j]]);
			mag = Math.sqrt(xMoves[1]*xMoves[1]+yMoves[1]*yMoves[1]);
			normx = xMoves[steps[j]]/mag;
			normy = yMoves[steps[j]]/mag;

			normx = xMoves[1]/mag;
			normy = yMoves[1]/mag;

			umList.push(startA[0]-normy*lineWidth, startA[1]+normx*lineWidth,
			startA[0]-normy*lineWidth, startA[1]+normx*lineWidth,
			startA[0]+normy*lineWidth, startA[1]-normx*lineWidth);
			//startA[0] += xMoves[steps[j]];
			//startA[1] += yMoves[steps[j]];

			startA[0] += xMoves[1];
			startA[1] += yMoves[1];
			umList.push(startA[0]-normy*lineWidth+normx*lineWidth, startA[1]+normx*lineWidth+normy*lineWidth,
			startA[0]+normy*lineWidth+normx*lineWidth, startA[1]-normx*lineWidth+normy*lineWidth,
			startA[0]+normy*lineWidth+normx*lineWidth, startA[1]-normx*lineWidth+normy*lineWidth);

			umFauxVerts.push(0,0,0,
			mag,0,lineWidth,
			mag,0,-lineWidth,
			mag,mag+lineWidth,lineWidth,
			mag,mag+lineWidth,-lineWidth,
			0,0,0);
		}

		moveLength = umList.length/2.0;
		gl.bindBuffer(gl.ARRAY_BUFFER, moveLine);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(umList), gl.STATIC_DRAW);

		gl.bindBuffer(gl.ARRAY_BUFFER, moveVerts);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(umFauxVerts), gl.STATIC_DRAW);

	}

	function orderMove() {
		//alert(moveString.toString());
		var sendString = "";
		for (var i=1; i<moveString.length; i++) {
			sendString = sendString + moveString[i];
		}
		//alert(sendString);
		scrMod("1045,"+moveString[0]+","+sendString);
	}


	function passClick(val, trg) {
		params = "val1="+val;
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open("POST", "gameScr.php?gid='.$_GET['gameID'].'", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		xmlhttp.onreadystatechange = function() {
			if (typeof(trg) == "string") target = document.getElementById(trg);
			else target = trg;
			//console.log("send to " + target);
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				//console.log("sending...");
				target.innerHTML = xmlhttp.response;
				//console.log("sent");
				ncode_div(target);
				//console.log("ncoding");
				}
			}

		xmlhttp.send(params);
		}

	function scrMod(val) {
		params = "val1="+val;
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open("POST", "gameScr.php?gid='.$_GET['gameID'].'", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("scrBox").innerHTML = xmlhttp.response;
				ncode_div("scrBox");
				}
			}

		xmlhttp.send(params);
		}

	function showBar() {

		}

		var zCount=0;
	function makeBox(bName, val, h, w, x, y) {
		console.log(arguments);
		e = window.event || arguments[0];
		console.log(window.event);
		useDeskTop.newPane(bName);
		//console.log(bName + " = " + useDeskTop.getPane(bName));
		//console.log("passClick to " + useDeskTop.getPane(bName));
		//console.log("event: " + event);
		//e.stopPropagation();
		passClick(val, useDeskTop.getPane(bName));
		}

	function closeBox() {
		/*
		//this.parentNode.parentNode.removeChild(this.parentNode);
		var scanBox = this.parentNode.contentBox;
		//console.log("check " + this.parentNode.contentBox.id);
		for (i=0; i<=scanBox.childNodes.length; i++) {
			console.log(typeof(scanBox.childNodes[i]));
			//if (scanBox.childNodes[i].hasAttribute("data-unitid")) console.log("found");
		}

		*/
		this.parentNode.remove();
		//this.remove();
	}

	function killBox(trg) {
		console.log(trg + " name is " + trg.nodeName)
		if (trg.nodeName == "DIV") {
			testNode = trg;

		} else {

			testNode = this;
		}
		while (testNode.parentNode.nodeName != "BODY") {
			testNode = testNode.parentNode;
			if (testNode.parentNode.parentObj) {
				console.log(testNode.parentNode.parentObj + " found");
				break;
			}
		}
		//testNode.remove();
		console.log("destroying " + testNode.parentNode.parentObj.nodeType + "  via " + testNode);
		testNode.parentNode.parentObj.destroyWindow();
	}

	var taskList = new Array();
	function startTask() {
		taskList.push(new Date().getTime(), 10, 1);
		}

    var gl;
	var ANGLEia;
	var tileNormals = new Array();
	var tileForrests = new Array();
	var forrestSizes = new Array();

    function initGL(canvas) {
        try {
            gl = canvas.getContext("webgl");
            gl.viewportWidth = canvas.width;
            gl.viewportHeight = canvas.height;
			ANGLEia = gl.getExtension("ANGLE_instanced_arrays"); // Vendor prefixes may apply!
        } catch (e) {
        }
        if (!gl) {
            alert("Could not initialise WebGL, sorry :-(");
        }
    }
	var tileDat;
	function getData(rTrg, prm, tTrg)
		{
		var tot_length = 0;
		params = "val1="+prm.join();
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open("POST", rTrg, true);

		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		xmlhttp.responseType = "arraybuffer";

		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

				loadRivers(prm, xmlhttp.response, tTrg);
				}
			}
		xmlhttp.send(params);
		//return xmlhttp.response.byteLength;
		}


	function getMaxOfArray(numArray) {
		return Math.max.apply(null, numArray);
		}
	var lastHeights;
	function fill_buffers() {
		}
	var heightMaps = new Array();
	function handleMapTextures(texture, x, y, tileNum) {
		var imageDat = ctx.getImageData(x, y, 128, 128);
		var pixDat = imageDat.data;

        gl.bindTexture(gl.TEXTURE_2D, texture);
        gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, false);

        gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, imageDat);

        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.NEAREST);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.NEAREST);
        gl.bindTexture(gl.TEXTURE_2D, null);
		var newNormals = new Array();
		var newForrest = new Array();
		// i = row, j = column
		for (var i=0; i<121; i++) {
			for (var j=0; j<121; j++) {
				baseRef = 4*((i+1)*128+j+1);
				tmpVec = [-pixDat[baseRef-4*128]+pixDat[baseRef-4*128+4]-2*pixDat[baseRef-4]+2*pixDat[baseRef+4]-pixDat[baseRef+128*4-4]+pixDat[baseRef+128*4], 10.25, -2*pixDat[baseRef-128*4]-pixDat[baseRef-128*4+4]-pixDat[baseRef-4]+pixDat[baseRef+4]+pixDat[baseRef+128*4-4]+2*pixDat[baseRef+128*4]];
				tmpVec = vec3.normalize(tmpVec)
				newNormals.push(tmpVec[0], tmpVec[1], tmpVec[2]);
				//newNormals = newNormals.concat([1.0, 1.0, 0.0]);
				if (pixDat[baseRef+2] > 0 && pixDat[baseRef+2] <7) {
					newForrest.push(0.0+j/12, 0.0, 0.0+i/12,
								0.0+j/12, 0.0, 0.0+i/12,
								0.0+j/12, 0.1, 0.0+i/12,
								0.0+j/12, 0.0, 0.0+i/12,
								0.0+j/12, 0.1, 0.0+i/12,
								0.0+j/12, 0.1, 0.0+i/12);
					}
				}
			}

		tileNormals[tileNum] = gl.createBuffer();
		gl.bindBuffer(gl.ARRAY_BUFFER, tileNormals[tileNum]);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(newNormals), gl.STATIC_DRAW);

		tileForrests[tileNum] = gl.createBuffer();
		gl.bindBuffer(gl.ARRAY_BUFFER, tileForrests[tileNum]);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(newForrest), gl.STATIC_DRAW);
		//alert(newForrest.length);
		forrestSizes[tileNum] = newForrest.length/3;
		//alert(forrestSizes[tileNum])
		}


    var tileTextures = new Array();
	var textureList = new Array();
    function mapTextures(i, x, y) {

        tileTextures[i] = gl.createTexture();
		handleMapTextures(tileTextures[i], x, y, i);
        tileTextures.image = tileCanvas;
		}

	function handleLoadedTexture(texture) {
        gl.bindTexture(gl.TEXTURE_2D, texture);
        gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, false);
        gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, texture.image);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.NEAREST);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.NEAREST);
        gl.bindTexture(gl.TEXTURE_2D, null);

		}
	var loadedImages = 0;
	var requiredImages = 0;
	function loadTexture(textureNumber, src) {

		requiredImages++;
		textureList[textureNumber].image = new Image();
        textureList[textureNumber].image.onload = function () {
            handleLoadedTexture(textureList[textureNumber]);
						loadedImages++;
						if (loadedImages == requiredImages) {initBuffers();}
			}
        textureList[textureNumber].image.src = src;

		}

    function getShader(gl, id) {
        var shaderScript = document.getElementById(id);
        if (!shaderScript) {
            return null;
        }

        var str = "";
        var k = shaderScript.firstChild;
        while (k) {
            if (k.nodeType == 3) {
                str += k.textContent;
            }
            k = k.nextSibling;
        }

        var shader;
        if (shaderScript.type == "x-shader/x-fragment") {
            shader = gl.createShader(gl.FRAGMENT_SHADER);
        } else if (shaderScript.type == "x-shader/x-vertex") {
            shader = gl.createShader(gl.VERTEX_SHADER);
        } else {
            return null;
        }

        gl.shaderSource(shader, str);
        gl.compileShader(shader);

        if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
            alert(gl.getShaderInfoLog(shader));
            return null;
        }

        return shader;
    }


    var shaderProgram;
    var bufferProgram;
	var riverProgram;
	var colorProgram;
	var unitProgram;
	var treeProgram;
	var areaProgram;
	var oceanTexProgram;

    function initShaders() {

		// <--- ocean program --->

		var fragShader = getShader(gl, "oceanFS");
        var vertShader = getShader(gl, "oceanVS");
		oceanTexProgram = gl.createProgram();
        gl.attachShader(oceanTexProgram, vertShader);
        gl.attachShader(oceanTexProgram, fragShader);
        gl.linkProgram(oceanTexProgram);

        if (!gl.getProgramParameter(oceanTexProgram, gl.LINK_STATUS)) {
            alert("Could not initialise shaders - ocean");
        }
		gl.useProgram(oceanTexProgram);
		oceanTexProgram.vertexPositionAttribute = gl.getAttribLocation(oceanTexProgram, "aVertexPosition");
        gl.enableVertexAttribArray(oceanTexProgram.vertexPositionAttribute);

		oceanTexProgram.samplerUniform = gl.getUniformLocation(oceanTexProgram, "uSampler");
		//oceanTexProgram.samplerUniformf = gl.getUniformLocation(oceanTexProgram, "uSampler2");
		oceanTexProgram.timeUniform = gl.getUniformLocation(oceanTexProgram, "uTime");


		// <--- buffer program --->

		var fragShader = getShader(gl, "buffer-fs");
        var vertShader = getShader(gl, "buffer-vs");
		bufferProgram = gl.createProgram();
        gl.attachShader(bufferProgram, vertShader);
        gl.attachShader(bufferProgram, fragShader);
        gl.linkProgram(bufferProgram);

        if (!gl.getProgramParameter(bufferProgram, gl.LINK_STATUS)) {
            alert("Could not initialise shaders - buffer");
        }
		gl.useProgram(bufferProgram);
        bufferProgram.VPAttribute = gl.getAttribLocation(bufferProgram, "aVertexPosition");
        gl.enableVertexAttribArray(bufferProgram.VPAttribute);

		bufferProgram.textureCoordAttribute = gl.getAttribLocation(bufferProgram, "aTextureCoord");
        gl.enableVertexAttribArray(bufferProgram.textureCoordAttribute);

        bufferProgram.pMatrixUniform = gl.getUniformLocation(bufferProgram, "uPMatrix");
        bufferProgram.mvMatrixUniform = gl.getUniformLocation(bufferProgram, "uMVMatrix");

		bufferProgram.samplerUniform = gl.getUniformLocation(bufferProgram, "uSampler");
		bufferProgram.tileNumberUniform = gl.getUniformLocation(bufferProgram, "uTileNum");
		bufferProgram.scaleUniform = gl.getUniformLocation(bufferProgram, "uMapScale");
		bufferProgram.offsetUniform = gl.getUniformLocation(bufferProgram, "uMapOffset");

		// <--- tree program --->

		var fragShader = getShader(gl, "treeFS");
        var vertShader = getShader(gl, "treeVS");
		treeProgram = gl.createProgram();
        gl.attachShader(treeProgram, vertShader);
        gl.attachShader(treeProgram, fragShader);
        gl.linkProgram(treeProgram);

        if (!gl.getProgramParameter(treeProgram, gl.LINK_STATUS)) {
            alert("Could not initialise shaders - tree");
        }
		gl.useProgram(treeProgram);
        treeProgram.VPAttribute = gl.getAttribLocation(treeProgram, "aVertexPosition");
        gl.enableVertexAttribArray(treeProgram.VPAttribute);

		treeProgram.tOAttribute = gl.getAttribLocation(treeProgram, "aTreeOffset");
        gl.enableVertexAttribArray(treeProgram.tOAttribute);

        treeProgram.pMatrixUniform = gl.getUniformLocation(treeProgram, "uPMatrix");
        treeProgram.mvMatrixUniform = gl.getUniformLocation(treeProgram, "uMVMatrix");

		treeProgram.samplerUniform = gl.getUniformLocation(treeProgram, "uSampler");
		treeProgram.treeSampler = gl.getUniformLocation(treeProgram, "uTSampler");
		treeProgram.scaleUniform = gl.getUniformLocation(treeProgram, "uMapScale");
		treeProgram.offsetUniform = gl.getUniformLocation(treeProgram, "uMapOffset");

		var fragShader = getShader(gl, "buffer-fs");
        var vertShader = getShader(gl, "buffer-vs");
		bufferProgram = gl.createProgram();
        gl.attachShader(bufferProgram, vertShader);
        gl.attachShader(bufferProgram, fragShader);
        gl.linkProgram(bufferProgram);

        if (!gl.getProgramParameter(bufferProgram, gl.LINK_STATUS)) {
            alert("Could not initialise shaders - buffer");
        }
		gl.useProgram(bufferProgram);
        bufferProgram.VPAttribute = gl.getAttribLocation(bufferProgram, "aVertexPosition");
        gl.enableVertexAttribArray(bufferProgram.VPAttribute);

		bufferProgram.textureCoordAttribute = gl.getAttribLocation(bufferProgram, "aTextureCoord");
        gl.enableVertexAttribArray(bufferProgram.textureCoordAttribute);

        bufferProgram.pMatrixUniform = gl.getUniformLocation(bufferProgram, "uPMatrix");
        bufferProgram.mvMatrixUniform = gl.getUniformLocation(bufferProgram, "uMVMatrix");

		bufferProgram.samplerUniform = gl.getUniformLocation(bufferProgram, "uSampler");
		bufferProgram.tileNumberUniform = gl.getUniformLocation(bufferProgram, "uTileNum");
		bufferProgram.scaleUniform = gl.getUniformLocation(bufferProgram, "uMapScale");
		bufferProgram.offsetUniform = gl.getUniformLocation(bufferProgram, "uMapOffset");

        var fragmentShader = getShader(gl, "shader-fs");
        var vertexShader = getShader(gl, "shader-vs");

        shaderProgram = gl.createProgram();
        gl.attachShader(shaderProgram, vertexShader);
        gl.attachShader(shaderProgram, fragmentShader);
        gl.linkProgram(shaderProgram);

        if (!gl.getProgramParameter(shaderProgram, gl.LINK_STATUS)) {
            alert("Could not initialise shaders");
        }
		gl.useProgram(shaderProgram);
        shaderProgram.vertexPositionAttribute = gl.getAttribLocation(shaderProgram, "aVertexPosition");
        gl.enableVertexAttribArray(shaderProgram.vertexPositionAttribute);

		shaderProgram.tileNumberUniform = gl.getUniformLocation(shaderProgram, "uTileNum");
		shaderProgram.scaleUniform = gl.getUniformLocation(shaderProgram, "uMapScale");
		shaderProgram.offsetUniform = gl.getUniformLocation(shaderProgram, "uMapOffset");

        shaderProgram.pMatrixUniform = gl.getUniformLocation(shaderProgram, "uPMatrix");
        shaderProgram.mvMatrixUniform = gl.getUniformLocation(shaderProgram, "uMVMatrix");
        shaderProgram.nMatrixUniform = gl.getUniformLocation(shaderProgram, "uNMatrix");

		shaderProgram.textureCoordAttribute = gl.getAttribLocation(shaderProgram, "aTextureCoord");
        gl.enableVertexAttribArray(shaderProgram.textureCoordAttribute);

		shaderProgram.normalAttribute = gl.getAttribLocation(shaderProgram, "aVertexNormal");
    gl.enableVertexAttribArray(shaderProgram.normalAttribute);

    shaderProgram.samplerUniform = gl.getUniformLocation(shaderProgram, "uSampler");
    shaderProgram.hexPatternSampler = gl.getUniformLocation(shaderProgram, "uHexPSampler");
    shaderProgram.terrainSampler = gl.getUniformLocation(shaderProgram, "uTSampler");
    shaderProgram.oceanSampler = gl.getUniformLocation(shaderProgram, "uOSampler");
    shaderProgram.areaSampler = gl.getUniformLocation(shaderProgram, "uAreaSampler");
    shaderProgram.borderSampler = gl.getUniformLocation(shaderProgram, "uBSampler");
    shaderProgram.roadSampler = gl.getUniformLocation(shaderProgram, "uRoadSampler");
    shaderProgram.plainsSampler = gl.getUniformLocation(shaderProgram, "uPlainsSampler");
    shaderProgram.grassSampler = gl.getUniformLocation(shaderProgram, "uGrassSampler");
		shaderProgram.mover = gl.getUniformLocation(shaderProgram, "uOffset");
		shaderProgram.hexOn = gl.getUniformLocation(shaderProgram, "uHexOn");
		shaderProgram.useOn = gl.getUniformLocation(shaderProgram, "uUseColor");
		shaderProgram.hexMap = gl.getUniformLocation(shaderProgram, "uHexMap");

		shaderProgram.timeUniform = gl.getUniformLocation(shaderProgram, "uTime");
		shaderProgram.bumpUniform = gl.getUniformLocation(shaderProgram, "uBumpSampler");
		shaderProgram.fBumpUniform = gl.getUniformLocation(shaderProgram, "ufBumpSampler");
		shaderProgram.noiseUniform = gl.getUniformLocation(shaderProgram, "uNoiseSampler");
		shaderProgram.maskUniform = gl.getUniformLocation(shaderProgram, "uMaskSampler");

		//////
		var fragmentShader = getShader(gl, "riverFS");
        var vertexShader = getShader(gl, "riverVS");
		riverProgram = gl.createProgram();

        gl.attachShader(riverProgram, vertexShader);
        gl.attachShader(riverProgram, fragmentShader);
        gl.linkProgram(riverProgram);

        if (!gl.getProgramParameter(riverProgram, gl.LINK_STATUS)) {
            alert("Could not initialise shaders - river");
			}

        gl.useProgram(riverProgram);
		riverProgram.pMatrixUniform = gl.getUniformLocation(riverProgram, "uPMatrix");
        riverProgram.mvMatrixUniform = gl.getUniformLocation(riverProgram, "uMVMatrix");

		riverProgram.vertexPositionAttribute = gl.getAttribLocation(riverProgram, "aVertexPosition");
        gl.enableVertexAttribArray(riverProgram.vertexPositionAttribute);

		riverProgram.shadePoints = gl.getAttribLocation(riverProgram, "aVertexShade");
        gl.enableVertexAttribArray(riverProgram.shadePoints);

		riverProgram.scaleUniform = gl.getUniformLocation(riverProgram, "uMapScale");
		riverProgram.offsetUniform = gl.getUniformLocation(riverProgram, "uMapOffset");
		riverProgram.widthUniform = gl.getUniformLocation(riverProgram, "uRiverWidth");


		///// - color Program
		var fragmentShader = getShader(gl, "colorFS");
        var vertexShader = getShader(gl, "colorVS");
		colorProgram = gl.createProgram();

        gl.attachShader(colorProgram, vertexShader);
        gl.attachShader(colorProgram, fragmentShader);
        gl.linkProgram(colorProgram);

        if (!gl.getProgramParameter(colorProgram, gl.LINK_STATUS)) {
            alert("Could not initialise shaders");
			}

        gl.useProgram(colorProgram);
		colorProgram.pMatrixUniform = gl.getUniformLocation(colorProgram, "uPMatrix");
        colorProgram.mvMatrixUniform = gl.getUniformLocation(colorProgram, "uMVMatrix");

		colorProgram.vertexPositionAttribute = gl.getAttribLocation(colorProgram, "aVertexPosition");
        gl.enableVertexAttribArray(colorProgram.vertexPositionAttribute);

		colorProgram.scaleUniform = gl.getUniformLocation(colorProgram, "uMapScale");
		colorProgram.offsetUniform = gl.getUniformLocation(colorProgram, "uMapOffset");

		colorProgram.mover = gl.getUniformLocation(colorProgram, "uOffset");

		///// - UNIT Program
		var fragmentShader = getShader(gl, "unitFS");
    var vertexShader = getShader(gl, "unitVS");
		unitProgram = gl.createProgram();

        gl.attachShader(unitProgram, vertexShader);
        gl.attachShader(unitProgram, fragmentShader);
        gl.linkProgram(unitProgram);

        if (!gl.getProgramParameter(unitProgram, gl.LINK_STATUS)) {
            alert("Could not unit shaders");
			}

        gl.useProgram(unitProgram);


		unitProgram.vertexPositionAttribute = gl.getAttribLocation(unitProgram, "aVertexPosition");
        gl.enableVertexAttribArray(unitProgram.vertexPositionAttribute);

		unitProgram.pointLocation = gl.getAttribLocation(unitProgram, "aUnitLoc");
		gl.enableVertexAttribArray(unitProgram.pointLocation);

		//unitProgram.dummyLocation = gl.getAttribLocation(unitProgram, "aDummyThing");
		//gl.enableVertexAttribArray(unitProgram.dummyLocation);

		unitProgram.scaleUniform = gl.getUniformLocation(unitProgram, "uMapScale");
		unitProgram.offsetUniform = gl.getUniformLocation(unitProgram, "uMapOffset");
		unitProgram.pMatrixUniform = gl.getUniformLocation(unitProgram, "uPMatrix");
        unitProgram.mvMatrixUniform = gl.getUniformLocation(unitProgram, "uMVMatrix");


		// < --- AREA PROGRAM --- >

		var fragmentShader = getShader(gl, "areaFS");
    var vertexShader = getShader(gl, "areaVS");

    areaProgram = gl.createProgram();
    gl.attachShader(areaProgram, vertexShader);
    gl.attachShader(areaProgram, fragmentShader);
    gl.linkProgram(areaProgram);

    if (!gl.getProgramParameter(areaProgram, gl.LINK_STATUS)) {
        alert("Could not initialise shaders");
    }

    gl.useProgram(areaProgram);

    areaProgram.vertexPositionAttribute = gl.getAttribLocation(areaProgram, "aVertexPosition");
    gl.enableVertexAttribArray(areaProgram.vertexPositionAttribute);

		areaProgram.circleCenterAttribute = gl.getAttribLocation(areaProgram, "aCircleCenter");
    gl.enableVertexAttribArray(areaProgram.circleCenterAttribute);

		areaProgram.circleColorAttribute = gl.getAttribLocation(areaProgram, "aCircleColor");
    gl.enableVertexAttribArray(areaProgram.circleColorAttribute);

    areaProgram.pMatrixUniform = gl.getUniformLocation(areaProgram, "uPMatrix");
    areaProgram.mvMatrixUniform = gl.getUniformLocation(areaProgram, "uMVMatrix");
		areaProgram.scaleUniform = gl.getUniformLocation(areaProgram, "uMapScale");
		areaProgram.offsetUniform = gl.getUniformLocation(areaProgram, "uMapOffset");

		//tick();
		}


    var mvMatrix = mat4.create();
    var bbMatrix = mat4.create();
    var pMatrix = mat4.create();

    function setMatrixUniforms() {
        gl.uniformMatrix4fv(shaderProgram.pMatrixUniform, false, pMatrix);
        gl.uniformMatrix4fv(shaderProgram.mvMatrixUniform, false, mvMatrix);

		var normalMatrix = mat3.create();
        mat4.toInverseMat3(mvMatrix, normalMatrix);
        mat3.transpose(normalMatrix);
        gl.uniformMatrix3fv(shaderProgram.nMatrixUniform, false, normalMatrix);
		}

	function setColorUniforms() {
        gl.uniformMatrix4fv(colorProgram.pMatrixUniform, false, pMatrix);
        gl.uniformMatrix4fv(colorProgram.mvMatrixUniform, false, mvMatrix);
		}

	function setAreaUniforms() {
        gl.uniformMatrix4fv(areaProgram.pMatrixUniform, false, pMatrix);
        gl.uniformMatrix4fv(areaProgram.mvMatrixUniform, false, mvMatrix);
		}
	//alert(mvMatrix[0] + mvMatrix[1] + mvMatrix[2] + mvMatrix[3] + mvMatrix[4] + mvMatrix[5] + mvMatrix[6] + mvMatrix[7] + mvMatrix[8] + mvMatrix[9] + mvMatrix[10] + mvMatrix[11] + mvMatrix[12] + mvMatrix[13] + mvMatrix[14] + mvMatrix[15]);
	var tileBuffers;
	var texCoordBuffer;
	var drawPoints = 0;
	var baseMap = [4800, 5260];

	var zoomLvl = 8;
	var drawLength = 0;
	var mapScale = 1.0;
	var borderBuffer;
	var baseNormal;
	var riverPoints = [];
	var riverCenter = [];
	var riverFauxVerts = [];
	var riverLine;
	var drawRiverLength = [];
	var moveLength=0;
	var moveLine;
	var moveVerts;
	var gridUnits = [];
	var gridUnitsLength = [];
	var gridUniforms = [];
	var gridUnitLists = [];
	var riverLength = 0;
	var indexBuffer;
	var unitIndexBuffer;
	var simpleBox;
	var treeBuffer;
	var treeOffsets;
	var baseTile = new Array(Math.round(baseMap[0]/(120*zoomLvl)), Math.round(baseMap[1]/(120*zoomLvl)));
	var locTr = new Array(0, 0, 1, 1, 1);
	var baseOffset = new Array((baseMap[0]-baseTile[0]*120*zoomLvl)/(12*zoomLvl), (baseTile[1]*120*zoomLvl-baseMap[1])/(12*zoomLvl), 1, 1, 1);

	var areaBuffer;
	var areaCenters;
	var areaColors;

	var unitBox;

	baseOffset[0] = (baseMap[0]-baseTile[0]*120*zoomLvl)/(12*zoomLvl)
	baseOffset[1] = -(baseTile[1]*120*zoomLvl-baseMap[1])/(12*zoomLvl);

    function initBuffers() {
		circleCenters = [4800, 5260];
		var circleVerts = new Array();
		var useCenter = new Array();
		var circleColors = new Array();
		var radiuuus = 100.0
		for (i=0; i<circleCenters.length/2; i++) {
			//circleVerts.push(circleCenters[i*2]/120-0.834, circleCenters[i*2+1]/120-0.834, circleCenters[i*2]/120+2.054, circleCenters[i*2+1]/120-0.834, circleCenters[i*2]/120-0.834, circleCenters[i*2+1]/120+2.054);
			circleVerts.push(circleCenters[i*2]-radiuuus, circleCenters[i*2+1]-radiuuus, circleCenters[i*2]+radiuuus*2.5, circleCenters[i*2+1]-radiuuus, circleCenters[i*2]-radiuuus, circleCenters[i*2+1]+radiuuus*2.5);
			//useCenter.push(circleCenters[i*2], circleCenters[i*2+1], circleCenters[i*2], circleCenters[i*2+1], circleCenters[i*2], circleCenters[i*2+1])
			useCenter.push(circleCenters[i*2], circleCenters[i*2+1], circleCenters[i*2], circleCenters[i*2+1], circleCenters[i*2], circleCenters[i*2+1])
			}
		//alert(circleVerts)
		//var baseMap = [4800, 5260];
		areaBuffer = gl.createBuffer();
		gl.bindBuffer(gl.ARRAY_BUFFER, areaBuffer);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(circleVerts), gl.STATIC_DRAW);

		areaCenters = gl.createBuffer();
		gl.bindBuffer(gl.ARRAY_BUFFER, areaCenters);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(useCenter), gl.STATIC_DRAW);

		areaColors = gl.createBuffer();
		gl.bindBuffer(gl.ARRAY_BUFFER, areaColors);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([1.0, 0.0, 0.0,
														1.0, 0.0, 0.0,
														1.0, 0.0, 0.0,
														1.0, 0.0, 0.0,
														0.0, 1.0, 0.0,
														0.0, 1.0, 0.0,
														0.0, 1.0, 0.0,
														0.0, 1.0, 0.0]), gl.STATIC_DRAW);

		var geometry = new Array();
		var texGeometry = new Array();
		var normals = new Array();
		var elementList = new Array();
		for (var i=0; i<121; i++) {
			for (var j=0; j<121; j++) {
				geometry.push(j*10/120, i*10/120);
				texGeometry.push(j/128, i/128);
				normals.push(0.0, 1.0, 0.0);
				}
			}

		for (var i=0; i<120; i++) {
			elementList.push(i*121);
			for (var j=0; j<121; j++) {
				elementList.push(i*121+j, (i+1)*121+j);
				}
			elementList.push((i+1)*121+120);
			}
		indexBuffer = gl.createBuffer();
		gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, indexBuffer);
		gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, new Uint16Array(elementList), gl.STATIC_DRAW);

		unitIndexBuffer = gl.createBuffer();
		gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, unitIndexBuffer);
		gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, new Uint16Array([0,1,2,3,4,5,6,7,8,9,10]), gl.STATIC_DRAW);

		baseNormal = gl.createBuffer();
		gl.bindBuffer(gl.ARRAY_BUFFER, baseNormal);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(normals), gl.STATIC_DRAW);

		for (var i=0; i<36; i++) {
			tileNormals[i] = baseNormal;
			gridUnitsLength[i]= 0;
			gridUniforms[i] = gl.createBuffer();
			gl.bindBuffer(gl.ARRAY_BUFFER, gridUniforms[i]);
			gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([]), gl.STATIC_DRAW);

			gridUnitLists[i] = [];
			}

		tileBuffers = gl.createBuffer();
		gl.bindBuffer(gl.ARRAY_BUFFER, tileBuffers);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(geometry), gl.STATIC_DRAW);

		texCoordBuffer = gl.createBuffer();
		gl.bindBuffer(gl.ARRAY_BUFFER, texCoordBuffer);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(texGeometry), gl.STATIC_DRAW);

		drawLength = elementList.length;
		//alert(geometry.length/2.0);

		borderBuffer = gl.createBuffer();
		gl.bindBuffer(gl.ARRAY_BUFFER, borderBuffer);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-10.0, -10.0, -1.0, 1.0, 10.0, -10.0, 1.0, 1.0]), gl.STATIC_DRAW);


		for (var i=0; i<36; i++) {
			riverPoints[i] = gl.createBuffer();
			gl.bindBuffer(gl.ARRAY_BUFFER, riverPoints[i]);
			gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([]), gl.STATIC_DRAW);

			riverCenter[i] = gl.createBuffer();
			gl.bindBuffer(gl.ARRAY_BUFFER, riverCenter[i]);
			gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([]), gl.STATIC_DRAW);

			riverFauxVerts[i] = gl.createBuffer();
			gl.bindBuffer(gl.ARRAY_BUFFER, riverFauxVerts[i]);
			gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([]), gl.STATIC_DRAW);
			drawRiverLength[i] = 0;
			}
		simpleBox = gl.createBuffer();
		gl.bindBuffer(gl.ARRAY_BUFFER, simpleBox);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([0.0, 0.0, 0.0,
														0.0, 0.25, 0.0,
														0.0833, 0.0, 0.0,
														0.0833, 0.25, 0.0,
														0.0833, 0.0, 0.0833,
														0.0833, 0.25, 0.0833,
														0.0, 0.0, 0.0833,
														0.0, 0.25, 0.0833,
														0.0, 0.0, 0.0,
														0.0, 0.25, 0.0]), gl.STATIC_DRAW);

		unitBox = gl.createBuffer();
		gl.bindBuffer(gl.ARRAY_BUFFER, unitBox);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([0.0, 0.0, 0.0,
														0.0, 1.0, 0.0,
														0.167, 0.0, 0.0,
														0.167, 1.0, 0.0,
														0.167, 0.0, 0.167,
														0.167, 1.0, 0.167,
														0.0, 0.0, 0.167,
														0.0, 1.0, 0.167,
														0.0, 0.0, 0.0,
														0.0, 1.0, 0.0]), gl.STATIC_DRAW);

		riverLine = gl.createBuffer();
		gl.bindBuffer(gl.ARRAY_BUFFER, riverLine);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-10.0, 0.0, 10.0, -10.0, 0.0, 10.05, 10.0, 0.0, -10.05, 10.0, 0.0, -10.0]), gl.STATIC_DRAW);

		moveLine = gl.createBuffer();
		moveVerts = gl.createBuffer();

		treePoints = new Array();
		treeOPoints = new Array();
		for (i=0; i<120; i++) {
			for (j=0; j<120; j++) {
				treeOPoints.push(-0.02, -0.02, -0.02, 0.02, 0.02, 0.02);
				}
			}
		treeBuffer = gl.createBuffer();
		gl.bindBuffer(gl.ARRAY_BUFFER, treeBuffer);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(), gl.STATIC_DRAW);

		treeOffsets = gl.createBuffer();
		gl.bindBuffer(gl.ARRAY_BUFFER, treeOffsets);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(treeOPoints), gl.STATIC_DRAW);

		for (var i=0; i<36; i++) {
			tileForrests[i] = treeBuffer;
			}
		getData("../public_html/rivers/loadRivers_v2.php", [zoomLvl, baseTile[0], baseTile[1], 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35], [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]);
		tick();
		}

	function degToRad(degrees) {
        return degrees * Math.PI / 180;
		}

	var rY = 0.0;
	var rotShift = [0,0];
	var testXShift = [-30, -20, -10, 0, 10, 20,
						-30, -20, -10, 0, 10, 20,
						-30, -20, -10, 0, 10, 20,
						-30, -20, -10, 0, 10, 20,
						-30, -20, -10, 0, 10, 20,
						-30, -20, -10, 0, 10, 20,
						-30, -20, -10, 0, 10, 20,
						-30, -20, -10, 0, 10, 20,
					  ];
	var testZShift = [-30, -30, -30, -30, -30, -30,
					-20, -20, -20, -20, -20, -20,
					-10, -10, -10, -10, -10, -10,
					0, 0, 0, 0, 0, 0,
					10, 10, 10, 10, 10, 10,
					20, 20, 20, 20, 20, 20];
	var drawOrder = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35];
	var zoomRot = [0, 3, 2, 0, 1, 0, 0, 0, 0];

    function drawScene() {
		drawNum = Math.round((rY - Math.floor(rY/(2*3.141592654))*2*3.141592654)/0.7854);
		document.getElementById("drawNum").value = drawNum;
		mat4.perspective(45, gl.viewportWidth / gl.viewportHeight, 0.1, 500.0, pMatrix);
		gl.viewport(0, 0, gl.viewportWidth, gl.viewportHeight);
        gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);

        mat4.identity(mvMatrix);

		mat4.rotate(mvMatrix, degToRad(45), [1, 0, 0]);
		//mat4.rotate(mvMatrix, degToRad(45-(zoomRot[zoomLvl]+mapScale-1)*5.0), [1, 0, 0]);
		mat4.rotate(mvMatrix, rY, [0, 1, 0]);
		mat4.translate(mvMatrix, [0.0-rotShift[0], -10.0+Math.min(9.0, (zoomRot[zoomLvl]+mapScale-1)*1.5), 0.0+rotShift[1]]);

		/* <--------------
		gl.useProgram(bufferProgram);
    bufferProgram.VPAttribute = gl.getAttribLocation(bufferProgram, "aVertexPosition");
    gl.enableVertexAttribArray(bufferProgram.VPAttribute);

		bufferProgram.textureCoordAttribute = gl.getAttribLocation(bufferProgram, "aTextureCoord");
    gl.enableVertexAttribArray(bufferProgram.textureCoordAttribute);

    bufferProgram.pMatrixUniform = gl.getUniformLocation(bufferProgram, "uPMatrix");
    bufferProgram.mvMatrixUniform = gl.getUniformLocation(bufferProgram, "uMVMatrix");

		bufferProgram.samplerUniform = gl.getUniformLocation(bufferProgram, "uSampler");
		bufferProgram.tileNumberUniform = gl.getUniformLocation(bufferProgram, "uTileNum");
		bufferProgram.scaleUniform = gl.getUniformLocation(bufferProgram, "uMapScale");
		bufferProgram.offsetUniform = gl.getUniformLocation(bufferProgram, "uMapOffset");
		-----------> */

		// <--- Draw ocean shading texture --->
		/* <--------------
		gl.useProgram(oceanTexProgram);
		gl.bindFramebuffer(gl.FRAMEBUFFER, oceanFrameBuffer);
		gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);

		gl.bindBuffer(gl.ARRAY_BUFFER, tileBuffers);
		gl.vertexAttribPointer(oceanTexProgram.vertexPositionAttribute, 2, gl.FLOAT, false, 0, 0);

		gl.uniform1i(oceanTexProgram.samplerUniform, 0);
		gl.activeTexture(gl.TEXTURE0);
		gl.bindTexture(gl.TEXTURE_2D, textureList[8]);

		//gl.uniform1i(oceanTexProgram.samplerUniformf, 0);
		//gl.activeTexture(gl.TEXTURE0);
		//gl.bindTexture(gl.TEXTURE_2D, textureList[9]);

		gl.uniform1f(oceanTexProgram.timeUniform, (lastTime/1000.0)%60);

		gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, indexBuffer);
		gl.drawElements(gl.TRIANGLE_STRIP, drawLength, gl.UNSIGNED_SHORT, 0);

		gl.bindFramebuffer(gl.FRAMEBUFFER, null);
		-----------> */
		// <-- End draw ocean shading texture --->

		gl.useProgram(bufferProgram);
		gl.bindFramebuffer(gl.FRAMEBUFFER, rttFramebuffer);
		gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);

		// draw whatever on the fb
		gl.bindBuffer(gl.ARRAY_BUFFER, tileBuffers);
		gl.vertexAttribPointer(bufferProgram.VPAttribute, 2, gl.FLOAT, false, 0, 0);

		gl.bindBuffer(gl.ARRAY_BUFFER, texCoordBuffer);
		gl.vertexAttribPointer(bufferProgram.textureCoordAttribute, 2, gl.FLOAT, false, 0, 0);

		gl.uniform3f(bufferProgram.offsetUniform, locTr[0]+baseOffset[0], 0.0, locTr[1]+baseOffset[1]);

		gl.uniformMatrix4fv(bufferProgram.pMatrixUniform, false, pMatrix);
    gl.uniformMatrix4fv(bufferProgram.mvMatrixUniform, false, mvMatrix);
		for (var i=0; i<drawList[drawNum].length; i++) {
			gl.uniform3f(bufferProgram.scaleUniform, mapScale, testXShift[drawList[drawNum][i]], testZShift[drawList[drawNum][i]]);
			gl.uniform1i(bufferProgram.samplerUniform, 0);
			gl.activeTexture(gl.TEXTURE0);
			gl.bindTexture(gl.TEXTURE_2D, tileTextures[drawList[drawNum][i]]);

			gl.uniform1f(bufferProgram.tileNumberUniform, i);

			gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, indexBuffer);
			gl.drawElements(gl.TRIANGLE_STRIP, drawLength, gl.UNSIGNED_SHORT, 0);
			}

		// Draw unit boxes

		gl.useProgram(unitProgram);
		gl.bindBuffer(gl.ARRAY_BUFFER, unitBox);
		gl.vertexAttribPointer(unitProgram.vertexPositionAttribute, 3, gl.FLOAT, false, 0, 0);
		gl.uniform4f(unitProgram.offsetUniform, locTr[0], baseMap[0], locTr[1], baseMap[1]);
		//gl.uniform3f(unitProgram.scaleUniform, mapScale, testXShift[drawList[drawNum][i]], testZShift[drawList[drawNum][i]]);
		gl.uniform3f(unitProgram.scaleUniform, mapScale, zoomLvl, 0.0);

		gl.uniformMatrix4fv(unitProgram.pMatrixUniform, false, pMatrix);
    gl.uniformMatrix4fv(unitProgram.mvMatrixUniform, false, mvMatrix);

		for (var i=0; i<drawList[drawNum].length; i++) {
			gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, unitIndexBuffer);

			// Bind the instance position data
			gl.bindBuffer(gl.ARRAY_BUFFER, gridUniforms[drawList[drawNum][i]]);
			gl.vertexAttribPointer(unitProgram.pointLocation, 3, gl.FLOAT, false, 0, 0);

			//gl.drawArrays(gl.TRIANGLE_STRIP, 0, 10);
			ANGLEia.vertexAttribDivisorANGLE(unitProgram.pointLocation, 1);
			ANGLEia.drawElementsInstancedANGLE(gl.TRIANGLE_STRIP, 10, gl.UNSIGNED_SHORT, 0, gridUnitsLength[drawList[drawNum][i]]);
			ANGLEia.vertexAttribDivisorANGLE(unitProgram.pointLocation, 0);
		}

    gl.bindFramebuffer(gl.FRAMEBUFFER, null);

		// Draw Controled areas
		gl.useProgram(areaProgram);
		//gl.blendFunc(gl.SRC_ALPHA, gl.ONE_MINUS_SRC_ALPHA);  This kind of works
		gl.blendFunc(gl.SRC_ALPHA, gl.ONE); // This is the gl.ONE :)
    gl.enable(gl.BLEND);
    gl.disable(gl.DEPTH_TEST);

		gl.bindFramebuffer(gl.FRAMEBUFFER, terFramebuffer);
		gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);
		gl.bindBuffer(gl.ARRAY_BUFFER, areaBuffer);
		gl.vertexAttribPointer(areaProgram.vertexPositionAttribute, 2, gl.FLOAT, false, 0, 0);

		gl.bindBuffer(gl.ARRAY_BUFFER, areaCenters);
		gl.vertexAttribPointer(areaProgram.circleCenterAttribute, 2, gl.FLOAT, false, 0, 0);

		gl.bindBuffer(gl.ARRAY_BUFFER, areaColors);
		gl.vertexAttribPointer(areaProgram.circleColorAttribute, 3, gl.FLOAT, false, 0, 0);

		gl.uniform3f(areaProgram.scaleUniform, mapScale, zoomLvl, 0.0);
		gl.uniform4f(areaProgram.offsetUniform, locTr[0], baseMap[0], locTr[1], baseMap[1]);
		setAreaUniforms();

		gl.drawArrays(gl.TRIANGLE_STRIP, 0, 3);
		gl.disable(gl.BLEND);
    gl.enable(gl.DEPTH_TEST);

		// End draw Controlled areas

		gl.useProgram(colorProgram);

		gl.bindBuffer(gl.ARRAY_BUFFER, simpleBox);
		gl.vertexAttribPointer(colorProgram.vertexPositionAttribute, 3, gl.FLOAT, false, 0, 0);
		gl.uniform3f(colorProgram.offsetUniform, locTr[0]+baseOffset[0], 0.0, locTr[1]+baseOffset[1]);
		gl.uniform3f(colorProgram.scaleUniform, mapScale, testXShift[drawList[drawNum][i]], testZShift[drawList[drawNum][i]]);
		setColorUniforms();
		//////////////////////gl.drawArrays(gl.TRIANGLE_STRIP, 0, 10);

		gl.bindBuffer(gl.ARRAY_BUFFER, riverLine);
		gl.vertexAttribPointer(colorProgram.vertexPositionAttribute, 3, gl.FLOAT, false, 0, 0);
		gl.uniform3f(colorProgram.offsetUniform, locTr[0]+baseOffset[0], 0.0, locTr[1]+baseOffset[1]);
		gl.uniform3f(colorProgram.scaleUniform, mapScale, testXShift[drawList[drawNum][i]], testZShift[drawList[drawNum][i]]);
		setColorUniforms();
		gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);

		// Draw Rivers
		gl.useProgram(riverProgram);
		gl.uniform1i(riverProgram.waterSampler, 0);
		gl.activeTexture(gl.TEXTURE0);
		gl.bindTexture(gl.TEXTURE_2D, textureList[2]);

		gl.uniform4f(riverProgram.offsetUniform, locTr[0], baseMap[0], locTr[1], baseMap[1]);
		gl.uniform3f(riverProgram.scaleUniform, mapScale, zoomLvl, 0.0);
		gl.uniform1f(riverProgram.widthUniform, 0.75); // set Line widht

		gl.uniformMatrix4fv(riverProgram.pMatrixUniform, false, pMatrix);
		gl.uniformMatrix4fv(riverProgram.mvMatrixUniform, false, mvMatrix);

		for (var i=0; i<drawList[drawNum].length; i++) {
			gl.bindBuffer(gl.ARRAY_BUFFER, riverPoints[drawList[drawNum][i]]);
			gl.vertexAttribPointer(riverProgram.vertexPositionAttribute, 2, gl.FLOAT, false, 0, 0);

			gl.bindBuffer(gl.ARRAY_BUFFER, riverFauxVerts[drawList[drawNum][i]]);
			gl.vertexAttribPointer(riverProgram.shadePoints, 3, gl.FLOAT, false, 0, 0);

			if (drawRiverLength[drawList[drawNum][i]]>0) 	gl.drawArrays(gl.TRIANGLE_STRIP, 0, drawRiverLength[drawList[drawNum][i]]);
			}
		// End Draw Rivers

		// Draw unit Moves

		gl.uniform1f(riverProgram.widthUniform, 0.75); // set Line widht

		gl.bindBuffer(gl.ARRAY_BUFFER, moveLine);
		gl.vertexAttribPointer(riverProgram.vertexPositionAttribute, 2, gl.FLOAT, false, 0, 0);

		gl.bindBuffer(gl.ARRAY_BUFFER, moveVerts);
		gl.vertexAttribPointer(riverProgram.shadePoints, 3, gl.FLOAT, false, 0, 0);

		if (moveLength > 0)		gl.drawArrays(gl.TRIANGLE_STRIP, 0, moveLength);
    gl.bindFramebuffer(gl.FRAMEBUFFER, null);
		//End Draw Unit Moves
		document.getElementById("moveLength").value = moveLength;

		//draw trees
		/*
		gl.useProgram(treeProgram);


		gl.bindBuffer(gl.ARRAY_BUFFER, treeOffsets);
		gl.vertexAttribPointer(treeProgram.tOAttribute, 1, gl.FLOAT, false, 0, 0);

		gl.uniform3f(treeProgram.offsetUniform, locTr[0]+baseOffset[0], 0.0, locTr[1]+baseOffset[1]);
		gl.uniform1i(treeProgram.treeSampler, 1);
		gl.activeTexture(gl.TEXTURE1);
		gl.bindTexture(gl.TEXTURE_2D, textureList[7]);

		gl.uniformMatrix4fv(treeProgram.pMatrixUniform, false, pMatrix);
    gl.uniformMatrix4fv(treeProgram.mvMatrixUniform, false, mvMatrix);
		for (var i=0; i<drawList[drawNum].length; i++) {
			gl.uniform3f(treeProgram.scaleUniform, mapScale, testXShift[drawList[drawNum][i]], testZShift[drawList[drawNum][i]]);
			gl.uniform1i(treeProgram.samplerUniform, 0);
			gl.activeTexture(gl.TEXTURE0);
			gl.bindTexture(gl.TEXTURE_2D, tileTextures[drawList[drawNum][i]]);

			gl.bindBuffer(gl.ARRAY_BUFFER, tileForrests[drawList[drawNum][i]]);
			gl.vertexAttribPointer(treeProgram.vertexPositionAttribute, 3, gl.FLOAT, false, 0, 0);

			/////////////////gl.drawArrays(gl.TRIANGLE_STRIP, 0, forrestSizes[drawList[drawNum][i]]);
			}
		*/
		// end draw trees

		gl.useProgram(shaderProgram);

		gl.bindBuffer(gl.ARRAY_BUFFER, tileBuffers);
		gl.vertexAttribPointer(shaderProgram.vertexPositionAttribute, 2, gl.FLOAT, false, 0, 0);

		gl.bindBuffer(gl.ARRAY_BUFFER, texCoordBuffer);
		gl.vertexAttribPointer(shaderProgram.textureCoordAttribute, 2, gl.FLOAT, false, 0, 0);

		gl.uniform3f(shaderProgram.offsetUniform, locTr[0]+baseOffset[0], 0.0, locTr[1]+baseOffset[1]);
		gl.activeTexture(gl.TEXTURE3);
		gl.bindTexture(gl.TEXTURE_2D, textureList[1]);
		gl.uniform1i(shaderProgram.borderSampler, 3);

		gl.uniform1i(shaderProgram.hexPatternSampler, 4);
		gl.activeTexture(gl.TEXTURE4);
		gl.bindTexture(gl.TEXTURE_2D, textureList[4]);

		gl.uniform1i(shaderProgram.roadSampler, 5);
		gl.activeTexture(gl.TEXTURE5);
		gl.bindTexture(gl.TEXTURE_2D, textureList[5]);

		gl.uniform1i(shaderProgram.hexMap, 6);
		gl.activeTexture(gl.TEXTURE6);
		gl.bindTexture(gl.TEXTURE_2D, textureList[6]);

		gl.activeTexture(gl.TEXTURE7);
		gl.bindTexture(gl.TEXTURE_2D, terTexture);
		gl.uniform1i(shaderProgram.areaSampler, 7);

		gl.activeTexture(gl.TEXTURE8);
		gl.bindTexture(gl.TEXTURE_2D, oceanTexture);
		gl.uniform1i(shaderProgram.oceanSampler, 8);

		//gl.activeTexture(gl.TEXTURE11);
		//gl.bindTexture(gl.TEXTURE_2D, textureList[11]);
		//gl.uniform1i(shaderProgram.plainsSampler, 11);

		//gl.activeTexture(gl.TEXTURE10);
		//gl.bindTexture(gl.TEXTURE_2D, textureList[12]);
		//gl.uniform1i(shaderProgram.grassSampler, 10);

		if (document.getElementById("showMask").checked) {
			gl.uniform1f(shaderProgram.hexOn, 1.0);
			}
		else gl.uniform1f(shaderProgram.hexOn, 0.0);

		if (document.getElementById("showUseColor").checked) {
			gl.uniform1f(shaderProgram.useOn, 1.0);
			}
		else gl.uniform1f(shaderProgram.useOn, 0.0);
		setMatrixUniforms();

		gl.uniform1i(shaderProgram.bumpUniform, 9);
		gl.activeTexture(gl.TEXTURE9);
		gl.bindTexture(gl.TEXTURE_2D, textureList[10]);

		gl.uniform1i(shaderProgram.fBumpUniform, 10);
		gl.activeTexture(gl.TEXTURE10);
		gl.bindTexture(gl.TEXTURE_2D, textureList[14]);

		gl.uniform1i(shaderProgram.noiseUniform, 11);
		gl.activeTexture(gl.TEXTURE11);
		gl.bindTexture(gl.TEXTURE_2D, textureList[15]);

		gl.uniform1i(shaderProgram.maskUniform, 12);
		gl.activeTexture(gl.TEXTURE12);
		gl.bindTexture(gl.TEXTURE_2D, textureList[16]);

		gl.uniform1f(shaderProgram.timeUniform, (lastTime/1000)%600);

		for (var i=0; i<drawList[drawNum].length; i++) {
			gl.uniform3f(shaderProgram.scaleUniform, mapScale, testXShift[drawList[drawNum][i]], testZShift[drawList[drawNum][i]]);
			gl.uniform1i(shaderProgram.samplerUniform, 0);
			gl.activeTexture(gl.TEXTURE0);
			gl.bindTexture(gl.TEXTURE_2D, tileTextures[drawList[drawNum][i]]);

			gl.uniform1i(shaderProgram.terrainSampler, 1);
			gl.activeTexture(gl.TEXTURE1);
			gl.bindTexture(gl.TEXTURE_2D, textureList[0]);

			gl.uniform1f(shaderProgram.tileNumberUniform, i);

			//gl.activeTexture(gl.TEXTURE2);
			//gl.bindTexture(gl.TEXTURE_2D, rttTexture);
			//gl.uniform1i(shaderProgram.otherSampler, 2);

			gl.bindBuffer(gl.ARRAY_BUFFER, tileNormals[drawList[drawNum][i]]);
			gl.vertexAttribPointer(shaderProgram.normalAttribute, 3, gl.FLOAT, false, 0, 0);

			gl.uniform1f(shaderProgram.mover, cycleAdj);


			gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, indexBuffer);
			gl.drawElements(gl.TRIANGLE_STRIP, drawLength, gl.UNSIGNED_SHORT, 0);
			}

		// Draw unit boxes

		gl.useProgram(unitProgram);
		gl.bindBuffer(gl.ARRAY_BUFFER, unitBox);
		gl.vertexAttribPointer(unitProgram.vertexPositionAttribute, 3, gl.FLOAT, false, 0, 0);
		gl.uniform4f(unitProgram.offsetUniform, locTr[0], baseMap[0], locTr[1], baseMap[1]);
		//gl.uniform3f(unitProgram.scaleUniform, mapScale, testXShift[drawList[drawNum][i]], testZShift[drawList[drawNum][i]]);
		gl.uniform3f(unitProgram.scaleUniform, mapScale, zoomLvl, 0.0);

		gl.uniformMatrix4fv(unitProgram.pMatrixUniform, false, pMatrix);
    gl.uniformMatrix4fv(unitProgram.mvMatrixUniform, false, mvMatrix);

		totalUnits = 0;
		for (var i=0; i<drawList[drawNum].length; i++) {
			gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, unitIndexBuffer);

			// Bind the instance position data
			gl.bindBuffer(gl.ARRAY_BUFFER, gridUniforms[drawList[drawNum][i]]);
			gl.vertexAttribPointer(unitProgram.pointLocation, 3, gl.FLOAT, false, 0, 0);


			ANGLEia.vertexAttribDivisorANGLE(unitProgram.pointLocation, 1);
			ANGLEia.drawElementsInstancedANGLE(gl.TRIANGLE_STRIP, 10, gl.UNSIGNED_SHORT, 0, gridUnitsLength[drawList[drawNum][i]]);
			ANGLEia.vertexAttribDivisorANGLE(unitProgram.pointLocation, 0);
			totalUnits += gridUnitsLength[drawList[drawNum][i]];
		}

		/*
		gl.useProgram(colorProgram);
		gl.bindBuffer(gl.ARRAY_BUFFER, simpleBox);
		gl.vertexAttribPointer(colorProgram.vertexPositionAttribute, 3, gl.FLOAT, false, 0, 0);
		gl.uniform3f(colorProgram.offsetUniform, locTr[0]+baseOffset[0], 0.0, locTr[1]+baseOffset[1]);
		gl.uniform3f(colorProgram.scaleUniform, mapScale, testXShift[drawList[drawNum][i]], testZShift[drawList[drawNum][i]]);
		setColorUniforms();
		gl.drawArrays(gl.TRIANGLE_STRIP, 0, 8);
		*/
		document.getElementById("zVal").value = zoomLvl;
		document.getElementById("landRot").value = 45-(zoomRot[zoomLvl]+mapScale-1)*5;
		document.getElementById("locX").value = locTr[0];
		document.getElementById("locY").value = locTr[1];
		document.getElementById("zLvl").value = zoomLvl;
		document.getElementById("baseMap").value = baseMap[0] + ", " + baseMap[1];
		document.getElementById("locLock").value = locTr[0] + "," + locTr[1] + "," + locTr[4];
		document.getElementById("unitLength").value = totalUnits;
		document.getElementById("mapScale").value = mapScale;
	}

	var lastTime = 0;
	var wY = 0;
	var xSpeed = 0;
	var zSpeed = 0;
	var cycleAdj = 0;
	var switchOption=6;
	function tileSwitch() {
		if (switchOption == 0) {
			locTr[0] += 10;
			tmp = [drawOrder[5], drawOrder[0], drawOrder[1], drawOrder[2], drawOrder[3], drawOrder[4],
						drawOrder[11], drawOrder[6], drawOrder[7], drawOrder[8], drawOrder[9], drawOrder[10],
						drawOrder[17], drawOrder[12], drawOrder[13], drawOrder[14], drawOrder[15], drawOrder[16],
						drawOrder[23], drawOrder[18], drawOrder[19], drawOrder[20], drawOrder[21], drawOrder[22],
						drawOrder[29], drawOrder[24], drawOrder[25], drawOrder[26], drawOrder[27], drawOrder[28],
						drawOrder[35], drawOrder[30], drawOrder[31], drawOrder[32], drawOrder[33], drawOrder[34]]
					drawOrder = tmp;
			loadTiles();
			}
		else if (switchOption == 1) {
			locTr[0] -= 10;
			tmp = [drawOrder[1], drawOrder[2], drawOrder[3], drawOrder[4], drawOrder[5], drawOrder[0],
						drawOrder[7], drawOrder[8], drawOrder[9], drawOrder[10], drawOrder[11], drawOrder[6],
						drawOrder[13], drawOrder[14], drawOrder[15], drawOrder[16], drawOrder[17], drawOrder[12],
						drawOrder[19], drawOrder[20], drawOrder[21], drawOrder[22], drawOrder[23], drawOrder[18],
						drawOrder[25], drawOrder[26], drawOrder[27], drawOrder[28], drawOrder[29], drawOrder[24],
						drawOrder[31], drawOrder[32], drawOrder[33], drawOrder[34], drawOrder[35], drawOrder[30]]
				drawOrder = tmp;
			loadTiles();
			}
		else if (switchOption == 2) {
			locTr[1] +=10;
			tmp = [drawOrder[30], drawOrder[31], drawOrder[32], drawOrder[33], drawOrder[34], drawOrder[35],
						drawOrder[0], drawOrder[1], drawOrder[2], drawOrder[3], drawOrder[4], drawOrder[5],
						drawOrder[6], drawOrder[7], drawOrder[8], drawOrder[9], drawOrder[10], drawOrder[11],
						drawOrder[12], drawOrder[13], drawOrder[14], drawOrder[15], drawOrder[16], drawOrder[17],
						drawOrder[18], drawOrder[19], drawOrder[20], drawOrder[21], drawOrder[22], drawOrder[23],
						drawOrder[24], drawOrder[25], drawOrder[26], drawOrder[27], drawOrder[28], drawOrder[29]]
				drawOrder = tmp;
			loadTiles();
			}
		else if (switchOption == 3) {
			locTr[1] -=10;
			tmp = [drawOrder[6], drawOrder[7], drawOrder[8], drawOrder[9], drawOrder[10], drawOrder[11],
						drawOrder[12], drawOrder[13], drawOrder[14], drawOrder[15], drawOrder[16], drawOrder[17],
						drawOrder[18], drawOrder[19], drawOrder[20], drawOrder[21], drawOrder[22], drawOrder[23],
						drawOrder[24], drawOrder[25], drawOrder[26], drawOrder[27], drawOrder[28], drawOrder[29],
						drawOrder[30], drawOrder[31], drawOrder[32], drawOrder[33], drawOrder[34], drawOrder[35],
						drawOrder[0], drawOrder[1], drawOrder[2], drawOrder[3], drawOrder[4], drawOrder[5]]
				drawOrder = tmp;
			loadTiles();
			}
		else if (switchOption == 4) {
			loadTiles();
			baseOffset[0] = (baseMap[0]-baseTile[0]*120*zoomLvl/2)/(12*zoomLvl/2)
			baseOffset[1] = -(baseTile[1]*120*zoomLvl/2-baseMap[1])/(12*zoomLvl/2);
			mapScale /= 2.0;
			zoomLvl /= 2;
			locTr[0] = 0;
			locTr[1] = 0;
			document.getElementById("baseOff").value = baseOffset[0]+", "+baseOffset[1];
			}
		else if (switchOption == 5) {
			loadTiles();
			baseOffset[0] = (baseMap[0]-baseTile[0]*120*zoomLvl*2)/(12*zoomLvl*2)
			baseOffset[1] = -(baseTile[1]*120*zoomLvl*2-baseMap[1])/(12*zoomLvl*2);
			mapScale *= 2.0;
			zoomLvl *= 2;
			locTr[0] = 0;
			locTr[1] = 0;
			document.getElementById("baseOff").value = baseOffset[0]+", "+baseOffset[1];
			}
		else if (switchOption == 6) loadTiles();
		}
	var viewAngle;
    function animate() {
        var timeNow = new Date().getTime();
        if (lastTime != 0) {
            var elapsed = timeNow - lastTime;
			rY += elapsed*wY;

			//viewAngle = degToRad(45-(zoomRot[zoomLvl]+mapScale-1)*5.00);
			viewAngle = degToRad(45);
			height = -10.0+Math.min(9.0, (zoomRot[zoomLvl]+mapScale-1)*1.5);
			//height = -1.0;
			dist = height/Math.tan(viewAngle);
			dist = height/Math.tan(viewAngle)
			dCos = Math.cos(rY);
			dSin = Math.sin(rY);
			rotShift[0] = dist*dSin;
			rotShift[1] = dist*dCos;
			document.getElementById("rotate").value = rY + "," + rotShift[0] + "," + rotShift[1];

			baseMap[0] += 120*(-zSpeed*elapsed*Math.sin(rY)+xSpeed*elapsed*Math.cos(rY))*zoomLvl;
			baseMap[1] += 120*(zSpeed*elapsed*Math.cos(rY)+xSpeed*elapsed*Math.sin(rY))*zoomLvl;

			if (baseMap[0] < zoomLvl*120) {
				baseMap[0] = zoomLvl*120;
				xSpeed = 0;
				}
			else if (baseMap[0] > 14400-zoomLvl*120) {
				baseMap[0] = 14400-zoomLvl*120;
				xSpeed = 0;
				}

			if (baseMap[1] < zoomLvl*120) {
				baseMap[1] = zoomLvl*120;
				zSpeed = 0;
				}
			else if (baseMap[1] > 10800-zoomLvl*120) {
				baseMap[1] = 10800-zoomLvl*120;
				zSpeed = 0;
				}

			locTr[0] += 10*(-zSpeed*elapsed*Math.sin(rY)+xSpeed*elapsed*Math.cos(rY));
			locTr[1] += 10*(zSpeed*elapsed*Math.cos(rY)+xSpeed*elapsed*Math.sin(rY));
			if (locTr[0]<-10 && locTr[2]) {
				locTr[2] = 0;
				baseTile[0]--;
				switchOption = 0;
				initTiles(baseTile[0], baseTile[1], zoomLvl, [0,6,12,18,24,30], [5,11,17,23,29,35])
				}
			else if (locTr[0]>10 && locTr[2]) { //moving right
				locTr[2] = 0;
				baseTile[0]++;
				switchOption = 1;
				initTiles(baseTile[0], baseTile[1], zoomLvl, [5,11,17,23,29,35],  [0,6,12,18,24,30])
				}
			if (locTr[1]<-10 && locTr[2]) { // up
				locTr[2] = 0;
				baseTile[1]--;
				switchOption = 2;
				initTiles(baseTile[0], baseTile[1], zoomLvl, [0,1,2,3,4,5], [30,31,32,33,34,35])
				}
			else if (locTr[1]>10 && locTr[2]) { // down
				locTr[2] = 0;
				baseTile[1]++;
				switchOption = 3;
				initTiles(baseTile[0], baseTile[1], zoomLvl, [30,31,32,33,34,35], [0,1,2,3,4,5])
				}
			document.getElementById("baseTile").value = baseTile[0]+", "+baseTile[1];
			}
		cycleAdj = (timeNow/10000)%1.0;
        lastTime = timeNow;
		}


	var currentlyPressedKeys = {};

	function handleKeyDown(event) {
        currentlyPressedKeys[event.keyCode] = true;
		}

    function handleKeyUp(event) {
        currentlyPressedKeys[event.keyCode] = false;
		}

	function handleKeys() {
		//alert("set speed");
		if (currentlyPressedKeys[37] || currentlyPressedKeys[65]) {

			// Left cursor key or A
			xSpeed = -0.0005;
			} else if (currentlyPressedKeys[39] || currentlyPressedKeys[68]) {
			// Right cursor key or D
			xSpeed = 0.0005;
			} else {
			xSpeed = 0;
			}

		if (currentlyPressedKeys[38] || currentlyPressedKeys[87]) {

			// Up cursor key or W
			zSpeed = -0.0005;
			} else if (currentlyPressedKeys[40] || currentlyPressedKeys[83]) {
			// Down cursor key
			zSpeed = 0.0005;
			} else {
			zSpeed = 0;
			}
		if (currentlyPressedKeys[81]) {

			// Up cursor key or W
			wY = 0.001;
			} else if (currentlyPressedKeys[69]) {
			// Down cursor key
			wY = -0.001;
			} else {
			wY = 0;
			}
		}


  function tick() {
      requestAnimFrame(tick);
			handleKeys();
      drawScene();
      animate();
  }

	var tileCanvas;
	var ctx;

	var elList = new Array();
	var terList = new Array();
	var aspectX = new Array();
	var aspectY = new Array();

	var loaded = 0;
	var loadTarg = 0;
	function checkLoad() {
		loaded++;
		//alert(num + ": " + src);
		document.getElementById("loadedQty").value = "fok u"
		if (loaded >= loadTarg) loadTiles(x, y, z);
		}

	function initTiles(x, y, z, initList, trgList) {
		//alert("init");
		loaded = 0;
		loadTarg = 2*initList.length;
		//alert(initList);
		var initX = [-3, -2, -1, 0, 1, 2,
					-3, -2, -1, 0, 1, 2,
					-3, -2, -1, 0, 1, 2,
					-3, -2, -1, 0, 1, 2,
					-3, -2, -1, 0, 1, 2,
					-3, -2, -1, 0, 1, 2]
		var initY = [-3, -3, -3, -3, -3, -3,
					-2, -2, -2, -2, -2, -2,
					-1, -1, -1, -1, -1, -1,
					0,0,0,0,0,0,
					1,1,1,1,1,1,
					2,2,2,2,2,2]

		for (var i=0; i<initList.length; i++) {
			tmpX = x+initX[initList[i]];
			tmpY = y+initY[initList[i]];
			elList[drawOrder[trgList[i]]] = new Image();
			elList[drawOrder[trgList[i]]].onload = function () {loaded++;
				document.getElementById("loadedQty").value = loaded;
				if (loaded>=loadTarg) {
					tileSwitch();
					}
				}
			elList[drawOrder[trgList[i]]].onerror = function () {alert("Elevation Load error")}
			elList[drawOrder[trgList[i]]].src = "./imgTiles/el/"+z+"/s"+z+"_"+tmpX+"_"+tmpY+".png"

			terList[drawOrder[trgList[i]]] = new Image();
			terList[drawOrder[trgList[i]]].onload = function () {loaded++;
				document.getElementById("loadedQty").value = loaded;
				if (loaded>=loadTarg) {
					tileSwitch();
					}
				}
			terList[drawOrder[trgList[i]]].onerror = function () {alert("terrain Load error")}
			terList[drawOrder[trgList[i]]].src = "./imgTiles/ter/"+z+"/s"+z+"_"+tmpX+"_"+tmpY+".png"
			}

		// generate terrain ownership texture
		}

	function loadTiles() {
		//alert("draw");
		tileCanvas = document.getElementById("tCanvas");
		ctx = tileCanvas.getContext("2d");

		//document.getElementById("tileRef").value = x + ", " +y
		ctx.clearRect(0, 0, 128*6+1, 128*6+1);
		ctx.globalCompositeOperation = "lighten";
		//alert(terList[35].src);
		for (var i=0; i<6; i++) {
			for (var j=0; j<6; j++) {
				ctx.drawImage(elList[drawOrder[i*6+j]], 0, 0, 120, 120,  j*120, i*120, 120, 120);
				ctx.drawImage(terList[drawOrder[i*6+j]], 0, 0, 120, 120,  j*120, i*120, 120, 120);
				}
			}

		for(var i=0; i<6; i++) {
			  for(var j=0; j<6; j++) {
				mapTextures(i*6+j, j*120, i*120);
				}
			}
		if (mapScale > 2.0) {
			//mapScale = 1.0+(mapScale-2.0)/2.0;
			}
		else if (mapScale < 1.0) {
			//mapScale = 2.0-(1.0-mapScale);
			}
		locTr[2] = 1;
		locTr[3] = 1;
		locTr[4] = 1;
		//tileSwitch();
		}



	function MouseWheelHandler(e) {
		// cross-browser wheel delta
		var e = window.event || e; // old IE support
		delta = e.wheelDelta || -e.detail;
		delta = Math.max(Math.min(delta, 10.0), -10.0);
		mapScale = Math.max(Math.min(mapScale+delta/50.0,6.0),1.0);
		//mapScale += delta/50.0;
		viewAngle = degToRad(45);
		height = -10.0+Math.min(9.0, (zoomRot[zoomLvl]+mapScale-1)*1.5);

		dist = height/Math.tan(viewAngle);

		dCos = Math.cos(rY);
		dSin = Math.sin(rY);
		rotShift[0] = dist*dSin;
		rotShift[1] = dist*dCos;

		if (mapScale >= 2.0) {
			if (zoomLvl > 1 && locTr[4]) {
				locTr[4] = 0;

				baseTile[0] = Math.round(baseMap[0]/(120*zoomLvl/2))
				baseTile[1] = Math.round(baseMap[1]/(120*zoomLvl/2));

				document.getElementById("baseTile").value = baseTile[0]+", "+baseTile[1];
				switchOption = 4;
				getData("../public_html/rivers/loadRivers_v2.php", [zoomLvl/2, baseTile[0], baseTile[1], 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35], [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]);
				//drawOrder = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35];
				initTiles(baseTile[0], baseTile[1], zoomLvl/2, [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35], [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]);

				}
			if (mapScale >= 6.0 && zoomLvl == 1) mapScale = 6.0;
			else {

				}

			}
		else if (mapScale+delta/50.0 < 1.0) {
			if (zoomLvl < 8 && locTr[4]) {

				locTr[4] = 0;

				baseTile[0] = Math.round(baseMap[0]/(120*zoomLvl*2));
				baseTile[1] = Math.round(baseMap[1]/(120*zoomLvl*2));
				document.getElementById("baseTile").value = baseTile[0]+", "+baseTile[1];
				switchOption = 5;
				getData("../public_html/rivers/loadRivers_v2.php", [zoomLvl*2, baseTile[0], baseTile[1], 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35], [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]);
				initTiles(baseTile[0], baseTile[1], zoomLvl*2, [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35], [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]);
				//drawOrder = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35];
				}
			else {
				mapScale = 1.0;
				}
			}
		}

	var rttFramebuffer;
	var rttTexture;
	var terFramebuffer;
	var terTexture;
	var oceanFrameBuffer;
	var oceanTexture;

	function initTextureFramebuffer(trg, trgTex, width, height) {
        gl.bindFramebuffer(gl.FRAMEBUFFER, trg);

        gl.bindTexture(gl.TEXTURE_2D, trgTex);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
        gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR_MIPMAP_NEAREST);
        gl.generateMipmap(gl.TEXTURE_2D);

        gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, width, height, 0, gl.RGBA, gl.UNSIGNED_BYTE, null);

        trg.renderbuffer = gl.createRenderbuffer();
        gl.bindRenderbuffer(gl.RENDERBUFFER, trg.renderbuffer);
        gl.renderbufferStorage(gl.RENDERBUFFER, gl.DEPTH_COMPONENT16, width, height);

        gl.framebufferTexture2D(gl.FRAMEBUFFER, gl.COLOR_ATTACHMENT0, gl.TEXTURE_2D, trgTex, 0);
        gl.framebufferRenderbuffer(gl.FRAMEBUFFER, gl.DEPTH_ATTACHMENT, gl.RENDERBUFFER, trg.renderbuffer);

        gl.bindTexture(gl.TEXTURE_2D, null);
        gl.bindRenderbuffer(gl.RENDERBUFFER, null);

        gl.bindFramebuffer(gl.FRAMEBUFFER, null);
    }


	function loadRivers(tiles, rData, rivTargets) {
		lineWidth = 0.75;
		//alert("load Rivers");
		headDat = new Uint16Array(rData.slice(0, 2*(tiles.length-3)));
		segStart = false;
		reset = false;

		totalOffset = 0;

		for (var i=0; i<tiles.length - 3; i++) {
			pVec = [0, 0];
			nVec = [0, 0];
			normVec = [0, 0];
			normMag = 1;
			test = new Uint16Array(rData.slice(2*(tiles.length-3)+totalOffset, 2*(tiles.length-3)+totalOffset+headDat[i]));
			totalOffset += headDat[i];

			rpList = new Array();
			fauxVerts = new Array();
			resetCount=0;
			if (test.length > 0) {
				//alert(test[0] + ", " + test[1]);
				for (var j=0; j<test.length/2-2; j++) {
				if (test[j*2+2] != 0 && test[j*2+3] != 0) {
					dirX = test[j*2+2]-test[j*2];
					dirY = test[j*2+3]-test[j*2+1];
					mag = Math.sqrt(dirX*dirX+dirY*dirY);
					normx = dirX/mag;
					normy = dirY/mag;

					if (reset) {
						rpList.push(test[j*2]-normy*lineWidth, test[j*2+1]+normx*lineWidth,
						test[j*2]-normy*lineWidth, test[j*2+1]+normx*lineWidth,
						test[j*2]-normy*lineWidth, test[j*2+1]+normx*lineWidth,
						test[j*2]+normy*lineWidth, test[j*2+1]-normx*lineWidth,
						test[j*2+2]-normy*lineWidth+normx*lineWidth, test[j*2+3]+normx*lineWidth+normy*lineWidth,
						test[j*2+2]+normy*lineWidth+normx*lineWidth, test[j*2+3]-normx*lineWidth+normy*lineWidth,
						test[j*2+2]+normy*lineWidth+normx*lineWidth, test[j*2+3]-normx*lineWidth+normy*lineWidth);

						fauxVerts.push(0,0,0,
						0,0,0,
						mag,0,lineWidth,
						mag,0,-lineWidth,
						mag,mag+lineWidth,lineWidth,
						mag,mag+lineWidth,-lineWidth,
						0,0,0);
						reset = false;
						resetCount++;
						}
					else {
						rpList.push(test[j*2]-normy*lineWidth, test[j*2+1]+normx*lineWidth,
						test[j*2]-normy*lineWidth, test[j*2+1]+normx*lineWidth,
						test[j*2]+normy*lineWidth, test[j*2+1]-normx*lineWidth,
						test[j*2+2]-normy*lineWidth+normx*lineWidth, test[j*2+3]+normx*lineWidth+normy*lineWidth,
						test[j*2+2]+normy*lineWidth+normx*lineWidth, test[j*2+3]-normx*lineWidth+normy*lineWidth,
						test[j*2+2]+normy*lineWidth+normx*lineWidth, test[j*2+3]-normx*lineWidth+normy*lineWidth);

						fauxVerts.push(0,0,0,
						mag,0,lineWidth,
						mag,0,-lineWidth,
						mag,mag+lineWidth,lineWidth,
						mag,mag+lineWidth,-lineWidth,
						0,0,0);
						}
				}
				else {
					rpList.push(rpList[rpList.length-2], rpList[rpList.length-1]);
					fauxVerts.push(0,0,0);
					//alert(rpList[rpList.length-4] +", " + rpList[rpList.length-3] + ", " + rpList[rpList.length-2] +", "+ rpList[rpList.length-1]);
					reset = true;
					j++;
					}
				}
			}
			drawRiverLength[i] = rpList.length/2.0;
			gl.bindBuffer(gl.ARRAY_BUFFER, riverPoints[i]);
			gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(rpList), gl.STATIC_DRAW);

			gl.bindBuffer(gl.ARRAY_BUFFER, riverFauxVerts[i]);
			gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(fauxVerts), gl.STATIC_DRAW);

			}
			unitOffset = 2*(tiles.length-3)+totalOffset;
			totalOffset = 4*(tiles.length-3);

			headDat = new Int32Array(rData.slice(unitOffset, unitOffset+4*(tiles.length-3)));

			minVals = [100000,100000];
			maxVals = [0,0];
			tmptotalUnits = 0;

			for (var i=0; i<tiles.length - 3; i++) {
				unitStuff = new Int32Array(rData.slice(unitOffset+totalOffset, unitOffset+totalOffset+16*headDat[i]));
				start = unitOffset+totalOffset;
				totalOffset += 16*headDat[i];

				gridUnits[i] = [];
				gridUnitsLength[i] = 0;
				gridUniforms[i] = gl.createBuffer();
				gridUnitLists[i].splice(0,gridUnitLists[i].length);
				dumbVal = [1, 1];

				for (var uCount=0; uCount<headDat[i]; uCount++) {
					// Data is X Point, Y Point, Unit ID
					gridUnitLists[i].push(unitStuff[uCount*4], unitStuff[uCount*4+1], unitStuff[uCount*4+3]);
					gridUnitsLength[i]+=1;
				}
				//console.log(gridUnitLists[i]);
				tmptotalUnits += gridUnitsLength[i];
				gridUnitsLength[i] = gridUnitLists[i].length/3.0;
				gl.bindBuffer(gl.ARRAY_BUFFER, gridUniforms[i]);
				gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(gridUnitLists[i]), gl.STATIC_DRAW);
			}
		}

	function updateUnitPosition(unitID, X, Y) {
		//alert("update position");
		idCheck: {
			for (var i=0; i<36; i++) {
				for (var j=2; j<gridUnitLists[i].length; j+=3) {
					if (gridUnitLists[i][j] == unitID) {
						//alert("spot found: " + j + ", Old length: " + gridUnitLists[i].length);
						//alert(gridUnitLists[i]);
						gridUnitLists[i][j-2] = X;
						gridUnitLists[i][j-1] = Y;
						//alert("New length: " + gridUnitLists[i].length);
						//alert(gridUnitLists[i]);
						gl.bindBuffer(gl.ARRAY_BUFFER, gridUniforms[i]);
						gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(gridUnitLists[i]), gl.STATIC_DRAW);
						break idCheck;
					}
				}
			}
		}
	}

	var drawList = [];
	drawList[0] = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35];
	drawList[1] = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35];
	drawList[2] = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35];
	drawList[3] = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35];
	drawList[4] = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35];
	drawList[5] = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35];
	drawList[6] = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35];
	drawList[7] = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35];
	drawList[8] = drawList[0];
	var clickParams = [];
	var clickTarg = "";
	function handleClick(event)	{
		//alert(testParam + "," + clickParams);
		document.body.style.cursor = "auto";
		var loc = findPos(this);
		var rect = this.getBoundingClientRect();
		var cpos = [(event.clientX - loc[0]), (document.getElementById("lesson03-canvas").height - (event.clientY - loc[1]))];
		//alert(cpos[0] + ", " + cpos[1]);

		var pixelValues = new Uint8Array(4);
		gl.bindFramebuffer(gl.FRAMEBUFFER, rttFramebuffer);
		//gl.bindFramebuffer(gl.FRAMEBUFFER, terFramebuffer);
		gl.readPixels(cpos[0], cpos[1], 1, 1, gl.RGBA, gl.UNSIGNED_BYTE, pixelValues);
		gl.bindFramebuffer(gl.FRAMEBUFFER, null);
		if (pixelValues[0] > 36) {
			sendStr = "1019,"+pixelValues[0]+","+pixelValues[1]+","+pixelValues[2]+","+clickParams;
			//makeBox("unit", sendStr, 500, 500, 200, 50);
			passClick(sendStr, "rtPnl");
		}
		else {
			clickY = Math.floor(pixelValues[0]/6.0);
			clickX = pixelValues[0] - clickY*6;
			longitude = (baseTile[0] + clickX-3)*zoomLvl + pixelValues[1]*zoomLvl/255-30;
			latitude = 90 - ((baseTile[1]-3+clickY)*zoomLvl+zoomLvl*pixelValues[2]/255);
			//alert(pixelValues[0] + ", " + pixelValues[1] + "," + pixelValues[2] + "base: " + baseTile[0] + ", " + baseTile[1] + "/" + clickX + ", " + clickY + " = " + longitude+"/"+latitude);
			document.getElementById("clickLat").value = latitude;
			document.getElementById("clickLong").value = longitude;
			sendStr = clickParams + ","+pixelValues+","+baseTile+","+zoomLvl;
			if (clickParams[0] != 0) {
				passClick(sendStr, clickTarg);
				//alert("blah " + baseTile);
			}
			//else passClick(sendStr, "rtPnl");
		}

	clickParams = [0];
	clickTarg = "";
	}

	function setClick(params, style, trg) {

		clickParams=params;
		clickTarg = trg;
		//alert(params + " ==> " + clickParams + ", " + style);

		document.body.style.cursor = style;
	}

	function findPos(obj) {
		var curleft = curtop = 0;
		if (obj.offsetParent) {
			do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
			} while (obj = obj.offsetParent);
			return [curleft,curtop];
			}
		}

	function canvasInit() {
		var new_canvas = document.getElementById("lesson03-canvas");

		new_canvas.onclick = handleClick;
		//new_canvas.addEventListener("onclick", handleClick(event));

		new_canvas.style.width = 1200;
		new_canvas.style.height = 700;

		new_canvas.width = parseInt(new_canvas.style.width);
		new_canvas.height = parseInt(new_canvas.style.height);
		}

	function createAndSetupTexture(gl) {
		var texture = gl.createTexture();
		gl.bindTexture(gl.TEXTURE_2D, texture);

		// Set up texture so we can render any size image and so we are
		// working with pixels.
		gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
		gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
		gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.NEAREST);
		gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.NEAREST);

		return texture;
		}


	function webGLStart() {
		document.getElementById("readMsg").addEventListener("click", function(event) {console.log(event);makeBox(\'inBox\', 1099, 500, 500, 200, 50)});

		useDeskTop = new deskTop;
		taskList = new unitList();
		unitList = new unitList();
		setClick([0], "auto")
		var canvas = document.getElementById("lesson03-canvas");
		canvasInit();

    initGL(canvas);
		textureList[0] = gl.createTexture();
		loadTexture(0, "./textures/terrainTex3.png");
		textureList[1] = gl.createTexture();
		loadTexture(1, "./textures/borderMask.png");
		textureList[2] = gl.createTexture();
		loadTexture(2, "./textures/waterTex3.png");
		textureList[3] = gl.createTexture();
		loadTexture(3, "./textures/riverScreen1.png");
		textureList[4] = gl.createTexture();
		loadTexture(4, "./textures/hexPattern.png");
		textureList[5] = gl.createTexture();
		loadTexture(5, "./textures/roadScreens.png");
		textureList[6] = gl.createTexture();
		loadTexture(6, "./textures/hexMap.png");
		textureList[7] = gl.createTexture();
		loadTexture(7, "./textures/treeTex.png");
		textureList[8] = gl.createTexture();
		loadTexture(8, "./textures/PerlinExample_256.png");
		//////textureList[9] = gl.createTexture();
		//////loadTexture(9, "./textures/PerlinExample_256f.png");
		textureList[10] = gl.createTexture();
		loadTexture(10, "./textures/bump_water.jpg");
		//////textureList[11] = gl.createTexture();
		//////loadTexture(11, "./textures/grass_256.jpg");
		//////textureList[12] = gl.createTexture();
		//////loadTexture(12, "./textures/plains_256.jpg");
		//////textureList[13] = gl.createTexture();
		//////loadTexture(13, "./textures/forestBump_256.png");
		textureList[14] = gl.createTexture();
		loadTexture(14, "./textures/forestBump1_256.jpg");
		textureList[15] = gl.createTexture();
		loadTexture(15, "./textures/PerlinExample_256.png");
		textureList[16] = gl.createTexture();
		loadTexture(16, "./textures/borderMask3.png");

		initTiles(baseTile[0], baseTile[1], zoomLvl, [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35], [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]);
		//loadTiles(baseTile[0], baseTile[1], zoomLvl);

		//initTextureFramebuffer(rttFramebuffer, rttTexture);

		oceanTexture = createAndSetupTexture(gl);
		gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, 1200, 700, 0,gl.RGBA, gl.UNSIGNED_BYTE, null);

		oceanFrameBuffer = gl.createFramebuffer();
		gl.bindFramebuffer(gl.FRAMEBUFFER, oceanFrameBuffer);
		gl.framebufferTexture2D(gl.FRAMEBUFFER, gl.COLOR_ATTACHMENT0, gl.TEXTURE_2D, oceanTexture, 0);
		gl.bindFramebuffer(gl.FRAMEBUFFER, null);

		terTexture =  createAndSetupTexture(gl);
		gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, 1200, 700, 0,gl.RGBA, gl.UNSIGNED_BYTE, null);

		terFramebuffer = gl.createFramebuffer();
		gl.bindFramebuffer(gl.FRAMEBUFFER, terFramebuffer);
		gl.framebufferTexture2D(gl.FRAMEBUFFER, gl.COLOR_ATTACHMENT0, gl.TEXTURE_2D, terTexture, 0);
		gl.bindFramebuffer(gl.FRAMEBUFFER, null);

		rttTexture = gl.createTexture();
		rttFramebuffer = gl.createFramebuffer();
		initTextureFramebuffer(rttFramebuffer, rttTexture, 1200, 700);

		document.getElementById("baseOff").value = baseOffset[0]+", "+baseOffset[1];
		document.getElementById("baseTile").value = baseTile[0]+", "+baseTile[1];
		if (canvas.addEventListener) {
			// IE9, Chrome, Safari, Opera
			canvas.addEventListener("mousewheel", MouseWheelHandler, false);
			// Firefox
			canvas.addEventListener("DOMMouseScroll", MouseWheelHandler, false);
			}
		// IE 6/7/8
		else canvas.attachEvent("onmousewheel", MouseWheelHandler);
		//alert(baseTile[0]);
		//getData("../public_html/rivers/loadRivers_v2.php", [zoomLvl, baseTile[0], baseTile[1], 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35], [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]);

    gl.clearColor(0.0, 0.0, 0.0, 0.0);
    gl.enable(gl.DEPTH_TEST);

		document.onkeydown = handleKeyDown;
		document.onkeyup = handleKeyUp;

		initShaders();
		}

	function showDiagnostics() {
		if (document.getElementById("diagCB").checked) document.getElementById("diagBox").style.width = "300";
		else  document.getElementById("diagBox").style.width = 0;
	}

	function sendValue(src, dst) {
		//alert("source has a value of " + document.getElementById(src).value);
		dst = dst + ","+document.getElementById(src).value;
		alert(dst);
		makeBox("someBox", dst, 500, 500, 200, 50);
	}

	function getDescription(trg, info, src) {
		info = info  + ","+document.getElementById(src).value;
		passClick(info, trg);
	}

window.addEventListener("load", webGLStart);
</script>

	<html>
	<body>
	<div id="ltPnl" style="position:absolute; top:15; left:10; height:675; width:100; border:1px solid #000000">
		Culture: '.$playerDat[3].'<br>
		ID: '.$pGameID.'<br>
		<a href="javascript:void(0);" onclick="scrMod(1001)">Faction chars</a>
		<a href="javascript:void(0);" onclick="makeBox(\'fOrders\', 1005, 500, 500, 200, 50)">Lands</a><br>
		<a href="javascript:void(0);" onclick="makeBox(\'laws\', 1007, 500, 500, 200, 50)">Faction Laws</a><br>
		<a href="javascript:void(0);" onclick="makeBox(\'diplomacy\', 1008, 500, 500, 200, 50)">Diplomacy</a><br>
		<a href="javascript:void(0);" onclick="makeBox(\'intrigue\', 1009, 500, 500, 200, 50)">Intrigue</a><br>
		<a href="javascript:void(0);" onclick="scrMod(1011)">Military</a><br>
		<a href="javascript:void(0);" onclick="passClick(\'1012,0\' , "infoBar")">Rule</a><br>
		<a href="javascript:void(0);" onclick="makeBox(\'economy\', 1015, 500, 500, 200, 50)">Economy</a><br>
		<a href="javascript:void(0);" onclick="makeBox(\'tech\', 1017, 500, 500, 200, 50)">Advances</a><br>
		<a href="./index.php" style="position:absolute; bottom:0">Back to Main</a>
	</div>
	<div id="infoBar" style="position:absolute; top:640; left:110; height:50; width:1200; border:1px solid #000000">infoBar</div>
	<div id="rtPnl" style="position:absolute; top:15; left:1310; height:675; width:200; border:1px solid #000000; display:inline;"></div>
	<div id="botPnl" style="position:absolute; top:690; left:10; height:40; width:1400; border:1px solid #000000">
		<a href="javascript:void(0);" id="readMsg">Read Messages</a>
	</div>
	<div id="gmPnl" style="position:absolute; top:15; left:110; height:675; width:1200; border:1px solid #000000; overflow:hidden">
		<canvas style="position:absolute" id="lesson03-canvas" style="border: none; " width=1200 height=700></canvas>
	</div>

	<div id="scrBox" style="width:0; height:0; overflow:hidden;">
	</div>
	<div style="width:0; height:0; overflow:hidden;">
		<div style="position:absolute;  overflow:hidden; width:0; height:0; left:1210; top:150;"><canvas id="tCanvas" style="border: 1px solid black;" width=720 height=720></canvas></div>
		<div id="pointDat" style="position:absolute; right:0; bottom:0;"></div>
		<div id="diagBox" style="position:absolute; right:50; top:0; width:0; overflow:hidden;">
			<table>
				<tr><td>Lat:</td><td><input id="clickLat" value="0"></td></tr>
				<tr><td>Long:</td><td><input id="clickLong" value="0"></td></tr>
				<tr><td>Mask:</td><td><input type="checkbox" id="showMask"></td></tr>
				<tr><td>UseColor:</td><td><input type="checkbox" id="showUseColor"></td></tr>
				<tr><td>zVal:</td><td><input id="zVal"></td></tr>
				<tr><td>locX:</td><td><input id="locX"></td></tr>
				<tr><td>locY:</td><td><input id="locY"></td></tr>
				<tr><td>zLvl:</td><td><input id="zLvl"></td></tr>
				<tr><td>baseMap:</td><td><input id="baseMap"></td></tr>
				<tr><td>tileRef:</td><td><input id="tileRef"></td></tr>
				<tr><td>rivTargs:</td><td><input id="rivTargs"></td></tr>
				<tr><td>rivTiles:</td><td><input id="rivTiles"></td></tr>
				<tr><td>baseOff:</td><td><input id="baseOff"></td></tr>
				<tr><td>lookAt:</td><td><input id="lookAt"></td></tr>
				<tr><td>rotate:</td><td><input id="rotate"></td></tr>
				<tr><td>drawNum:</td><td><input id="drawNum"></td></tr>
				<tr><td>loadedQty:</td><td><input id="loadedQty"></td></tr>
				<tr><td>locLock:</td><td><input id="locLock"></td></tr>
				<tr><td>baseTile:</td><td><input id="baseTile"></td></tr>
				<tr><td>landRot:</td><td><input id="landRot"></td></tr>
				<tr><td>unitLength:</td><td><input id="unitLength"></td></tr>
				<tr><td>mapScale:</td><td><input id="mapScale"></td></tr>
				<tr><td>moveLength:</td><td><input id="moveLength"></td></tr>
			</table>
		</div>
	</div>
	<div style="position:absolute; right:0; bottom:5;">Show diagnostics? <input id="diagCB" type="checkbox" onchange="showDiagnostics()"></div>
	</body>
	</html>';

?>
