<?php

/**
 * Image modifier class
 * Supports image resizing using various modes.
 */
class ImageUtil {
	
	/**
	 * No resizing is dones
	 */
	const RESIZE_NONE		= 0;

	/**
	 * Scale image in given size
	 */
	const RESIZE_FIT		= 1;

	/**
	 * Strech image without propotionm keeping.
	 */
	const RESIZE_STRETCH	= 2;
	
	/**
	 * Target resource
	 *
	 * @var 	resource
	 */
	private $target			= null;
	
	/**
	 * Target width
	 *
	 * @var 	int
	 */
	private $targetWidth	= null;
	
	/**
	 * Target height
	 *
	 * @var 	int
	 */
	private $targetHeight	= null;
	
	/**
	 * Source path
	 *
	 * @var 	string
	 */
	private $sourceFile		= false;
		
	/**
	 * Source width
	 *
	 * @var int
	 */
	private $sourceWidth	= false;

	/**
	 * Source height
	 *
	 * @var int
	 */
	private $sourceHeight	= false;
	
	/**
	 * Source type
	 *
	 * @var int
	 */
	private $sourceType		= false;
	
	/**
	 * Source data, source create, save settings
	 *
	 * @var 	array
	 */
	private $sourceSettings	= array();
	
	/**
	 * Alpha setting for png
	 * 
	 * @var
	 */
	private $alpha = false;
	
	/**
	 * Image types, some data by image type
	 *
	 * @var array
	 */
	private $imageSettings = array(

		IMAGETYPE_GIF 	=> array(
			'extension'	=> 'gif',					
			'gd_create_function'	=> 'imagecreatefromgif',
			'gd_save_function'		=> 'imagegif'			
		),


		IMAGETYPE_JPEG 	=> array(
			'extension' => 'jpg',			
			'gd_create_function'	=> 'imagecreatefromjpeg',
			'gd_save_function'		=> 'imagejpeg'			
		),

		IMAGETYPE_PNG 	=> array(
			'extension' => 'png',			
			'gd_create_function'	=> 'imagecreatefrompng',
			'gd_save_function'		=> 'imagepng'			
		)
		
	);
	
	/**
	 * Load image util
	 *
	 * @param 	string $sourceFile
	 */
	public function __construct( $sourceFile = null, $alpha = false ) {
				
		if( !is_null($sourceFile) ) {
			$this->loadSource( $sourceFile );
		}		
		$this->alpha = $alpha;
				
	}
	
	/**
	 * Load source image file for modifying (unloads previous source file)	
	 *
	 * @return bool success?
	 */
	public function loadSource( $sourceFile ) {
		
		$retVal = false;
		
		// unload old image source
		if ( $this->isSourceLoaded() ) {
			$this->unloadSource();
		}
		
		$imageData = @getimagesize( $sourceFile );		
		if ( is_file( $sourceFile ) && is_readable( $sourceFile ) && isset( $this->imageSettings[$imageData[2]] ) ) {
			
			$this->sourceFile		= $sourceFile;
			$this->sourceWidth 		= $imageData[0];
			$this->sourceHeight 	= $imageData[1];
			$this->sourceType 		= $imageData[2];
			$this->sourceSettings	= $this->imageSettings[$imageData[2]];
			
			$retVal = true;
						
		} else {		
			throw new prox_error_Exception( "Can't read:".$sourceFile.", or wrong file format only (jpg,gif,png).");		
		}
				
		return( $retVal );
				
	}
	
	/**
	 * Unload loaded file, free all allocated resources.
	 *
	 * @return bool success?
	 */
	public function unloadSource() {

		$retVal = false;
		
		if ( $this->isSourceLoaded() ) {
						
			$this->sourceWidth 		= false;
			$this->sourceHeight 	= false;
			$this->sourceType 		= false;
			$this->sourceSettings 	= false;
			$this->sourceFile 		= false;						
			$this->unloadTarget();
			$retVal = true;
		}
				
		return $retVal;

	}
	
	/**
	 * Unload target, but leave source
	 *
	 * @return 	bool
	 */
	public function unloadTarget() {
		$retVal = false;
		if ( is_resource( $this->target ) ) {
			imagedestroy( $this->target );
			$this->target			= null;
			$this->targetWidth		= false;
			$this->targetHeight		= false;
			$retVal = true;
		}
		return( $retVal );
	}
	
