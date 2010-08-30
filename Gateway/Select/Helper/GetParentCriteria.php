<?php
class Domain_Gateway_Select_Helper_GetParentCriteria extends Domain_Gateway_Select_Helper_GetReferancedAbstract
{
	/**
	 *
	 * @param $parent
	 * @param $rule
	 * @return Domain_Model_Criteria
	 */
	public function getParentCriteria($parent, $rule)
	{
		return $this->_gateway->table()->getSelectToParent(
			$this->_prepareTable($parent),
			$rule,
			$this->_prepareSelect($this->_gateway),
			$this->_prepareSelect($parent));
	}
}