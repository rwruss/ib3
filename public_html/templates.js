
addDiv = function(id, useClassName, target) {
	var newDiv = document.createElement("div");
	newDiv.className = useClassName;
	newDiv.id = id;
	target.appendChild(newDiv);
	return newDiv;
}

addImg = function(id, useClassName, target) {
	var newImg = document.createElement("img");
	newImg.className = useClassName;
	newImg.id = id;
	//alert(target)
	target.appendChild(newImg);
}

confirmBox = function (msg, prm, type, trg, aSrc, dSrc) {
	var boxHolder = addDiv("confirmBox", "cBox", document.getElementsByTagName('body')[0]);
	boxHolder.style.zIndex = zCount+9999;
	zCount++;
	var boxMsg = addDiv("confirmBox", "cBoxM", boxHolder)
	var dButton = addDiv("optionDecline", "cBoxD", boxHolder);


	boxMsg.innerHTML = msg;
	if (type == 2 || 3) {
		var acceptButton = addDiv("optionAccept", "cBoxA", boxHolder);
		if (aSrc) {
			acceptButton.innerHTML = aSrc;
		} else {
			acceptButton.innerHTML = "Accept";
		}
		acceptButton.addEventListener("click", function() {
			console.log("accepted");
			this.parentNode.parentNode.removeChild(this.parentNode);
			scrMod(prm)});
	}

	if (type == 1 || 2) {
		if (dSrc.length > 0) {
			dButton.innerHTML = dSrc;
		} else {
			dButton.innerHTML = "Decline";
		}
	}

	if (trg.length > 0) {
		dButton.addEventListener("click", function() {
			this.parentNode.parentNode.removeChild(this.parentNode);
			killBox(document.getElementById(trg))});
	} else {
		dButton.addEventListener("click", function() {
			this.parentNode.parentNode.removeChild(this.parentNode)});
		}
}

confirmButtons = function (msg, prm, trg, opt, asrc, dsrc) {

	var boxHolder = addDiv(trg+"_confirmButtons", "cButtons", document.getElementById(trg));

	var boxMsg = addDiv("confirmBox", "cBoxM", boxHolder);
	var buttonHolder = addDiv(trg+"buttonHolder", "cButtons", boxHolder);

	boxMsg.innerHTML = msg;

	if (opt == 2 || opt == 3) {
		var acceptButton = addDiv("optionAccept", "cBoxA", buttonHolder);
		if (asrc) {
			acceptButton.innerHTML = asrc;
		} else {
			acceptButton.innerHTML = "Accept";
		}
		acceptButton.addEventListener("click", function() {scrMod(prm)});
	}

	if (opt == 1 || opt == 2) {
		var dButton = addDiv("optionDecline", "cBoxD", buttonHolder);
		if (dsrc) {
			dButton.innerHTML = dsrc;
		} else {
			dButton.innerHTML = "Decline";
		}
		dButton.addEventListener("click", killBox);
	}
}

optionButton = function (prm, trg, src) {
	var newButton = addDiv("button", "cBoxA", document.getElementById(trg));
	//newButton.addEventListener("click", function () {scrMod(prm)})
	newButton.innerHTML = src;
	return newButton;
}

scrButton = function (prm, trg, src) {
	//console.log("Use prm " + prm);
	var newButton = addDiv("button", "button", trg);
	newButton.addEventListener("click", function () {scrMod(prm)})
	newButton.innerHTML = src;
	return newButton;
}

killButton = function (trg, src) {
	var newButton = addDiv("button", "button", trg);
	newButton.addEventListener("click", function () {
		this.parentNode.parentNode.removeChild(this.parentNode);
		killBox(document.getElementById(trg))});
}

boxButton = function (prm, trg, src) {
	var newButton = addDiv("button", "button", trg);
	newButton.addEventListener("click", function () {makeBox("assignLeader", prm, 500, 500, 200, 50)})
	newButton.innerHTML = src;
	return newButton;
}

confirmButton = function (msg, prm, trg, src) {
	var newButton = addDiv("button", "button", trg);
	newButton.addEventListener("click", function () {confirmBox(msg, prm, 2, document.getElementsByTagName('body')[0], "Yes", "No")});
	//confirmBox = function (msg, prm, type, trg, aSrc, dSrc)
	newButton.innerHTML = src;
	return newButton;
}

