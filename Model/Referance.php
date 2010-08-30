<?php
class Domain_Model_Referance
{	
	protected static $_referanceMap = null;
	
	protected $_modelClass = null;
	
	protected $_criteria = null;
	
	public function __construct($model_class, Domain_Model_Criteria $criteria)
	{
		$this->_modelClass = is_object($model_class) ? get_class($model_class) : $model_class;
		$this->_criteria = $criteria;
	}
	
	public function __call($method, $args)
	{
		isset($args[0]) ?: $args[0] = null;
		isset($args[1]) ?: $args[1] = null;
		
		if (preg_match('/^get(\w+)ModelThrough(\w+)/', $method, $rules)) {
			return $this->_getManyToManyReferanceModel($rules[1], $rules[2], $args[0], $args[1]); 
		} else if (preg_match('/^get(\w+)Model/', $method, $rules)) {
			$ref = $this->_getReferance($ref);
		
			return $ref->isParent($this->_modelClass) ? 
				$this->getDependentModel($ref, $criteria) :
				$this->getParentModel($ref, $criteria);
		}
		
		trigger_error('Call to undefined method ' . __CLASS__ . '::' . $method . '()', E_USER_ERROR);
	}

	protected function _getManyToManyReferanceModel($callerRef, $matchRef,
		Domain_Model_Criteria $intersectionCriteria, Domain_Model_Criteria $matchCriteria)
	{
		$callerRef = $this->_getReferance($callerRef);
		$matchRef = $this->_getReferance($matchRef);
		
		$select = $this->table()->getSelectManyToMany(
			$matchCriteria->table(),
			$intersectionCriteria->table(),
			$callerRefRule->getTableRule(),
			$matchRefRule->getTableRule(),
			$this->_criteria->getSelectForReferanceModel(),
			$matchCriteria->getSelectForReferanceModel());
		
		$class = $matchRefRule;
		return new $class($select);
	}
	
/**
	 *
	 * @param $parent
	 * @param $rule
	 * @return Domain_Model_Criteria
	 */
	protected function _getParentModel(Domain_Model_Criteria $parent, $rule)
	{
		$select = $this->table()->getSelectToParent(
		$parent->table(),
		$rule,
		$this->getSelectForReferanceModel(),
		$parent->getSelectForReferanceModel());

		$class = get_called_class();
		return new $class($select, $useable = false);
	}

	/**
	 *
	 * @param $dependent
	 * @param $rule
	 * @return Domain_Model_Criteria
	 */
	protected function _getDependentModel(Domain_Model_Criteria $dependent, $rule)
	{
		$select = $this->table()->getSelectToDependent(
		$dependent->table(),
		$rule,
		$this->getSelectForReferanceModel(),
		$dependent->getSelectForReferanceModel());

		$class = get_called_class();
		return new $class($select, $useable = false);
	}
	
	/**
	 * 
	 * Enter description here...
	 * @return Domain_Model_Referance_Map
	 */
	protected function _getReferanceMap()
	{
		if (!self::$_referenceMap) {
			throw new Domain_Model_Exception('ReferanceMap object was not set and referances cannot be used without it');	
		}
		
		return self::$_referenceMap;		
	}
	
	/**
	 * 
	 * Enter description here...
	 * @param $name
	 * @return Domain_Model_Referance_Item
	 */
	protected function _getReferance($name)
	{
		if (false === $referance = $this->_getReferanceMap()->getReferance($name)) {
			throw new Domain_Model_Exception('Requested referance `' . $name . '` does not exist');
		}
		
		return $referance;
	}

	public static function setReferanceMap(Domain_Model_Referance_Map $map)
	{
		self::$_referanceMap = $map;
	}
}