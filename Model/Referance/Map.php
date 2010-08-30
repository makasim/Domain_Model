<?php
class Domain_Model_Referance_Map
{	
	protected $_referanceMap = array();
	
	public function __construct()
	{
		$args = func_get_args();
		if (!empty($args)) {
			call_user_func_array(array($this, 'setReferance'), $args);
		}
	}
	
	/**
	 * 
	 * Enter description here...
	 * @return unknown_type
	 */
	public function setReferance()
	{
		$args = func_get_args();
		if (count($args) == 1) {
			$referances = $args[0];
		} else if (count($args) == 2) {
			$referances = array($args[0] => $args[1]);
		} else {
			throw new Domain_Model_Exception('Invalid number of method params. Must be one or two. Number of given: ' . count($args));
		}
		
		$this->removeReferance();
		foreach ($referances as $name => $options) {
			$this->addReferance($name, $options);
		}
	}
	
	public function addReferance($name, $options)
	{
		$name = strtolower($name);	
		$this->_referanceMap[$name] = new Domain_Model_Referance_Item($name, $options);
	}
	
	public function removeReferance($name = null)
	{
		if (is_null($name)) {
			$this->_referanceMap = array();
		} else {
			unset($this->_referanceMap[strtolower($name)]);
		}
	}
	
	public function getReferance($name)
	{
		$name = strtolower($name);
		return isset($this->_referanceMap[$name]) ? $this->_referanceMap[$name] : false;
	}
	
	public function __set($name, $value) 
	{
		if (preg_match('/^set(\w+)/', $name, $matches)) {
			$this->addReferance($matches[1], $value);
		}
	}
	
	public function __get($name) 
	{
		return preg_match('/^get(\w+)/', $name, $matches) ? 
			$this->getReferance($matches[1]) : false;
	}
	
	public function __isset($name)
	{
		return false !== $this->getReferance($name);
	}
	
	public function __unset($name)
	{
		return $this->removeReferance($name); 		
	}

/**
	 * 
	 * @param Domain_Model_Abstract $parent
	 * @paramsstring $rule
	 * @return Domain_Model_Abstract
	 */
	protected function _createParentModel(Domain_Model_Abstract $parent, $rule)
	{				
		$criteria = $this->_getCriteria()->getCriteriaToParent(
			$this->_getCriteria($parent), $rule);
		$class = get_class($parent);	
		return new $class($criteria);
	}
	
	/**
	 * 
	 * @param Domain_Model_Abstract $dependent
	 * @param string $rule
	 * @return Domain_Model_Abstract
	 */
	protected function _createDependentModel(Domain_Model_Abstract $dependent, $rule)
	{
		$criteria = $this->_getCriteria()->getCriteriaToDependent(
			$this->_getCriteria($dependent), $rule);
		
		$class = get_class($dependent);
		return new $class($criteria);
	}
	
	/**
	 *
	 * @param string $matchRefRule
	 * @param Domain_Model_Abstract|Domain_Table_Abstract $intersection
	 * @param string $callerRefRule
	 * @param Domain_Model_Abstract $matchCriteria
	 * 
	 * @return Domain_Model_Abstract
	 */
	protected function _createManyToManyModel(Domain_Model_Abstract $match,
		$intersection, $callerRefRule, $matchRefRule)
	{
		if ($intersection instanceof Domain_Model_Abstract) {
			$intersection_table = $this->_getTable($intersection);
		} elseif ($intersection instanceof Domain_Table_Abstract) {
			$intersection_table = $intersection;
		} else {
			throw new Domain_Model_Exception('Invalid intersaction object given. Must be instance of eather `Domain_Model_Abstract` or `Domain_Table_Abstract`');
		}
		
		$criteria = $this->_getCriteria()->getCriteriaManyToMany(
			$this->_getCriteria($match), 
			$intersection_table,
			$callerRefRule,
			$matchRefRule);
		
		$class = get_class($match);
		return new $class($criteria);
	}
}