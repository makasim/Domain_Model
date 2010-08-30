<?php

/**
 * 
 * @author maksim
 *
 */
class Domain_Gateway_Helper_Loader 
{
	/**
	 * 
	 * @var Zend_Loader_PluginLoader
	 */
	protected $_pluginLoader = null;

	/**
	 * @var Domain_Gateway_Interface
	 */
	protected $_gateway = null;
	
	/**
	 *
	 */
	public function __construct(Domain_Gateway_Interface $gateway, $sources = null)
	{
		if (is_null($sources)) {
			return;
		}
		
		$this->addSource('Domain_Gateway_Helper', 'Domain/Gateway/Helper/');
		if (is_array($sources)) {
			foreach ($sources as $prefix => $path) {
				$this->addSource($prefix, $path);
			}
		} else {
			throw new Domain_Gateway_Exception('Helper source(s) must be string or array of strings');
		}
		
		$this->_gateway = $gateway;
	}
	
	public function addSource($prefix, $path)
	{
		$this->_getPluginLoader()->addPrefixPath($prefix, $path);
		
		return $this;
	}
	
	/**
	 *
	 * @throws Domain_Gateway_Exception if no one source was set.
	 * @throws Domain_Gateway_Exception if no helper class(object) was not found.
	 * @return mixed
	 */
	public function __call($method, $args = array())
	{
		try {
			$helperClass = $this->_getPluginLoader()->load(ucfirst($method), true);
			$helper = new $helperClass($this->_gateway);
			if (!$helper instanceof Domain_Gateway_Helper_Abstract) {
				throw new Domain_Gateway_Exception('The Gateway helper is not instance of `Domain_Gateway_Helper_Abstract`');
			}
		
			return call_user_func_array(array($helper, $method), $args);
		} catch (Zend_Loader_PluginLoader_Exception $e) {
			throw new Domain_Gateway_Exception($e->getMessage());
		}
	}
	
	protected function _getPluginLoader()
	{
		is_object($this->_pluginLoader) ?: $this->_pluginLoader = new Zend_Loader_PluginLoader();
		
		return $this->_pluginLoader;
	}
}