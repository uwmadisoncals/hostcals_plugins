<?php function wpts_pro_wp_init_sliding($atts=array(),$data=array()) { 
    global $post,$wpts;
	if(is_singular() or $wpts['enable_everywhere'] == '1') { 
		$enablewpts = get_post_meta($post->ID, 'enablewpts', true);
		if( (is_page() and ((!empty($enablewpts) and $enablewpts=='1') or  $wpts['pages'] != '0'  ) ) 
			or (is_single() and ((!empty($enablewpts) and $enablewpts=='1') or $wpts['posts'] != '0'  ) ) or $wpts['enable_everywhere'] == '1' ) 
		{
			//Effects JS
			if(is_array($atts)) extract($atts,EXTR_PREFIX_ALL,'in');
			if(empty($in_effect)) $in_effect=$wpts['fade'];
			//Backward compatibility with lite version for Fade effect
			if($in_effect=='1')$in_effect='fade';
			if(empty($in_auto)) $in_auto='0';
			$effects_handle='';
			if(!empty($in_effect) ) {
				if( $in_effect!='0' and $in_effect!='1' and $in_effect!='2' and $in_effect!='3'){
					$effects_handle='jquery-effects-'.$in_effect;
				}
			}
			//Load CSS and JS files
			$wpts_stylesheet='sliding';
			$css='skins/'.$wpts_stylesheet.'/style.css';
			$js_folder='skins/'.$wpts_stylesheet.'/js';
			wp_enqueue_style( 'wpts_ui_css_sliding', wpts_pro_plugin_url( $css ),false, WPTSPRO_VER, 'all'); 
			if(isset($wpts['jquerynoload']) and $wpts['jquerynoload']=='1') {
			    wp_deregister_script( 'jquery' );
				wp_enqueue_script('slidingtabs', WPTSPRO_URLPATH . 'skins/sliding/js/jquery.sliderTabs.js');
				
				
			}
			else{
				wp_enqueue_script('jquery');				
				wp_enqueue_script('slidingtabs', WPTSPRO_URLPATH . 'skins/sliding/js/jquery.sliderTabs.js');
				
			}
			if(!empty($effects_handle)) wp_enqueue_script($effects_handle, false, array('jquery-effects-core'), WPTSPRO_VER, true);
			//action hook
			do_action('wptspro_skin_init',$atts,$data,$wpts_stylesheet);
		}
	}
}
function return_wpts_tab_script_sliding( $wpts_count=0,$atts=array(),$data=array() ){
	global $post,$wpts,$wpts_style,$default_tab_settings;

	foreach($default_tab_settings as $key=>$value){
		if(!isset($wpts[$key])) $wpts[$key]='';
	}
	$wpts_stylesheet='sliding';
	$post_id = $post->ID;
	$enablewpts = get_post_meta($post->ID, 'enablewpts', true);
	$script='';
	if( (!empty($enablewpts) and $enablewpts=='1') or $wpts['posts'] != '0'  ) 	{  
		//Set Parameters
		if(is_array($atts)) extract($atts,EXTR_PREFIX_ALL,'in');
		if(empty($in_onhover) and !empty($wpts['onhover']))	$in_onhover=$wpts['onhover'];
		if(empty($in_effect)) $in_effect="slide";
		//Backward compatibility with lite version for Fade effect
		if($in_effect=='1')$in_effect='fade';
		if(empty($in_effectduration)) $in_effectduration='800';
		if(empty($in_easing)) $in_easing='linear';
		if(empty($in_prevnext)) $in_prevnext=$wpts['nav'];
		if(empty($in_prevnext)) $in_prevnext="";
		if(empty($in_prevtext)) $in_prevtext=$wpts['prev_text'];
		if(empty($in_nexttext)) $in_nexttext=$wpts['next_text'];
		if(empty($in_location)) $in_location=$wpts['location'];
		if(empty($in_reload)) $in_reload=$wpts['reload'];
		if(empty($in_loadhash)) $in_loadhash=$wpts['taburl'];
		if(empty($in_showhash)) $in_showhash=$wpts['showurl'];
		if(empty($in_auto)) $in_auto='0';		
		if(empty($in_timer)) $in_timer='4';
		if(empty($in_buttons)) $in_buttons="";
		if(empty($in_onhover)) $in_onhover="";
		$script = $script.'<script type="text/javascript">jQuery(document).ready(function(){';
		if($wpts_count and $wpts_count!=0){ 
			$i = $wpts_count-1;
		$tab_name='tabs_'.$post_id.'_'.$i;
		//Tabs Location
		if( $in_location=='bottom' ) $param="position: 'bottom',";
		else $param='';
		//Next Prev Arrow 
		if($in_prevnext=='1') $param.="panelArrows: true,";
		// Width of tabs		
		if(!empty($in_width)) $param.="width: ".$in_width.",";
		if(!empty($in_effect)) $param .= "transition: '".$in_effect."',";
		if(!empty($in_effectduration)) $param .= "transitionSpeed: ".$in_effectduration.",";
		//Autorotate tabs
		if( $in_auto == '1' ) $param .= "autoplay: ".$in_timer ."* 1000,";
		//Transition on
		if($in_onhover=='1') $param .= "selectEvent: 'mouseover',";
		// default selected tab
		if( isset($data['selected']) ) $param .= "defaultTab: ".$data['selected'] .",";
		//content indicator buttons
		if($in_buttons == '1') $param .= "indicators: true,";
		$param.="mousewheel: false,";
		// Easing
		if(!empty($in_easing)) $param .= "transitionEasing: '".$in_easing."'";
		$tabtop='';
			//if(!empty($wpts['tabtop']) and $wpts['tabtop']=='1')
			//	$script .='jQuery("body,html").animate({"scrollTop":   jQuery("#"+(ui.newPanel).attr("id")).offset().top}, 1000);';

			/************************************************************************/
			/*---------------------------- STYLE EDITOR ----------------------------*/
			if(isset($wpts_style[$wpts_stylesheet]) and is_array($wpts_style[$wpts_stylesheet])){
				extract($wpts_style[$wpts_stylesheet],EXTR_PREFIX_ALL,'style');
				$script .='jQuery("head").append("<style type=\"text/css\">';
				if(!empty($style_bg) and $style_bg!="#") $script .= ' #'.$tab_name.' ul li { background: '.$style_bg.';}';
				else {if(!empty($style_active_bg) and $style_active_bg!="#"){ $script .= ' #'.$tab_name.' ul li { background: initial;}';}}	
				if(!empty($style_active_bg) and $style_active_bg!="#") $script .= ' #'.$tab_name.' ul li.tabactive { background: '.$style_active_bg.'; border-bottom-color: '.$style_active_bg.'}';
				else {if(!empty($style_bg) and $style_bg!="#"){$script .= ' #'.$tab_name.' ul li.tabactive { background: initial;}';}}	
				if(!empty($style_color) and $style_color!="#") $script .= ' #'.$tab_name.' ul li a { color: '.$style_color.';}';
				else {if(!empty($style_active_color) and $style_active_color!="#"){ $script .= ' #'.$tab_name.' ul li a { color: initial;}';} }
				if(!empty($style_active_color) and $style_active_color!="#") $script .= ' #'.$tab_name.' ul li.tabactive a { color: '.$style_active_color.';}'; else {if(!empty($style_color) and $style_color!="#"){$script .= ' #'.$tab_name.' ul li.tabactive a { color: initial;}'; }}	
				if(!empty($style_hover_color) and $style_hover_color!="#") $script .= '#'.$tab_name.' > ul > li a:hover { color: '.$style_hover_color.' !important;}'; else {$script .= '#'.$tab_name.' > ul > li a:hover { color: #111111 !important;}';}
				$script .='</style>");';
				
				
													
			}
			/*------------------------- END STYLE EDITOR -------------------------*/
		}
		$script = $script.'var $'.$tab_name.' = jQuery("#'.$tab_name.'").sliderTabs({'.$param.'}); ';
		//Linkable tabs
		if( (!empty($in_loadhash) and $in_loadhash=='1') or (!empty($in_reload) and $in_reload=='1') ){
			$script = $script.'var anchor=jQuery(document).attr("location").hash;
						if(anchor){ 		
						var index = jQuery("#'.$tab_name.' div.ui-slider-tab-content:not(.wpts_cl,.wpts_cr)").index(jQuery(anchor));
							if(index != -1)	$'.$tab_name.'.data("sliderTabs").selectTab(index+1);
						}';	

			
		}
			//filter hook
			$script=apply_filters('wptspro_sliding_tabjs',$script,'$'.$tab_name,$wpts_stylesheet);
			$script = $script.'})';
			
			$script = apply_filters('wptspro_skin_scripts',$script,$atts,$data,$wpts_count,$wpts_stylesheet);
$script = $script.'</script> ';
	}
	return $script;
}
?>
