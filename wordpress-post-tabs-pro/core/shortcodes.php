<?php
function wpts_pro_tab_shortcode($atts,$content) {
	$tab_atts_arr=array(
		'name' => 'Tab Name',
		'link' => '',
		'class' => '',
		'active' => '',
		'set' => '0',
	);
	$tab_atts_arr=apply_filters('wptspro_tab_atts',$tab_atts_arr);
	
	extract(shortcode_atts($tab_atts_arr, $atts));
	
    global $wpts;
	global $wpts_content,$wpts_tab_count,$wpts_count;
	
	if(isset($wpts_tab_count[$set]))$ncount=$wpts_tab_count[$set];
	if(empty($ncount))$ncount=0;
	$wpts_content[$set][$ncount]['name'] = $name;
	$wpts_content[$set][$ncount]['link'] = $link;
	$wpts_content[$set][$ncount]['class'] = $class;
	$wpts_content[$set][$ncount]['selected'] = $active;	
	$wpts_content[$set][$ncount]['content'] = do_shortcode($content);
    $wpts_tab_count[$set] = $ncount+1;
	
	$wpts_content[$set]=apply_filters('wptspro_tab_content',$wpts_content[$set],$set,$tab_atts_arr,$atts);
		
	if(is_feed()){
	  $return = '<h4>'.$name.'</h4>'.$content;
	  return $return;
	}
    return null;
}
if(isset($wpts['nested']) and $wpts['nested']!=''){
	for($wpts_ncount=0;$wpts_ncount<=$wpts['nested'];$wpts_ncount++){
		if($wpts_ncount==0) $wpts_ncount_str='';
		else $wpts_ncount_str=$wpts_ncount;
		add_shortcode($wpts['tab_code'].$wpts_ncount_str, 'wpts_pro_tab_shortcode');
	}
}
else{
	add_shortcode($wpts['tab_code'], 'wpts_pro_tab_shortcode');
}

