<?php

/**
 * 
 * @author maksim
 *
 */
class Domain_Gateway_Select_Helper_Abstract extends Domain_Gateway_Helper_Abstract
{
	/**
	 * 
	 * Enter description here...
	 * @param Domain_Gateway_Select $gateway
	 * @return void
	 */
	public function __construct(Domain_Gateway_Select $gateway)
	{
		return parent::__construct($gateway);
	}
}