plotDetail = function (obj, trg) {
	var newPlot = document.createElement("div");
	newPlot.className = "plotSummary";

	var descBox = addDiv("descBox", "plotDesc", newPlot);
	descBox.innerHTML = obj.desc;
	var prm1 = "1081,"+obj.id;
	descBox.addEventListener("click", function () {makeBox("plotDtl", prm1, 500, 500, 200, 50)});

	var targetBox = addDiv("targetBox", "plotTarget", newPlot);
	var dtlBox = addDiv("dtlBox", "plotDtl", newPlot);

	trg.appendChild(newPlot);
	return newPlot;
}

plotSummary = function (obj, trg) {
	//console.log("mk a plot sum - " + trg);
	var newPlot = document.createElement("div");
	newPlot.className = "plotSummary";

	var dtlButton = addDiv("", "sumDtlBut", newPlot);
	var prm1 = "1081,"+obj.unitID;
	dtlButton.addEventListener("click", function () {makeBox("plotDtl", prm1, 500, 500, 200, 50)});

	var targetBox = addDiv("targetBox", "plotTarget", newPlot);
	newPlot.dtlBox = addDiv("dtlBox", "plotDtl", newPlot);

	newPlot.dtlBox.innerHTML = "plot Details";
	//console.log("attach the plot to " + trg);
	var actDiv = addDiv("asdf", "sumAct", newPlot.dtlBox);
	actDiv.setAttribute("data-boxName", "apBar");
	actDiv.setAttribute("data-boxUnitID", obj.unitID);

	var optBar = addDiv("", "fullBar", newPlot);
	targetBox.innerHTML = obj.unitName;
	trg.appendChild(newPlot);
	return newPlot;
}

plotDtlWork = function (obj, trg) {
	//console.log("work options for " + obj.unitID);
	console.log("dtl work " + obj)
	var plotBox = plotSummary(obj, document.getElementById("plotDtlContent"));
	trgBox = addDiv("charBox", "tdHolder", plotBox);

	plotBox.buttonBox = addDiv("", "fullBar", plotBox);
	plotBox.buttonBox2 = addDiv("", "fullBar", plotBox);
	confirmButton("Leave this plot?", "1088,"+obj.unitID, plotBox.buttonBox2, "Leave Plot");
	//scrButton("1087", plotBox.buttonBox, "Leave Plot");
	scrButton("1084,6,"+ obj.unitID+",1", plotBox.buttonBox, "10%");
	scrButton("1084,6,"+ obj.unitID+",2", plotBox.buttonBox, "25%");
	scrButton("1084,6,"+ obj.unitID+",3", plotBox.buttonBox, "50%");
	scrButton("1084,6,"+ obj.unitID+",4", plotBox.buttonBox, "100%");

	return plotBox;
}

makeTabMenu = function(id, trg) {
	var tabObject = addDiv(id+"_header", "taskHeader", trg);
	var tabCM = addDiv(id+"_tabs", "centeredmenu", trg);
	var tabUL = document.createElement("ul");
	tabUL.id = id+"_tabs_ul";
	tabCM.appendChild(tabUL);
	addDiv("task_"+id+"_options", "taskOptions", trg);
	
	newTabMenu("task_"+id+"_tabs");
	return tabObject;
	//<div class="taskHeader" id="task_'.$postVals[1].'_header"></div>
	//<div class="centeredmenu" id="task_'.$postVals[1].'_tabs"><ul id="task_'.$postVals[1].'_tabs_ul"></ul></div>
	//<div class="taskOptions" id="task_'.$postVals[1].'_options"></div>';
}

newTabMenu = function(target) {
	var tabHolder = document.getElementById(target+"_tabs");
	tabHolder.currentSelection = 1;
}

newTab = function(target, count, desc) {
	var tabHead = document.createElement("li");
	tabHead.id = target+"_head"+count;
	document.getElementById(target+"_tabs_ul").appendChild(tabHead);
	tabHead.addEventListener("click", function() {tabSelect(target, count);});
	if (desc) tabHead.innerHTML = desc;
	else tabHead.innerHTML = "Option " + count;

	var tabContent = document.createElement("div");
	tabContent.className = "tabBox";
	tabContent.id = target+"_tab"+count;
	document.getElementById(target+"_options").appendChild(tabContent);
	
	return tabContent;
}

