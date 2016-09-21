<?php

session_start();

if (isset($_GET['side'])) $playerID = $_GET['side'];
else $playerID = 1;
$_SESSION['side'] = $playerID;
echo '
<html>

<head>
<title>On the use of buffers and triangles to create entertainment</title>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">

<script type="text/javascript" src="stratego.js"></script>
<script type="text/javascript" src="glMatrix-0.9.5.min.js"></script>
<script type="text/javascript" src="webgl-utils.js"></script>


<script id="boardFS" type="x-shader/x-fragment">
    precision mediump float;

	uniform sampler2D uSampler;

    varying vec3 vBoardVertex;
	varying vec3 vThree;
	varying vec2 vBoardTex;

    void main(void) {
    //gl_FragColor = vec4(vBoardVertex, 1.0);
	gl_FragColor = texture2D(uSampler, vec2(vBoardTex.s, vBoardTex.t));
	//gl_FragColor = vec4(vBoardTex, 0.5, 1.0);
	//gl_FragColor = vec4(vThree, 1.0);
    }
</script>

<script id="boardVS" type="x-shader/x-vertex">
    attribute vec3 aBoardVertex;
	attribute vec2 aBoardTex;
	attribute vec3 aThree;

    uniform mat4 uMVMatrix;
    uniform mat4 uPMatrix;

    varying vec3 vBoardVertex;
	varying vec3 vThree;
	varying vec2 vBoardTex;

    void main(void) {
        gl_Position = uPMatrix * uMVMatrix * vec4(aBoardVertex, 1.0);
        vBoardVertex = aBoardVertex;
		vBoardTex = aBoardTex;
		vThree = aThree;
    }
</script>

<script id="pieceFS" type="x-shader/x-fragment">
    precision mediump float;

    varying vec2 vSkinCoord;
    varying vec2 vTextureCoord;

    varying float pieceColor;

    uniform sampler2D uSampler;

    void main(void) {
        vec4 texColor = texture2D(uSampler, vec2(vTextureCoord.s, vTextureCoord.t));
        //gl_FragColor = texColor.x*vec4(pieceColor, 0.0, 1.0) + (1.-texColor.x)*vec4(pieceColor, 1.0, 1.0);
        gl_FragColor = vec4(pieceColor-1.0, 1.0, 0.0, 1.0);
    }
</script>

<script id="pieceVS" type="x-shader/x-vertex">
    attribute vec3 aVertexPosition;
    attribute vec2 aPieceLocation;
    attribute vec2 aSkinCoord;
    attribute vec2 aTextureCoord;
	  attribute float aSideColor;

    uniform mat4 uMVMatrix;
    uniform mat4 uPMatrix;
	  uniform sampler2D uSampler;

    varying vec2 vSkinCoord;
    varying vec2 vTextureCoord;
    varying float pieceColor;

    void main(void) {
        //gl_Position = uPMatrix * uMVMatrix * vec4(aVertexPosition*0.1+vec3(0.1, 0.0, 0.10), 1.0);
        gl_Position = uPMatrix * uMVMatrix * vec4(aVertexPosition*0.1+vec3(0.1, 0.0, 0.10)+vec3(aPieceLocation.x, 0.0, aPieceLocation.y)*0.20, 1.0);
        vSkinCoord = aSkinCoord;


	   float yOff = floor((aSkinCoord.x-1.0)/4.0);
     pieceColor = aSideColor;
    vTextureCoord = vec2(aTextureCoord.x+0.25*((aSkinCoord.x-1.)-yOff*4.0), aTextureCoord.y-yOff*0.25);
    }
</script>

<script id="boardBackFS" type="x-shader/x-fragment">
    precision mediump float;

    varying vec3 vPosition;

    void main(void) {
        gl_FragColor = vec4(vPosition, 1.0);
    }
</script>

<script id="boardBackVS" type="x-shader/x-vertex">
    attribute vec3 aVertexPosition;

    uniform mat4 uMVMatrix;
    uniform mat4 uPMatrix;

	   varying vec3 vPosition;

    void main(void) {
        gl_Position = uPMatrix * uMVMatrix * vec4(aVertexPosition, 1.0);
		    vPosition = (aVertexPosition+1.0)/2.0;
    }
</script>

<script id="highLightFS" type="x-shader/x-fragment">
    precision mediump float;

    void main(void) {
        gl_FragColor = vec4(1.0, 1.0, 0.0, 1.0);
    }
</script>

<script id="highLightVS" type="x-shader/x-vertex">
    attribute vec3 aVertexPosition;
    uniform vec3 uOffset;

    uniform mat4 uMVMatrix;
    uniform mat4 uPMatrix;

    void main(void) {
        gl_Position = uPMatrix * uMVMatrix * vec4(aVertexPosition+uOffset, 1.0);
    }
</script>


