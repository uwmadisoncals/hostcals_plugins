jQuery(document).ready(function($){ if( window.ContentBuilder !== undefined && $('#content-cb').length == 1 ) { window.ContentBuilder.init({"ajaxUrl":"http:\/\/host.cals.wisc.edu\/wp-content\/plugins\/content-builder\/content-builder.php","basePath":"http:\/\/host.cals.wisc.edu\/wp-content\/plugins\/content-builder\/cb\/","blocks":{"heading":{"label":"Heading","name":"heading","editor":"ContentBlockHeading","form":"<fieldset><label>Heading:<\/label><p><select name=\"heading\" class=\"cb-choice\"><option value=\"h2\">H2<\/option><option value=\"h3\">H3<\/option><option value=\"h4\">H4<\/option><option value=\"h5\">H5<\/option><option value=\"h6\">H6<\/option><\/select><\/p><\/fieldset><fieldset class=\"optional\"><label>Link:<\/label><p><input type=\"text\" name=\"link\" class=\"text\"\/><input type=\"checkbox\" name=\"target\" value=\"_blank\" style=\"float:left;margin-right:5px;margin-top:6px;\"\/><span style=\"float:left;line-height:25px;\">new window<\/span><\/p><\/fieldset><fieldset class=\"optional\"><label>Title:<\/label><p><input type=\"text\" name=\"title\" class=\"text\" \/><\/p><\/fieldset><fieldset class=\"optional\" ><label>Class:<\/label><p><input type=\"text\" name=\"class\" class=\"text\" \/><\/p><\/fieldset>"},"image":{"label":"Image","name":"image","editor":"ContentBlockImage","form":"<fieldset><label>Address:<\/label><p><input type=\"text\" name=\"source\" class=\"text\" \/><a style=\"font-weight:bold;\" href=\"#browse\" data-act=\"browse\">change<\/a><\/p><\/fieldset><fieldset><label>Scale:<\/label><p><select name=\"scale\" class=\"cb-choice\"><option value=\"100%\">Fit<\/option><option value=\"75%\">75%<\/option><option value=\"50%\">50%<\/option><option value=\"25%\">25%<\/option><option value=\"none\">Original<\/option><\/select><\/p><\/fieldset><fieldset class=\"optional\"><label>Alt:<\/label><p><input type=\"text\" name=\"alt\" class=\"text\" \/><\/p><\/fieldset><fieldset class=\"optional\"><label>Link:<\/label><p><input type=\"text\" name=\"link\" class=\"text\"\/><input type=\"checkbox\" name=\"target\" value=\"_blank\" style=\"float:left;margin-right:5px;margin-top:6px;\"\/><span style=\"float:left;line-height:25px;\">new window<\/span><\/p><\/fieldset><fieldset class=\"optional\" ><label>Class:<\/label><p><input type=\"text\" name=\"class\" class=\"text\" \/><\/p><\/fieldset>"},"video":{"label":"Video","name":"video","editor":"ContentBlockVideo","form":"<fieldset><label>Address:<\/label><p><input type=\"text\" name=\"url\" class=\"text\" \/> (youtube.com , vimeo.com)<\/p><\/fieldset><fieldset><label>Scale:<\/label><p style=\"float:left;\"><select name=\"scale\" class=\"cb-choice\"><option value=\"default\">500px<\/option><option value=\"100%\">Fit<\/option><option value=\"75%\">75%<\/option><option value=\"50%\">50%<\/option><option value=\"25%\">25%<\/option><\/select><\/p><label style=\"width:auto;margin-left:20px;margin-right:5px;\">Ratio:<\/label><p style=\"float:left;\"><select name=\"ratio\" class=\"cb-choice\"><option value=\"16:9\">16:9<\/option><option value=\"4:3\">4:3<\/option><\/select><\/p><\/fieldset><fieldset class=\"optional\" ><label>Class:<\/label><p><input type=\"text\" name=\"class\" class=\"text\" \/><\/p><\/fieldset>"},"rte":{"label":"Rich Text Editor","name":"rte","editor":"ContentBlockRedactor","form":"<fieldset class=\"optional\" ><label>Class:<\/label><p><input type=\"text\" name=\"class\" class=\"text\" \/><\/p><\/fieldset>"},"layout":{"label":"Layout","name":"layout","layout":"6-6","editor":"ContentBlockLayout","types":["12","6-6","8-4","4-8","4-4-4"]},"tab":{"label":"Tab block","name":"tab","layout":"a","editor":"ContentBlockTab","form":"<fieldset><label>Label:<\/label><p><input type=\"text\" name=\"label\" class=\"text\" \/><\/p><\/fieldset>"},"divider":{"label":"Divider","name":"divider","editor":"ContentBlockDivider"},"gallery":{"label":"Gallery","name":"gallery","editor":"ContentBlockGallery","form":"<fieldset><label>Address:<\/label><p><input type=\"text\" name=\"source\" class=\"text\" \/><a style=\"font-weight:bold;\" href=\"#browse\" data-act=\"browse\">change<\/a><\/p><\/fieldset><fieldset class=\"optional\"><label>Alt:<\/label><p><input type=\"text\" name=\"alt\" class=\"text\" \/><\/p><\/fieldset><fieldset class=\"optional\"><label>Link:<\/label><p><input type=\"text\" name=\"link\" class=\"text\" \/><\/p><\/fieldset>"},"gmap":{"label":"Google Map","name":"gmap","editor":"ContentBlockGoogleMap","form":"<fieldset><label>Location:<\/label><p><input type=\"text\" name=\"location\" class=\"text\" \/><a data-act=\"locate\" style=\"font-weight:bold;\" href=\"#https:\/\/maps.google.com\/\">locate address<\/a><\/p><\/fieldset><fieldset><label>Height:<\/label><p><input type=\"text\" name=\"height\" class=\"text\" value=\"300\" \/><\/p><\/fieldset><fieldset class=\"optional\" ><label>Class:<\/label><p><input type=\"text\" name=\"class\" class=\"text\" \/><\/p><\/fieldset>"}},"version":"1.0.4","trails":{"builder":"http:\/\/host.cals.wisc.edu\/wp-content\/plugins\/content-builder\/cb","cache":"http:\/\/host.cals.wisc.edu\/wp-content\/uploads\/content-builder","wp-uploads":"http:\/\/host.cals.wisc.edu\/wp-content\/uploads"},"toolbar":"<div class=\"cb-toolbar-wrap\"><div id=\"ContentBuilder-Toolbar\" class=\"cb-toolbar cb-clearfix\"><div class=\"cb-center\"><div class=\"cb-toolbar-dropdown\"><span>Add elements<em><\/em><\/span><ul><li class=\"first\">Add elements<em><\/em><\/li><\/ul><\/div><a href=\"http:\/\/www.contentbuilder.net\" onclick=\"window.open(this.href);return false;\" class=\"cb-toolbar-logo\"><img src=\"http:\/\/host.cals.wisc.edu\/wp-content\/plugins\/content-builder\/cb\/assets\/images\/toolbar-logo.png\" style=\"width:137px;height:43px;\"\/><\/a><\/div><div class=\"cb-toolbar-locker\"><\/div><\/div><\/div>","width":600}); window.ContentBuilder.replace('content-cb'); } 	$('#content-mode-button').insertBefore('#wp-content-editor-tools'); 		$('#content-mode-button').click(function(){		var answer = confirm( $(this).attr('title') );		if (answer){			if( $('#wp-content-wrap').hasClass('tmce-active') ) {				$('#content-html').trigger('click');			}			$('#content-builder-mode').val( ($(this).data('mode') ) );			$('#publish').trigger('click');		}		return false; 	});  });