<?php
namespace ZfcDatagrid\Column\Formatter;

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
     * @param RendererInterface $viewRenderer
     * @param                   $route
     * @param null              $key
     * @param array             $params
     */
    public function __construct($viewRenderer, $route, $key = null, $params = [])
    {
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
        $row   = $this->getRowData();
        $value = $row[$column->getUniqueId()];

        $routeKey = !is_null($this->getRouteKey()) ?
            $this->getRouteKey()
            :
            $column->getUniqueId();

        $params            = $this->getRouteParams();
        $params[$routeKey] = $value;

        $url = (string) $this->getViewRenderer()->url($this->getRoute(), $params);

        return sprintf('<a href="%s">%s</a>', $url, $value);
    }

    /**
     * @return RendererInterface
     */
    public function getViewRenderer()
    {
        return $this->viewRenderer;
    }

    /**
     * @param RendererInterface $viewRenderer
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