<script type="text/javascript">

    var gl;
    var ANGLEia;

    function initGL(canvas) {
        try {
            gl = canvas.getContext("experimental-webgl");
            gl.viewportWidth = canvas.width;
            gl.viewportHeight = canvas.height;
            ANGLEia = gl.getExtension("ANGLE_instanced_arrays"); // Vendor prefixes may apply!
        } catch (e) {
        }
        if (!gl) {
            alert("Could not initialise WebGL, sorry :-(");
        }
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


    var boardProgram;
    var boardBGProgram;
    var pieceProgram;
	   var highLightProgram;

    function initShaders() {
        var fragmentShader = getShader(gl, "boardFS");
        var vertexShader = getShader(gl, "boardVS");

        boardProgram = gl.createProgram();
        gl.attachShader(boardProgram, vertexShader);
        gl.attachShader(boardProgram, fragmentShader);
        gl.linkProgram(boardProgram);

        if (!gl.getProgramParameter(boardProgram, gl.LINK_STATUS)) {
            alert("Could not initialise first boardProgram");
        }

        gl.useProgram(boardProgram);

		boardProgram.textureCoordAttr = gl.getAttribLocation(boardProgram, "aBoardTex");
        gl.enableVertexAttribArray(boardProgram.textureCoordAttr);

        boardProgram.VPA = gl.getAttribLocation(boardProgram, "aBoardVertex");
        gl.enableVertexAttribArray(boardProgram.VPA);

		boardProgram.thirdAttr = gl.getAttribLocation(boardProgram, "aThree");
        gl.enableVertexAttribArray(boardProgram.thirdAttr);

		boardProgram.samplerUniform = gl.getUniformLocation(boardProgram, "uSampler");

        boardProgram.pMatrixUniform = gl.getUniformLocation(boardProgram, "uPMatrix");
        boardProgram.mvMatrixUniform = gl.getUniformLocation(boardProgram, "uMVMatrix");

        var fragmentShader = getShader(gl, "pieceFS");
        var vertexShader = getShader(gl, "pieceVS");

        pieceProgram = gl.createProgram();
        gl.attachShader(pieceProgram, vertexShader);
        gl.attachShader(pieceProgram, fragmentShader);
        gl.linkProgram(pieceProgram);

        if (!gl.getProgramParameter(pieceProgram, gl.LINK_STATUS)) {
            alert("Could not initialise first pieceProgram");
        }

        gl.useProgram(pieceProgram);

        pieceProgram.vertexPositionAttribute = gl.getAttribLocation(pieceProgram, "aVertexPosition");
        gl.enableVertexAttribArray(pieceProgram.vertexPositionAttribute);

        pieceProgram.pieceLA = gl.getAttribLocation(pieceProgram, "aPieceLocation");
        gl.enableVertexAttribArray(pieceProgram.pieceLA);

        pieceProgram.pieceSkinAttribute = gl.getAttribLocation(pieceProgram, "aSkinCoord");
        gl.enableVertexAttribArray(pieceProgram.pieceSkinAttribute);

		pieceProgram.pieceSideAttribute = gl.getAttribLocation(pieceProgram, "aSideColor");
        gl.enableVertexAttribArray(pieceProgram.pieceSideAttribute);

        pieceProgram.pieceTextureAttribute = gl.getAttribLocation(pieceProgram, "aTextureCoord");
        gl.enableVertexAttribArray(pieceProgram.pieceTextureAttribute);

        pieceProgram.samplerUniform = gl.getUniformLocation(pieceProgram, "uSampler");

        pieceProgram.pMatrixUniform = gl.getUniformLocation(pieceProgram, "uPMatrix");
        pieceProgram.mvMatrixUniform = gl.getUniformLocation(pieceProgram, "uMVMatrix");

		gl.bindBuffer(gl.ARRAY_BUFFER, emptyBuffer);
        gl.vertexAttribPointer(pieceProgram.vertexPositionAttribute, 1, gl.FLOAT, false, 0, 0);
		gl.bindBuffer(gl.ARRAY_BUFFER, emptyBuffer);
        gl.vertexAttribPointer(pieceProgram.pieceLA, 1, gl.FLOAT, false, 0, 0);
		gl.bindBuffer(gl.ARRAY_BUFFER, emptyBuffer);
        gl.vertexAttribPointer(pieceProgram.pieceSkinAttribute, 1, gl.FLOAT, false, 0, 0);
		gl.bindBuffer(gl.ARRAY_BUFFER, emptyBuffer);
        gl.vertexAttribPointer(pieceProgram.pieceSideAttribute, 1, gl.FLOAT, false, 0, 0);
		gl.bindBuffer(gl.ARRAY_BUFFER, emptyBuffer);
        gl.vertexAttribPointer(pieceProgram.pieceTextureAttribute, 1, gl.FLOAT, false, 0, 0);

		var fragmentShader = getShader(gl, "boardBackFS");
        var vertexShader = getShader(gl, "boardBackVS");

        boardBGProgram = gl.createProgram();
        gl.attachShader(boardBGProgram, vertexShader);
        gl.attachShader(boardBGProgram, fragmentShader);
        gl.linkProgram(boardBGProgram);

        if (!gl.getProgramParameter(boardBGProgram, gl.LINK_STATUS)) {
            alert("Could not initialise first boardbackground");
        }

        gl.useProgram(boardBGProgram);

        boardBGProgram.vertexPos = gl.getAttribLocation(boardBGProgram, "aVertexPosition");
        gl.enableVertexAttribArray(boardBGProgram.vertexPos);

        boardBGProgram.pMatrixUniform = gl.getUniformLocation(boardBGProgram, "uPMatrix");
        boardBGProgram.mvMatrixUniform = gl.getUniformLocation(boardBGProgram, "uMVMatrix");

		    var fragmentShader = getShader(gl, "highLightFS");
        var vertexShader = getShader(gl, "highLightVS");

        highLightProgram = gl.createProgram();
        gl.attachShader(highLightProgram, vertexShader);
        gl.attachShader(highLightProgram, fragmentShader);
        gl.linkProgram(highLightProgram);

        if (!gl.getProgramParameter(highLightProgram, gl.LINK_STATUS)) {
            alert("Could not initialise first boardbackground");
        }

        gl.useProgram(highLightProgram);

        highLightProgram.vertexPositionAttribute = gl.getAttribLocation(highLightProgram, "aVertexPosition");
        gl.enableVertexAttribArray(highLightProgram.vertexPositionAttribute);

		highLightProgram.offsetUniform = gl.getUniformLocation(highLightProgram, "uOffset");

        highLightProgram.pMatrixUniform = gl.getUniformLocation(highLightProgram, "uPMatrix");
        highLightProgram.mvMatrixUniform = gl.getUniformLocation(highLightProgram, "uMVMatrix");

		tick();
    }


    var mvMatrix = mat4.create();
    var mvMatrixStack = [];
    var pMatrix = mat4.create();

    function mvPushMatrix() {
        var copy = mat4.create();
        mat4.set(mvMatrix, copy);
        mvMatrixStack.push(copy);
    }

    function mvPopMatrix() {
        if (mvMatrixStack.length == 0) {
            throw "Invalid popMatrix!";
        }
        mvMatrix = mvMatrixStack.pop();
    }


    function setMatrixUniforms(program) {
      //console.log("set " + program);
        gl.uniformMatrix4fv(program.pMatrixUniform, false, pMatrix);
        gl.uniformMatrix4fv(program.mvMatrixUniform, false, mvMatrix);
    }


    function degToRad(degrees) {
        return degrees * Math.PI / 180;
    }

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

    var gameBoard, gameBoardTex, emptyBuffer;
    var pieces;
    var pieceLocations, pieceSkins, pieceSides;
  	var boardFrameBuffer;
  	var boardFrameTexture;
  	var highLightPoints;
    var textureCoords;

    function initBuffers() {
		highLightPoints = gl.createBuffer();
		gl.bindBuffer(gl.ARRAY_BUFFER, highLightPoints);
		gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([0.0, 0.0, 0.0,
													0.0, 0.0, 0.2,
													0.2, 0.0, 0.0,
													0.2, 0.0, 0.2]), gl.STATIC_DRAW);

        emptyBuffer = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, emptyBuffer);
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([]), gl.STATIC_DRAW);

		gameBoard = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, gameBoard);
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1.0,0.0,-1.0,-1.0,0.0,1.0,1.0,0.0,-1.0,1.0,0.0,1.0]), gl.STATIC_DRAW);

		gameBoardTex = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, gameBoardTex);
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([0.0, 0.0, 0.0, 1.0, 1.0, 0.0, 1.0, 1.0]), gl.STATIC_DRAW);

        pieces = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, pieces);
        var vertices = [-0.5, 0.0, -0.5,
                        -0.5, 2.0, -0.5,
                        -0.5, 0.0, 0.5,
                        -0.5, 2.0, 0.5,
                        -0.5, 2.0, 0.5,
                        0.5, 0.0, -0.5,
                        0.5, 0.0, -0.5,
                        0.5, 2.0, -0.5,
                        0.5, 0.0, 0.5,
                        0.5, 2.0, 0.5,
                        0.5, 2.0, 0.5,
                        -0.5, 0.0, 0.0,
                        -0.5, 0.0, 0.0,
                        -0.5, 2.0, 0.0,
                        0.5, 0.0, 0.0,
                        0.5, 2.0, 0.0];
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(vertices), gl.STATIC_DRAW);
        pieces.itemSize = 3;

        textureCoords = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, textureCoords);
        var coords = [0.0, 0.0,
                    0.0, 0.0,
                    0.0, 0.0,
                    0.0, 0.0,
                    0.0, 0.0,
                    0.0, 0.0,
                    0.0, 0.0,
                    0.0, 0.0,
                    0.0, 0.0,
                    0.0, 0.0,
                    0.0, 0.0,
                    0.0, 0.750,
                    0.0, 0.750,
                    0.0, 1.0,
                    0.250, 0.750,
                    0.250, 1.0];
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(coords), gl.STATIC_DRAW);

        var locList = [];
		for (i=0; i<80; i++ ) {
			locList.push(0.0, 0.0, 0.0, 0.0);
		}
		pieceLocations = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, pieceLocations);
    	gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(locList), gl.STATIC_DRAW);

        pieceSkins = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, pieceSkins);
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([0.0, 0.0, 0.0, 1.0, 0.0, 2.0, 0.0, 3.0, 0.0, 4.0, 1.0, 0.0, 1.0, 1.0, 1.0, 2.0, 1.0, 3.0]), gl.STATIC_DRAW);

		var sideList = [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2];

		pieceSides = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, pieceSides);
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([]), gl.STATIC_DRAW);


		boardFrameBuffer = gl.createFramebuffer();
		boardFrameTexture = gl.createTexture();

		initTextureFramebuffer(boardFrameBuffer, boardFrameTexture, 1000, 600);
    }

    var textureList = [];
    function initTextures(id, src) {
      textureList[id] = gl.createTexture();
      textureList[id].image = new Image();
      textureList[id].image.onload = function() {
        handleLoadedTexture(textureList[id])
      }

    textureList[id].image.src = src;
    console.log("loaded " + src);
    }

    function handleLoadedTexture(texture) {
      gl.bindTexture(gl.TEXTURE_2D, texture);
      gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
      gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, texture.image);
      gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.NEAREST);
      gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.NEAREST);
      gl.bindTexture(gl.TEXTURE_2D, null);
    }


    function drawScene() {
        gl.viewport(0, 0, gl.viewportWidth, gl.viewportHeight);
        gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);

        mat4.perspective(45, gl.viewportWidth / gl.viewportHeight, 0.1, 100.0, pMatrix);
        mat4.identity(mvMatrix);

    	mat4.rotate(mvMatrix, degToRad(25), [1, 0, 0]);

    		//mat4.rotate(mvMatrix, degToRad(180*(playerSide-2)), [0, 1, 0]);
      	//mat4.translate(mvMatrix, [xPos*flipBoard, -1.0, (-2.5+zPos)]);

        mat4.rotate(mvMatrix, degToRad(0), [0, 1, 0]);
      	mat4.translate(mvMatrix, [xPos*flipBoard, -1.0, (-2.5+zPos)]);
        mvPushMatrix();


    	// Draw whatever on the framebuffer

    	gl.bindFramebuffer(gl.FRAMEBUFFER, boardFrameBuffer);
    	gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);
      gl.useProgram(boardBGProgram);
      gl.bindBuffer(gl.ARRAY_BUFFER, gameBoard);
      gl.vertexAttribPointer(boardBGProgram.vertexPos, 3, gl.FLOAT, false, 0, 0);

      setMatrixUniforms(boardBGProgram);
      gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);

		  gl.bindFramebuffer(gl.FRAMEBUFFER, null);

		  // Draw whatever on the output buffer

      gl.useProgram(boardProgram);

      gl.bindBuffer(gl.ARRAY_BUFFER, gameBoard);
      gl.vertexAttribPointer(boardProgram.VPA, 3, gl.FLOAT, false, 0, 0);

		  gl.bindBuffer(gl.ARRAY_BUFFER, gameBoardTex);
      gl.vertexAttribPointer(boardProgram.textureCoordAttr, 2, gl.FLOAT, false, 0, 0);

		  gl.bindBuffer(gl.ARRAY_BUFFER, gameBoard);
      gl.vertexAttribPointer(boardProgram.thirdAttr, 3, gl.FLOAT, false, 0, 0);

		  gl.activeTexture(gl.TEXTURE1);
      gl.bindTexture(gl.TEXTURE_2D, textureList[1]);
      gl.uniform1i(boardProgram.samplerUniform, 1);

      setMatrixUniforms(boardProgram);
      gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);

    	//gl.blendFunc(gl.SRC_ALPHA, gl.ONE); // This is the gl.ONE :)
    	gl.disable(gl.DEPTH_TEST);

    	gl.useProgram(highLightProgram);
    	gl.bindBuffer(gl.ARRAY_BUFFER, highLightPoints);
        gl.vertexAttribPointer(highLightProgram.vertexPositionAttribute, 3, gl.FLOAT, false, 0, 0);

    	gl.uniform3f(highLightProgram.offsetUniform, highLiteOffset[0],0.0,highLiteOffset[1]);

    	setMatrixUniforms(highLightProgram);
        gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);

        mvPopMatrix();

		gl.enable(gl.DEPTH_TEST);


		// Draw the pieces
        gl.useProgram(pieceProgram);
        gl.bindBuffer(gl.ARRAY_BUFFER, pieces);
        gl.vertexAttribPointer(pieceProgram.vertexPositionAttribute, pieces.itemSize, gl.FLOAT, false, 0, 0);

        gl.bindBuffer(gl.ARRAY_BUFFER, textureCoords);
        gl.vertexAttribPointer(pieceProgram.pieceTextureAttribute, 2, gl.FLOAT, false, 0, 0);

        gl.activeTexture(gl.TEXTURE0);
        gl.bindTexture(gl.TEXTURE_2D, textureList[0]);
        gl.uniform1i(pieceProgram.samplerUniform, 0);

        setMatrixUniforms(pieceProgram);

		gl.bindBuffer(gl.ARRAY_BUFFER, pieceSkins);
        gl.vertexAttribPointer(pieceProgram.pieceSkinAttribute, 2, gl.FLOAT, false, 8, 0);
        ANGLEia.vertexAttribDivisorANGLE(pieceProgram.pieceSkinAttribute, 1);

		    gl.bindBuffer(gl.ARRAY_BUFFER, pieceSides);
        gl.vertexAttribPointer(pieceProgram.pieceSideAttribute, 1, gl.FLOAT, false, 0, 0);
        ANGLEia.vertexAttribDivisorANGLE(pieceProgram.pieceSideAttribute, 1);

		    gl.bindBuffer(gl.ARRAY_BUFFER, pieceLocations);
        gl.vertexAttribPointer(pieceProgram.pieceLA, 2, gl.FLOAT, false, 8, 0);
		    ANGLEia.vertexAttribDivisorANGLE(pieceProgram.pieceLA, 1);

        ANGLEia.drawArraysInstancedANGLE(gl.TRIANGLE_STRIP, 0, 16, drawPieceCount);
		    //ANGLEia.vertexAttribDivisorANGLE(pieceProgram.pieceLA, 0);

    }


    var lastTime = 0;
    var xPos = 0.0;
    var zPos = 0.0;
    var xSpeed = 0.0;
    var zSpeed = 0.0;
    function animate() {
        var timeNow = new Date().getTime();
        if (lastTime != 0) {
            var elapsed = timeNow - lastTime;

            //xPos += xSpeed*elapsed;
			//zPos += zSpeed*elapsed;
        }
        lastTime = timeNow;
		//console.log(xPos);
    }

    var currentlyPressedKeys = {};
	var highLiteOffset = [0, 0];

  	function handleKeyDown(event) {
        //console.log("Press " + event.keyCode);
          currentlyPressedKeys[event.keyCode] = true;
  		}

      function handleKeyUp(event) {
          currentlyPressedKeys[event.keyCode] = false;
  		}

  	function handleKeys() {
  		//alert("set speed");
  		if (currentlyPressedKeys[37] || currentlyPressedKeys[65]) {

  			// Left cursor key or A
  			xSpeed = 0.0005;
  			} else if (currentlyPressedKeys[39] || currentlyPressedKeys[68]) {
  			// Right cursor key or D
  			xSpeed = -0.0005;
  			} else {
  			xSpeed = 0;
  			}

  		if (currentlyPressedKeys[38] || currentlyPressedKeys[87]) {

  			// Up cursor key or W
  			zSpeed = 0.0005;
  			} else if (currentlyPressedKeys[40] || currentlyPressedKeys[83]) {
  			// Down cursor key
  			zSpeed = -0.0005;
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

	var mouseDown = false;
	var lastMouseX = null;
	var lastMouseY = null;
	function handleMouseDown(event) {
		mouseDown = true;
		lastMouseX = event.clientX;
		lastMouseY = event.clientY;
	}

	function handleMouseUp(event) {
		mouseDown = false;
	}

	function handleMouseMove(event) {
		if (!mouseDown) {
			return;
		}
		var newX = event.clientX;
		var newY = event.clientY;

		var deltaX = newX - lastMouseX;
		xPos -= deltaX/500;

		var deltaY = newY - lastMouseY;
		zPos += deltaY/500;

		lastMouseX = newX
		lastMouseY = newY;
	}

	var selectedSquare = [];
	function handleClick(event)	{
		document.body.style.cursor = "auto";
		var loc = findPos(this);
		var rect = this.getBoundingClientRect();
		var cpos = [(event.clientX - loc[0]), (document.getElementById("gameScreen").height - (event.clientY - loc[1]))];

		var pixelValues = new Uint8Array(4);
		gl.bindFramebuffer(gl.FRAMEBUFFER, boardFrameBuffer);
		gl.readPixels(cpos[0], cpos[1], 1, 1, gl.RGBA, gl.UNSIGNED_BYTE, pixelValues);
		gl.bindFramebuffer(gl.FRAMEBUFFER, null);

		if (pixelValues[0] + pixelValues[2] > 0) {
			highLiteOffset[0] = (Math.floor(pixelValues[0]/25.5)-5.0)*0.2;
			highLiteOffset[1] = (Math.floor(pixelValues[2]/25.5)-5.0)*0.2;
			}

		selectedSquare[0] = Math.round(highLiteOffset[0]*5+5);
		selectedSquare[1] = Math.round(highLiteOffset[1]*5+5);
		//console.log("Selected square " + selectedSquare + " - " + highLiteOffset);

    if (gameStatus == 1) {
      if (moveSet == 0) moveOptions();
      else makeMove();
		}
	}

  var moveSet = 0;
  var pvsSpot = [0,0];
  function moveOptions() {
    console.log("moveOptions show Move options for piece " + boardSquares[selectedSquare[0] + selectedSquare[1]*10]);
    var spotPiece = boardSquares[selectedSquare[0] + selectedSquare[1]*10];
    if (spotPiece < 80) {
      console.log("piece selected " + spotPiece + " / " + selectedSquare);
      if (pieceList[spotPiece].side == playerSide) {

        moveSet = 1;
        pvsSpot[0] = selectedSquare[0];
        pvsSpot[1] = selectedSquare[1];
      } else console.log("not youur pieces Moveset:" + moveSet);
    } else console.log("epty sq");
  }

  function makeMove() {
    console.log("Previous is " + pvsSpot);

    // check for friendly units at the locRough
    var newIndex = selectedSquare[0] + selectedSquare[1]*10;
    var oldIndex = pvsSpot[0] + pvsSpot[1]*10;
    if (boardSquares[newIndex] < 99 && Math.floor(boardSquares[newIndex]/40)+1== playerSide) {
      console.log("space is occupied - " + boardSquares[newIndex]);
      moveOptions();
    } else {
      // check that the move is valid by sending it to the server
	  var msg = {
    type: "move",
    gameID: gameID,
		oldSpot: oldIndex,
		newSpot: newIndex};
	  //websocket.send(JSON.stringify(msg));
	  sendToSocket(msg);
    }
  }

  function sync(locs, status) {
	  for (var i=0; i<80; i++) {
		  pieceList[i].changeLoc(locs[2*i], locs[2*i+1]);
		  boardSquares[locs[2*i]+locs[2*i+1]*10] = i;
      pieceList[i].newStatus(status[i]);
      }
    placePieces();
  }

  function syncSide(side, locs, status) {
    var offset = (side-1)*40;
    for (var i=0; i<40; i++) {
      console.log("synce piece " + (i+offset) + " at (" + locs[i*2] +  ", " + locs[i*2+1] +")");
      pieceList[i+offset].changeLoc(locs[i*2], locs[i*2+1]);
      pieceList[i+offset].newStatus(status[i]);
      boardSquares[locs[2*i]+locs[2*i+1]*10] = i;
    }
    console.log("complete side synce for player " + side);
    placePieces();
  }

  function showMove(oldSquare, newSquare, oldPos) {
  	//var newSquare = selectedSquare[0] + selectedSquare[1]*10;
    //var oldSquare = pvsSpot[0] + pvsSpot[1]*10;
  	console.log("Move piece " + boardSquares[oldSquare] + " from " + pvsSpot[0] + ", " + pvsSpot[1] + " to " + oldPos[0] + ", " + oldPos[1]);
  	if (pieceList[boardSquares[oldSquare]].status == 1) {
  		pieceList[boardSquares[oldSquare]].changeLoc(oldPos[0], oldPos[1]);
  		boardSquares[newSquare] = boardSquares[oldSquare];
  	}
  	boardSquares[oldSquare] = 100;
  	moveSet = 0;
  	placePieces();
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

    function tick() {
        requestAnimFrame(tick);
        handleKeys();
        drawScene();
        animate();
    }

    function webGLStart() {
        var canvas = document.getElementById("gameScreen");
		canvas.addEventListener("click", handleClick);
		canvas.onmousedown = handleMouseDown;
		document.onmouseup = handleMouseUp;
		document.onmousemove = handleMouseMove;
    		//canvas.onclick = handleClick;
        initGL(canvas);
        initBuffers();
		initShaders();


		initTextures(0, "pieceSkins2.png");
		initTextures(1, "boardBackGround.jpg");

        gl.clearColor(0.0, 0.0, 0.0, 1.0);
        gl.enable(gl.DEPTH_TEST);

        //document.onkeydown = handleKeyDown;
    	//document.onkeyup = handleKeyUp;


    }

	function addDiv(id, useClassName, target) {
		var trg;
		if (typeof(target) == "string") trg = document.getElementById(target);
		else trg = target;

		var newDiv = document.createElement("div");
		newDiv.className = useClassName;
		newDiv.id = id;
		trg.appendChild(newDiv);
		return newDiv;
	}

	var flipBoard = -1;
	function init() {
		webGLStart();
    loadSocket();

		//console.log(thisPiece);

		offset = 0;
		if (playerSide == 2) {
			offset = 60;
			flipBoard = 0;}
		for (i=0; i<40; i++) {
			boardSquares[i+offset] = 0;
		}

		document.getElementById("startButton").addEventListener("click", function() {startGame(playerSide)});
    document.getElementById("makeGame").addEventListener("click", gameMenu);
		document.getElementById("randomSetup").addEventListener("click", function () {
		if (gameStatus == 0) {
		importSetup(playerSide)}});
	}

	var gameStatus = 0;
	function startGame(sideToStart) {
    console.log("Start side " + sideToStart);
		//gameStatus = 1;

    var offset = (sideToStart-1)*40;
    var locList = [];
    var gameReady = true;

    for (var i=0; i<40; i++) {
      //console.log(pieceList[i+offset].status);
      if (pieceList[i+offset].status ==1) {
        locList = locList.concat(pieceList[i+offset].position);
      } else {
        console.log("not all pieces set");
        gameReady = false;
        break;
      }
    }
  if (gameReady) {
    var msg = {type: "startGame",
    gameID: gameID,
    startSide: sideToStart,
    startSpots: locList,
	startRanks: useRanks};
    //websocket.send(JSON.stringify(msg));
    //console.log("start message prepared")
	   sendToSocket(msg);
  } else console.log("Start not sent");

}
	var useRanks;
	function loadPieces() {
		rankList = [1, 2, 3, 3, 4, 4, 4, 5, 5, 5, 5, 6, 6, 6, 6, 7, 7, 7, 7, 8, 8, 8, 8, 8, 9, 9, 9, 9, 9, 9, 9, 9, 10, 11, 11, 11, 11, 11, 11, 12];
		useRanks = [];
		var placeNum;
		while (rankList.length>0) {
			placeNum = Math.floor(Math.random()*rankList.length);
			useRanks.push(rankList[placeNum]);
			rankList.splice(placeNum, 1);
		}
		var count = 0;
		for (side=1; side<3; side++) {
			//console.log("Make " + rankList.length + " piece for side "+ side);
			for (rank=0; rank<useRanks.length; rank++) {
				//console.log("make " + count + ", " + useRanks[rank] + ", " + side);
				if (side == playerSide) pieceList.push(new piece(count, useRanks[rank], side));
				else pieceList.push(new piece(count, 0, side));
				count++;
			}
		}
	//console.log(pieceList);
	}

	function selectPiece() {
		squareID = selectedSquare[0] + selectedSquare[1]*10;

		if (gameStatus == 0) {
			if (boardSquares[squareID] == 100) {
				// Place the piece in an empty tile
				boardSquares[squareID] = this.pieceID;
				pieceList[this.pieceID].changeLoc(selectedSquare[0], selectedSquare[1]);
				pieceList[this.pieceID].newStatus(1);
				placePieces();
			}
			else if (boardSquares[squareID] < 80) {
				// Swithc pieces already placed on a tile
				// Remove the existing unit
				pieceList[boardSquares[squareID]].changeLoc(0, 0);
				pieceList[boardSquares[squareID]].newStatus(0);

				// Add the new unit
				boardSquares[squareID] = this.pieceID;
				pieceList[this.pieceID].changeLoc(selectedSquare[0], selectedSquare[1]);
				pieceList[this.pieceID].newStatus(1);
				placePieces();
			}
			else {
				console.log("invalid location");
			}
		}
		else if (gameStatus == 1) {}
	}

	class piece {
		constructor (id,rank, side) {
		this.pieceID = id;
		this.pieceRank = rank;
		this.position = [0,0];
		this.status = 0; // 0 = not set, 1 = in play, 2 = dead;
		this.side = side;
		}

		changeLoc(x, y) {
			this.position = [x, y];
			//console.log("Set piece " + this.pieceID + " to location " + this.position);
		}

		newStatus(val) {
			this.status = val;
			if (this.side == playerSide) document.getElementById("piece_"+this.pieceID).className = "pieceStyle_"+val;
		}
	}

	var drawPieceCount = 0;
	function killPiece(tileID) {
		var trg = boardSquares[tileID];
		boardSquares[tileID]= 100;
		pieceList[trg].newStatus(2);
    placePieces();
	}

	function placePieces() {
		var locList = [];
		var skinList = [];
		var sideList = [];
		for (var i=0; i<pieceList.length; i++) {
			if (pieceList[i].status == 1) {
				//console.log(pieceList[i].pieceID + " has status of " + pieceList[i].status + " and rank of " + pieceList[i].pieceRank + " and a loc of " + pieceList[i].position);
				locList.push(pieceList[i].position[0]-5, pieceList[i].position[1]-5);
				skinList.push(pieceList[i].pieceRank, pieceList[i].pieceRank);
				sideList.push(pieceList[i].side);
				}
		}
  console.log("Sidelist = " + sideList);
	gl.bindBuffer(gl.ARRAY_BUFFER, pieceLocations);
	gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(locList), gl.STATIC_DRAW);

	gl.bindBuffer(gl.ARRAY_BUFFER, pieceSkins);
	gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(skinList), gl.STATIC_DRAW);

	gl.bindBuffer(gl.ARRAY_BUFFER, pieceSides);
	gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(sideList), gl.STATIC_DRAW);

	drawPieceCount = locList.length/2;
	//console.log("draw " + drawPieceCount);
	//console.log(locList);
	}

	function importSetup(playerNum) {
    console.log("start import");;
		var pieceOffset = 0;
		var boardOffset = 0;
		if (playerNum == 2) {
			pieceOffset = 40;
			boardOffset = 60;
			}

		// generate list of opp pieces
		var oppPieces = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39];
		var pieceNum, placeNum, x, y;
		for (var i=0; i<40; i++) {
			placeNum = Math.floor(Math.random()*oppPieces.length);
			pieceNum = oppPieces[placeNum] + pieceOffset;
			oppPieces.splice(placeNum, 1);
			//console.log("place piece " + pieceNum);

			y = Math.floor((i+boardOffset)/10);
			x = (i+boardOffset)-y*10;
			boardSquares[i+boardOffset] = pieceNum;

      //console.log("set square " + (i+boardOffset) + " with piece " + pieceNum);
			pieceList[pieceNum].changeLoc(x, y);
			pieceList[pieceNum].newStatus(1);
		}

    console.log("complete import");
		placePieces();
	}

	var pieceList = new Array;
	var boardSquares = Array(100).fill(100);
	var playerSide = 1;
	var turn = 1;
  var playerID = '.$playerID.';
  var gameID = 0;
	window.addEventListener("load", init);
