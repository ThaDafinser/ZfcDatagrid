<?php
namespace ZfcDatagrid\Column\Formatter;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use ZfcDatagrid\Column\AbstractColumn;

class GenerateLink extends AbstractFormatter implements ServiceManagerAwareInterface
{
    protected $validRenderers = [
        'jqGrid',
        'bootstrapTable',
    ];

    /** @var  string */
    protected $route;
    /** @var  array */
    protected $routeParams;
    /** @var  string|null */
    protected $routeKey;
    /** @var  ServiceManager */
    protected $serviceManager;
    /** @var  \Zend\View\Renderer\PhpRenderer */
    protected $viewRenderer;

    /**
     * @param ServiceManager $sm
     * @param                $route
     * @param null           $key
     * @param array          $params
     */
    public function __construct(ServiceManager $sm, $route, $key = null, $params = [])
    {
        $this->setServiceManager($sm);
        $this->setRoute($route);
        $this->setRouteParams($params);
        $this->setRouteKey($key);
    }

    /**
     * @param  AbstractColumn $column
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
     * Set service manager
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @return \Zend\View\Renderer\PhpRenderer
     */
    public function getViewRenderer()
    {
        if (! $this->viewRenderer) {
            $this->viewRenderer = $this->getServiceManager()->get('ViewRenderer');
        }

        return $this->viewRenderer;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param  string       $route
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
     * @param  array        $routeParams
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
     * @param  null|string  $routeKey
     */
    public function setRouteKey($routeKey)
    {
        $this->routeKey = $routeKey;
    }
}
