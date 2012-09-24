<?php

namespace SmartyModule;

use Zend\ModuleManager\ModuleManager,
    Zend\EventManager\StaticEventManager;

class Module
{
	
	public function onBootstrap($e) {
		$this->initializeView($e);	
		$this->setupView($e);
	} 

	public function initializeView($e)
	{
		global $config;
		$app          = $e->getParam('application');
		$request = $app->getRequest();

		// support cli requests which do not have a base path
		if (method_exists($request, 'getBasePath')) {
			$basePath     = $app->getRequest()->getBasePath();
		}
		$serviceManager      = $app->getServiceManager();

		$view         = $serviceManager->get('Zend\View\View');
		$strategy     = $serviceManager->get('SmartyModule\View\Strategy\SmartyStrategy');
		$renderer = $strategy->getRenderer();
		$resolver = $serviceManager->get('viewresolver');
		$renderer->setResolver($resolver);
        
		$smarty = $renderer->getEngine();
		$config = $serviceManager->get('Config');
		//if ($config['environment'] == "production") {
		//	$smarty->compile_check = false;
		//	$smarty->force_compile = false;
		//}
		
		$renderer->setHelperPluginManager(new HelperPluginManager(new HelperConfig()));
		$config = $serviceManager->get('config');
		$router = \Zend\Mvc\Router\SimpleRouteStack::factory($config['router']);
		$renderer->plugin('url')->setRouter($router);
		
		if (isset($basePath)) {
			$renderer->plugin('basePath')->setBasePath($basePath);
		}
	}
	
	
/*
    public function init($manager)
    {
        // Register a bootstrap event
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'setupView'));
    }
*/
    public function setupView($e)
    {
          // Register a render event
          $application = $e->getParam('application');
          $serviceManager             = $application->getServiceManager();
          $view                = $serviceManager->get('Zend\View\View');
          $smartyRendererStrategy = $serviceManager->get('SmartyModule\View\Strategy\SmartyStrategy');
          $view->addRenderingStrategy(array($smartyRendererStrategy, 'selectRenderer'), 100);
          $view->addResponseStrategy(array($smartyRendererStrategy,  'injectResponse'), 100);
    } 


    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }


}
