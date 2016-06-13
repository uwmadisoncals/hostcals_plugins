/*Logo Upload Script */
jQuery(document).ready(function() {
 
jQuery('#upload_logo_button').click(function() {
 formfield = jQuery('#logo').attr('name');
 tb_show('logo_upload', 'media-upload.php?type=image&amp;TB_iframe=true');
 
 
 window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 jQuery('#logo').val(imgurl);
 tb_remove();
}
 
 return false;
});
 

 
});

/*Background Image Upload Script */
jQuery(document).ready(function() {
 
jQuery('#background_image_button').click(function() {
 formfield2 = jQuery('#background-image').attr('name');
 tb_show('background_image', 'media-upload.php?type=image&amp;TB_iframe=true');
 
 
 
 window.send_to_editor = function(html) {
 imgurl2 = jQuery('img',html).attr('src');
 jQuery('#background-image').val(imgurl2);
 tb_remove();
}
 
 
 
 return false;
});
 

 
});

/*Ajax call to get mail list info */
jQuery(document).ready( function() {
	 
	   jQuery("#my_submit").click( function() {
	   
	      //landing_page_id = jQuery(this).attr("landing_page_id")
	     // nonce = jQuery(this).attr("data-nonce")
	     landing_page_id = "123"
	 
	      jQuery.ajax({
	         type : "post",
	         dataType : "jsonp",
	         //jsonp: "callback",
	         //dataType: "html",
	         //url : myAjax.ajaxurl,
	         //url: "http://www.menuflavors.com/LandingPages.action",
	         url: "http://www.menuflavors.com/mytest.html",
	         //data : {action: "my_user_vote", post_id : post_id, nonce: nonce},
	         data:{landingPageId : landing_page_id},
	         jsonpCallback:"myFunction",
	         //success: function(response) {
	         //alert(response)
	         	//jQuery("#the_message").html("the test worked")
	            //if(response.type == "success") {
	               //jQuery("#vote_counter").html(response.vote_count)
	            //   jQuery("#the_message").html("the test worked")
	           // }
	           // else {
	              // alert("Your vote could not be added")
	           // }
	         //}
	      })  
	 
	   })
	 
	})

function myFunction(data){
jQuery("#the_message").html(data.text)
	alert(data.text)
}