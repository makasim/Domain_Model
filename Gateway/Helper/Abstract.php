<?php

/**
 * 
 * @author maksim
 *
 */
abstract class Domain_Gateway_Helper_Abstract
{
	/**
	 * 
	 * @var Domain_Gateway_Interface
	 */
	protected $_gateway = null;
	
	/**
	 * 
	 * Enter description here...
	 * @param Domain_Gateway_Interface $gateway
	 * @return unknown_type
	 */
	public function __construct(Domain_Gateway_Interface $gateway)
	{
		$this->_gateway = $gateway;
		
		$this->_init();	
	}
	
	/**
	 * 
	 * Enter description here...
	 * @return void
	 */
	protected function _init(){}
}