<?php

require_once( dirname(__FILE__).'/WidgetBlock.php' );
require_once( dirname(__FILE__).'/LayoutBlock.php' );

/**
 * Content Builder
 * @version $id$
 */
class ContentBuilder {

	/**
	 * Content Builder Version
	 * @var string
	 */
	public	$version = '1.0.4';

	/**
	 * Next block id
	 * @var int
	 */
	public 	$nextId	 = 1;

	/**
	 * Builder width
	 * @var int
	 */
	public	$width	= 600;

	/**
	 * Block by type
	 * @var
	 */
	public	$blocks = array();

	/**
	 * Created Block by ID
	 * @var array
	 */
	private $blockById = array();

	/**
	 * Content Builder Base path
	 * @var
	 */
	public 	$basePath = 'cbuilder';

	/**
	 * Ajax url
	 * @var
	 */
	public	$ajaxUrl = '';

	/**
	 * Trails link
	 * @var array
	 */
	public $trails 	= array();

	/**
	 * Root
	 * @var ContentBlock
	 */
	private	$root = null;

	/**
	 * Initialize
	 */
	public function create() {


		$block = new WidgetBlock('heading','Heading');
		$this->addBlock( $block );

		$block = new WidgetBlock('image','Image');
		$this->addBlock( $block );

		$block = new WidgetBlock('video','Video');
		$this->addBlock( $block );

		$block = new WidgetBlock('rte','Rich Text Editor');
		$this->addBlock( $block );

		$block = new LayoutBlock('layout','Layout');
		$this->addBlock( $block );

		$block = new LayoutBlock('tab','Tab block');
		$this->addBlock( $block );

		$block = new WidgetBlock('divider','Divider');
		$this->addBlock( $block );

		$block = new WidgetBlock('gallery','Gallery');
		$this->addBlock( $block );

		$block = new WidgetBlock('gmap','Google Map');
		$this->addBlock( $block );

	}

	public function toolbar() {
		$toolbar = '<div class="cb-toolbar-wrap"><div id="ContentBuilder-Toolbar" class="cb-toolbar cb-clearfix"><div class="cb-center"><div class="cb-toolbar-dropdown"><span>Add elements<em></em></span><ul><li class="first">Add elements<em></em></li></ul></div><a href="http://www.contentbuilder.net" onclick="window.open(this.href);return false;" class="cb-toolbar-logo"><img src="'.$this->basePath.'assets/images/toolbar-logo.png" style="width:137px;height:43px;"/></a></div><div class="cb-toolbar-locker"></div></div></div>';
		return( $toolbar );
	}

	/**
	 * Add block
	 * @param BasicBlock $block
	 */
	public function addBlock( WidgetBlock $block ) {
		$this->blocks[ $block->name ] = $block;
	}

	/**
	 * Get field
	 * @param $name
	 * @return string
	 */
	public function getField( $name ) {

		$blocks = array();
		foreach( $this->blocks as $oBlock ) {
			$oBlock = unserialize( serialize( $oBlock ) );
			$cfg = $oBlock->config();
			$blocks[ $oBlock->name ] = $cfg;
		}

		$builder = array();
		$builder['basePath'] = $this->basePath;
		$builder['blocks'] = $blocks;
		$builder['version'] = $this->version;

		//$smartbox['editors'] = $editors;
		$config = array();

		$script = '';
		$script = '<script type="text/javascript">';
		$script .= " jQuery(document).ready(function(){ ContentBuilder.init(".json_encode($builder)."); ContentBuilder.replace('".$name."', ".json_encode($config)."); }); ";
		$script .= '</script>';
		return( $script );

	}

	/**
	 * Load block from data
	 * @param array $block
	 * @return BasicBlock
	 */
	public function loadBlock( $block = array() ) {
		$root = array(
			'block'=>'root',
			'version'=>$this->version,
			'nextId'=>'1',
			'id'=>'1',
			'layout'=>'a',
			'childs'=>array()
		);
		$block['id'] = '2';
		array_push($root['childs'],$block);
		$this->root = $this->loadData( $root );
		return( $this->getBlockById($block['id']));
	}

	/**
	 * Load data
	 * @param null $data
	 * @return LayoutBlock|null
	 */
	public function loadData( $data = null ) {
		$oRoot = null;
		if( !is_null($data) && is_string($data) ) {
			$data = json_decode( $data, true );
		}
		if( is_array($data) && isset($data['block']) ) {
			$oRoot = $this->createBlock( null, $data );
			$this->root = $oRoot;
		}
		return( $oRoot );
	}

	/**
	 * 	Dispatch store event for builder blocks
	 */
	public function store() {
		foreach( $this->blockById as $id=>$oBlock ) {
			if( $oBlock instanceof BasicBlock ) {
				$oBlock->store();
			}
		}
	}

	/**
	 * Get builder data
	 * @param bool $json
	 * @return string|void
	 */
	public  function data( $json = false ){
		$retVal = '';
		if( !is_null($this->root) ) {
			$retVal = $this->root->getData();
			if( $json ) {
				$retVal = json_encode($retVal);
			}
		}
		return( $retVal );
	}

	/**
	 * Get builder html
	 * @return string
	 */
	public function html() {
		$retVal = '';
		if(!is_null($this->root) ) {
			$retVal = '<div class="content-builder2">'.$this->root->html().'</div>';
		}
		return( $retVal );
	}