tabSelect = function(target, selection) {
	var tabHolder = document.getElementById(target+"_tabs");
	document.getElementById(target+"_tab"+selection).style.visibility =  "visible";
	//alert(document.getElementById(target+"_tabs").style.visibility);
	if (tabHolder.currentSelection != selection)	{
		//alert("set " + target+"_tab"+tabHolder.currentSelection + "to 1");
		document.getElementById(target+"_tab"+tabHolder.currentSelection).style.visibility =  "hidden";
	}
	tabHolder.currentSelection = selection;
}

messageBox = function (msg, trg) {
	var boxHolder = addDiv("confirmBox", "cBox", document.getElementsByTagName('body')[0]);
	var boxMsg = addDiv("confirmBox", "cBoxM", boxHolder)
	var dButton = addDiv("optionDecline", "cBoxD", boxHolder);

	boxMsg.innerHTML = msg;
	dButton.innerHTML = "OK";
	dButton.addEventListener("click", function() {
		this.parentNode.parentNode.removeChild(this.parentNode)});
}

reqBox = function (src, trg, have, need) {

		if (have < need) {
			var rscBox = addDiv("confirmBox", "reqBox2", document.getElementById(trg));
		} else {
			var rscBox = addDiv("confirmBox", "reqBox1", document.getElementById(trg));
		}

		addImg(src, "reqImg", rscBox);
		var textDiv = addDiv("a", "reqText", rscBox);
		textDiv.innerHTML = src + ': ' + have + '/' + need;
}

resourceBox = function (id, qty, target) {
	rBox = addDiv(id, "rscQty", document.getElementById(target));
	rBox.innerHTML = id + ' = ' + qty;

	return rBox;
}



taskOpt = function(id, target, prm, desc) {
	var thisOpt = addDiv(id, "tdHolder", document.getElementById(target));
	if (desc) thisOpt.innerHTML = desc;

	if (prm) thisOpt.addEventListener("click", function() {makeBox("taskDtl", "1026,"+id+","+prm,500, 500, 200, 50);});
	else thisOpt.addEventListener("click", function() {makeBox("taskDtl", "1026,"+id,500, 500, 200, 50);});
}

textBlob = function (id, target, content) {
	var thisBlob = addDiv(id, "textBlob", document.getElementById(target));
	thisBlob.innerHTML= content;
	thisBlob.style.width = "100%";

	return thisBlob;
}

newBldgOpt = function(id, base, target, desc) {
	var thisDetail = addDiv(id, "tdHolder", document.getElementById(target));
	addImg("bldg_"+id+"_img", "bldgImg", thisDetail);
	var lvlDiv = addDiv("bldg_"+id+"_lvl", "bldgLvl", thisDetail);
	var descDiv = addDiv("bldg_"+id+"_desc", "bldgDesc", thisDetail);
	lvlDiv.innerHTML = "1";
	descDiv.innerHTML = desc;
	document.getElementById("bldg_"+id+"_img").src = "./textures/borderMask3.png"

	thisDetail.addEventListener("click", function() {makeBox("bldgStart", "1049,"+id + "," + base, 500, 500, 200, 50);});
}

newBldgSum = function(id, target, pctComplete, status) {
	var thisDetail = addDiv(id, "tdHolder", document.getElementById(target));
	addDiv("bldg_"+id+"_cond", "udAct", thisDetail);
	var statusBox = addDiv("bldg_"+id+"_stat", "bldgLvl", thisDetail);
	statusBox.innerHTML = status;
	setBarSize("bldg_"+id+"_cond", pctComplete, 150);
	addImg("bldg_"+id+"_img", "tdImg", thisDetail);
	document.getElementById("bldg_"+id+"_img").src = "./textures/borderMask3.png"

	thisDetail.addEventListener("click", function() {makeBox("bldgDtl", "1048,"+id, 500, 500, 200, 50);});
}

newTaskDetail = function(id, target, pctComplete, killLink) {
	var thisDetail = addDiv(id, "tdHolder", document.getElementById(target));
	addDiv(id+"_prog", "udAct", thisDetail);
	setBarSize(id+"_prog", pctComplete, 150);
	addImg(id+"_img", "tdImg", thisDetail);
	document.getElementById(id+"_img").src = "./textures/borderMask3.png"

	if (!killLink) thisDetail.addEventListener("click", function() {makeBox("taskDtl", "1040,"+id, 500, 500, 200, 50);});
	//alert('New task finished');

}

