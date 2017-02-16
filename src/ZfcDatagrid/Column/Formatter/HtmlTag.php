<?php
namespace ZfcDatagrid\Column\Formatter;

use Zend\Router\RouteStackInterface;
use ZfcDatagrid\Column\AbstractColumn;

class HtmlTag extends AbstractFormatter implements RouterInterface
{
    const ROW_ID_PLACEHOLDER = ':rowId:';

    /**
     * @var array
     */
    protected $validRenderers = [
        'jqGrid',
        'bootstrapTable',
    ];

    /**
     * @var string
     */
    protected $name = 'span';

    /**
     * @var AbstractColumn[]
     */
    protected $linkColumnPlaceholders = [];

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var string
     */
    protected $route;

    /**
     * @var array
     */
    protected $routeParams = [];

    /**
     * @var RouteStackInterface
     */
    public $router;

    /**
     * @param \Zend\Router\RouteStackInterface $router
     *
     * @return void
     */
    public function setRouter(RouteStackInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return \Zend\Router\RouteStackInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set a HTML attributes.
     *
     * @param string $name
     * @param string $value
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = (string) $value;
    }

    /**
     * Get a HTML attribute.
     *
     * @param string $name
     *
     * @return string
     */
    public function getAttribute($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return '';
    }

    /**
     * Removes an HTML attribute.
     *
     * @param string $name
     */
    public function removeAttribute($name)
    {
        if (isset($this->attributes[$name])) {
            unset($this->attributes[$name]);
        }
    }

    /**
     * Get all HTML attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the link.
     *
     * @param string $href
     */
    public function setLink($href)
    {
        $this->setAttribute('href', $href);
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->getAttribute('href');
    }

    /**
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param array $params
     */
    public function setRouteParams(array $params)
    {
        $this->routeParams = $params;
    }

    /**
     * @return array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * Get the column row value placeholder
     * $fmt->setLink('/myLink/something/'.$fmt->getColumnValuePlaceholder($myCol));.
     *
     * @param AbstractColumn $col
     *
     * @return string
     */
    public function getColumnValuePlaceholder(AbstractColumn $col)
    {
        $this->linkColumnPlaceholders[] = $col;

        return ':' . $col->getUniqueId() . ':';
    }

    /**
     * @return AbstractColumn[]
     */
    public function getLinkColumnPlaceholders()
    {
        return $this->linkColumnPlaceholders;
    }

    /**
     * Returns the rowId placeholder.
     *
     * @return string
     */
    public function getRowIdPlaceholder()
    {
        return self::ROW_ID_PLACEHOLDER;
    }

    /**
     * @param AbstractColumn $col
     *
     * @return string
     */
    public function getFormattedValue(AbstractColumn $col)
    {
        $row = $this->getRowData();

        return sprintf('<%s %s>%s</%s>', $this->getName(), $this->getAttributesString($col), $row[$col->getUniqueId()], $this->getName());
    }

    /**
     * Get the string version of the attributes.
     *
     * @param AbstractColumn $col
     *
     * @return string
     */
    protected function getAttributesString(AbstractColumn $col)
    {
        $attributes = [];

        if ($this->getRoute() && $this->getRouter() instanceof RouteStackInterface) {
            $this->setLink($this->getRouter()->assemble(
                $this->getRouteParams(),
                ['name' => $this->getRoute()]
            ));
        }

        foreach ($this->getAttributes() as $attrKey => $attrValue) {
            if ('href' === $attrKey) {
                $attrValue = $this->getLinkReplaced($col);
            }
            $attributes[] = $attrKey . '="' . $attrValue . '"';
        }

        return implode(' ', $attributes);
    }

    /**
     * This is needed public for rowClickAction...
     *
     * @param AbstractColumn $col
     *
     * @return string
     */
    protected function getLinkReplaced(AbstractColumn $col)
    {
        $row = $this->getRowData();

        $link = $this->getLink();
        if ($link == '') {
            return $row[$col->getUniqueId()];
        }

        // Replace placeholders
        if (strpos($link, self::ROW_ID_PLACEHOLDER) !== false) {
            $id = '';
            if (isset($row['idConcated'])) {
                $id = $row['idConcated'];
            }
            $link = str_replace(self::ROW_ID_PLACEHOLDER, rawurlencode($id), $link);
        }

        foreach ($this->getLinkColumnPlaceholders() as $col) {
            $link = str_replace(':' . $col->getUniqueId() . ':', rawurlencode($row[$col->getUniqueId()]), $link);
        }

        return $link;
    }
}
