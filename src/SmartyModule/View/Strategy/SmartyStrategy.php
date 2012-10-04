<?php

namespace SmartyModule\View\Strategy;

use Zend\EventManager\EventCollection,
    Zend\EventManager\EventManagerInterface,
    Zend\EventManager\ListenerAggregateInterface,
    Zend\View\ViewEvent,
    SmartyModule\View\Renderer\SmartyRenderer;

class SmartyStrategy implements ListenerAggregateInterface {
    protected $renderer;
    protected $listeners = array();

    public function __construct(SmartyRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function attach(EventManagerInterface $events) {
        $this->listeners[] = $events->attach('renderer', array($this, 'selectRenderer'));
        $this->listeners[] = $events->attach('response', array($this, 'injectResponse'));
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Retrieve the composed renderer
     *
     * @return SmartyRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Retrieve the composed renderer
     *
     * @param \SmartyModule\View\Strategy\ViewEvent|\Zend\View\ViewEvent $e
     * @return SmartyRenderer
     */
    public function selectRenderer(ViewEvent $e)
    {
        return $this->renderer;
    }

    /**
     * Populate the response object from the View
     *
     * Populates the content of the response object from the view rendering
     * results.
     *
     * @param \SmartyModule\View\Strategy\ViewEvent|\Zend\View\ViewEvent $e
     * @return void
     */
    public function injectResponse(ViewEvent $e)
    {
        $renderer = $e->getRenderer();
        if ($renderer !== $this->renderer) {
            return;
        }
        $result   = $e->getResult();
        $response = $e->getResponse();
        $response->setContent($result);
    }
}