newTaskSummary = function(id, target, pctComplete) {
	var thisDetail = addDiv("tSum_"+id, "tdHolder", document.getElementById(target));
	addDiv("tSum_"+id+"_prog", "udAct", thisDetail);
	setBarSize("tSum_"+id+"_prog", pctComplete, 150)
	addImg("tSum_"+id+"_img", "tdImg", thisDetail);
	document.getElementById("tSum_"+id+"_img").src = "./textures/borderMask3.png"

	thisDetail.addEventListener("click", function() {makeBox("taskDtl", "1040,"+id, 500, 500, 200, 50);});
	//alert('New task finished');
}

newTaskOpt = function(id, target) {
	var thisDetail = addDiv("taskOpt_"+id, "tdHolder", document.getElementById(target));
	thisDetail.innerHTML = id;
}

unitTaskOpt = function(id, target, desc) {
	var thisOpt = addDiv("utOpt_"+id, "tdHolder", document.getElementById(target));
	thisOpt.innerHTML = desc;

	thisOpt.addEventListener("click", function () {makeBox("taskDtl", "1060,"+id, 500, 500, 200, 50);});
}

charTaskOpt = function(id, target, desc) {
	var thisOpt = addDiv("utOpt_"+id, "tdHolder", document.getElementById(target));
	thisOpt.innerHTML = desc;

	thisOpt.addEventListener("click", function () {makeBox("taskDtl", "1078,"+id, 500, 500, 200, 50);});
}

newUnitDetail = function(id, target) {

	var holderDiv = document.createElement("div")
	holderDiv.className = "udHolder";
	holderDiv.id = "Udtl_"+id;

	var uDAv = document.createElement("img");
	uDAv.className = "udAvatar";
	uDAv.id = "Udtl_"+id+"_avatar";
	holderDiv.appendChild(uDAv);

	var uDType = document.createElement("div");
	uDType.className = "udType"
	uDType.id = "Udtl_"+id+"_type";
	holderDiv.appendChild(uDType);

	var uDLvl = document.createElement("div");
	uDLvl.className = "udLvl";
	uDLvl.id = "Udtl_"+id+"_lvl";
	holderDiv.appendChild(uDLvl);

	var uDAct = document.createElement("div");
	uDAct.className = "uDAct";
	uDAct.id = "Udtl_"+id+"_act";
	holderDiv.appendChild(uDAct);

	var uDExp = document.createElement("div");
	uDExp.className = "udExp";
	uDExp.id = "Udtl_"+id+"_exp";
	holderDiv.appendChild(uDExp);

	var uDName = document.createElement("div");
	uDName.className = "udName";
	uDName.id = "Udtl_"+id+"_name";
	holderDiv.appendChild(uDName);

	var uDGoto = document.createElement("img");
	uDGoto.className = "udGoto";
	holderDiv.appendChild(uDGoto);

	document.getElementById(target).appendChild(holderDiv);

	return
}

plotSum = function (id, target) {
	var holder = document.createElement("div");
	holder.className = "tdHolder";

	var targets = document.createElement("div");
	targets.className = "stdContain";
	targets.id = "plot_"+id+"_targets";

	var progress = document.createElement("div");
	progress.className = "stdContain";
	progress.id = "plot_"+id+"_progress";

	document.getElementById(target).appendChild(holder);
}

scrSelectBox = function (trg) {
	if (selectedItem)	selectedItem.style.borderColor = "000000";
	selectedItem = trg.parentNode;
	trg.parentNode.style.borderColor = "#FF0000";
	console.log(trg);
}

selectionHead = function (trg) {
	var container = addDiv("", "stdContainer", trg);
	container.left = addDiv("", "stdContainer", container);
	container.center = addDiv("", "stdContainer", container);
	container.right = addDiv("", "stdContainer", container);

	container.left.style.width = "33%";
	container.center.style.width = "33%";
	container.center.style.textAlign = "CENTER";
	container.right.style.width = "33%";

	return container;
}
/*
var selectedItem;
selectItem = function (trg) {
	if (selectedItem)	selectedItem.style.borderColor = "000000";
	selectedItem = trg.parentNode;
	trg.parentNode.style.borderColor = "#FF0000";
	console.log(trg);
}
*/
setBarSize = function(id, pct, full) {
	if (document.getElementById(id)) {
		document.getElementById(id).style.width = full * pct;
		//document.getElementById("Udtl_"+id+"_act").style.color = 150 * pct;
		var colorVal = 255*pct;
		var r = parseInt(255*(1-pct));
		var g = parseInt(255*pct);
		var b = parseInt(0);
		document.getElementById(id).style.background = "rgb(" + r + "," + g + ",0)";
	}
}

