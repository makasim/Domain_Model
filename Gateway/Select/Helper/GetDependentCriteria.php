<?php
class Domain_Gateway_Select_Helper_GetDependentCriteria extends Domain_Gateway_Select_Helper_GetReferancedAbstract
{
	/**
	 *
	 * @param $parent
	 * @param $rule
	 * @return Domain_Model_Criteria
	 */
	public function getDependentCriteria($dependent, $rule)
	{
		return $this->_gateway->table()->getSelectToDependent(
			$this->_prepareTable($dependent),
			$rule,
			$this->_prepareSelect($this->_gateway),
			$this->_prepareSelect($dependent));
	}
}