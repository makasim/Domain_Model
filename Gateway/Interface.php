<?php

/**
 * 
 * @author maksim
 *
 */
interface Domain_Gateway_Interface extends Countable
{
	/**
	 *
	 * @param array $options array of options if needed.
	 * @return void
	 */
	public function __construct(array $options = array());
	
	/**
	 * 
	 * @param mixed $criteria
	 * @throws Domain_Gateway_Exception
	 * @return Domain_Gateway_Interface Must return itself.
	 */
	public function setCriteria($criteia);
	
	/**
	 * 
	 * Enter description here...
	 * @return bool
	 */
	public function isInit();
	
	/**
	 * 
	 * @param array $fields array of fields that must be present in result set. If set to NULL all fields will be returned
	 * @todo Think about what should return the method
	 * @return
	 */
	public function get(array $fields = array());
	
	/**
	 * 
	 * Enter description here...
	 * @param array $data 
	 * @return int number of affected items
	 */
	public function update(array $data);
	
	/**
	 * 
	 * Enter description here...
	 * @return int number of affected items
	 */
	public function delete();
	
	/**
	 * @param array $data
	 * @return mixed id of created data
	 */
	public function create(array $data);
}