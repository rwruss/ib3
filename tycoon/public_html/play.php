<?php

require_once('./slotFunctions.php');
require_once('./unitClass.php');

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
//$mapBounds = unpack("S*", substr($paramDat, 100, 8));
$gameTimes = unpack("N*", substr($paramDat, 0, 8));

$paramFile = fopen('../games/'.$gameID.'/params.ini', 'rb');
$params = unpack('i*', fread($paramFile, 100));
$_SESSION['game_'.$gameID]['scenario'] = $params[9];
$_SESSION['game_'.$gameID]['scenario'] = 1;
$_SESSION['game_'.$gameID]['culture'] = 1; // Set and record player culture
fclose($paramFile);


$gamePath = "../games/".$gameID;
$scnPath = "../scenarios/".$_SESSION['game_'.$gameID]['scenario'];
// Read player info
$defaultBlockSize = 100;
$unitFile = fopen($gamePath."/unitDat.dat", "rb");
$slotFile = fopen($gamePath.'/gameSlots.slt', 'rb');
//$playerDat = file_get_contents($gamePath."/unitDat.dat", NULL, NULL, $pGameID*400, 400);
$thisPlayer = loadPlayer($pGameID, $unitFile, 400);
//$playerDat = unpack("i*", file_get_contents($gamePath."/unitDat.dat", NULL, NULL, $pGameID*100, 400));


if ($thisPlayer->unitDat[1] == 0) {include("../gameScripts/1003.php"); exit;}



echo '
<link rel="stylesheet" type="text/css" href="ib3styles.css">
<script type="text/javascript" src="glMatrix-0.9.5.min.js"></script>
<script type="text/javascript" src="webgl-utils.js"></script>
<script type="text/javascript" src="templates.js"></script>
<script type="text/javascript" src="selectList.js"></script>

<script id="shader-fs" type="x-shader/x-fragment">
</script>

<script id="shader-vs" type="x-shader/x-vertex">
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
	var playerUnits;
	var moveString = new Array();
	var umList = [];
	var umFauxVerts = [];
	var drawLoc = [];



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
		//console.log(window.event);
		useDeskTop.newPane(bName);
		//console.log(bName + " = " + useDeskTop.getPane(bName));
		//console.log("passClick to " + useDeskTop.getPane(bName));
		//console.log("event: " + event);
		//e.stopPropagation();
		passClick(val, useDeskTop.getPane(bName));
		}

	function closeBox() {
		this.parentNode.remove();
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
		for (var i=0; i<121; i++) {
			for (var j=0; j<121; j++) {
				baseRef = 4*((i+1)*128+j+1);
				tmpVec = [-pixDat[baseRef-4*128]+pixDat[baseRef-4*128+4]-2*pixDat[baseRef-4]+2*pixDat[baseRef+4]-pixDat[baseRef+128*4-4]+pixDat[baseRef+128*4], 10.25, -2*pixDat[baseRef-128*4]-pixDat[baseRef-128*4+4]-pixDat[baseRef-4]+pixDat[baseRef+4]+pixDat[baseRef+128*4-4]+2*pixDat[baseRef+128*4]];
				tmpVec = vec3.normalize(tmpVec)
				newNormals.push(tmpVec[0], tmpVec[1], tmpVec[2]);
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
		forrestSizes[tileNum] = newForrest.length/3;
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
	}

	var lastTime = 0;
	var wY = 0;
	var xSpeed = 0;
	var zSpeed = 0;

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
		//alert(clickParams);
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
				makeBox(clickTarg, sendStr, 500, 500, 200, 50);
				//passClick(sendStr, clickTarg);
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

	var genCharList = [];
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
		

		initTiles(baseTile[0], baseTile[1], zoomLvl, [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35], [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35]);

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
	}';



echo '
window.addEventListener("load", webGLStart);
</script>

	<html>
	<body>
	<div id="ltPnl" style="position:absolute; top:15; left:10; height:675; width:100; border:1px solid #000000">
		ID: '.$pGameID.'<br>
		<a href="javascript:void(0);" onclick="scrMod(1001)">Financial</a>
		<a href="javascript:void(0);" onclick="makeBox(\'fOrders\', 1002, 500, 500, 200, 50)">Busineses</a><br>
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
