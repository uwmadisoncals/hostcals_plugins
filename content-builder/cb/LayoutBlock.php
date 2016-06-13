<?php

require_once( dirname(__FILE__).'/WidgetBlock.php' );

/**
 * Layout Block
 * @version $id$
 */
class LayoutBlock extends WidgetBlock {

	/**
	 * Childs container
	 * @var array
	 */
	public	$childs	= array();

	/**
	 * All columns blocks
	 * @var array
	 */
	public	$blocks	= array();

	/**
	 * Layout type
	 * @var array
	 */
	public	$layout = 'a';

	/**
	 * Tabs container
	 * @var array
	 */
	public	$tabs	= array();

	/**
	 * Set data
	 * @param object $data
	 * @return
	 */
	public function setData( $data ) {
		parent::setData( $data );
		if( isset($data['childs']) ) {
			$this->layout = $data['layout'];
			if( $this->layout == 'a') {
				$this->layout = '12';
			}
			$this->createBlocks( $data['childs'] );
		}
		if( isset($data['tabs']) ) {
			foreach( $data['tabs'] as $key=>$tabData ) {
				$blocks = $this->getBuilder()->createBlocks( $this, $tabData['childs'] );
				$this->blocks = array_merge( $this->blocks, $blocks );
				$tabData['childs'] = $blocks;
				$this->tabs[ $key ] = $tabData;
			}
		}
	}

	/**
	 * Create Blocks
	 */
	private function createBlocks( $childs = array() ) {

		$this->blocks = array();
		$this->childs = array();

		$columns = explode('-', $this->layout );
		if( count($columns) == 1 ) {
			$blocks = $this->getBuilder()->createBlocks( $this, $childs );
			$this->childs[0] = $blocks;
			$this->blocks = $blocks;
		}
		if( count($columns) > 1 ) {
			foreach( $columns as $key=>$colName ) {
				$blocks = $childs[$key];
				$blocks = $this->getBuilder()->createBlocks( $this, $blocks );
				$this->childs[ $key ] = $blocks;
				$this->blocks = array_merge( $this->blocks, $blocks );
			}
		}

	}

	/**
	 * Get blocks html
	 * @param array $blocks
	 * @return string
	 */
	private function getBlocksHtml( $blocks = array() ) {
		$retVal = '';
		foreach( $blocks as $oBlock ) {
			$retVal .= $oBlock->html();
		}
		return( $retVal );
	}

	/**
	 * Get data
	 * @return
	 */
	public function getData() {
		$data = array();
		if( $this->name == 'root' ) {
			$data['id'] = $this->id;
			$data['version'] = $this->data['version'];
			$data['nextId'] = $this->data['nextId'];
		}
		$data['block'] = $this->name;
		if( $this->name=='root' || $this->name == 'layout' ) {
			$data['layout'] = $this->layout;
			$data['childs'] = array();
			$childs = array();
			foreach( $this->childs as $key=>$blocks ) {
				$childs[] = $this->getBuilder()->getBlocksData( $blocks );
			}
			$columns = explode('-', $this->layout );
			if( count($columns) == 1 && count($childs) == 1 ) {
				$childs = $childs[0];
			}
			$data['childs'] = $childs;
		}
		if( $this->name == 'tab' ) {
			$data['tabs'] = array();
			foreach( $this->tabs as $key=>$tabData ) {
				$c = array();
				$tabData['childs'] = $this->getBuilder()->getBlocksData( $tabData['childs'] );
				$data['tabs'][] = $tabData;
			}
		}
		return( $data );
	}

	/**
	 * Render widget html
	 * @return string
	 */
	public  function html() {
		$retVal = '';
		if( $this->name == 'tab' ) {
			$tabs = $this->tabs;
			if( count($tabs) > 0 ) {
				$tabNav = array();
				$tabPane = array();
				foreach( $tabs as $key=>$tabData ) {
					$isActive = ($key==0);
					$tabNav[] = '<li'.($isActive?' class="active"':'').'><a href="#tab-'.$this->id.'-'.$key.'" data-toggle="tab">'.$tabData['label'].'</a></li>';
					$tabPane[] = '<div class="tab-pane'.($isActive?' active':'').'" id="tab-'.$this->id.'-'.$key.'">'.$this->getBlocksHtml($tabData['childs']).'</div>';;
				}
				$retVal = '<div class="tabbable">';
				$retVal .= '<ul class="nav nav-tabs">'.implode('',$tabNav).'</ul>';
				$retVal .= '<div class="tab-content">'.implode('',$tabPane).'</div>';
				$retVal .= '</div>';
			}
		} else {
			$columns = explode('-', $this->layout );
			$retVal = '<div class="row-fluid">';
			foreach( $this->childs as $colIndex=>$blocks ) {
				$colName = $columns[ $colIndex ];
				$retVal .=	'<div class="span'.$colName.'">'.$this->getBlocksHtml($blocks).'</div>';
			}
			$retVal .= '</div>';
		}
		return( $retVal );
	}

	/**
	 * Return widget editing options
	 * @return array
	 */
	public  function config() {
		$retVal = parent::config();
		$retVal['layout'] = 'a';
		$retVal['editor'] = 'ContentBlockLayout';
		switch( $this->name ) {
			case 'layout':
				$retVal['layout'] = '6-6';
				$retVal['editor'] = 'ContentBlockLayout';
				$retVal['types'] = array('12','6-6','8-4','4-8','4-4-4');
			break;
			case 'tab':
				$retVal['editor'] = 'ContentBlockTab';
				$retVal['form'] = ''.
					'<fieldset>'.
						'<label>Label:</label>'.
						'<p><input type="text" name="label" class="text" /></p>'.
					'</fieldset>';
			break;
		}
		return( $retVal );
	}

}

?>