	/**
	 * Get all blocks text content
	 */
	public function toString() {
		$retVal = '';
		foreach( $this->blockById as $id=>$oBlock ) {
			if( !($oBlock instanceof LayoutBlock) ) {
				$retVal .= $oBlock->toString();
			}
		}
		return( $retVal );
	}

	/**
	 * Create block with data
	 * @param 	BasicBlock $parent
	 * @param 	array $data
	 * @return 	BasicBlock
	 */
	public function createBlock( BasicBlock $parent = null, $data = array(), $autoInit = true ) {
		if( $data['block'] == 'devider' ) {
			$data['block'] = 'divider';
		}
		if( $data['block'] == 'root' ) {
			$oBlock = clone $this->blocks[ 'layout' ];
			$oBlock->name = 'root';
			$oBlock->builder = $this;
		} else {
			$oBlock = clone $this->blocks[ $data['block'] ];
		}
		if( !isset($data['id']) ) {
			$data['id'] = $this->nextId;
		}
		$this->nextId = max((intval($data['id'])+1),$this->nextId);
		$oBlock->id = intval($data['id']);
		//echo "<hr/>".$oBlock->id.'<hr/>';
		if( $autoInit ) {
			$oBlock->parent = $parent;
			$oBlock->init();
			$oBlock->setData( $data );
			$oBlock->ready();
		}
		if( isset($data['id']) ) {
			$this->blockById[ $data['id'] ] = $oBlock;
		}
		return( $oBlock );
	}

	/**
	 * Create Blocks
	 * @param BasicBlock|null $parent
	 * @param array $childs
	 */
	public function createBlocks( BasicBlock $parent = null, $childs = array() ) {
		$c = array();
		foreach( $childs as $data ) {
			$oBlock = $this->createBlock( $parent, $data );
			if(!is_null($oBlock)) {
				$c[] = $oBlock;
			}
		}
		return( $c );
	}

	/**
	 * Get block by id
	 * @param $id
	 * @return 	BasicBlock
	 */
	public function getBlockById( $id ) {
		$retVal = isset( $this->blockById[ $id ] )?$this->blockById[ $id ]:null;
		return( $retVal );
	}

	/**
	 * Get block data
	 * @param BasicBlock $oBlock
	 */
	public function getBlockData( BasicBlock $oBlock ) {
		$data = array();
		$data['id'] = $oBlock->id;
		$data = array_merge($data, $oBlock->getData() );
		//$data['id'] = $oBlock->id;
		return( $data );
	}

	/**
	 * Get blocks list data
	 * @param array 	$blocks container of BasicBlock
	 * @return array	array
	 */
	public function getBlocksData( $blocks = array() ) {
		$c = array();
		foreach( $blocks as $oBlock ) {
			if( $oBlock instanceof BasicBlock ) {
				$c[] = $this->getBlockData( $oBlock );
			}
		}
		return( $c );
	}

	/**
	 * Add trail
	 * @param $key
	 * @param $url
	 * @param $path
	 */
	public function addTrail( $key, $url, $path ) {
		if( !isset($this->trails['byPath']) ) {
			$this->trails['byPath'] = array();
		}
		if( !isset($this->trails['byUrl']) ) {
			$this->trails['byUrl'] = array();
		}
		$url = rtrim($url,'/');
		$path = rtrim($path,'/');
		$this->trails[ $key ] = array(
			'url'=>$url,
			'path'=>$path,
			'key'=>$key,
		);
		$this->trails['byPath'][ $key ] = $path;
		$this->trails['byUrl'][ $key ] = $url;
	}

	/**
	 * Get trail
	 * @param $key
	 * @return mixed
	 */
	public  function getTrail( $key ) {
		return( $this->trails[$key] );
	}

	/**
	 * Get file base url
	 * @param $file
	 * @return string
	 */
	public function base( $file ) {
		$retVal = ltrim($file,'/');
		$trails = (isset($this->trails['byUrl'])?$this->trails['byUrl']:array());
		$regexp = '^('.addcslashes(implode('|',$trails),'/').')\/(.*)';
		if( preg_match('/'.$regexp.'/', $file, $match ) > 0 ) {
			$key = array_search($match[1],$trails);
			$retVal = '~'.$key.'/'.$match[2];
		}
		return( $retVal );
	}

	/**
	 * Get file or directory url
	 * @param $file
	 * @return string
	 */
	public function url( $file ) {
		$file = ltrim($file,'/');
		$retVal = $file;
		$trails = (isset($this->trails['byUrl'])?array_keys($this->trails['byUrl']):array());
		if( preg_match('/^~('.implode('|',$trails).')\/(.*)/', $file, $match ) > 0 ) {
			$retVal = $this->trails[ $match[1] ]['url'].'/'.$match[2];
		}
		return( $retVal );
	}

	/**
	 * Get file or directory path
	 * @param $file
	 * @return string
	 */
	public function path( $file ) {
		$file = ltrim($file,'/');
		$retVal = $file;
		$trails = (isset($this->trails['byPath'])?array_keys($this->trails['byPath']):array());
		if( preg_match('/^~('.implode('|',$trails).')\/(.*)/', $file, $match ) > 0 ) {
			$retVal = $this->trails[ $match[1] ]['path'].'/'.$match[2];
		}
		return( $retVal );
	}

}

?>