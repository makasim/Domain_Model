<?php

/**
 * 
 * @author maksim
 *
 */
class Domain_Gateway_Helper_ThrowIfNotSingle extends Domain_Gateway_Helper_Abstract
{
	/**
	 * 
	 * Enter description here...
	 * @throws Domain_Gateway_Exception if gateway is not present a single data.
	 * @return unknown_type
	 */
	public function throwIfNotSingle()
	{
		$quantity = count($this->_gateway);
		if ($quantity !== 1) {
			throw new Domain_Gateway_Exception('Gateway `' . get_class($this->_gateway) . '` is not present a single data. Now present ' . $quantity);
		}
	}
}