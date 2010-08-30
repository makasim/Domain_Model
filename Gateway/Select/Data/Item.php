<?php

/**
 * 
 * @author maksim
 *
 */
class Domain_Gateway_Select_Data_Item 
  implements Domain_Gateway_Data_Item_Interface
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
  
  /**
   * 
   * @var Zend_Db_Table_Row
   */
  protected $_row = null;
	
  public function __construct(Zend_Db_Table_Row $data, $modelClass = null)
  {
  	$this->_row = $data;
  	$this->_modelClass = $modelClass;
  }
  
  public function offsetExists ($offset) 
  {
  	return $this->_getRow()->offsetExists($offset);
  }

  public function offsetGet ($offset) 
  {
    return $this->_getRow()->offsetGet($offset);
  }
  
  public function offsetSet ($offset, $value) 
  {
    return $this->_getRow()->offsetSet($offset, $value);
  }

  public function offsetUnset ($offset) 
  {
    return $this->_getRow()->offsetUnset($offset);
  }
  
  public function getIterator()
  {
  	return new ArrayIterator($this->_getRow());
  }
  
	/**
	 * (non-PHPdoc)
	 * @see Lib/Domain/Gateway/Data/Item/Domain_Gateway_Data_Item_Interface#isConvertable()
	 */
  public function isConvertable()
  {
  	$primary = array_shift($this->getTable()->info(Zend_Db_Table::PRIMARY));
    if (!isset($this->$primary)) {
      $this->_errorMessage = 'The primary id does not present in the row data set so the Model object cannot be created without id';
      return false;
    }
    if (is_null($this->_modelClass)) {
      $this->_errorMessage = 'The model class is not defined at all';
      return false;
    }
    if (!class_exists($this->_modelClass, $autoload = true)) {
        $this->_errorMessage = 'Can not find model class `' . $this->_modelClass . '`';
      return false;
      }
      $reflection = new ReflectionClass($this->_modelClass);
      if (!$reflection->isSubclassOf('Domain_Model_Abstract')) {
        $this->_errorMessage = 'The model class `' . $this->_modelClass . '` must be instance of `Domain_Model_Abstract`';
        return false;
      }
      
      /*$is_creation_method_valid = ($method = $reflection->getMethod('getById') && $method->isStatic());
      if (!$is_creation_method_valid) {
        $this->_errorMessage = 'The model must redefine static method for correct work';
        return false;
      }*/
    
    return true;
  }
  
  /**
   * @throws Domain_Model_Exception if model class can not be found.
   * @throws Domain_Model_Exception if the model class is not instance of `Domain_Model_Abstract`.
   * @throws Domain_Model_Exception if needed creation method is invalid or not exist.
   * 
   * (non-PHPdoc)
   * @see Lib/Domain/Gateway/Data/Item/Domain_Gateway_Data_Item_Interface#toModel()
   */
  public function toModel()
  {
  	if (is_null($this->_model)) {
      if (!$this->isConvertable()) {
        throw new Domain_Model_Exception($this->_errorMessage);
      }
        
      $primary = array_shift($this->getTable()->info(Zend_Db_Table::PRIMARY));
      $class = $this->_modelClass;
        
      $this->_model = $class::getById($this->$primary); 
    }
      
    return $this->_model;
  }
  
  /**
   * 
   * Enter description here...
   * @param $class iterator class if null must be set default iterator class.
   * @return void
   */
  public function setIteratorClass($classIterator = null)
  {
  	//TODO
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

  /**
   * 
   * @return Zend_Db_Table_Row
   */
  protected function _getRow()
  {
  	return $this->_row;
  }
}