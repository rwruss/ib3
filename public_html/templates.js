
addDiv = function(id, useClassName, target) {
	var trg;
	if (typeof(target) == "string") trg = document.getElementById(target);
	else trg = target;

	var newDiv = document.createElement("div");
	newDiv.className = useClassName;
	newDiv.id = id;
	trg.appendChild(newDiv);
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

	var boxHolder = addDiv(trg+"_confirmButtons", "cButtons", trg);

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
		dButton.addEventListener("click", function () {killBox(dButton);event.stopPropagation();});
	}
}

optionButton = function (prm, trg, src) {
	var newButton = addDiv("button", "cBoxA", trg);
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

msgBox = function (trg, prm, opt) {
	console.log("opt = " + opt);
	subBox = document.createElement("input");
	if (opt == 0) {

		subBox.style.width="100%";
		subBox.addEventListener("keydown", function (event) {event.stopPropagation()});
		subBox.addEventListener("click", function (event) {event.stopPropagation()});
		trg.appendChild(subBox);
	} else {
		subBox.value = "";
	}

	box = document.createElement("textArea");
	box.style.width="100%";
	box.addEventListener("keydown", function (event) {event.stopPropagation()});
	box.addEventListener("mousedown", function (event) {console.log(event); this.parentNode.parentNode.setAttribute("draggable", false); });
	box.addEventListener("mouseup", function (event) {console.log(event); this.parentNode.parentNode.setAttribute("draggable", true); });
	box.addEventListener("mouseout", function (event) {console.log(event); this.parentNode.parentNode.setAttribute("draggable", true); });
	trg.appendChild(box);

	sendButton = addDiv("", "", trg);
	sendButton.innerHTML = "send message";
	sendButton.addEventListener("click", function () {
		//alert("send");
		//alert(msgBox.value);});
	scrMod(prm + "<!*!>" + subBox.value + "<!*!>" + box.value);});
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



makeTabMenu = function(id, trg) {
	var tabObject = addDiv(id+"_header", "taskHeader", trg);
	var tabCM = addDiv(id+"_tabs", "centeredmenu", trg);
	var tabUL = document.createElement("ul");
	tabUL.id = id+"_tabs_ul";
	tabCM.appendChild(tabUL);
	addDiv(id+"_options", "taskOptions", trg);

	newTabMenu(id);
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

	var thisOpt = addDiv(id, "tdHolder", target);
	if (desc) thisOpt.innerHTML = desc;

	if (prm) thisOpt.addEventListener("click", function() {scrMod("1026,"+id+","+prm);});
	else thisOpt.addEventListener("click", function() {scrMod("taskDtl", "1026,"+id,500, 500, 200, 50);});
}

textBlob = function (id, target, content) {
	if (typeof(target) == "string") trg = document.getElementById(target);
	else trg = target;

	var thisBlob = addDiv(id, "textBlob", trg);
	thisBlob.innerHTML= content;
	thisBlob.style.width = "100%";

	return thisBlob;
}

newBldgOpt = function(id, base, target, desc) {

}

newBldgSum = function(id, target, pctComplete, status) {

}

newTaskDetail = function(id, target, pctComplete, killLink) {
	var thisDetail = addDiv(id, "tdHolder", target);
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
	var thisOpt = addDiv("utOpt_"+id, "tdHolder", target);
	thisOpt.innerHTML = desc;

	thisOpt.addEventListener("click", function () {
		//makeBox("taskDtl", "1060,"+id, 500, 500, 200, 50);
	scrMod("1060,"+id)});
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

	newDiv.addEventListener("drag", function (event) {
		if (event.clientX > 0) {
		console.log("drag start");
		this.style.left = bPos[0] - bPos[2] + event.clientX;
		this.style.top = bPos[1] - bPos[3] + event.clientY;
		}
	});
	newDiv.addEventListener("dragend", function (event) {
		event = event || window.event;
		console.log(event + " = " + event.clientX + ", " + event.clientY);
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
			if (this["unit_" + object.unitID].type == object.unitType) {
				this["unit_" + object.unitID].update(object);
			} else {
				delete this["unit_" + object.unitID];
				this.newUnit(object);
			}
		} else {
			switch (object.unitType) {
				case "warband":
					this["unit_" + object.unitID] = new warband(object);
					break;

				case "character":
					this["unit_" + object.unitID] = new character(object);
					break;

				case "plot":
					this["unit_" + object.unitID] = new plot(object);
					break;

				case "building":
					this["unit_" + object.unitID] = new building(object);
					break;

				case "task":
					this["unit_" + object.unitID] = new task(object);
					break;

				case "trainingUnit":
					console.log("mk training unit");
					this["unit_" + object.unitID] = new trainingUnit(object);
					break;

				default:
					console.log("Unknown Type");
					break;
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
		this.tNum = options.tNum || 0,
		this.trainPts = 0,
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

	}

	renderSingleSummary(target) {
		while (target.firstChild) {
			target.removeChild(target.firstChild);
		}
		this.renderSummary(target);
	}

	update(object) {
		console.log("update unit");
		this.aps = object.actionPoints || this.aps,
		this.status = object.status || this.status,
		this.exp = object.exp || this.exp,
		this.str = object.strength || this.str,
		this.subType = object.subType || this.subType,
		this.tNum = object.tNum || this.tNum;
	}
}

class trainingUnit extends unit {
	constructor (object) {
		super(object);
		this.oTrainPts = object.trainPts || 0;
		this.trainReq = object.trainReq || 100;
		this.wtf = object.trainPts;
		this.unitID = object.unitID;
		console.log(this.unitID + "training unit made with " + this.oTrainPts + " points");
		console.log(this);
		console.log(object);
	}

	set trainPts(x) {
		this.aps = Math.max(0, Math.min(x, 1000));
		console.log("set training points to " + this.oTrainPts);
		setBar(this.unitID, ".sumAct", this.oTrainPts*100/this.trainReq);
	}

	renderSummary(target) {
		console.log('draw ttraing ' + this.unitID)
		var thisDiv = addDiv(null, 'udHolder', target);
		thisDiv.setAttribute("data-unitid", this.unitID);

		thisDiv.nameDiv = addDiv("asdf", "sumName", thisDiv);
		thisDiv.nameDiv.setAttribute("data-boxName", "unitName");

		thisDiv.actDiv = addDiv("asdf", "sumAct", thisDiv);
		thisDiv.actDiv.setAttribute("data-boxName", "apBar");
		thisDiv.actDiv.setAttribute("data-boxunitid", this.unitID);

		thisDiv.expDiv = addDiv("asdf", "sumStr", thisDiv);
		thisDiv.expDiv.setAttribute("data-boxName", "strBar");
		thisDiv.expDiv.setAttribute("data-boxunitid", this.unitID);

		thisDiv.dtlButton = addDiv("", "sumDtlBut", thisDiv);
		var prm = "1034,"+this.unitID;
		thisDiv.dtlButton.addEventListener("click", function () {passClick(prm, "rtPnl")});

		thisDiv.nameDiv.innerHTML = this.unitName + " - " + this.unitID;

		this.trainPts = this.oTrainPts;
	}

	update (object) {
		super.update(object);

		this.oTrainPts = object.trainPts || this.oTrainPts,
		this.trainReq = object.trainReq,
		this.unitID = object.unitID;

		this.trainPts = this.oTrainPts;
		console.log("set train pts to " + this.oTrainPts);
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
		thisDiv.nameDiv.setAttribute("data-boxName", "unitName");

		thisDiv.actDiv = addDiv("asdf", "sumAct", thisDiv);
		thisDiv.actDiv.setAttribute("data-boxName", "apBar");
		thisDiv.actDiv.setAttribute("data-boxunitid", this.unitID);

		thisDiv.expDiv = addDiv("asdf", "sumStr", thisDiv);
		thisDiv.expDiv.setAttribute("data-boxName", "strBar");
		thisDiv.expDiv.setAttribute("data-boxunitid", this.unitID);

		thisDiv.dtlButton = addDiv("", "sumDtlBut", thisDiv);
		var prm = "1034,"+this.unitID;
		thisDiv.dtlButton.addEventListener("click", function () {passClick(prm, "rtPnl")});

		thisDiv.nameDiv.innerHTML = this.unitName + " - " + this.unitID;

		this.actionPoints = this.aps;
		this.strength = this.str;
		//this.changeAttr(this.unitId, "actionPoints", this.aps)
		//this.changeAttr(this.unitId, "strength", this.str)
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

		nameDiv.innerHTML = this.type + " - " + this.unitID;
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
		this.plotType = object.plotType || 1;
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

		setBar(this.unitID, ".sumAct", 100*this.aps*this.lSkill/(1000*(this.tResist+this.lSkill)));
		return plotBox;
	}

	renderDetailWork(target) {
		var plotBox = plotSummary(this, target);
		trgBox = addDiv("charBox", "tdHolder", plotBox);

		plotBox.buttonBox = addDiv("", "fullBar", plotBox);
		plotBox.buttonBox2 = addDiv("", "fullBar", plotBox);
		confirmButton("Leave this plot?", "1088,"+this.unitID, plotBox.buttonBox2, "Leave Plot");
		//scrButton("1087", plotBox.buttonBox, "Leave Plot");
		scrButton("1084," + this.plotType + ","+ this.unitID+",1", plotBox.buttonBox, "10%");
		scrButton("1084," + this.plotType + ","+ this.unitID+",2", plotBox.buttonBox, "25%");
		scrButton("1084," + this.plotType + ","+ this.unitID+",3", plotBox.buttonBox, "50%");
		scrButton("1084," + this.plotType + ","+ this.unitID+",4", plotBox.buttonBox, "100%");

		this.detailEl = plotBox;
		setBar(this.unitID, ".sumAct", 100*this.aps*this.lSkill/(1000*(this.tResist+this.lSkill)));
	}
}

class building extends unit {
	set actionPoints(x) {
		this.aps = Math.max(0, Math.min(x, 1000));
		console.log("set aps to " + this.aps);
		setBar(this.unitID, ".sumAct", this.aps/10);
	}

	get actionPoints() {
		return this.aps;
	}

	renderSummary(target) {
		var thisDetail = addDiv("", "tdHolder", target);
		thisDetail.act = addDiv("", "udAct", thisDetail);
		thisDetail.statusBox = addDiv("", "bldgLvl", thisDetail);
		thisDetail.statusBox.innerHTML = this.unitName + " " + this.unitID;


		var actDiv = addDiv("", "sumAct", thisDetail);
		actDiv.setAttribute("data-boxName", "apBar");
		actDiv.setAttribute("data-boxunitid", this.unitID);

		var prm = "1048,"+this.unitID;
		thisDetail.addEventListener("click", function() {scrMod(prm);});
		this.actionPoints = this.aps;
	}

	buildOpt(target, base) {
		var thisDetail = addDiv("", "tdHolder", target);
		addImg("", "bldgImg", thisDetail);
		var lvlDiv = addDiv("", "bldgLvl", thisDetail);
		var descDiv = addDiv("", "bldgDesc", thisDetail);
		lvlDiv.innerHTML = "1";
		descDiv.innerHTML = this.unitName;
		//document.getElementById("bldg_"+id+"_img").src = "./textures/borderMask3.png"
		var prm = "1049,"+ this.unitID.substr(1) + ","+base;
		thisDetail.addEventListener("click", function() {scrMod(prm);});
	}
}

class task extends unit {
	constructor (object) {
		//console.log("make a plot");
		super(object);
		this.ptsNeed = object.reqPts || 500;
	}

	set actionPoints(x) {
		this.aps = Math.max(0, Math.min(x, 1000));
		//console.log("set aps to " + this.aps);
		setBar(this.unitID, ".sumAct", this.aps*100/this.ptsNeed);
		console.log(this.unitID + " set bar to " + this.aps*100/this.ptsNeed)
	}

	get actionPoints() {
		return this.aps;
	}

	renderSummary(target) {
		var thisDetail = addDiv("", "tdHolder", target);
		//thisDetail.act = addDiv("", "udAct", thisDetail);
		thisDetail.statusBox = addDiv("", "bldgLvl", thisDetail);
		thisDetail.statusBox.innerHTML = "Task" + this.unitID;


		var actDiv = addDiv("", "sumAct", thisDetail);
		actDiv.setAttribute("data-boxName", "apBar");
		actDiv.setAttribute("data-boxunitid", this.unitID);

		var prm = "1040,"+this.unitID;
		thisDetail.addEventListener("click", function() {scrMod(prm);});
		this.actionPoints = this.aps;
	}

	update(object) {
		super.update(object);
		this.ptsNeed = object.ptsNeed || this.ptsNeed;
	}
}

setBar = function (id, desc, pct) {
	thisList = document.body.querySelectorAll(desc);
	for (n=0; n<thisList.length; n++) {
		if (thisList[n].getAttribute("data-boxunitid") == id) {
			thisList[n].style.width = pct*125/100;
			thisList[n].style.backgroundColor = "rgb("+parseInt((100-pct)*2.55)+", "+parseInt(pct*2.55)+", 0)";
		}
	}
}


class pane {
	constructor (desc, desktop) {
		console.log("Make a pane " + this);
		this.element = paneBox(desc, 0, 500, 500, 250, 250);
		//console.log(this.element.childNodes);
		this.desc = desc;
		this.deskHolder = desktop;
		//this.element.childNodes[0].parentObj = this;
		this.element.parentObj = this;
		this.deskHolder.arrangePanes();
		this.nodeType = "pane";

		this.element.addEventListener("click", function(event) {this.parentObj.toTop()});
		this.element.childNodes[0].addEventListener("click", function (event) {
			//console.log("destroying " + this.parentNode.parentObj.nodeType + "  via " + this);
			this.parentNode.parentObj.destroyWindow();
			event.stopPropagation();
			});
		this.element.addEventListener("dragstart", function (event) {
			this.parentObj.toTop();
			event.dataTransfer.setData('application/node type', this);
			bPos = [parseInt(this.offsetLeft), parseInt(this.offsetTop), event.clientX, event.clientY];

			console.log(bPos);
		});
		this.toTop();
	}

	destroyWindow() {
		//console.log("remove " + this.desc)
		this.element.remove();
		this.deskHolder.removePane(this);
		//console.log("final " + Object.keys(this.deskHolder.paneList));
	}

	toTop() {
		this.deskHolder.paneToTop(this);
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
			//console.log("created " + desc);
		}

	}

	arrangePanes() {
		var count = 1;
		for (var item in this.paneList) {
			//console.log("arrange " + item + " = " + count);
			this.paneList[item].element.style.zIndex = count;
			count++;
		}
	}

	getPane(desc) {
		if (this.paneList[desc]) {
			//console.log("dound " + desc);
			return this.paneList[desc].element.childNodes[1];
		} else {
			//console.log(desc + " does not ex");
		}
	}

	paneToTop(thisPane) {
		//console.log("to top");
		delete this.paneList[thisPane.desc];
		this.paneList[thisPane.desc] = thisPane;
		this.arrangePanes();
	}

	removePane (thisPane) {
		//console.log("Base array " + Object.keys(this.paneList));
		//console.log("remove from " + this.constructor.name + " looking for " + thisPane.desc);
		//this.paneList.splice(thisPane.desc, 1);
		delete this.paneList[thisPane.desc];
		//console.log("current List " + Object.keys(this.paneList) + " leaving a size of " + this.paneList.length);
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
	//console.log("selectedID is " + selectedID);
	for (var i=0; i<others.length; i++) {
		unitList.renderSingleSum(id, others[i]);
	}
}
