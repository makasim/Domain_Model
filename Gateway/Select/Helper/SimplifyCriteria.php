<?php

/**
 * 
 * @author maksim
 *
 */
class Domain_Gateway_Select_Helper_SimplifyCriteria extends Domain_Gateway_Select_Helper_Abstract
{
	/**
	 * 
	 * Enter description here...
	 * @return Zend_Db_Table_Select
	 */
	public function simplifyCriteria()
	{
		$select = $this->_gateway->select();
		if (count($select->getPart(Zend_Db_Select::SQL_FROM)) == 0) {
			return $select;
		}
		
		$select = $this->_gateway->table()->select();
		$primary = $this->_gateway->table()->quoteColumnAs();
		$ids = $this->_gateway->getPrimaryIds();
		return $select->where("$primary  IN (?)", $ids);
	}

	/**
	 *
	 * @param $dependent
	 * @param $rule
	 * @return Domain_Model_Criteria
	 */
	public function getCriteriaToDependent(Domain_Model_Criteria $dependent, $rule)
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
	 * @param $matchCriteria
	 * @param $intersectionTable
	 * @param $callerRefRule
	 * @param $matchRefRule
	 * @return Domain_Model_Criteria
	 */
	public function getCriteriaManyToMany(Domain_Model_Criteria $matchCriteria, $intersectionTable,
	$callerRefRule, $matchRefRule)
	{
		$select = $this->table()->getSelectManyToMany(
			$matchCriteria->table(),
			$intersectionTable,
			$callerRefRule,
			$matchRefRule,
			$this->getSelectForReferanceModel(),
			$matchCriteria->getSelectForReferanceModel());
		
		$class = get_called_class();
		return new $class($select, $useable = false);
	}
}