setUnitAction = function(id, pct) {
	document.getElementById("Udtl_"+id+"_act").style.width = 150 * pct;
	//document.getElementById("Udtl_"+id+"_act").style.color = 150 * pct;
	var colorVal = 255*pct;
	var r = parseInt(255*(1-pct));
	var g = parseInt(255*pct);
	var b = parseInt(0);
	document.getElementById("Udtl_"+id+"_act").style.background = "rgb(" + r + "," + g + ",0)";
}

setUnitExp = function(id, pct) {
	//alert("exp set");
	document.getElementById("Udtl_"+id+"_exp").style.width = 150 * pct;
	//document.getElementById("Udtl_"+id+"_act").style.color = 150 * pct;
	var colorVal = 255*pct;
	var r = parseInt(255*(1-pct));
	var g = parseInt(255*pct);
	var b = parseInt(0);
	//alert("rgb(" + r + "," + g + ",0)");
	document.getElementById("Udtl_"+id+"_exp").style.background = "rgb(" + r + "," + g + ",0)";
}
bPos = [0,0];
paneBox = function(bName, val, h, w, x, y) {
	var newDiv = document.createElement('div');
	newDiv.draggable = "true";

	newDiv.addEventListener("drag", function () {
		if (event.clientX > 0) {
		this.style.left = bPos[0] - bPos[2] + event.clientX;
		this.style.top = bPos[1] - bPos[3] + event.clientY;
		}
	});
	newDiv.addEventListener("dragend", function () {

		this.style.left = bPos[0] - bPos[2] + event.clientX;
		this.style.top = bPos[1] - bPos[3] + event.clientY;
	});

	var killBut = document.createElement('div');
	killBut.className = "paneCloseButton";
	killBut.innerHTML = 'X';

	var newContent = document.createElement('div');
	newContent.className = "paneContent"
	newContent.style.overflow = "auto";

	document.getElementsByTagName('body')[0].appendChild(newDiv);
	newDiv.appendChild(killBut);
	newDiv.appendChild(newContent);
	return newDiv;
}

newMoveBox = function(id, x, y, target) {
	moveString[0] = id;
	drawLoc = [x, y, x, y];

	var mBContain = document.createElement("div");
	mBContain.id = "moveBox_"+id;
	mBContain.className = "mbContain";

	var mbBL = document.createElement("img");
	mbBL.className = "mbBL";
	mbBL.addEventListener("click", function() {move(1)});
	mBContain.appendChild(mbBL);

	var mbBC = document.createElement("img");
	mbBC.className = "mbBC";
	mbBC.addEventListener("click", function() {move(2)});
	mBContain.appendChild(mbBC);

	var mbBR = document.createElement("img");
	mbBR.className = "mbBR";
	mbBR.addEventListener("click", function() {move(3)});
	mBContain.appendChild(mbBR);

	var mBTL = document.createElement("img");
	mBTL.className = "mbTL";
	mBTL.addEventListener("click", function() {move(7)});
	mBContain.appendChild(mBTL);

	var mBCL = document.createElement("img");
	mBCL.className = "mbCL";
	mBCL.addEventListener("click", function() {move(4)});
	mBContain.appendChild(mBCL);

	var mBCR = document.createElement("img");
	mBCR.className = "mbCR";
	mBCR.addEventListener("click", function() {move(6)});
	mBContain.appendChild(mBCR);

	var mBTC = document.createElement("img");
	mBTC.className = "mbTC";
	mBTC.addEventListener("click", function() {move(8)});
	mBContain.appendChild(mBTC);

	var mBTR = document.createElement("img");
	mBTR.className = "mbTR";
	mBTR.addEventListener("click", function() {move(9)});
	mBContain.appendChild(mBTR);

	var mBBK = document.createElement("img");
	mBBK.className = "mbBK";
	mBBK.addEventListener("click", function() {move(10)});
	mBContain.appendChild(mBBK);

	var mBClear = document.createElement("img");
	mBClear.className = "mbClear";
	mBClear.addEventListener("click", function() {move(11)});
	mBContain.appendChild(mBClear);

	var mBSend = document.createElement("img");
	mBSend.className = "mbSend";
	mBSend.addEventListener("click", function() {orderMove()});
	mBContain.appendChild(mBSend);


	document.getElementById(target).appendChild(mBContain);
}