	/**
	 * Is file loaded?
	 *
	 * @return bool is loaded?
	 */
	public function isSourceLoaded() {
		return $this->sourceFile !== false;
	}
	
	/**
	 * Calculate image width and height by resize mode
	 *
	 * @param 	int $targetWidth
	 * @param 	int $targetHeight
	 * @param 	int $sourceWidth
	 * @param 	int $sourceHeight
	 * @param 	string $resizeMode
	 * @return 	array (width, height)
	 */
	private function calcucaleSize( $targetWidth = null, $targetHeight = null, $sourceWidth, $sourceHeight, $resizeMode = ImageUtil::RESIZE_FIT ) {
			
		// Calculate multipliers
		switch ( $resizeMode ) {
			case ImageUtil::RESIZE_FIT:
				$ratioX = $targetWidth/$sourceWidth;
				$ratioY = $targetHeight/$sourceHeight;
				if( is_null($targetWidth) ) { $ratioX = $ratioY; }
				if( is_null($targetHeight) ) { $ratioY = $ratioX; }
				$ratio 	= min( 1, min( $ratioX, $ratioY ) );
				$width 	= $sourceWidth * $ratio;
				$height = $sourceHeight * $ratio; 				
			break;
			case ImageUtil::RESIZE_STRETCH:
				//$ratioX = min( 1, $targetWidth / $sourceWidth );
				//$ratioY = min( 1, $targetHeight / $sourceHeight );
				$ratioX = ( $targetWidth / $sourceWidth );
				$ratioY = ( $targetHeight / $sourceHeight );
				$width 	= $sourceWidth * $ratioX;
				$height = $sourceHeight * $ratioY;
			break;			
			case ImageUtil::RESIZE_NONE:
				$width 	= $sourceWidth;
				$height = $sourceHeight;
			break;
		}
		
		// Create return value
		$retVal = array(
			'width' 	=> round( $width ),
			'height' 	=> round( $height ),
		);
				
		return( $retVal );
		
	}
	
	/**
	 * Get image source
	 *
	 * @return 	array
	 */
	public function getSourceData() {
		$retVal = array();		
		if( is_null($this->target) ) {
			$retVal['source'] = @$this->sourceSettings['gd_create_function']($this->sourceFile);
			$retVal['width'] = $this->sourceWidth;
			$retVal['height'] = $this->sourceHeight; 	
		} else {
			$retVal['source'] = $this->target;
			$retVal['width'] = $this->targetWidth;
			$retVal['height'] = $this->targetHeight;
		}

		if( !is_resource( $retVal['source']) ) {
			throw new prox_error_Exception( 'No resource file, need to load source' );
		}
						
		return( $retVal );
	}
	
    /**
     * Resize thumb, fit to largest dimension & crop 50% 50% 
     */
    public  function resizeThumb( $width, $height ) {
       
        $sourceData = $this->getSourceData();				
		$source = $sourceData['source'];
		$sourceWidth = $sourceData['width'];
		$sourceHeight = $sourceData['height'];

		$scaleX = $width / $sourceWidth;
		$scaleY = $height / $sourceHeight;
		$scale = max( $scaleX, $scaleY );
				
        $this->resizeTarget( $sourceWidth*$scale, $sourceHeight*$scale, ImageUtil::RESIZE_FIT );
        $this->cropTarget('50%','50%', $width, $height );
                
    }
    
	/**
	 * Resize target
	 *
	 * @param 	int 	$width
	 * @param 	int 	$height
	 * @param 	string 	$resizeMode
	 */
	public function resizeTarget( $width = null, $height = null, $resizeMode = ImageUtil::RESIZE_FIT ) {
		
		$sourceData = $this->getSourceData();				
		$source = $sourceData['source'];
		$sourceWidth = $sourceData['width'];
		$sourceHeight = $sourceData['height'];
		
		$size = $this->calcucaleSize( $width, $height, $sourceWidth, $sourceHeight, $resizeMode );		
		$target = @imagecreatetruecolor( $size['width'], $size['height'] );
		if( $this->alpha ) {
			$this->setAlphaChannel( $target );	
		}		
		@imagecopyresampled( $target, $source, 0, 0, 0, 0, $size['width'], $size['height'], $sourceWidth, $sourceHeight );
		
		if ( is_resource( $source ) ) {
			imagedestroy( $source );
		}
		
		$this->target = $target;
		$this->targetWidth = $size['width'];
		$this->targetHeight = $size['height'];
		
	}
	
