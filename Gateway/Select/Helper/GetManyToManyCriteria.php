<?php
class Domain_Gateway_Select_Helper_GetManyToManyCriteria extends Domain_Gateway_Select_Helper_GetReferancedAbstract
{
	/**
	 *
	 * @param $parent
	 * @param $rule
	 * @return Domain_Model_Criteria
	 */
	public function GetManyToManyCriteria($match, $intersect, $callerRefRule, $matchRefRule)
	{
		return $this->_gateway->table()->getSelectManyToMany(
			$this->_prepareTable($match),
			$this->_prepareTable($intersect),
			$callerRefRule,
			$matchRefRule,
			$this->_prepareSelect($this->_gateway),
			$this->_prepareSelect($match));
	}
}