warDetail = function(id, target) {
	var container = addDiv(id, "tdHolder", document.getElementById(target));
	container.innerHTML = "War "+id;

	container.addEventListener("click", function () {makeBox("warDtl", "1057,"+id, 500, 500, 200, 50);});
}


addtion = function () {
	var trg = document.getElementById("objNum").value;
	var amt = document.getElementById("amt").value;
	unitList.add(trg, "strength", amt);
}

class unitList {

	newUnit (object) {
		if (this["unit_" + object.unitID]) {

		} else {
			if (object.unitType == "warband") {
				this["unit_" + object.unitID] = new warband(object);
			}
			else if (object.unitType == "character") {
				this["unit_" + object.unitID] = new character(object);
			}
			else if (object.unitType == "plot") {
				this["unit_" + object.unitID] = new plot(object);
			}
		}
	}

	renderSum(id, target) {
		if (this["unit_"+id]) {
			return this["unit_"+id].renderSummary(target);
			//console.log("Unit " + id + " Summary");
		} else {
			console.log("Unit " + id + " Render Error")
		}
	}

	renderDtl(id, target) {
		if (this["unit_"+id]) {
			this["unit_"+id].renderDetail(target);
		} else {
		}
	}

	renderDtlWork(id, target) {
		if (this["unit_"+id]) {
			console.log(this["unit_"+id]);
			this["unit_"+id].renderDetailWork(target);
		} else {
		}
	}

	change(id, desc, value) {
		if (this["unit_"+id]) {
			//this["unit_"+id].changeAttr(id, desc, value);
			console.log("Change " + this["unit_"+id][desc] + " to " + value);
			this["unit_"+id][desc] = value;

		} else {
		}
	}

	add(id, desc, value) {
		if (this["unit_"+id]) {
			value = parseInt(value) +  this["unit_"+id][desc];
			console.log(parseInt(value) + " + " + this["unit_"+id][desc] + " = " + value);
			console.log(desc + " = " + value);
			this["unit_"+id][desc] = value;
			//this["unit_"+id].changeAttr(id, desc, value);
		} else {
		}
	}

	renderSingleSum(id, target) {
		if (this["unit_"+id]) {
			this["unit_"+id].renderSingleSummary(target);
		}
	}
}

class unit {
	constructor(options) {
		this.type = options.unitType || 'unknown',
		this.unitName = options.unitName || 'unnamed',
		this.aps = options.actionPoints || 0,
		this.status = options.status || 0,
		this.exp = options.exp || 0,
		this.str = options.strength || 0,
		this.subType = options.subType || 0,
		this.unitID = options.unitID;
	}



	set strength(x) {
		this.str = Math.min(x, 100);
		setBar(this.unitID, ".sumStr", this.str);
	}

	get strength() {
		return this.str;
	}

	changeAttr(id, desc, value) {
		/*
		this[desc] = value;
		console.log("set " + desc + " to " + value)
		thisList = document.body.querySelectorAll(".udHolder");
		for (var n=0; n<thisList.length; n++) {
			if (thisList[n].getAttribute("data-unitid") == id) {
				for (var i=0; i<thisList[n].childNodes.length; i++) {
					if (thisList[n].childNodes[i].getAttribute("data-boxName") == desc) {
						thisList[n].childNodes[i].innerHTML = this[desc];
					}
				}
			} else {
			}
		}*/
	}


	renderSingleSummary(target) {
		while (target.firstChild) {
			target.removeChild(target.firstChild);
		}
		this.renderSummary(target);
	}
}

class warband extends unit {
	set actionPoints(x) {

		this.aps = Math.max(0, Math.min(x, 1000));
		console.log("set aps to " + this.aps);
		setBar(this.unitID, ".sumAct", this.aps/10);
	}

	get actionPoints() {
		return this.aps;
	}

	renderSummary(target) {
		//console.log('draw ' + this.type)
		var thisDiv = addDiv(null, 'udHolder', target);
		thisDiv.setAttribute("data-unitid", this.unitID);

		thisDiv.nameDiv = addDiv("asdf", "sumName", thisDiv);
		nameDiv.setAttribute("data-boxName", "unitName");

		thisDiv.actDiv = addDiv("asdf", "sumAct", thisDiv);
		actDiv.setAttribute("data-boxName", "apBar");

		thisDiv.expDiv = addDiv("asdf", "sumStr", thisDiv);
		expDiv.setAttribute("data-boxName", "strBar");

		thisDiv.dtlButton = addDiv("", "sumDtlBut", thisDiv);
		dtlButton.addEventListener("click", function () {console.log("show detail")});

		thisDiv.nameDiv.innerHTML = this.unitName;
		this.changeAttr(this.unitId, "actionPoints", this.aps)
		this.changeAttr(this.unitId, "strength", this.str)
	}

}