</script>

<style>
.infoPane {
	width:310;
	height:90%;
	border: 1px solid red;
	right:5;
	top:5;
	position:absolute;
}

.pieceBox {
	width:75;
	height:75;
	border: 1px solid blue;
	position:relative;
	float:left;
}

.pieceStyle_0 {
	width:75;
	height:75;
	border: 1px solid red;
	position:relative;
	float:left;
  background:#AAAAAA;
}

.pieceStyle_1 {
	width:75;
	height:75;
	border: 1px solid green;
	position:relative;
	float:left;
  background:#FFFFFF;
}

.pieceStyle_2 {
	width:75;
	height:75;
	border: 1px solid purple;
	position:relative;
	background:#000000;
	float:left;
}

.chat_wrapper {
	width: 500px;
	margin-right: auto;
	margin-left: auto;
	background: #CCCCCC;
	border: 1px solid #999999;
	padding: 10px;
	font: 12px "lucida grande",tahoma,verdana,arial,sans-serif;
  position:absolute;
  top:505;
  left:200;
}
.chat_wrapper .message_box {
	background: #FFFFFF;
	height: 150px;
	overflow: auto;
	padding: 10px;
	border: 1px solid #999999;
}
.chat_wrapper .panel input{
	padding: 2px 2px 2px 5px;
}
.system_msg{color: #BDBDBD;font-style: italic;}
.user_name{font-weight:bold;}
.user_message{color: #88B6E0;}

.createGameMenu{
  position:absolute;
  left:100;
  top:100;
  height:500;
  width:500;
  border:1px solid red;
  background-color: white;
}

.createButton{
  height:50;
  width:100;
  border:1px solid red;
}

.openGames{
  height:400;
  width:500;
  border:1px solid red;
  background-color: white;
}
.gameContain{
  height:50;
  width:500;
  border:1px solid blue;
}

.gameMessageBox{
  position:absolute;
  left:750;
  top:500;
  border:1px solid red;
  background-color: white;
  height:200;
  width:200;
}
</style>

</head>


<body>
  <div id="container">
    <canvas id="gameScreen" style="border: none;" width="1000" height="600"></canvas>
	  <div id="rightPane" class="infoPane"></div>
    <div id="startButton">Start Game</div>
    <div id="makeGame">Create a Game</div>
    <div id="gameMessageBox" class="gameMessageBox">Game Messages</div>

    <div id="randomSetup">Random Setup</div>
    <div id="infoPanel">
      <div id="gameID"></div>
    </div>


	<div class="chat_wrapper">
	<div class="message_box" id="message_box"></div>
	<div class="panel">
	<input type="text" name="name" id="name" placeholder="Your Name" maxlength="10" style="width:20%"  />
	<input type="text" name="message" id="message" placeholder="Message" maxlength="80" style="width:60%" />
	<button id="send-btn">Send</button>
    <br/>
  </div>
</body>

</html>';

?>
