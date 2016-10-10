class objectList {
	constructor () {

	}

	SLsingleSelect(target, action) {
		console.log(target);
		let showContain = addDiv("", "selectMenu", "gmPnl");
		this.selected = [];
		target.selected = [];

		for (var i=0; i<this.listItems.length; i++) {
			let object = this.showItem(this.listItems[i], showContain);
			object.owner = this;
			object.objID = this.listItems[i];
			object.addEventListener("click", function () {
				object.owner.selected[0] = object.objID;
				console.log("set slected to " + object.objID)
				object.parentNode.remove();
				SlclearTarget(target);
				//target.innerHTML = object.objID;
				this.owner.showItem(object.objID, target);
				//target.appendChild(this);
				target.selected[0] = object.objID;
				action();
				});
		}
	}

	SLmultiSelect(target, limit) {
		let showContain = addDiv("", "selectMenu", "testContain");
		this.selected = [];
		this.limit = limit;
		target.selected = [];
		console.log(target);
		for (var i=0; i<this.listItems.length; i++) {
			let object = this.showItem(this.listItems[i], showContain);
			object.owner = this;
			object.objID = this.listItems[i];
			object.addEventListener("click", function () {
				var checkIndex = this.owner.selected.indexOf(this.objID);
				//console.log(checkIndex+ " look for " + this.objID + " in " + this.owner.selected);
				//console.log(this.owner.selected);
				if(checkIndex >= 0) {
					console.log("already in list");
					this.owner.selected.splice(checkIndex, 1);
					this.owner.unselectItem(this);
				}
				else {
					console.log(this.owner.limit + " vs " + this.owner.selected.length)
					if (this.owner.limit > this.owner.selected.length) {
					this.owner.selected.push(this.objID);
					console.log(this.owner.selected);
					this.owner.selectItem(this);
					}
				}
				});
		}

		var selButton = addDiv("", "button", showContain);
		selButton.innerHTML = "Select Thsese";
		selButton.owner = this;
		selButton.addEventListener("click", function () {
			selButton.parentNode.remove();
			SlclearTarget(target);
			console.log(this.owner.selected);
			for (var i=0; i<this.owner.selected.length; i++) {
				this.owner.showItem(this.owner.selected[i], target);
			}
			target.selected = this.owner.selected;
			});
	}

	getSelection() {
		return this.selected;
	}
}

class resourceList extends objectList {
	constructor(listItems) {
		super();
		this.listType = "rscList";
		this.listItems = listItems;
	}

	showItem(id, trg) {
		let objBox = addDiv("", "rscContain", trg);
		let objContent = addDiv("", "rscImg", objBox);
		let newImg = addImg(id, "rscImg", objContent);
		newImg.src = "./rscImages/"+id+".png";
		newImg.alt = id;

		return objBox;
	}

	selectItem (trg) {
		trg.className = "rscContainSelected";
	}

	unselectItem(trg) {
		trg.className = "rscContain";
	}

}

class uList extends objectList {
	constructor(listItems) {
		super();
		this.listType = "uList";
		this.listItems = listItems;
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


SlclearTarget = function(trg) {
	console.log("show object " + this);
	while (trg.firstChild) {
		trg.removeChild(trg.firstChild);
	}
}

sendSelection = function() {
	console.log("send " + SLshowList.selected);
}

readLists = function (targetList) {
	returnArray = [];
	console.log(targetList);
	for (var i=0; i<targetList.length; i++) {
		console.log("check " + targetList[i]);
		if (targetList[i].selected == "undefined") {
			console.log("not ready");
			return 0;
		} else {
			console.log("fouind " + targetList[i].selected);
			returnArray = returnArray.concat(targetList[i].selected)
		}
	}
	console.log(returnArray);
}