class character extends unit {
	set actionPoints(x) {

		this.aps = Math.max(0, Math.min(x, 1000));
		console.log("set aps to " + this.aps);
		setBar(this.unitID, ".sumAct", this.aps/10);
	}

	get actionPoints() {
		return this.aps;
	}

	renderSummary(target) {
		var thisDiv = addDiv(null, 'udHolder', target);
		thisDiv.setAttribute("data-unitid", this.unitID);

		var nameDiv = addDiv("", "sumName", thisDiv);
		nameDiv.setAttribute("data-boxName", "unitName");

		var imgDiv = addDiv("", "sumImg", thisDiv);

		var actDiv = addDiv("", "sumAct", thisDiv);
		actDiv.setAttribute("data-boxName", "apBar");
		actDiv.setAttribute("data-boxunitid", this.unitID);

		var expDiv = addDiv("", "sumStr", thisDiv);
		expDiv.setAttribute("data-boxName", "strBar");
		expDiv.setAttribute("data-boxunitid", this.unitID);

		var dtlButton = addDiv("", "sumDtlBut", thisDiv);
		var prm = "1074,"+this.unitID;
		dtlButton.addEventListener("click", function () {passClick(prm, "rtPnl")});

		nameDiv.innerHTML = this.unitName;
		this.actionPoints = this.aps;
		this.strength = this.str;
		//this.changeAttr(this.unitId, "actionPoints", this.aps);
		//this.changeAttr(this.unitId, "strength", this.str);

	}
}

class plot extends unit {

	constructor (object) {
		//console.log("make a plot");
		super(object);
		this.target = object.target || null;
		this.tResist = object.tResist || 1;
		this.lSkill = object.lSkill || 1;
		//console.log("plot target = " + this.target)
		console.log("Plot aps " + this.aps);
	}

	set actionPoints(x) {
		this.aps = x;
		console.log("set aps to " + this.aps + " resist " + this.tResist);
		setBar(this.unitID, ".sumAct", 100*(this.aps/1000+this.lSkill)/(this.aps/1000+this.tResist+this.lSkill));
	}

	get actionPoints() {
		return this.aps;
	}

	renderSummary(target) {
		var plotBox = plotSummary(this, target);
		if (this.target > 0) unitList.renderSum(this.target, plotBox.childNodes[1]);
		/*
		var actDiv = addDiv("", "plotPoints", plotBox.childNodes[2]);
		actDiv.setAttribute("data-boxName", "apBar");
		actDiv.setAttribute("data-unitNum", this.unitID);
		*/
		//console.log("Set action bar to " + this.aps*this.lSkill/(1000*(this.tResist+this.lSkill)));
		setBar(this.unitID, ".sumAct", 100*this.aps*this.lSkill/(1000*(this.tResist+this.lSkill)));
		return plotBox;
	}

	renderDetailWork(target) {
		var thisDiv = plotDtlWork(this, target);
		this.detailEl = thisDiv;
		setBar(this.unitID, ".sumAct", 100*this.aps*this.lSkill/(1000*(this.tResist+this.lSkill)));
	}
}

setBar = function (id, desc, pct) {
	//console.log("setting " + desc + " to " + pct)
  //thisList = document.body.querySelectorAll(".udHolder");
	thisList = document.body.querySelectorAll(desc);
	//console.log("Checking " + thisList.length + " nodes");
	for (n=0; n<thisList.length; n++) {
		if (thisList[n].getAttribute("data-boxunitid") == id) {
			//console.log("Dound and instance");
			thisList[n].style.width = pct*125/100;
			thisList[n].style.backgroundColor = "rgb("+parseInt((100-pct)*2.55)+", "+parseInt(pct*2.55)+", 0)";
		}
	}
	/*
	for (n=0; n<thisList.length; n++) {
		if (thisList[n].getAttribute("data-unitID") == id) {
			console.log("found 1" + thisList[n].childNodes );
			for (i=0; i<thisList[n].childNodes.length; i++) {
				console.log("check " + i + " of " + thisList[n].childNodes.length)
				if (thisList[n].childNodes[i].getAttribute("data-boxName") == desc) {
					//console.log("found it - " + desc + ", us set ti " + pct);
				  thisList[n].childNodes[i].style.width = pct*125/100;
				  thisList[n].childNodes[i].style.backgroundColor = "rgb("+parseInt((100-pct)*2.55)+", "+parseInt(pct*2.55)+", 0)";
				  //console.log("rgb("+parseInt((100-pct)*2.55)+", 0, "+parseInt(pct*2.55)+")");
				}
			}
		} else {

		}
	}
	*/
}


