class objectList {
	constructor () {

	}

	SLsingleSelect(target) {
		var showContain;
		target.selectedValue = 0;
		if (document.getElementById("selectMenu")) showContain = document.getElementById("selectMenu");
		else showContain = addDiv("selectMenu", "selectMenu", "gmPnl");
		showContain.innerHTML = "";
		for (var i=0; i<this.listItems.length; i++) {
			if (this.listItems[i] instanceof objectList) {
				let object = this.listItems[i].typeIcon(showContain);
				let subtarg = this.listItems[i];
				if (this.listItems[i] != "undefined") object.addEventListener("click", function () {
					subtarg.SLsingleSelect(target, function() {})
					});
			} else {
			let object = this.showItem(this.listItems[i], showContain);
			object.owner = this;
			object.objID = this.listItems[i];
			object.addEventListener("click", function () {
				console.log("set slected to " + object.objID)
				object.parentNode.remove();
				SlclearTarget(target);
				this.owner.showSelected(object.objID, target);
				});
			}
		}
	}

	SLmultiSelect(target, limit) {
		var selButton = addDiv("", "button", showContain);
		selButton.innerHTML = "Select Thsese";
		selButton.owner = this;
		selButton.addEventListener("click", function () {
			selButton.parentNode.remove();
			SlclearTarget(target);
			});
	}
}


class resourceList extends objectList {
	constructor(listItems) {
		super();
		this.listItems = listItems;
	}

	getValue(trg) {
		//let returnVal = "1,"+this.selection+","+trg.showBox.slider.slide.value;
		return "1,"+trg.selectedValue+","+trg.showBox.slider.slide.value;
	}

	showItem(id, trg) {
		let objBox = addDiv("", "rscContain", trg);
		let objContent = addDiv("", "rscImg", objBox);
		let newImg = addImg(id, "rscImg", objContent);
		newImg.src = "./rscImages/"+id+".png";
		newImg.alt = id;

		return objBox;
	}

	showSelected(id, trg) {
		this.selection = id;
		trg.selectedValue = id;
		trg.showBox = slideBox(trg,0);
		trg.showBox.unitSpace.innerHTML = id;
		setSlideQty(trg.showBox, playerRsc[id]);
		trg.listItem = this;
	}

	typeIcon(trg) {
		let objBox = addDiv("", "rscContain", trg);
		objBox.innerHTML = "resources";

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
		this.listItems = listItems;
	}

	getValue(trg) {
		return "3,"+this.selection;
	}

	showItem(id, trg) {
		let objBox = addDiv("", "objContain", trg);
		let objContent = addDiv("", "objContent", objBox);
		objContent.innerHTML = id;

		return objBox;
	}

	showSelected(id, trg) {
		this.selection = id;
		trg.innerHTML = id;
		trg.listItem = this;
	}

	typeIcon(trg) {
		let objBox = addDiv("", "rscContain", trg);
		objBox.innerHTML = "resources";

		return objBox;
	}
}

class charList extends objectList {
	constructor(listItems, parentList) {
		super();
		this.listItems = listItems;
		this.parentList = parentList;
	}

	getValue(trg) {
		//console.log("type ul");
		//console.log("selection " + this.selection);
		return "2,"+this.selection;
	}

	showItem(id, trg) {
		let objBox = addDiv("", "objContain", trg);
		let objContent = addDiv("", "objContent", objBox);

		return objBox;
	}

	showSelected(id, trg) {
		this.selection = id;
		this.parentList.renderSum(id, trg);
		//trg.innerHTML = id;
		trg.listItem = this;
	}

	typeIcon(trg) {
		let objBox = addDiv("", "rscContain", trg);
		objBox.innerHTML = "resources";

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

SLreadSelection = function(trg) {
	return(trg.listItem.getValue(trg));
}
