
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


/*
	var newButton = addDiv("button", "cBoxA", newPlot);
	newButton.innerHTML = obj.button;
	var prm = "1082,"+obj.id;
	newButton.addEventListener("click", function () {confirmBox("Are you sure you want to invite this person to this plot?", prm,"", 2, "Yes", "No")})
*/
	trg.appendChild(newPlot);
	return newPlot;
}

plotSummary = function (obj, trg) {
	console.log("mk a plot sum - " + trg);
	var newPlot = document.createElement("div");
	newPlot.className = "plotSummary";

	var dtlButton = addDiv("", "sumDtlBut", newPlot);
	//var dtlBox = addDiv("descBox", "plotDesc", newPlot);
	//descBox.innerHTML = obj.desc;
	var prm1 = "1081,"+obj.id;
	dtlButton.addEventListener("click", function () {makeBox("plotDtl", prm1, 500, 500, 200, 50)});

	var targetBox = addDiv("targetBox", "plotTarget", newPlot);
	var dtlBox = addDiv("dtlBox", "plotDtl", newPlot);
	//console.log("attach the plot to " + trg);

	var optBar = addDiv("", "fullBar", newPlot);

	trg.appendChild(newPlot);
	return newPlot;
}

plotDtlWork = function (obj, trg) {
	console.log("work options for " + obj.unitID);
	var plotBox = plotSummary({desc: "plot #'.$postVals[1].'", id:'.$postVals[1].'}, document.getElementById("plotDtlContent"));
	trgBox = addDiv("charBox", "tdHolder", plotBox);
	//unitList.newUnit({unitID : '.$target.', unitType : "character", actionPoints : 50, status : 1, unitName : "unit name", exp : 500});
	//unitList.renderSum('.$target.', plotBox.children[1]);

	buttonBox = addDiv("", "fullBar", plotBox);
	buttonBox2 = addDiv("", "fullBar", plotBox);
	confirmButton("Leave this plot?", "", buttonBox2, "Leave Plot");
	scrButton("1087", buttonBox, "Leave Plot");
	scrButton("1084,6,"+ obj.unitID+",1", buttonBox, "10%");
	scrButton("1084,6,"+ obj.unitID+",2", buttonBox, "25%");
	scrButton("1084,6,"+ obj.unitID+",3", buttonBox, "50%");
	scrButton("1084,6,"+ obj.unitID+",4", buttonBox, "100%");
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

tabSelect = function(target, selection) {
	var tabHolder = document.getElementById(target+"_tabs");
	document.getElementById(target+"_tab"+selection).style.visibility =  "visible";
	//alert(document.getElementById(target+"_tabs").style.visibility);
	if (tabHolder.currentSelection != selection)	{
		//alert("set " + target+"_tab"+tabHolder.currentSelection + "to 1");
		document.getElementById(target+"_tab"+tabHolder.currentSelection).style.visibility =  "hidden";
	}
	tabHolder.currentSelection = selection;
	/*
	document.getElementById(target+"_tab"+selection).style.zindex = 3;
	if (tabHolder.currentSelection != selection)	{
		//alert("set " + target+"_tab"+tabHolder.currentSelection + "to 1");
		document.getElementById(target+"_tab"+tabHolder.currentSelection).style.zIndex = 1;
	}
	document.getElementById(target+"_tab"+selection).style.zIndex = 2;
	tabHolder.currentSelection = selection;
	*/
	//alert("select " + selection)
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

var selectedItem;
selectItem = function (trg) {
	if (selectedItem)	selectedItem.style.borderColor = "000000";
	selectedItem = trg.parentNode;
	trg.parentNode.style.borderColor = "#FF0000";
	console.log(trg);
}

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
			this["unit_"+id].renderSummary(target);
		} else {
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
			this["unit_"+id].renderDetalWork(target);
		} else {
		}
	}

	change(id, desc, value) {
		if (this["unit_"+id]) {
			this["unit_"+id].changeAttr(id, desc, value);
		} else {
		}
	}

	add(id, desc, value) {
		if (this["unit_"+id]) {
			value = parseInt(value) +  this["unit_"+id][desc];
			this["unit_"+id].changeAttr(id, desc, value);
		} else {
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
		this.unitID = options.unitID;
	}

	set actionPoints(x) {

		this.aps = Math.max(0, Math.min(x, 1000));
		console.log("set aps to " + this.aps);
		setBar(this.unitID, "apBar", this.aps/10);
	}

	get actionPoints() {
		return this.aps;
	}

	set strength(x) {
		this.str = Math.min(x, 100);
		setBar(this.unitID, "strBar", this.str);
	}

	get strength() {
		return this.str;
	}

	changeAttr(id, desc, value) {
		this[desc] = value;
		console.log("set " + desc + " to " + value)
		thisList = document.body.querySelectorAll(".udHolder");
		for (n=0; n<thisList.length; n++) {
			if (thisList[n].getAttribute("data-unitid") == id) {
				for (i=0; i<thisList[n].childNodes.length; i++) {
					if (thisList[n].childNodes[i].getAttribute("data-boxName") == desc) {
						thisList[n].childNodes[i].innerHTML = this[desc];
					}
				}
			} else {
			}
		}
	}
}