class pane {
	constructor (desc, desktop) {
		//console.log("Make a pane " + this);
		this.element = paneBox(desc, 0, 500, 500, 250, 250);
		//console.log(this.element.childNodes);
		this.desc = desc;
		this.desktop = desktop;
		this.element.childNodes[0].parentObj = this;
		this.element.parentObj = this;
		this.desktop.arrangePanes();

		this.element.addEventListener("click", function() {this.parentObj.toTop()});
		this.element.childNodes[0].addEventListener("click", function () {
			this.parentObj.destroyWindow();
			event.stopPropagation();
			});
		this.element.addEventListener("dragstart", function () {
			this.parentObj.toTop();
			bPos = [parseInt(this.offsetLeft), parseInt(this.offsetTop), event.clientX, event.clientY];
			//console.log(bPos);
		});
	}

	destroyWindow() {
		//console.log("remove " + this.desc)
		this.element.remove();
		this.desktop.removePane(this);
		//console.log("final " + Object.keys(this.desktop.paneList));
	}

	toTop() {
		this.desktop.paneToTop(this);
	}
}

class hPane extends pane {
	constructor(desc, desktop) {
		super(desc, desktop);
		//console.log("set hPane style for " + this.element);
		this.element.className = "hPane";
	}
}

class regPane extends pane {
	constructor(desc, desktop) {
		super(desc, desktop);
		//console.log("set regPane style for " + this.element);
		this.element.className = "regPane";
	}
}

class deskTop {
	constructor () {
		this.paneList = {};
		//console.log("make list " + this.paneList);
		//console.log("List keys " + Object.keys(this.paneList))
		this.id = "a desktop";
	}

	newPane (desc, type) {
		//console.log("start list " + Object.keys(this.paneList))
		if (this.paneList[desc]) {
			//console.log("already made: " + this.constructor.name + " -> " + Object.keys(this.paneList));
		} else {

			if (type == "hPane") {
				var mkPane = new hPane(desc, this);
				this.paneList[desc] = mkPane;
				//console.log("just made " + desc + " --- "  + Object.keys(this.paneList));
			} else {
				var mkPane = new regPane(desc, this);
				this.paneList[desc] = mkPane;
				//console.log("just made " + desc + " --- "  + Object.keys(this.paneList));
			}

		}
	}

	arrangePanes() {
		var count = 1;
		for (var item in this.paneList) {
			this.paneList[item].element.style.zIndex = count;
			count++;
		}
	}

	getPane(desc) {
		if (this.paneList[desc]) {
			return this.paneList[desc].element.childNodes[1];
		}
	}

	paneToTop(thisPane) {
		//console.log("to top");
		delete this.paneList[thisPane.desc];
		this.paneList[thisPane.desc] = thisPane;
		this.arrangePanes();
	}

	removePane (thisPane) {
		console.log("Base array " + Object.keys(this.paneList));
		console.log("remove from " + this.constructor.name + " looking for " + thisPane.desc);
		delete this.paneList[thisPane.desc];
		console.log("current List " + Object.keys(this.paneList));
	}
}

selectButton = function (trg, src, id, others) {
	var newButton = addDiv("button", "button", trg);
	newButton.addEventListener("click", function () {
		selectItem(this, id, others);
		})
	newButton.innerHTML = src;
	//return newButton;
}

var selectedItem, selectedID;
selectItem = function (trg, id, others) {
	if (selectedItem)	selectedItem.style.borderColor = "000000";
	selectedItem = trg.parentNode;
	selectedID = id;
	trg.parentNode.style.borderColor = "#FF0000";
	console.log("selectedID is " + selectedID);
	for (var i=0; i<others.length; i++) {
		unitList.renderSingleSum(id, others[i]);
	}
}
