
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
