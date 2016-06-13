<?php
include( dirname(__FILE__).'/BasicBlock.php' );
/**
 * Widget Block
 * @version $id$
 */
class WidgetBlock extends BasicBlock {

	/**
	 * Execute custom action
	 * @param $action
	 * @param array $params
	 */
	public function execute( $action, $params = array() ) {
		$retVal = null;
		switch( $this->name ) {
			case 'gmap':
				//pa($this->data);
				if( $action == 'iframe' OR $action == 'iframe-edit' ) {
					include( dirname(__FILE__).'/blocks/gmap-iframe.php' );
					exit();
				}
			break;
		}
		return( $retVal );
	}

	/**
	 * Store event
	 */
	public function store() {

		switch( $this->name ) {
			case 'image':

				if( isset($this->data['source']) ) {
					$source = $this->data['source'];
					if( strlen($source) ) {

						$storeData = (isset($this->data['store'])?$this->data['store']:array('source'=>''));

						$oBuilder = $this->getBuilder();
						$sourceBase = $oBuilder->base( $this->data['source'] );
						$oBuilder->url($sourceBase);
						$sourcePath = $oBuilder->path( $sourceBase );
						$sourceInfo = pathinfo( $sourcePath );
						$params = array();
						$params['size'] = implode('x',$this->data['size']);
						$uploadBase = '~upload/'.$this->id.'-'.md5($sourceInfo['dirname'].'/'.$sourceInfo['filename'].'-cb('.implode('-',$params).').'.$sourceInfo['extension']).'.'.$sourceInfo['extension'];
						$uploadPath = $oBuilder->path($uploadBase);
						$uploadInfo = pathinfo( $uploadPath );

						//unlink old store file
						if( !is_file($sourcePath) ) {
							unset( $this->data['store'] );
							if( isset($storeData['source']) ) {
								$storeBase = $oBuilder->base( $storeData['source'] );
								$storePath = $oBuilder->path( $storeBase );
								if( is_file($storePath) ) {
									unlink( $storePath );
								}
							}
						}

						//create new image
						if( $storeData['source'] != $uploadBase || !is_file($uploadPath) ) {

							if( is_file($sourcePath) ) {
								if( is_file($uploadPath) ) {
									unlink( $uploadPath );
								}
								if( !is_dir($uploadInfo['dirname'])) {
									mkdir($uploadInfo['dirname'],0777, true );
								}
								//Create cache path
								require_once( dirname(__FILE__).'/utils/ImageUtil.php');
								$size = $this->data['size'];
								$image = new ImageUtil( $sourcePath, false );
								$image->resizeTarget( $size['width'] , $size['height'], ImageUtil::RESIZE_STRETCH );
								$image->saveTarget( $uploadPath );

								if( is_file($uploadPath) ) {
									$size = getimagesize( $uploadPath );

									$store = array();
									$store['source'] = $uploadBase;
									$store['width'] = $size[0];
									$store['height'] = $size[1];
									$store['mime'] = $size['mime'];
									$store['size'] = filesize($uploadPath);

									$this->data['store'] = $store;
								}

								if( strlen($storeData['source']) > 0 ) {
									$storeBase = $storeData['source'];
									$storePath = $oBuilder->path( $storeBase );
									//check is same block
									preg_match('/\/([0-9]+)-thumb/', $storeBase, $m );
									if( isset($m[1]) ) {
										if( is_file($storePath) && $storeBase!=$uploadBase && intval($m[1])==$this->id ) {
											unlink($storePath);
										}
									}
								}

							}
						}


					}
				}

			break;
			case 'gallery':

				if( isset($this->data['items']) ) {

					$oBuilder = $this->getBuilder();
					$items = $this->data['items'];
					$params = array();
					$params['size'] = $this->data['itemWidth'].'x'.$this->data['itemHeight'];
					$tmp = array();
					foreach($items as $key=>$item ) {

						$itemStore = isset($item['store'])?$item['store']:array('source'=>'');
						$sourceBase = $oBuilder->base( $item['source'] );
						$sourcePath = $oBuilder->path( $sourceBase );
						$sourceInfo = pathinfo( $sourcePath );

						$uploadBase = '~upload/'.$this->id.'-thumb-'.md5($sourceInfo['dirname'].'/'.$sourceInfo['filename'].'-cb('.implode('-',$params).').'.$sourceInfo['extension']).'.'.$sourceInfo['extension'];
						$uploadPath = $oBuilder->path($uploadBase);
						$uploadInfo = pathinfo( $uploadPath );

						$largeBase = '~upload/'.$this->id.'-large-'.md5($sourceInfo['dirname'].'/'.$sourceInfo['filename'].'-cb('.implode('-',$params).').'.$sourceInfo['extension']).'.'.$sourceInfo['extension'];
						$largePath = $oBuilder->path($largeBase);

						/*
						echo $sourceBase;
						echo $sourcePath;
						pa($item);
						exit();
						*/

						if( $itemStore['source']!=$uploadBase || !is_file($uploadPath) ) {
							if( is_file($sourcePath) ) {
								if( is_file($uploadPath) ) {
									unlink( $uploadPath );
								}
								if( !is_dir($uploadInfo['dirname'])) {
									mkdir($uploadInfo['dirname'],0777, true );
								}

								require_once( dirname(__FILE__).'/utils/ImageUtil.php');
								$image = new ImageUtil( $sourcePath, false );
								$image->resizeThumb( $this->data['itemWidth'] , $this->data['itemHeight'] );
								$image->saveTarget( $uploadPath );
								$size = getimagesize( $uploadPath );

								$store = array();
								$store['source'] = $uploadBase;
								$store['width'] = $size[0];
								$store['height'] = $size[1];
								$store['mime'] = $size['mime'];
								$store['size'] = filesize($uploadPath);
								$item['store'] = $store;

								$image = new ImageUtil( $sourcePath, false );
								$image->resizeTarget( 600 ,600, ImageUtil::RESIZE_FIT );
								$image->saveTarget( $largePath );
								$size = getimagesize( $largePath );
								$large = array();
								$large['source'] = $largeBase;
								$large['width'] = $size[0];
								$large['height'] = $size[1];
								$large['mime'] = $size['mime'];
								$large['size'] = filesize($largePath);
								$item['large'] = $large;

								if( strlen($itemStore['source']) > 0 ) {
									$storeBase = $itemStore['source'];
									$storePath = $oBuilder->path( $storeBase );
									//check is same block
									preg_match('/\/([0-9]+)-thumb/', $storeBase, $m );
									if( isset($m[1]) ) {
										if( is_file($storePath) && $storeBase!=$uploadBase && intval($m[1])==$this->id ) {
											unlink($storePath);
										}
									}
								}
							}
						}
						$tmp[] = $item;
					}
					$this->data['items'] = $tmp;
				}
			break;
		}

	}