class warband extends unit {
	renderSummary(target) {
		//console.log('draw ' + this.type)
		var thisDiv = addDiv(null, 'udHolder', target);
		thisDiv.setAttribute("data-unitid", this.unitID);

		var nameDiv = addDiv("asdf", "sumName", thisDiv);
		nameDiv.setAttribute("data-boxName", "unitName");

		var actDiv = addDiv("asdf", "sumAct", thisDiv);
		actDiv.setAttribute("data-boxName", "apBar");

		var expDiv = addDiv("asdf", "sumStr", thisDiv);
		expDiv.setAttribute("data-boxName", "strBar");

		var dtlButton = addDiv("", "sumDtlBut", thisDiv);
		dtlButton.addEventListener("click", function () {console.log("show detail")});

		nameDiv.innerHTML = this.unitName;
		this.changeAttr(this.unitId, "actionPoints", this.aps)
		this.changeAttr(this.unitId, "strength", this.str)
	}
}

class character extends unit {
	renderSummary(target) {
		var thisDiv = addDiv(null, 'udHolder', target);
		thisDiv.setAttribute("data-unitid", this.unitID);

		var nameDiv = addDiv("", "sumName", thisDiv);
		nameDiv.setAttribute("data-boxName", "unitName");

		var imgDiv = addDiv("", "sumImg", thisDiv);

		var actDiv = addDiv("", "sumAct", thisDiv);
		actDiv.setAttribute("data-boxName", "apBar");

		var expDiv = addDiv("", "sumStr", thisDiv);
		expDiv.setAttribute("data-boxName", "strBar");

		var dtlButton = addDiv("", "sumDtlBut", thisDiv);
		var prm = "1074,"+this.unitID;
		dtlButton.addEventListener("click", function () {passClick(prm, "rtPnl")});

		nameDiv.innerHTML = this.unitName;
		this.changeAttr(this.unitId, "actionPoints", this.aps)
		this.changeAttr(this.unitId, "strength", this.str)
	}
}

class plot extends unit {

	constructor (object) {
		console.log("make a plot");
		super(object);
		this.target = object.target || null;
		console.log("plot target = " + this.target)
	}

	renderSummary(target) {

		console.log("draw plot = " + this);

		var plotBox = plotSummary({desc: "uhhh", id:1}, target);

		//var trgBox = addDiv("charBox", "tdHolder", plotBox);
		console.log("draw " + this.target);
		unitList.renderSum(this.target, plotBox.childNodes[1]);

		var actDiv = addDiv("", "plotPoints", plotBox.childNodes[2]);
		actDiv.setAttribute("data-boxName", "apBar");


		//var dtlButton = addDiv("", "sumDtlBut", plotBox.childNodes[2]);
		//var prm = "1074,"+this.unitID;
		//dtlButton.addEventListener("click", function () {passClick(prm, "rtPnl")});



		this.changeAttr(this.unitId, "actionPoints", this.aps);
		return plotBox;
		//this.changeAttr(this.unitId, "strength", this.str)
	}

	renderDetalWork(target) {
		plotDtlWork(this, target);
		/*
		console.log("draw plot work opts = " + this);

		var trg = document.getElementById("plotDtlContent");
		//unitList.newUnit({unitType:"plot", unitID:'.$postVals[1].', actionPoints:500, target:20000});
		var thisSum = unitList.renderSum('.$postVals[1].', trg);
		console.log("add buttons to " + thisSum)
		//var plotBox = plotSummary({desc: "plot #'.$postVals[1].'", id:'.$postVals[1].'}, document.getElementById("plotDtlContent"));
		//trgBox = addDiv("charBox", "tdHolder", plotBox);
		//unitList.newUnit({unitID : '.$target.', unitType : "character", actionPoints : 50, status : 1, unitName : "unit name", exp : 500});
		//unitList.renderSum('.$target.', plotBox.children[1]);


		buttonBox = addDiv("", "fullBar", thisSum);
		buttonBox2 = addDiv("", "fullBar", thisSum);
		//confirmButton("Leave this plot?", "", buttonBox2, "Leave Plot");
		//scrButton("1087", buttonBox, "Leave Plot");
		//scrButton("1084,6,'.$postVals[1].',1", buttonBox, "10%");
		//scrButton("1084,6,'.$postVals[1].',2", buttonBox, "25%");
		//scrButton("1084,6,'.$postVals[1].',3", buttonBox, "50%");
		//scrButton("1084,6,'.$postVals[1].',4", buttonBox, "100%");';


			scrButton("1086", buttonBox2, "Carry Out Plot");
			boxButton("1085,'.$postVals[1].'", buttonBox2, "ringleader");
			*/
	}
}

setBar = function (id, desc, pct) {
	console.log("setting " + desc + " to " + pct)
  thisList = document.body.querySelectorAll(".udHolder");

	for (n=0; n<thisList.length; n++) {
		if (thisList[n].getAttribute("data-unitID") == id) {
			//console.log("found 1" + thisList[n].childNodes );
			for (i=0; i<thisList[n].childNodes.length; i++) {
				//console.log("check " + i + " of " + thisList[n].childNodes.length)
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
}
