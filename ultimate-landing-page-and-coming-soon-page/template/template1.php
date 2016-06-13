<?php
$ulp_options = get_option('ulp_options');





?>


<html>
<head>
<title><?php echo stripslashes($ulp_options['title']) ?></title>
<!-- Version: 1.1.29 -->
<!-- <link rel="stylesheet" href="<?php echo plugins_url('template/style.css',dirname(__FILE__)); ?>"> -->
<link href='http://fonts.googleapis.com/css?family=<?php echo str_replace(" ", "+", $ulp_options['headline_font'])."|".str_replace(" ", "+", $ulp_options['description_font']) ?>' rel='stylesheet' type='text/css'>

<style type="text/css">

html, body {
  height: 100%;
  
}

body{
	 background-attachment: fixed;
    background-clip: border-box;
    background-color: <?php echo $ulp_options['my_background_color'] ?>;
   <?php if($ulp_options['background-image']){ ?>  background-image: url("<?php echo $ulp_options['background-image'] ?>"); <?php } //endif?>
    background-origin: padding-box;
    background-position: center top;
    background-repeat: no-repeat;
    background-size: cover;
}

img#bg {
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
 /* opacity:0.4; */
/* filter:alpha(opacity=40); */
z-index:-1
} 
 #headline{
 	font-family: '<?php echo $ulp_options['headline_font'] ?>';
 	font-size:28px;
 	color:<?php echo $ulp_options['headline_font_color'] ?>;
 	
 }
 
 #description{
 	font-family: '<?php echo $ulp_options['description_font'] ?>';
 	font-size:12 px;
 	color:<?php echo $ulp_options['description_font_color'] ?>;
 	
 }
 <?php if(isset($ulp_options['show_info_background_color']) && $ulp_options['show_info_background_color']){ ?>
 #content_area {
    background: none repeat scroll 0 0 padding-box rgba(0, 0, 0, 0.8);
    border-radius: 4px 4px 4px 4px;
    box-shadow: 0 0 6px rgba(0, 0, 0, 0.25);
}
<?php } ?>

#notify-btn {
    background-color: #80223F;
    background-image: -moz-linear-gradient(center top , #BC325D, #80223F);
    background-repeat: repeat-x;
    border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
    color: #FFFFFF;
    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
    height: 25px;
}

#notify-email {
    font-size: 18px;
    height: 25px;
    width: 270px;
}



</style>

</head>
<body>


<table align="center" valign="center">
	<!-- <table height="400" width="600" align="center" valign="center" <?php if($ulp_options['info_background_color']){ ?>style="background-color:<?php echo $ulp_options['info_background_color']; ?>" <?php } ?>> -->
		<table height="400" width="600" align="center" valign="center" id="content_area">
		<tr>
			<td>
				<?php if($ulp_options['logo']){ ?>
				<tr valign="bottom">
					<td align="center"><img src="<?php echo $ulp_options['logo'] ?>"></td>
				</tr>
				<?php } //endif ?>
				<tr valign="bottom">
					<td align="center" id="headline"><h1><?php echo stripslashes($ulp_options['headline']) ?></h1></td>
				</tr>
				<tr valign="top">
					<td id="description"><?php echo $ulp_options['description'] ?></td>
				</tr>
				<tr>
					 <td align="center">
					 <!--<input type="text" size="30">&nbsp;&nbsp;<input id="my_submit" type="submit" value="Sign me up"> -->

					<form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit='window.open("http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $ulp_options['feedburner_address'] ?>", "popupwindow", "scrollbars=yes,width=550,height=520");return true'>
                    <input type="hidden" value="<?php echo $ulp_options['feedburner_address'] ?>" name="uri"/>
                    <input type="hidden" name="loc" value="en_US"/>
		    <input type="hidden" name="landing_page_id" value="<?php echo $ulp_options['landing_page_id'] ?>"/>
                    <input id="notify-email" type="text" name="email" placeholder="Enter Your Email"/>

                    <button id="notify-btn" type="submit">Add Me!</button>
    			</form>


</td>
				</tr>
			</td>
		</tr>
	</table>
</table>

  
</body>
</html>
<?php exit(); ?>
