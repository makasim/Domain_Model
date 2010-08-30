<?php

/**
 * 
 * @author maksim
 *
 */
interface Domain_Gateway_Data_Collection_Interface extends ArrayAccess, IteratorAggregate, Countable
{
	/**
   * 
   * Check is the dataset can be converted to model object or not.
   * @return bool
   */
  public function isConvertable();
  
  public function toModel();
  
  /**
   * 
   * @return array
   */
  public function toArray();
  
  /**
   * 
   * Enter description here...
   * @param $class iterator class if null must be set default iterator class.
   * @return void
   */
  public function setIteratorClass($class = null);
  
  /**
   * 
   * Enter description here...
   * @param string
   * @return void
   */
  public function setModelClass($className);
}