	/**
	 * Render widget html
	 * @return string
	 */
	public  function html() {
		$retVal = '';
		switch( $this->name ) {
			case 'heading':
				$target = $title = $heading = $class = '';
				if(isset($this->data['target'])){
					$target = $this->data['target'];
				}
				if( isset($this->data['text'])) {
					$title = strip_tags($this->data['text']);
					if( isset($this->data['title']) ) {
						if( strlen($this->data['title'])>0 ) {
							$title = $this->data['title'];
						}
					}
				}
				if( isset($this->data['text'])) {
					$heading = $this->data['text'];
				}
				if( isset($this->data['class'])) {
					$class = $this->data['class'];
				}
				if( isset($this->data['link']) ) {
					if( strlen($this->data['link'])>0 ) {
						$heading = '<a href="'.$this->getBuilder()->url($this->data['link']).'"'.' title="'.$title.'"'.($target=='_blank'?' onclick="window.open(this.href);return false;"':'').'">'.$heading.'</a>';
					}
				}
				if( isset($this->data['heading']) ) {
					if( strlen($heading) > 0 ) {
						$retVal = '<'.$this->data['heading'].(strlen($class)>0?' class="'.$class.'"':'').'>'.$heading.'</'.$this->data['heading'].'>';
					}
				}
			break;
			case 'image':
				if( isset($this->data['source']) ) {
					$store = null;
					$target = '';
					if(isset($this->data['target'])){
						$target = $this->data['target'];
					}
					$source = $this->getBuilder()->url($this->data['source']);
					if( strlen($source) > 0 ) {
						if( isset($this->data['store']) ) {
							$store = $this->data['store'];
							$source = $this->getBuilder()->url($store['source']);
						}
						$style = 'width:auto;height:auto;margin:0px auto;display:block;';
						if( isset($this->data['size']) ) {
							$size = $this->data['size'];
							$style = 'width:'.$size['width'].'px;height:'.$size['height'].'px;margin:0px auto;display:block;';
						}
						if( $this->data['scale'] == '100%' ) {
							$style = 'width:100%;height:auto;margin:0px;display:block;';
						}
						$image = '<img src="'.$source.'" style="'.$style.'" alt="'.(isset($this->data['alt'])?$this->data['alt']:'').'" />';
						if( strlen($this->data['link'])>0 ) {
							$image = '<a href="'.$this->getBuilder()->url($this->data['link']).'"'.' style="'.$style.'" title="'.(isset($this->data['alt'])?$this->data['alt']:'').'"'.($target=='_blank'?' onclick="window.open(this.href);return false;"':'').'>'.$image.'</a>';
						}
						$retVal = '<div class="cb-image" '.(strlen($this->data['class'])>0?' class="'.$this->data['class'].'"':'').' style="overflow:hidden;width:100%;text-align:center;margin-bottom:10px;">'.$image.'</div>';
					}
				}
			break;
			case 'video':
				//pa($this->data);
				$retVal = '';
				$width = '500';
				$height = 0;
				$url = parse_url($this->data['url']);
				if( isset($url['host']) ) {
					$size = $this->data['size'];
					$width = $size['width'];
					$height = $size['height'];
					$style = 'width:'.$width.'px;height:'.$height.'px';
					if( $this->data['scale'] == '100%' ) {
						$style = 'width:100%;height:'.$height.'px';
					}
					$embed = '';
					if( strrpos($url['host'],'vimeo.com') !== false ) {
						$videoId = trim($url['path'],'/');
						$embed .= '<div class="cb-video-embed" style="margin:0px auto;'.$style.'"><iframe src="http://player.vimeo.com/video/'.$videoId.'?portrait=0&color=333" width="100%" height="100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>';
					}
					if( strrpos($url['host'],'youtube.com') !== false ) {
						parse_str( $url['query'], $query );
						$videoId = $query['v'];
						unset($query['v']);
						$embed .= '<div class="cb-video-embed" style="margin:0px auto;'.$style.'"><iframe class="youtube-player" type="text/html" width="100%" height="100%" src="http://www.youtube.com/embed/'.$videoId.'?wmode=opaque" frameborder="0"></iframe></div>';
					}
					if( $embed != '' ) {
						$retVal = '<div style="text-align:center;margin-bottom:10px;" class="cb-video '.(strlen($this->data['class'])>0?' class="'.$this->data['class'].'"':'').'">'.$embed.'</div>';
					}
				}
			break;
			case 'rte':
				$content ='';
				if( isset($this->data['content'])) {
					$content = strip_tags($this->data['content']);
				}
				if( strlen($content) ) {
					$class = '';
					if( isset($this->data['class']) ) {
						$class = $this->data['class'];
					}
					$retVal = '<div class="cb-rte'.(strlen($class)>0?' '.$class.'':'').'" >';
					$retVal .= $this->data['content'];
					$retVal .= '</div>';
				}
			break;
			case 'gmap':
				$location = $this->data['location'];
				if( strlen($location) ) {
					$query = array();
					$query['action'] = 'iframe';
					$query['block'] = $this->getData();
					$queryStr = http_build_query( $query );
					$mapUrl = $this->getBuilder()->ajaxUrl.'?'.$queryStr;
					$retVal = '<div style="margin-bottom:10px;" class="cb-gmap'.(strlen($this->data['class'])>0?' '.$this->data['class'].'':'').'"><iframe width="100%" height="'.(isset($this->data['height'])?$this->data['height']:'').'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$mapUrl.'"></iframe></div>';
				}
			break;
			case 'divider':
				$retVal = '<hr/>';
			break;
			case 'gallery':
				$items = $this->data['items'];
				if( count($items) > 0 ) {
					$width = $this->data['itemWidth'];
					$height = $this->data['itemHeight'];
					$itemsInRow = 3;
					$itemsTotal = count($items);
					$gallery = array();
					foreach( $items as $key=>$item ) {
						$thumbSource = $item['source'];
						if( isset($item['store']) ) {
							$thumbSource = $item['store']['source'];
						}
						$thumbUrl = $this->getBuilder()->url($thumbSource);
						$largeSource = $item['source'];
						if( isset($item['large']) ) {
							$largeSource = $item['large']['source'];
						}
						if( isset($item['link']) ) {
							if( strlen($item['link'])>0 ) {
								$largeSource = $item['link'];
							}
						}
						$largeUrl = $this->getBuilder()->url($largeSource);
						//$style = 'width:'.$width.'px;height:'.$height.'px;';
						$style = 'max-width:100%;width:100%;height:auto;display:block;';
						$image = '<img src="'.$thumbUrl.'" style="'.$style.'" alt="'.(isset($item['alt'])?$item['alt']:'').'" />';
						if( strlen($largeUrl) > 0 ) {
							$isLast = ($key%$itemsInRow==2||$key+1==$itemsTotal);
							$image = '<a href="'.$largeUrl.'" rel="cb-gallery-'.$this->id.'" class="thickbox cb-gallery-item '.($isLast?' end':'').'" style="width:32%;" title="'.(isset($item['alt'])?$item['alt']:'').'">'.$image.'</a>';						}
						$gallery[] = $image;
					}
					$column = 3;
					//pa($gallery);
					$rows = ceil( count($gallery)/3 );
					$retVal = '<div class="cb-gallery cb-clearfix">';
					for( $i=0; $i<$rows; $i++ ) {
						$rowItems = array_slice($gallery,$i*3, 3 );
						$retVal .= '<div class="row-fluid" style="margin-bottom:10px;">';
						//style="margin-left:-1px;"
						$retVal .= '<div class="span4">'.implode('</div><div class="span4">',$rowItems).'</div>';
						$retVal .= '</div>';
					}
					$retVal .= '</div>';
				}
			break;
		}
		return( $retVal );
	}