	/**
	 * Image crop
	 *
	 * @param 	string $left - could use percentage coord
	 * @param 	string $top - could use percentage coord
	 * @param 	int $width
	 * @param 	int $height
	 */
	public function cropTarget( $left=0, $top=0, $width=100, $height=100 ) {
		
		$sourceData = $this->getSourceData();				
		$source = $sourceData['source'];
		$sourceWidth = $sourceData['width'];
		$sourceHeight = $sourceData['height'];
		
		//percent mode
		if( strrpos($left,'%') > 0 ) {
			$left = round( ($sourceWidth * intval($left) ) / 100 - $width/2 );	
		}
				
		//percent mode
		if( strrpos($top,'%') > 0 ) {
			$top = round( ($sourceHeight * intval($top) ) / 100 - $height/2 );
		}
				
		$crop = @imagecreatetruecolor( $width, $height );
		if( $this->alpha ) {
			$this->setAlphaChannel( $crop );	
		}
		@imagecopy ( $crop, $source, 0, 0, $left, $top, $width, $height );		
		
		if ( is_resource( $source ) ) {
			imagedestroy( $source );
		}
		
		$this->target = $crop;
		$this->targetWidth = $width;
		$this->targetHeight = $height;
		
	}
	
	/**
	 * PNG ALPHA CHANNEL SUPPORT for imagecopymerge();
	 * by Sina Salek
	 *
	 * Bugfix by Ralph Voigt (bug which causes it
	 * to work only for $src_x = $src_y = 0.
	 * Also, inverting opacity is not necessary.)
	 * 08-JAN-2011
	 *
	 */
    public function mergeImage( $image, $top, $left, $width, $height, $pct = 100 ){
    	
		$target = @imagecreatetruecolor( $this->getWidth(), $this->getHeight() );		
		$merge = new ImageUtil( $image );
				
		// copying relevant section from background to the cut resource
        imagecopy($target, $this->getSource(), 0, 0, 0, 0, $this->getWidth(), $this->getHeight() );
       	// copying relevant section from watermark to the cut resource
        imagecopy($target, $merge->getSource(), 0, 0, $left, $top, $width, $height);
		 // insert cut resource to destination image
        imagecopymerge($this->getSource(), $target, 0, 0, 0, 0, $width, $height, $pct);
		$this->unloadTarget();
		$this->target = $target;
		$this->targetWidth = $this->getWidth();
		$this->targetHeight = $this->getHeight();
				
    }
	
	/**
	 * Set alpha channel
	 * @param object $target
	 * @return 
	 */
	private function setAlphaChannel( $target ) {		
		imagecolortransparent($target, imagecolorallocate($target, 0, 0, 0));
		imagealphablending($target, false);			
	}
	
	/**
	 * Save target 
	 *
	 * @param 	string 	$targetFile
	 * @param 	int 	$quality - image optimize quality
	 */
	public function saveTarget( $targetFile, $quality = 100 ) {
		
		$targetData = pathinfo( strtolower($targetFile) );				
		if( !is_dir($targetData['dirname']) ) {
			mkdir( $targetData['dirname'], 0777, true );
		}		
		switch( $targetData['extension'] ) {
			case 'png':		
				if( $this->alpha ) {
					imagesavealpha( $this->target, true );	
				}					
				@$this->imageSettings[ IMAGETYPE_PNG ]['gd_save_function']( $this->target, $targetFile );
			break;
			default:
				@$this->imageSettings[ IMAGETYPE_JPEG ]['gd_save_function']( $this->target, $targetFile, $quality );
			break;
		}
				
	}
	
	/**
	 * Get source width
	 * @return 
	 */
	public function getWidth() {
		$data = $this->getSourceData();
		return( $data['width'] );
	}
	
	/**
	 * Get source height
	 * @return 
	 */
	public function getHeight() {
		$data = $this->getSourceData();
		return( $data['height'] );
	}
	
	/**
	 * Get source
	 * @return 
	 */
	public function getSource() {
		$data = $this->getSourceData();
		return( $data['source'] );
	}
		
}

?>