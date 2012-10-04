<?php

namespace SmartyModule;

use Zend\ModuleManager\ModuleManager as Manager,
    Zend\EventManager\StaticEventManager,
    Zend\View\HelperPluginManager,
    Zend\Form\View\HelperConfig,
    Zend\Mvc\Router\SimpleRouteStack;
	

class Module
{
	
	public function onBootstrap($e) {
		$this->initializeView($e);	
		$this->setupView($e);
	} 

	public function initializeView($e)
	{
		$app = $e->getParam('application');
		$request = $app->getRequest();

		// support cli requests which do not have a base path
		if (method_exists($request, 'getBasePath')) {
			$basePath = $app->getRequest()->getBasePath();
		}
		$serviceManager = $app->getServiceManager();

		$view = $serviceManager->get('Zend\View\View');
		$strategy = $serviceManager->get('SmartyModule\View\Strategy\SmartyStrategy');
		$renderer = $strategy->getRenderer();
		$resolver = $serviceManager->get('viewresolver');
		$renderer->setResolver($resolver);
        
		$smarty = $renderer->getEngine();
		$config = $serviceManager->get('Config');

        foreach ($config['smarty'] as $key=>$value) {
            if (isset($smarty->$key))
                $smarty->$key = $value;
        }

		$renderer->setHelperPluginManager(new HelperPluginManager(new HelperConfig()));
		$renderer->plugin('url')->setRouter(SimpleRouteStack::factory($config['router']['routes']));
		
		if (isset($basePath)) {
			$renderer->plugin('basePath')->setBasePath($basePath);
		}
	}
	
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
