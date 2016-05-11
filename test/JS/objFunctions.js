
addDiv = function(id, useClassName, target) {
	var newDiv = document.createElement("div");
	newDiv.className = useClassName;
	newDiv.id = id;
	target.appendChild(newDiv);
	return newDiv;
}

scrButton = function (prm, trg, src) {
	var newButton = addDiv("button", "button", trg);
	newButton.addEventListener("click", function () {
		selectItem(this);
		//scrMod(prm);
		})
	newButton.innerHTML = src;
	return newButton;
}

selectButton = function (trg, src, id, others) {
	var newButton = addDiv("button", "button", trg);
	newButton.addEventListener("click", function () {
		selectItem(this, id, others);
		})
	newButton.innerHTML = src;
	//return newButton;
}

var selectedItem;
selectItem = function (trg, id, others) {
	if (selectedItem)	selectedItem.style.borderColor = "000000";
	selectedItem = trg.parentNode;
	trg.parentNode.style.borderColor = "#FF0000";	
	console.log(others[0]);
	for (var i=0; i<others.length; i++) {
		itemList.renderSingleSum(id, others[i]);
	}
}