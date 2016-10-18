<?php

namespace ZfcDatagrid\Column\Formatter;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Renderer\RendererInterface;
use ZfcDatagrid\Column\AbstractColumn;

class GenerateLink extends AbstractFormatter
{
    /** @var array */
    protected $validRenderers = [
        'jqGrid',
        'bootstrapTable',
    ];

    /** @var string */
    protected $route;
    /** @var array */
    protected $routeParams;
    /** @var string|null */
    protected $routeKey;
    /** @var \Zend\View\Renderer\PhpRenderer */
    protected $viewRenderer;

    /**
     * @param ServiceLocatorInterface|RendererInterface $viewRenderer
     * @param                                           $route
     * @param null                                      $key
     * @param array                                     $params
     */
    public function __construct($viewRenderer, $route, $key = null, $params = [])
    {
        /*
         * old fallback that should be removed in 2.0
         * TODO remove in 2.0
         */
        if (!$viewRenderer instanceof RendererInterface) {
            $viewRenderer = $viewRenderer->get('ViewRenderer');
        }

        $this->setViewRenderer($viewRenderer);
        $this->setRoute($route);
        $this->setRouteParams($params);
        $this->setRouteKey($key);
    }

    /**
     * @param AbstractColumn $column
     *
     * @return string
     */
    public function getFormattedValue(AbstractColumn $column)
    {
        $row = $this->getRowData();
        $value = $row[$column->getUniqueId()];

        $routeKey = !is_null($this->getRouteKey()) ?
            $this->getRouteKey()
            :
            $column->getUniqueId();

        $params = $this->getRouteParams();
        $params[$routeKey] = $value;

        $url = (string) $this->getViewRenderer()->url($this->getRoute(), $params);

        return sprintf('<a href="%s">%s</a>', $url, $value);
    }

    /**
     * @return \Zend\View\Renderer\PhpRenderer
     */
    public function getViewRenderer()
    {
        return $this->viewRenderer;
    }

    /**
     * @param \Zend\View\Renderer\PhpRenderer $viewRenderer
     *
     * @return self
     */
    public function setViewRenderer($viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * @param array $routeParams
     */
    public function setRouteParams($routeParams)
    {
        $this->routeParams = $routeParams;
    }

    /**
     * @return null|string
     */
    public function getRouteKey()
    {
        return $this->routeKey;
    }

    /**
     * @param null|string $routeKey
     */
    public function setRouteKey($routeKey)
    {
        $this->routeKey = $routeKey;
    }
}
