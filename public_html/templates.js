
function newUnitDetail(id, target) {
	var holderDiv = document.createElement("div")
	holderDiv.style.class = "udHolder";
	holderDiv.id = "Udtl_"+id;
	
	var uDAv = document.createElement("img");
	uDAv.style.class = "udAvatar";
	uDAv.id = "Udtl_"+id+"_avatar";
	holderDiv.appendChild(uDAv);
	
	var uDType = document.createElement("div");
	uDType.style.class = "udType"
	uDType.id = "Udtl_"+id+"_type";
	holderDiv.appendChild(uDType);
	
	var uDLvl = document.createElement("div");
	uDLvl.style.class = "udLvl";
	uDLvl.id = "Udtl_"+id+"_lvl";
	holderDiv.appendChild(uDLvl);
	
	var uDAct = document.createElement("div");
	uDAct.style.class = "uDAct";
	uDAct.id = "Udtl_"+id+"_act";
	holderDiv.appendChild(uDAct);
	
	var uDExp = document.createElement("div");
	uDExp.style.class = "udExp";
	uDExp.id = "Udtl_"+id+"_exp";
	holderDiv.appendChild(uDExp);
	
	var uDName = document.createElement("div");	
	uDName.style.class = "udName";
	uDName.id = "Udtl_"+id+"_name";
	holderDiv.appendChild(uDName);
	
	document.getElementById(target).appendChild(holderDiv);
}