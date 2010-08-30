<?php

/**
 * 
 * @author maksim
 *
 */
class Domain_Gateway_Select_Data_Collection 
  extends Zend_Db_Table_Rowset_Abstract
  implements Domain_Gateway_Data_Collection_Interface
{
	/**
   * 
   * @var string Contains model name.
   */
  protected $_modelClass = null;
  
  /**
   * 
   * @var Domain_Model_Abstract
   */
  protected $_model = null;
  
  /**
   * 
   * @var string contain errors from isConvertable method.
   */
  protected $_errorMessage = null;
	
	public function current()
	{
		return new Domain_Gateway_Select_Data_Item(parent::current(), $this->_modelClass);
	}
	
	public function getRow()
	{
		throw new Exception('Can not be used in this class');
	}
	
  /**
   * 
   * Enter description here...
   * @param string|Domain_Model_Abstract
   * @return void
   */
  public function setModelClass($className)
  {
    if ($className instanceof Domain_Model_Abstract) {
      $className = get_class($className);
    }
    
    $this->_modelClass = $className;
  }
}