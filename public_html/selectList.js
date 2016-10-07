
class objectList {
	constructor () {
		
	}
}

class resourceList extends objectList {
	constructor(listItems) {
		super();
		this.listType = "rscList";
	}
	
	showItem(id, trg) {
		objBox = addDiv("", "rscContain", trg);
		objContent = addDiv("", "rscImg", objBox);
		let newImg = addImg(id, "rscImg", objContent);
		newImg.src = "./rscImages/"+id+".png";

		return newImg;
	}
	
}

class unitList extends objectList {
	constructor(listItems) {
		super();
		this.listType = "unitList";
	}
	
	showItem(id, trg) {
		objBox = addDiv("", "objContain", trg);
		objContent = addDiv("", "objContent", objBox);
		objContent.innerHTML = id;

		return objBox;
	}
}

newButton = function(trg, action) {
	button1 = addDiv("button1", "button", trg);
	button1.addEventListener("click", action);

	button1.innerHTML = "button";

	return button1;
}

var SLshowListItems = [1, 2, 3];
var SLshowList = [];

SLenable = function(trg, action) {
	trg.selectedValue = [];
	trg.addEventListener("click", action);
}

SLnewList = function (listAction, limit, target) {
	console.log(SLshowListItems);
	let showContain = addDiv("", "selectMenu", "testContain");
	for (var i=0; i<SLshowListItems.length; i++) {
		object = renderObj(SLshowListItems[i], showContain);
		object.addEventListener("click", SLselect);
		object.objID = SLshowListItems[i];
	}
	SLshowList.selected = [];
	SLshowList.limit = limit;
	SLshowList.target = target;
	finishButton = newButton(showContain, listAction);
}

SLselect = function () {
	let checkIndex = SLshowList.selected.indexOf(this.objID);
	console.log("Look for " + this.objID + " in " + SLshowList.selected);
	if (checkIndex >= 0) {
		console.log("Delete " + this.className + " at index " + checkIndex);
			this.className = "objContain";
			SLshowList.selected.splice(checkIndex, 1);
			console.log("class is now " + this.className);
		}
	else if (SLshowList.selected.length < SLshowList.limit) {
		this.className = "objContainSelected";
		SLshowList.selected.push(this.objID);
	}
	console.log(SLshowList.selected);
}


SLshowSelected = function(trg) {
	console.log("Do something with the list " + SLshowList.selected);
	renderObj(SLshowList.selected[0], SLshowList.target);
}

SLsingleSelect = function(target) {
	let showContain = addDiv("", "selectMenu", "gmPnl");
	SLshowList.selected = [];
	console.log(SLshowList);
	for (var i=0; i<SLshowListItems.length; i++) {
		object = renderObj(SLshowListItems[i], showContain);
		object.objID = SLshowListItems[i];
		object.addEventListener("click", function () {SLshowList.selected[0] = this.objID;this.parentNode.remove();SlclearTarget(target);});
	}
}

SLsingleRsc = function(target) {
	// Show all resources
	let showContain = addDiv("", "selectMenu", "gmPnl");
	SLshowList.selected = [];
	console.log(SLshowList);
	for (var i=0; i<playerRsc.length; i+=2) {
		object = SLrenderImage(playerRsc[i], showContain, "");
		object.objID = playerRsc[i];
		object.qty = playerRsc[i+1];
		object.addEventListener("click", function () {SLshowList.selected[0] = this.objID;showContain.remove();SlclearTarget(target);SLrenderImage(this.objID, target, "");setSlideQty(target.parentNode, this.qty);});
	}
}

SlclearTarget = function(trg) {
	console.log("show object " + this);
	while (trg.firstChild) {
		trg.removeChild(trg.firstChild);
	}
}

SLrenderImage = function(id, trg, path) {
	objBox = addDiv("", "rscContain", trg);
	objContent = addDiv("", "rscImg", objBox);
	let newImg = addImg(id, "rscImg", objContent);
	newImg.src = path;

	return newImg;
}
/*
renderObj(id, trg) {
	objBox = addDiv("", "objContain", trg);
	objContent = addDiv("", "objContent", objBox);
	objContent.innerHTML = id;

	return objBox;
}
*/
sendSelection = function() {
	console.log("send " + SLshowList.selected);
}
/*
function init() {
	console.log("started var " + SLshowList);

	selectHead = addDiv("", "selectHead", "testContain")
	selectBox = addDiv("", "objContain", selectHead);
	selectBox.addEventListener("click", function() {SLsingleSelect(selectBox)});
	newButton(selectHead, sendSelection);
}

window.addEventListener("load", init);*/
