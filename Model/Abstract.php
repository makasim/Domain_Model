<?php
/**
 * 
 * @author maksim
 *
 */

/**
 * 
 * @author maksim
 * @abstract
 * 
 */
abstract class Domain_Model_Abstract implements Countable, Domain_Gateway_AgregatorInterface
{		
	/**
	 * 
	 * @var Domain_Gateway_Interface
	 */
	protected $_gateway = null;
	
	/**
	 * 
	 * @param Domain_Gateway_Interfac|mixed $criteria
	 * @return void
	 */
	public function __construct($criteria) 
	{				
		$gatewayClass = static::getGatewayClass();
		$gateway = null;
		
		if ($criteria instanceof $gatewayClass) {
			$gateway = $criteria;
		} else {
			$gateway = static::_getNewGateway()
				->setCriteria($criteria);
		}
		
		if (is_null($gateway)) {
			throw new Domain_Model_Exception('Model must be init by instance of `' . $gatewayClass . '` or criteria');
		}
		
		$this->_setGateway($gateway);

		$this->_init();
	}
	
	/**
	 * 
	 * @return void
	 */
	protected function _init()
	{
		
	}
	
	/**
	 * 
	 * @return Domain_Model_Abstract
	 */
	public function delete()
	{
		$this->_getGateway()->delete();
		return $this;
	}

	public function count()
	{
		return count($this->_getGateway());
	}
	
	protected function _update($data) 
	{
		return $this->_getGateway()->update($data);
	}
	
	/**
	 * 
	 * @param Domain_Model_Abstract $parent
	 * @paramsstring $rule
	 * @return Domain_Model_Abstract
	 */
	protected function _createParentModel(Domain_Model_Abstract $parent, $rule)
	{				
		$class = get_class($parent);
		return new $class(
			$this->_getGateway()->getParentCriteria($parent->_getGateway(), $rule));
	}
	
	/**
	 * 
	 * @param Domain_Model_Abstract $dependent
	 * @param string $rule
	 * @return Domain_Model_Abstract
	 */
	protected function _createDependentModel(Domain_Model_Abstract $dependent, $rule)
	{
		$class = get_class($dependent);		
		return new $class(
			$this->_getGateway()->getDependentCriteria($dependent->_getGateway(), $rule));
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
		$intersectionTable = $intersection;
		if ($intersection instanceof Domain_Model_Abstract) {
			$intersectionTable = $intersection->_getGateway()->table();
		}
		
		$criteria = $this->_getGateway()->getManyToManyCriteria(
			$match->_getGateway(), 
			$intersectionTable,
			$callerRefRule,
			$matchRefRule);
		
		$class = get_class($match);
		return new $class($criteria);
	}
	
	/**
	 * 
	 * Enter description here...
	 * @param Domain_Gateway_Interface $gateway
	 * @return Domain_Model_Abstract
	 */
	protected function _setGateway(Domain_Gateway_Interface $gateway)
	{
		$this->_gateway = $gateway;
		return $this;
	}
	
	/**
	 * 
	 * Enter description here...
	 * @return Domain_Gateway_Interface
	 */
	protected function _getGateway()
	{
		return $this->_gateway;
	}
	
	/*public function __call($method, $args)
	{
		isset($args[0]) ?: $args[0] = null;
		isset($args[1]) ?: $args[1] = null;
		
		if (preg_match('/^get(\w+)ModelThrough(\w+)/', $method, $rules)) {
			return $this->_createManyToManyModel($args[0], $args[1], $rules[2], $rules[1]); 
		} else if (preg_match('/^getDependUp(\w+)Model/', $method, $rules)) {
			return $this->_createDependentModel($args[0], $rules[1]);
		} else if (preg_match('/^get(\w+)Model/', $method, $rules)) {
			return $this->_createParentModel($args[0], $rules[1]);
		} 
		
		trigger_error('Call to undefined method ' . __CLASS__ . '::' . $method . '()', E_USER_ERROR);
	}*/
	
	/**
	 * 
	 * @param array|int $id
	 * @return Domain_Model_Abstract
	 */
	public static function getById($id)
	{
		$class = get_called_class();
		return new $class(static::_getNewGateway()->initById($id));
	}
	
	/**
	 * 
	 * @return Domain_Model_Abstract
	 */
	public static function getAll()
	{
		$class = get_called_class();
		return new $class(static::_getNewGateway()->initByAll());
	}
	
	/**
	 * 
	 * @return Domain_Model_Abstract
	 */
	public static function getEmpty()
	{
		$class = get_called_class();
		return new $class(static::_getNewGateway()->initByEmpty());
	}
	
	
	
	public static function getGatewayClass()
	{
		return 'Domain_Gateway_Select';	
	}
	
	/**
	 * 
	 * @return Domain_Model_Abstract
	 */
	protected static function _create($data)
	{
		$class = get_called_class();
		$gateway = static::_getNewGateway();
		$gateway->create($data);
		return new $class($gateway);
	}
	
	/**
	 * 
	 * @return Domain_Gateway_Interface
	 */
	protected static function _getNewGateway()
	{
		return Domain_Gateway_Builder::factory(get_called_class());
	}
}