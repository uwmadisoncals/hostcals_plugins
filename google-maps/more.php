<?php
echo '
var the_timer;
var chosen = [];
var buttonWidth=86;
var class1="", class2="", class3="";

/* Implementation of the button "More" with the code from Wolfgang Pichler -> http://www.wolfpil.de/more-button.html. Thanks Wolfgang :) */

/* Array of GLayers
 * The "name" property is not being used here
*/
var layers = [
 { name: "Pano", obj: new GLayer("com.panoramio.all") },
 { name: "Tube", obj: new GLayer("com.youtube.all") },
 { name: "Wiki", obj: new GLayer("org.wikipedia.en") },
 { name: "Cams", obj: new GLayer("com.google.webcams") }
];

function hideAll(id) {
	var boxes = document.getElementsByName("mark_"+id);
	for(var i = 0; i < boxes.length; i++) {
		if(boxes[i].checked) {
			boxes[i].checked = false;
			switchLayer(false, layers[i].obj, id);
			chosen.push(i);
		}
	}
	var button = document.getElementById("more_inner_"+id);
	button.className ="more_inner";
}

function checkChecked(id) {
	/* Returns true if a checkbox is still checked otherwise false */
	var boxes = document.getElementsByName("mark_"+id);
	for(var i = 0; i < boxes.length; i++) {
		if(boxes[i].checked) return true;
	}
	return false;
}

function switchLayer(checked, layer, id) {
	/* Function was originally borrowed from Esa: http://esa.ilmari.googlepages.com/dropdownmenu.htm */
	var layerbox = document.getElementById("box_"+id);
	var boxlink = document.getElementById("boxlink_"+id);
	var button = document.getElementById("more_inner_"+id);

	if(checked) {
		if(class1=="") {
			class1=boxlink.className;
			class2=layerbox.className;
			class3=button.className;
		}

		eval("map_"+id).addOverlay(layer);
		// Reset chosen array
		chosen.length = 0;
		/* Highlight the link and make the button font bold. */
		boxlink.className =class1+" highlight";
		layerbox.className =class2+" highlight";
		button.className =class3+" highlight";
	}
	else {
		eval("map_"+id).removeOverlay(layer);
		/* Reset the link and the button if all checkboxes were unchecked.	*/
		if(!checkChecked(id)) {
			boxlink.blur();
			boxlink.className = class1;
			layerbox.className = class2;
			button.className = class3;
		}
	}
}

function showLayerbox(id) {
	if(window.the_timer) clearTimeout(the_timer);
	document.getElementById("box_"+id).style.display = "block";
	var button = document.getElementById("more_inner_"+id);
	button.style.borderBottomWidth = "4px";
	button.style.borderBottomColor = "white";
}


function setClose(id) {
	var layerbox = document.getElementById("box_"+id);
	var button = document.getElementById("more_inner_"+id);
	var bottomColor = checkChecked(id) ? "#6495ed" : "#c0c0c0";

	the_timer = window.setTimeout(function() {
		layerbox.style.display = "none";
		button.style.borderBottomWidth = "1px";
		button.style.borderBottomColor = bottomColor;
	}, 100);
}


function toggleLayers(id) {
	if(chosen.length > 0 ) {
		/* Make an independent copy of chosen array since switchLayer() resets the chosen array, which may not be useful here. */
		var copy = chosen.slice();
		for(var i = 0; i < copy.length; i++) {
			var index = parseInt(copy[i]);
			switchLayer(true, layers[index].obj, id);
			document.getElementsByName("mark_"+id)[index].checked = true;
		}
	}
	else {
		hideAll(id);
	}
}

var use_yet=false;

function MoreControl_'.$count.'() {};
MoreControl_'.$count.'.prototype = new GControl();
MoreControl_'.$count.'.prototype.initialize = function(map_'.$count.') {

	if(use_yet==false) {
		if(document.createStyleSheet) {
			document.createStyleSheet("'.get_bloginfo('wpurl').'/wp-content/plugins/google-maps/maps.css");
		}
		else {
			var newSS=document.createElement("link");
			newSS.rel="stylesheet";
			newSS.type="text/css";
			newSS.href="'.get_bloginfo('wpurl').'/wp-content/plugins/google-maps/maps.css";
			document.documentElement.firstChild.appendChild(newSS)
		}
		use_yet=true;
	}

	var more_'.$count.' = document.getElementById("outer_more_'.$count.'");
	var buttonDiv = document.createElement("div");
	buttonDiv.id = "morebutton_'.$count.'";
	buttonDiv.title = "'.__('Show/Hide Layers',googlemaps).'";
	buttonDiv.className="more_button";
	buttonDiv.style.width = buttonWidth+"px";
	var textDiv = document.createElement("div");
	textDiv.id = "more_inner_'.$count.'";
	textDiv.className="more_inner";
	textDiv.appendChild(document.createTextNode("'.__('More...',googlemaps).'"));
	buttonDiv.appendChild(textDiv);

	// Register Event handlers
	more_'.$count.'.onmouseover = function () { showLayerbox('.$count.'); }
	more_'.$count.'.onmouseout = function () { setClose('.$count.'); }
	buttonDiv.onclick = function () { toggleLayers('.$count.'); }

	// Insert the button just after outer_more div
	more_'.$count.'.insertBefore(buttonDiv, document.getElementById("box_'.$count.'").parentNode);
	map_'.$count.'.getContainer().appendChild(more_'.$count.');

	return more_'.$count.';
}

MoreControl_'.$count.'.prototype.getDefaultPosition = function() {
	//moitie=(document.getElementById("Googlemap_'.$count.'").offsetWidth/2)-(buttonWidth/2);
	moitie=70;
	return new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(moitie, 7));
}
';
?>