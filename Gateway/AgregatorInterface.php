<?php

/**
 * 
 * @author maksim
 *
 */
interface Domain_Gateway_AgregatorInterface
{
	/**
	 * 
	 * Enter description here...
	 * @return array
	 */
	public static function getGatewayParameters();
	
	/**
	 * 
	 * Enter description here...
	 * @return string
	 */
	public static function getGatewayClass();
}