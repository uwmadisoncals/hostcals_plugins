<?php

/**
 * Basic Block
 * @version $id$
 */
class BasicBlock {

	/**
	 * Block name
	 * @var
	 */
	public	$name = null;

	/**
	 * Block id
	 * @var 	int
	 */
	public	$id	  = null;

	/**
	 * Label
	 * @var
	 */
	public	$label = null;

	/**
	 * Parent object
	 * @var 	LayoutBlock
	 */
	public 	$parent = null;

	/**
	 * ContentBuilder
	 * @var 	ContentBuilder
	 */
	public	$builder = null;

	/**
	 * Block cached data
	 * @var
	 */
	public	$data	= array();

	/**
	 * Constructor
	 */
	public function  BasicBlock( $name, $label = '' ) {
		$this->name = $name;
		$this->label = $label;
	}

	/**
	 * Initialized
	 * @return
	 */
	public	function init() {
	}

	/**
	 * Ready event, called after data is setted
	 * @return
	 */
	public function ready() {
	}

	/**
	 * Execute custom action
	 * @param $action
	 * @param array $params
	 */
	public function execute( $action, $params = array() ) {
	}

	/**
	 * Store event
	 */
	public function store() {
	}

	/**
	 * Set data
	 * @param $data
	 */
	public function setData( $data ) {
		$this->data = $data;
	}

	/**
	 * Get data
	 * @param $data
	 */
	public function getData() {
		return( $this->data );
	}

	/**
	 * Get builder
	 * @return ContentBuilder
	 */
	public function getBuilder() {
		if( $this->name == 'root' ) {
			return( $this->builder );
		}
		return( $this->parent->getBuilder() );
	}

	/**
	 * Get object root
	 * @return	LayoutBlock
	 */
	public function getRoot() {
		if( $this->name == 'root' ) {
			return( $this );
		}
		return( $this->parent->getRoot() );
	}

	/**
	 * Render widget html
	 * @return string
	 */
	public  function html() {
		return( '' );
	}

	/**
	 * Get widget in string
	 */
	public	function toString() {
		$retVal = '';
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
		return( $retVal );
	}

}

?>