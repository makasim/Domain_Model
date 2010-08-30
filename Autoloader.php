<?php

/**
 * 
 * @author maksim
 *
 */
class Domain_Autoloader extends Zend_Loader_Autoloader_Resource
{
	/**
	 * @var Domain_Autoloader
	 */
	protected static $_instance = null;
	
	public  function __construct()
	{
		$args = func_get_args();
		$options = isset($args[0]) ? $args[0] : array();
		$options = array_merge($this->_getDefaultOptions(), $options); 
		parent::__construct($options);
	
		//TODO must be refactored
		include $this->getBasePath() . '/Exception.php'; 
		
		$this->addResourceType('gateways', 'Gateway', 'Gateway');
		$this->addResourceType('models', 'Model', 'Model');
		$this->addResourceType('tables', 'Table', 'Table');
	}
	
	/**
	 * 
	 * Enter description here...
	 * @return Domain_Autoloader
	 */
	public function regestry()
	{
		Zend_Loader_Autoloader::getInstance()
			->unregisterNamespace('Domain')
			->pushAutoloader($this, $this->getNamespace());
			
		return $this;
	}
	
	/**
	 * 
	 * Enter description here...
	 * @return Domain_Autoloader
	 */
	public function unregestry()
	{
		Zend_Loader_Autoloader::getInstance()
			->removeAutoloader($this, $this->getNamespace());
		
		return $this;
	}
	
	/**
	 * 
	 * Enter description here...
	 * @return array
	 */
	protected function _getDefaultOptions()
	{
		return array('namespace' => 'Domain', 'basePath' => realpath(__DIR__));
	}
	
	/**
	 * 
	 * Enter description here...
	 * @return Domain_Autoloader
	 */
	public static function getInstance()
	{
		//TODO client can give user defined options. Must be implemented.
		is_object(self::$_instance) ?: self::$_instance = new self;
		
		return self::$_instance;
	}
}