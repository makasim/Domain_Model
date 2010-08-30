<?php
/**
 * 
 * @author maksim
 *
 */

/**
 * 
 * @author maksim
 *
 */
abstract class Domain_Model_Data_Abstract extends Zend_Db_Table_Row_Abstract
{
	/**
	 * 
	 * @var string Contains model name.
	 */
	protected $_model_class = null;
	
	/**
	 * 
	 * @var Domain_Model_Abstract
	 */
	protected $_model = null;
	
	protected $_error_msg = null;
	
	/**
	 * Whether the data object be converted to model or not.
	 * @access public
	 * @return bool
	 */
	public function isConvertable()
	{
		$primary = array_shift($this->getTable()->info(Zend_Db_Table::PRIMARY));
		if (!isset($this->$primary)) {
			$this->_error_msg = 'The primary id does not present in the row data set so the Model object cannot be created without id';
			return false;
		}
		if (is_null($this->_model_class)) {
			$this->_error_msg = 'The model class is not defined at all';
			return false;
		}
		if (!class_exists($this->_model_class, $autoload = true)) {
	    	$this->_error_msg = 'Can not find model class `' . $this->_model_class . '`';
			return false;
	    }
	    $reflection = new ReflectionClass($this->_model_class);
    	if (!$reflection->isSubclassOf('Domain_Model_Abstract')) {
	    	$this->_error_msg = 'The model class `' . $this->_model_class . '` must be instance of `Domain_Model_Abstract`';
	    	return false;
	    }
	    
	    /*$is_creation_method_valid = ($method = $reflection->getMethod('getById') && $method->isStatic());
    	if (!$is_creation_method_valid) {
	    	$this->_error_msg = 'The model must redefine static method for correct work';
	    	return false;
	    }*/
		
		return true;
	}
	
	/**
	 * @access public
	 * @throws Domain_Model_Exception if model class can not be found.
	 * @throws Domain_Model_Exception if the model class is not instance of `Domain_Model_Abstract`.
	 * @throws Domain_Model_Exception if needed creation method is invalid or not exist.
	 * 
	 * @return Domain_Model_Abstract
	 */
	public function toModel()
	{
		if (is_null($this->_model)) {
	    	if (!$this->isConvertable()) {
	    		throw new Domain_Model_Exception($this->_error_msg);
	    	}
	    	
	    	$primary = array_shift($this->getTable()->info(Zend_Db_Table::PRIMARY));
	    	$class = $this->_model_class;
	    	
	    	$this->_model = $class::getById($this->$primary); 
    	}
    	
    	return $this->_model;
	}
	
	public function setModelClass($class)
	{
		if ($class instanceof Domain_Model_Abstract) {
			$class = get_class($class);
		}
		
		$this->_model_class = $class;
	}
}