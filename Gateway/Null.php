<?php

/**
 * 
 * @author maksim
 *
 */
class Domain_Gateway_Null implements Domain_Gateway_Interface
{
	/**
	 * 
	 * (non-PHPdoc)
	 * @see Lib/Domain/Gateway/Domain_Gateway_Interface#__construct($options)
	 */
	public function __construct(array $options = array()) 
	{
		
	}
	
	/**
	 *
	 *(non-PHPdoc)
	 * @see Lib/Domain/Gateway/Domain_Gateway_Interface#setCriteria($criteria)
	 */
	public function setCriteria($criteria) 
	{
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Lib/Domain/Gateway/Domain_Gateway_Interface#isInit()
	 */
	public function isInit()
	{
		return true;
	}
	
	/**
	 * 
	 * (non-PHPdoc)
	 * @see Lib/Domain/Gateway/Domain_Gateway_Interface#get($fields)
	 * 
	 * @return array Always returns an empty array
	 */
	public function get(array $fields = array()) 
	{
		return array();
	}
	
	/**
	 * 
	 * Enter description here...
	 * @return int 0
	 */
	public function count()
	{
		return 0;
	}
	
	/**
	 * 
	 * (non-PHPdoc)
	 * @see Lib/Domain/Gateway/Domain_Gateway_Interface#update($data)
	 */
	public function update(array $data)
	{
		return 0;
	}
	
	/**
	 * 
	 * (non-PHPdoc)
	 * @see Lib/Domain/Gateway/Domain_Gateway_Interface#delete()
	 */
	public function delete()
	{
		return 0; 
	}
	
	/**
	 * 
	 * (non-PHPdoc)
	 * @see Lib/Domain/Gateway/Domain_Gateway_Interface#create($data)
	 */
	public function create(array $data)
	{
		return 0;
	}
}