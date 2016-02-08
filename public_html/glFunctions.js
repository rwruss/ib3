


var gl;
var taskList;
function initGL(canvas) {
        try {
            gl = canvas.getContext("experimental-webgl");
            gl.viewportWidth = canvas.width;
            gl.viewportHeight = canvas.height;
        } catch (e) {
        }
        if (!gl) {
            alert("Could not initialise WebGL, sorry :-(");
        }
    }
	
var lastTime = 0;
var wY = 0;
var xSpeed = 0;
var zSpeed = 0;

var gl;

function initGL(canvas) {
	try {
		gl = canvas.getContext('experimental-webgl');
		gl.viewportWidth = canvas.width;
		gl.viewportHeight = canvas.height;
	} catch (e) {
	}
	if (!gl) {
		alert('Could not initialise WebGL, sorry :-(');
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

var shaderProgram;

    function initShaders() {
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

        shaderProgram.pMatrixUniform = gl.getUniformLocation(shaderProgram, "uPMatrix");
        shaderProgram.mvMatrixUniform = gl.getUniformLocation(shaderProgram, "uMVMatrix");
		
		shaderProgram.progUniform = gl.getUniformLocation(shaderProgram, "progPct");
		
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

	

    function setMatrixUniforms() {
        gl.uniformMatrix4fv(shaderProgram.pMatrixUniform, false, pMatrix);
        gl.uniformMatrix4fv(shaderProgram.mvMatrixUniform, false, mvMatrix);		
    }

	var taskBuffer;
	var progBuffer;

    function initBuffers() {
        taskBuffer = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, taskBuffer);       
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([0.0, 0.0, 0.0, 0.0, 1.0, 0.0, 1.0, 0.0, 0.0, 1.0, 1.0, 0.0]), gl.STATIC_DRAW);		
		
		progBuffer = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, progBuffer);       
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([0.0, 0.0, 0.0, 
			0.0, 0.15, 0.0, 
			1.0, 0.0, 0.0, 
			1.0, 0.15, 0.0]), gl.STATIC_DRAW);		
		}
		
	function degToRad(degrees) {
        return degrees * Math.PI / 180;
		}
		
	function degToRad(degrees) {
        return degrees * Math.PI / 180;
		}
	
	var rY = 0.0;
    function drawScene() {
        gl.viewport(0, 0, gl.viewportWidth, gl.viewportHeight);
        gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);

        mat4.perspective(45, gl.viewportWidth / gl.viewportHeight, 0.1, 500.0, pMatrix);
        mat4.identity(mvMatrix);		
		
		for (var i=0; i<taskList.length/3.0; i++) {
			//gl.uniform1f(shaderProgram.progUniform, false, (timeNow/1000.0 - taskList[i*3])/(taskList[i*3+1]-taskList[i*3]));
			pct = (lastTime - taskList[i*3])/(taskList[i*3+1]*1000.0);
			if (pct < 1.0) {
				gl.uniform1f(shaderProgram.progUniform, (lastTime - taskList[i*3])/(taskList[i*3+1]*1000.0));
				mvPushMatrix();		
				mat4.translate(mvMatrix, [-8.0+i*1.5, -5.0, -15.0]);
				gl.bindBuffer(gl.ARRAY_BUFFER, taskBuffer);
				gl.vertexAttribPointer(shaderProgram.vertexPositionAttribute, 3, gl.FLOAT, false, 0, 0);

				setMatrixUniforms();
				gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
				mvPopMatrix();
				}
			else taskList.splice(i*3, 3);
			}
		}
var locTr = new Array(0, 0);
var lastTime = 0;
function animate() {
	var timeNow = new Date().getTime();
	if (lastTime != 0) {
		var elapsed = timeNow - lastTime;
		rY += elapsed*wY;
		
		locTr[0] += 10*(zSpeed*elapsed*Math.sin(rY)+xSpeed*elapsed*Math.cos(rY));
		locTr[1] += 10*(zSpeed*elapsed*Math.cos(rY)-xSpeed*elapsed*Math.sin(rY));
		}		
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
		xSpeed = -document.getElementById('zVal').value/10000;
		} else if (currentlyPressedKeys[39] || currentlyPressedKeys[68]) {
		// Right cursor key or D
		xSpeed = document.getElementById('zVal').value/10000;
		} else {
		xSpeed = 0;
		}

	if (currentlyPressedKeys[38] || currentlyPressedKeys[87]) {
		
		// Up cursor key or W
		zSpeed = -document.getElementById('zVal').value/10000;
		} else if (currentlyPressedKeys[40] || currentlyPressedKeys[83]) {
		// Down cursor key
		zSpeed = document.getElementById('zVal').value/10000;
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


 function webGLStart() {
	var canvas = document.getElementById('gameCanvas');
	initGL(canvas);
	initShaders()
	initBuffers();
	//getData();
	gl.clearColor(1.0, 1.0, 1.0, 1.0);
	gl.enable(gl.DEPTH_TEST);

   tick();
   
   document.onkeydown = handleKeyDown;
   document.onkeyup = handleKeyUp;
	}
	