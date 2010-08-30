<?php

/**
 * 
 * @author maksim
 *
 */
class Domain_Gateway_Builder
{
	/**
	 * You can use this method in two ways.
	 * 
	 * First:
	 * 	Give name of gateway as fitst parameter and options(if it is needed) as second.
	 * Second:
	 * 	Give className that implement interface Domain_Gateway_AgregatorInterface
	 * 
	 * 
	 * @return Domain_Gateway_Interface
	 */
	public static function factory()
	{
		$args = func_get_args();
		$gatewayClass = 'Domain_Gateway_Null';
		$options = array();
				
	 	if (count($args) == 2) {
			list($gatewayClass, $options) = $args;
		} else if (count($args) == 1 && is_string($args[0]) && class_exists($args[0], true)) {
			$agregatorClass = $args[0];
			$methodParameters = $agregatorClass.'::getGatewayParameters';
			$methodClass = $agregatorClass.'::getGatewayClass';
			if (!is_callable($methodParameters) || !is_callable($methodClass)) {
				throw new Domain_Gateway_Exception('The agregator class must implement `Domain_Gateway_AgregatorInterface`');
			}
			
			$options = call_user_func($methodParameters);
			$gatewayClass = call_user_func($methodClass);
		}
		
		$gateway = new $gatewayClass($options);
		if (!$gateway instanceof Domain_Gateway_Interface) {
			throw new Domain_Gateway_Exception('Criteria class should implements interface `Domain_Gateway_Interface`');
		}
				
		return $gateway;
	}
}