	/**
	 * Get widget in string
	 */
	public	function toString() {
		$retVal = '';
		switch( $this->name ) {
			case 'rte':
				if( isset($this->data['content'])) {
					$retVal = $this->data['content'];
				}
			break;
			case 'heading':
					$retVal = $this->html();
			break;
			case 'image':
				if( isset($this->data['source'])) {
					$retVal = '<p>'.$this->getBuilder()->url($this->data['source']).'</p>';
				}
			break;
			case 'video':
				if( isset($this->data['url'])) {
					$retVal = '<p>'.$this->getBuilder()->url($this->data['url']).'</p>';
				}
			break;
			case 'gmap':
				if( isset($this->data['location'])) {
					$location = $this->data['location'];
					if( strlen($location) ) {
						$retVal = '<p>'.$location.'</p>';
					}
				}
			break;
			case 'gallery':
				$items = $this->data['items'];
				if( count($items) > 0 ) {
					$tmp = array();
					foreach( $items as $key=>$item ) {
						if( isset($item['source'])){
							$tmp[] = $this->getBuilder()->url($item['source']);
						}
					}
					$retVal .= '<p>'.implode(', ', $tmp ).'</p>';
				}
			break;
		}
		return $retVal;
	}

	/**
	 * Return widget editing options
	 * @return array
	 */
	public 	function config() {
		$retVal = array();
		$retVal['label'] = $this->label;
		$retVal['name'] = $this->name;
		switch( $this->name ) {
			case 'divider':
				$retVal['editor'] = 'ContentBlockDivider';
			break;
			case 'gallery':
				$retVal['editor'] = 'ContentBlockGallery';
				$retVal['form'] = ''.
					'<fieldset>'.
						'<label>Address:</label>'.
						'<p><input type="text" name="source" class="text" /><a style="font-weight:bold;" href="#browse" data-act="browse">change</a></p>'.
					'</fieldset>'.
					'<fieldset class="optional">'.
						'<label>Alt:</label>'.
						'<p><input type="text" name="alt" class="text" /></p>'.
					'</fieldset>'.
					'<fieldset class="optional">'.
						'<label>Link:</label>'.
						'<p><input type="text" name="link" class="text" /></p>'.
					'</fieldset>';
			break;
			case 'rte':
				$retVal['editor'] = 'ContentBlockRedactor';
				$retVal['form'] = ''.
					'<fieldset class="optional" >'.
					'<label>Class:</label>'.
					'<p><input type="text" name="class" class="text" /></p>'.
					'</fieldset>';
			break;
			case 'video':
				$retVal['editor'] = 'ContentBlockVideo';
				$retVal['form'] = ''.
					'<fieldset>'.
					'<label>Address:</label>'.
					'<p><input type="text" name="url" class="text" /> (youtube.com , vimeo.com)</p>'.
					'</fieldset>'.
					'<fieldset>'.
						'<label>Scale:</label>'.
						'<p style="float:left;">'.
							'<select name="scale" class="cb-choice">'.
							'<option value="default">500px</option>'.
							'<option value="100%">Fit</option>'.
							'<option value="75%">75%</option>'.
							'<option value="50%">50%</option>'.
							'<option value="25%">25%</option>'.
						'</select></p>'.
						'<label style="width:auto;margin-left:20px;margin-right:5px;">Ratio:</label>'.
						'<p style="float:left;"><select name="ratio" class="cb-choice">'.
							'<option value="16:9">16:9</option>'.
							'<option value="4:3">4:3</option>'.
						'</select></p>'.
					'</fieldset>'.
					'<fieldset class="optional" >'.
						'<label>Class:</label>'.
						'<p><input type="text" name="class" class="text" /></p>'.
					'</fieldset>';
			break;
			case 'image':
				$retVal['editor'] = 'ContentBlockImage';
				$retVal['form'] = ''.
					'<fieldset>'.
						'<label>Address:</label>'.
						'<p><input type="text" name="source" class="text" /><a style="font-weight:bold;" href="#browse" data-act="browse">change</a></p>'.
					'</fieldset>'.
					'<fieldset>'.
						'<label>Scale:</label>'.
						'<p><select name="scale" class="cb-choice">'.
							'<option value="100%">Fit</option>'.
							'<option value="75%">75%</option>'.
							'<option value="50%">50%</option>'.
							'<option value="25%">25%</option>'.
							'<option value="none">Original</option>'.
						'</select></p>'.
					'</fieldset>'.
					'<fieldset class="optional">'.
						'<label>Alt:</label>'.
						'<p><input type="text" name="alt" class="text" /></p>'.
					'</fieldset>'.
					'<fieldset class="optional">'.
						'<label>Link:</label>'.
						'<p><input type="text" name="link" class="text"/><input type="checkbox" name="target" value="_blank" style="float:left;margin-right:5px;margin-top:6px;"/><span style="float:left;line-height:25px;">new window</span></p>'.
					'</fieldset>'.
					'<fieldset class="optional" >'.
						'<label>Class:</label>'.
						'<p><input type="text" name="class" class="text" /></p>'.
					'</fieldset>';
			break;
			case 'heading':
				$retVal['editor'] = 'ContentBlockHeading';
				$retVal['form'] = ''.
					'<fieldset>'.
						'<label>Heading:</label>'.
						'<p><select name="heading" class="cb-choice">'.
							//'<option value="h1">H1</option>'.
							'<option value="h2">H2</option>'.
							'<option value="h3">H3</option>'.
							'<option value="h4">H4</option>'.
							'<option value="h5">H5</option>'.
							'<option value="h6">H6</option>'.
						'</select></p>'.
					'</fieldset>'.
					'<fieldset class="optional">'.
						'<label>Link:</label>'.
						'<p><input type="text" name="link" class="text"/><input type="checkbox" name="target" value="_blank" style="float:left;margin-right:5px;margin-top:6px;"/><span style="float:left;line-height:25px;">new window</span></p>'.
					'</fieldset>'.
					'<fieldset class="optional">'.
						'<label>Title:</label>'.
						'<p><input type="text" name="title" class="text" /></p>'.
					'</fieldset>'.
					'<fieldset class="optional" >'.
						'<label>Class:</label>'.
						'<p><input type="text" name="class" class="text" /></p>'.
					'</fieldset>';
			break;
			case 'gmap':
				$retVal['editor'] = 'ContentBlockGoogleMap';
				$retVal['form'] = ''.
					'<fieldset>'.
						'<label>Location:</label>'.
						'<p><input type="text" name="location" class="text" /><a data-act="locate" style="font-weight:bold;" href="#https://maps.google.com/">locate address</a></p>'.
					'</fieldset>'.
					'<fieldset>'.
						'<label>Height:</label>'.
						'<p><input type="text" name="height" class="text" value="300" /></p>'.
					'</fieldset>'.
					'<fieldset class="optional" >'.
						'<label>Class:</label>'.
						'<p><input type="text" name="class" class="text" /></p>'.
					'</fieldset>';
			break;
		}
		return( $retVal );
	}

}

?>