function wpts_pro_end_shortcode($atts=array()) {
 if(is_feed()){
   return null;
 }
 global $wpts,$post,$wpts_content,$wpts_tab_count,$wpts_count,$wpts_prev_post,$wpts_skin,$default_tab_settings;
 $data=array();
 
 $post_id = $post->ID;
 foreach($default_tab_settings as $key=>$value){
		if(!isset($wpts[$key])) $wpts[$key]='';
	}
 if($wpts_prev_post!=$post_id){$wpts_count=0;}

	if(is_singular() or $wpts['enable_everywhere'] == '1') {
		//Set Parameters
		if(is_array($atts))	extract($atts,EXTR_PREFIX_ALL,'in');
		if(empty($in_set)) $in_set='0';
		if(empty($in_showtitle)) $in_showtitle=$wpts['showtitle']; 
		// For External link Icon
		if(empty($in_ext_link_icon)) $in_ext_link_icon=$wpts['ext_link_icon'];
		if(empty($in_targetblank)) $in_targetblank=$wpts['linktarget'];
		if(empty($in_skin)) $in_skin=$wpts['stylesheet']; if(empty($in_skin)) $in_skin='default';
		if(empty($in_location)) $in_location=$wpts['location'];	if(empty($in_location)) $in_location='top';
		//wrap CSS
		if(empty($in_width)) $in_width_css=''; else $in_width_css='width:'.$in_width.';';
		$wpts_wrap_css_string='"'.$in_width_css.'"';
		if(!empty($wpts_wrap_css_string) and $wpts_wrap_css_string!=''){$wpts_wrap_css=' style='.$wpts_wrap_css_string; }
		//panel CSS
		if(empty($in_contentwidth)) $in_contentwidth_css=''; else $in_contentwidth_css='width:'.$in_contentwidth.';';
		$wpts_panel_css_string='"'.$in_contentwidth_css.'"';
		if(!empty($wpts_panel_css_string) and $wpts_panel_css_string!=''){$wpts_panel_css=' style='.$wpts_panel_css_string; }
		//ul nav CSS
		if(empty($in_navwidth)) $in_nav_css=''; else $in_nav_css='max-width:'.$in_navwidth.';';
		$wpts_nav_css_string='"'.$in_nav_css.'"';
		if(!empty($wpts_nav_css_string) and $wpts_nav_css_string!=''){$wpts_nav_css=' style='.$wpts_nav_css_string; }
		
		if($wpts_tab_count[$in_set]!=0 and isset($wpts_tab_count[$in_set])) {
			 $tab_content = '<ul '.$wpts_nav_css.'>';
			 $tab_i=0;
			 
			 //Get Page URL
			 $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			if(empty($in_nourl)) $pageurl=$protocol . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];
			else $pageurl='';
			 
			 for($i=0;$i<$wpts_tab_count[$in_set];$i++) {
				$link = $wpts_content[$in_set][$i]['link'];
				if ($in_showtitle == '1') $linktitle = 'title="'.strip_tags($wpts_content[$in_set][$i]['name']).'"';
				else $linktitle = '';
				if ($in_targetblank == '1') $linktarget = 'target="_blank"';
				else $linktarget = '';
				if($in_ext_link_icon == '1') $ext_link_icon_add = '<span class="wpts_ext_icon"></span> ';
				else $ext_link_icon_add = '';
				//Separate class for each tab
				$class='wpts_li_a '.$wpts_content[$in_set][$i]['class'];
				$class=!empty($class)?('class="'.$class.'"'):'';
				//tab/link aatributes
				$linkattr='';
				$linkattr=apply_filters('wptspro_tab_linkattr',$linkattr,$atts,$wpts_content[$in_set],$in_set);
				//name specific links				
				$linkhref=preg_replace('/<[^>]*>/', '', $wpts_content[$in_set][$i]['name']);				
				$linkhref=preg_replace('/\W/', '-', $linkhref);

				if(isset($wpts_content['id']) and is_array($wpts_content['id'])){if(in_array($linkhref,$wpts_content['id']))$linkhref.=$wpts_count;}		
					
				if(!empty($link)) {
					if($in_skin=="sliding")
						$tab_content = $tab_content.'<li><a href="'.$wpts_content[$in_set][$i]['link'] .'" '.$linktitle .$linktarget. '>'.$wpts_content[$in_set][$i]['name'].$ext_link_icon_add.'</a></li>';
					else
						$tab_content = $tab_content.'<span class="wpts_ext"><a href="'.$wpts_content[$in_set][$i]['link'] .'" '.$linktitle .$linktarget.$class. '>'.$wpts_content[$in_set][$i]['name'].$ext_link_icon_add.'</a></span>';
				}
				else {
					//$tab_content = $tab_content.'<li><a href="'.$pageurl.'#tabs-'.$post_id.'-'.$wpts_count.'-'.$i.'" '.$linktitle .$linktarget.$class.'>'.$wpts_content[$in_set][$i]['name'].'</a></li>';
					$tab_content = $tab_content.'<li><a href="'.$pageurl.'#'.$linkhref.'" '.$linktitle .$class.'>'.$wpts_content[$in_set][$i]['name'].'</a></li>';
					//Selected tab by default
					$selected=$wpts_content[$in_set][$i]['selected'];
					if($selected=='1')$data['selected']=$tab_i;
					$tab_i++;
				}
			  }
			$tab_content = $tab_content.'</ul>';
			$clear_div='';
			if($in_skin!="sliding")$clear_div='<div class="wpts_cl"></div><div class="wpts_cr"></div>';
			
			$tab_html='';
			for($i=0;$i<$wpts_tab_count[$in_set];$i++) {
				$link_html = $wpts_content[$in_set][$i]['link'];
				//name specific links
				$linkhref=preg_replace('/<[^>]*>/', '', $wpts_content[$in_set][$i]['name']);				
				$linkhref=preg_replace('/\W/', '-', $linkhref);
				if(isset($wpts_content['id']) and is_array($wpts_content['id'])){
					if(in_array($linkhref,$wpts_content['id']))$linkhref.=$wpts_count;
					else $wpts_content['id'][]=$linkhref;
				}				
				else $wpts_content['id'][]=$linkhref;

				if(!empty($link_html)) {
					$tab_html_indv='';
				}
				else {
					//$tab_html_indv='<div id="tabs-'.$post_id.'-'.$wpts_count.'-'.$i.'" '.$wpts_panel_css.'><p>'.$wpts_content[$in_set][$i]['content'].'</p></div>';
					$tab_html_indv='<div id="'.$linkhref.'" '.$wpts_panel_css.'><p>'.$wpts_content[$in_set][$i]['content'].'</p></div>';
				}	
				$tab_html_indv=preg_replace("/<p[^>]*>[\s|&nbsp;]*<\/p>/", '', $tab_html_indv);
				$tab_html=$tab_html.$tab_html_indv;
			}
			$tab_content = ( $in_location=='top' ) ? ( $tab_content.$clear_div ) : ( $tab_content ) ;
			$tab_html = ( $in_location=='bottom' ) ? ( $tab_html.$clear_div ) : ( $tab_html ) ;
			$tab_content = ( $in_location=='top' or $in_location=='left' ) ? ( $tab_content.$tab_html ) : ( $tab_html.$tab_content ) ;
		}
		$clear_div='';
		if($in_skin!="sliding")$clear_div='<div class="wpts_cl"></div><div class="wpts_cr"></div>';
		$tab_content = '<div id="tabs_'.$post_id.'_'.$wpts_count.'">'.$tab_content.$clear_div.'</div>';
		
		$wpts_count = $wpts_count+1;
		$wpts_tab_count[$in_set] = 0;
		
		$script = '';

		global $post;
		$post_id = $post->ID;
		
		//Choose Skin
		require_once ( dirname( dirname(__FILE__) ) . '/skins/'.$in_skin.'/functions.php');
		
		$wpts_skin[]=$in_skin;
		if($in_set=='0'){
			$wpts_skin=array_reverse($wpts_skin);
			foreach($wpts_skin as $indv_skin){
				require_once ( dirname( dirname(__FILE__) ) . '/skins/'.$indv_skin.'/functions.php');
				$init_function='wpts_pro_wp_init_'.$indv_skin;
				$init_function($atts,$data);
			}
			$wpts_skin=array();
		}
		
		$script_function='return_wpts_tab_script_'.$in_skin;
		$script=$script_function($wpts_count,$atts,$data);
		
		$line_breaks = array("\r\n", "\n", "\r");
		$script = str_replace($line_breaks, "", $script);
		
		$wpts_prev_post = $post_id;
		
		$print_html = '<div class="wordpress-post-tabs tabs_'.$post_id.'_'.( $wpts_count - 1 ).'_wrap wordpress-post-tabs-skin-'.$in_skin.'" '.$wpts_wrap_css.'>'.$tab_content.'</div>'.$script;
		
		return $print_html;
	}
	else {
		return null;
	}
}
if(isset($wpts['nested']) and $wpts['nested']!=''){
	for($wpts_ncount=0;$wpts_ncount<=$wpts['nested'];$wpts_ncount++){
		if($wpts_ncount==0) $wpts_ncount_str='';
		else $wpts_ncount_str=$wpts_ncount;
		add_shortcode($wpts['tab_end_code'].$wpts_ncount_str, 'wpts_pro_end_shortcode');
	}
}
else{
	add_shortcode($wpts['tab_end_code'], 'wpts_pro_end_shortcode');
}
?>
