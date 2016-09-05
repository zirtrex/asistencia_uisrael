<?php

namespace Users\Storage;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\Storage\Session;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SaveHandler\DbTableGateway;
use Zend\Session\SaveHandler\DbTableGatewayOptions;
use Zend\Db\TableGateway\TableGateway;


class AuthStorage extends Session implements ServiceLocatorAwareInterface
{	
	protected $namespace;
	protected $serviceLocator;
	
	public function __construct($namespace = null)
	{
		parent::__construct ( $namespace );
		
		$this->namespace = $namespace;
	}
	
	public function setDbHandler()
	{
		$tableGateway = new TableGateway('session', $this->getServiceLocator ()->get('Zend\Db\Adapter\Adapter'));
		
		$saveHandler = new DbTableGateway($tableGateway, new DbTableGatewayOptions());
		
		$sessionConfig = new SessionConfig ();
		$saveHandler->open($sessionConfig->getOption('save_path'), $this->namespace);

		$this->session->getManager()->setSaveHandler($saveHandler);
	}
	
	public function write($contents)
	{
		parent::write ($contents);
		/**
		 * when $this->authService->authenticate(); is valid, the session
		 * automatically called write('username')
		 * in this case, i want to save data like
		 * ["storage"] => array(4) {
		 * ["id"] => string(1) "1"
		 * ["username"] => string(5) "admin"
		 * ["ip_address"] => string(9) "127.0.0.1"
		 * ["user_agent"] => string(81) "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7;
		 * rv:18.0)
		 * Gecko/20100101 Firefox/18.0"
		 * }
		 */
		if (is_array($contents) && !empty($contents))
		{
			$this->getSessionManager()->getSaveHandler()->write($this->getSessionId(), \Zend\Json\Json::encode($contents));
		}
	}
	
	public function clear()
	{
		$this->getSessionManager()->getSaveHandler()->destroy($this->getSessionId());
		parent::clear();
	}
	
	public function getSessionManager()
	{
		return $this->session->getManager();
	}
	
	public function getSessionId()
	{
		return $this->session->getManager()->getId();
	}
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
	}
	
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}
}









