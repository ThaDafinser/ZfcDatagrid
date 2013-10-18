<?php
namespace ZfcDatagrid;

use ZfcDatagrid\DataSource;
use ZfcDatagrid\Renderer;
use Doctrine\ORM\QueryBuilder;
use ArrayIterator;
use Zend\Mvc\MvcEvent;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Console\Request as ConsoleRequest;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Cache;
use Zend\Session\Container as SessionContainer;
use Zend\Db\Sql\Select as ZendSelect;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\ResponseInterface;
use Doctrine\Common\Collections\Collection;

class Datagrid implements ServiceLocatorAwareInterface
{

    /**
     *
     * @var array
     */
    protected $options = array();

    /**
     *
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     *
     * @var SessionContainer
     */
    protected $session;

    /**
     *
     * @var Cache\Storage\StorageInterface
     */
    protected $cache;

    /**
     *
     * @var string
     */
    protected $cacheId;

    /**
     *
     * @var MvcEvent
     */
    protected $mvcEvent;

    protected $parameters = array();

    protected $url;

    /**
     *
     * @var HttpRequest
     */
    protected $request;

    /**
     * View or Response
     *
     * @var \Zend\Http\Response\Stream \Zend\View\Model\ViewModel
     */
    protected $response;

    /**
     *
     * @var Renderer\AbstractRenderer
     */
    private $renderer;

    /**
     *
     * @var Translator
     */
    protected $translator;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     * The grid title
     *
     * @var string
     */
    protected $title = '';

    /**
     *
     * @var DataSource\DataSourceInterface
     */
    protected $dataSource = null;

    protected $defaulItemsPerPage = 25;

    /**
     *
     * @var array
     */
    protected $columns = array();

    /**
     *
     * @var Column\Action\AbstractAction
     */
    protected $rowClickAction;

    /**
     * The prepared data
     *
     * @var array
     */
    protected $preparedData = array();

    /**
     *
     * @var array
     */
    protected $isUserFilterEnabled = true;

    /**
     *
     * @var Paginator
     */
    protected $paginator = null;

    /**
     *
     * @var array
     */
    protected $exportRenderers;

    protected $toolbarTemplate;

    /**
     *
     * @var ViewModel
     */
    protected $viewModel;

    protected $isInit = false;

    protected $isDataLoaded = false;

    protected $isRendered = false;

    protected $forceRenderer;

    private $specialMethods = array(
        'filterSelectOptions' => 2,
        'rendererParameter' => 3,
        'replaceValues' => 2,
        'select' => 2,
        'sortDefault' => 2
    );

    /**
     * Init method is called automatically with the service creation
     */
    public function init()
    {
        if ($this->getCache() === null) {
            $options = $this->getOptions();
            $this->setCache(Cache\StorageFactory::factory($options['cache']));
        }
        
        $this->isInit = true;
    }

    /**
     *
     * @return boolean
     */
    public function isInit()
    {
        return (bool) $this->isInit;
    }

    /**
     * Set the options from config
     *
     * @param array $config            
     */
    public function setOptions(array $config)
    {
        $this->options = $config;
    }

    /**
     * Get the config options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the grid id
     *
     * @param string $id            
     */
    public function setId($id = null)
    {
        if ($id !== null) {
            $id = preg_replace("/[^a-z0-9_\\\d]/i", '_', $id);
            
            $this->id = (string) $id;
        }
    }

    /**
     * Get the grid id
     *
     * @return string
     */
    public function getId()
    {
        if ($this->id === null) {
            $this->id = 'defaultGrid';
        }
        
        return $this->id;
    }

    /**
     * Set the session
     *
     * @param \Zend\Session\Container $session            
     */
    public function setSession(SessionContainer $session)
    {
        $this->session = $session;
        
        return $this;
    }

    /**
     * Get session container
     *
     * Instantiate session container if none currently exists
     *
     * @return SessionContainer
     */
    public function getSession()
    {
        if (null === $this->session) {
            // Using fully qualified name, to ensure polyfill class alias is used
            $this->session = new SessionContainer($this->getId());
        }
        
        return $this->session;
    }

    public function setCache(Cache\Storage\StorageInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     *
     * @return Cache\Storage\StorageInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set the cache id
     *
     * @param string $id            
     */
    public function setCacheId($id)
    {
        $this->cacheId = (string) $id;
    }

    /**
     * Get the cache id
     *
     * @return string
     */
    public function getCacheId()
    {
        if ($this->cacheId === null) {
            $this->cacheId = $this->getSession()
                ->getManager()
                ->getId() . '_' . $this->getId();
        }
        
        return $this->cacheId;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator            
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setMvcEvent(MvcEvent $mvcEvent)
    {
        $this->mvcEvent = $mvcEvent;
        $this->request = $mvcEvent->getRequest();
    }

    /**
     *
     * @return MvcEvent
     */
    public function getMvcEvent()
    {
        return $this->mvcEvent;
    }

    /**
     *
     * @return HttpRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set the translator
     *
     * @param Translator $translator            
     */
    public function setTranslator(Translator $translator = null)
    {
        $this->translator = $translator;
    }

    /**
     *
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     *
     * @return boolean
     */
    public function hasTranslator()
    {
        if ($this->translator !== null) {
            return true;
        }
        
        return false;
    }

    /**
     * Set the data source
     *
     * @param mixed $data            
     * @throws \Exception
     */
    public function setDataSource($data)
    {
        if ($data instanceof DataSource\DataSourceInterface) {
            $this->dataSource = $data;
        } elseif (is_array($data)) {
            $this->dataSource = new DataSource\PhpArray($data);
        } elseif ($data instanceof QueryBuilder) {
            $this->dataSource = new DataSource\Doctrine2($data);
        } elseif ($data instanceof ZendSelect) {
            $args = func_get_args();
            if (count($args) === 1 || (! $args[1] instanceof \Zend\Db\Adapter\Adapter && ! $args[1] instanceof \Zend\Db\Sql\Sql)) {
                throw new \InvalidArgumentException('For "Zend\Db\Sql\Select" also a "Zend\Db\Adapter\Sql" or "Zend\Db\Sql\Sql" is needed.');
            }
            $this->dataSource = new DataSource\ZendSelect($data);
            $this->dataSource->setAdapter($args[1]);
        } elseif ($data instanceof Collection) {
            $em = func_get_arg(1);
            if ($em === false || ! $em instanceof \Doctrine\ORM\EntityManager) {
                throw new \Exception('If providing a Collection, also the EntityManager is needed as a second parameter');
            }
            $this->dataSource = new DataSource\Doctrine2Collection($data);
            $this->dataSource->setEntityManager($em);
        } else {
            throw new \InvalidArgumentException('$data must implement the interface ZfcDatagrid\DataSource\DataSourceInterface');
        }
    }

    /**
     *
     * @return \ZfcDatagrid\DataSource\DataSourceInterface
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * Datasource defined?
     *
     * @return boolean
     */
    public function hasDataSource()
    {
        if ($this->dataSource !== null) {
            return true;
        }
        
        return false;
    }

    /**
     * Set default items per page (-1 for unlimited)
     *
     * @param integer $count            
     */
    public function setDefaultItemsPerPage($count = 25)
    {
        $this->defaulItemsPerPage = (int) $count;
    }

    /**
     *
     * @return integer
     */
    public function getDefaultItemsPerPage()
    {
        return (int) $this->defaulItemsPerPage;
    }

    /**
     * Set the title
     *
     * @param string $title            
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add a external parameter
     *
     * @param string $name            
     * @param mixed $value            
     */
    public function addParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     *
     * @param array $parameters            
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Has parameters?
     *
     * @return boolean
     */
    public function hasParameters()
    {
        if (count($this->getParameters()) > 0) {
            return true;
        }
        
        return false;
    }

    /**
     * Set the base url
     *
     * @param string $url            
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the export renderers (overwrite the config)
     *
     * @param array $renderers            
     */
    public function setExportRenderers(array $renderers = array())
    {
        $this->exportRenderers = $renderers;
    }

    /**
     * Get the export renderers
     *
     * @return array
     */
    public function getExportRenderers()
    {
        if ($this->exportRenderers === null) {
            $options = $this->getOptions();
            $this->exportRenderers = $options['settings']['export']['formats'];
        }
        
        return $this->exportRenderers;
    }

    /**
     * Create a column from array instanceof
     *
     * @param mixed $column            
     *
     * @return Column\AbstractColumn
     */
    private function createColumn($column)
    {
        if (is_array($column)) {
            $type = isset($column['type']) ? $column['type'] : 'Select';
            if (class_exists($type, true)) {
                $class = $type;
            } elseif (class_exists('ZfcDatagrid\\Column\\' . $type, true)) {
                $class = 'ZfcDatagrid\\Column\\' . $type;
            } else {
                throw new \Exception('Column type: "' . $type . '" not found!');
            }
            
            if ($class == 'ZfcDatagrid\\Column\\Select') {
                if (! isset($column['index'])) {
                    throw new \InvalidArgumentException('For "ZfcDatagrid\\Column\\Select" an index to select must be defined!');
                }
                $table = isset($column['table']) ? $column['table'] : null;
                $instance = new $class($column['index'], $table);
            } else {
                $instance = new $class();
            }
            
            foreach ($column as $key => $value) {
                $method = 'set' . ucfirst($key);
                if (method_exists($instance, $method)) {
                    if (in_array($key, $this->specialMethods)) {
                        if ($key == 'style') {
                            $instance->addStyle($value);
                            break;
                        }
                        $count = $this->specialMethods[$key];
                        
                        if ($count == 2) {
                            if (is_array($value) && count($value) === 2) {}
                        } else {
                            throw new \Exception('currently not supported. count arguments: "' . $count . '"');
                        }
                    }
                    
                    $instance->{$method}($value);
                    break;
                }
            }
            
            $column = $instance;
        }
        
        if (! $column instanceof Column\AbstractColumn) {
            throw new \InvalidArgumentException('addColumn supports only array or instanceof Column\AbstractColumn as a parameter');
        }
        
        return $column;
    }

    /**
     * Set all columns by an array
     *
     * @param array $columns            
     */
    public function setColumns(array $columns)
    {
        $useColumns = array();
        
        foreach ($columns as $column) {
            $col = $this->createColumn($column);
            
            $useColumns[$col->getUniqueId()] = $col;
        }
        
        $this->columns = $useColumns;
    }

    /**
     * Add a column by array config or instanceof Column\AbstractColumn
     *
     * @param Column\AbstractColumn $col            
     */
    public function addColumn($col)
    {
        $col = $this->createColumn($col);
        $this->columns[$col->getUniqueId()] = $col;
    }

    /**
     *
     * @return \ZfcDatagrid\Column\AbstractColumn[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     *
     * @param string $id            
     * @return Column\AbstractColumn null
     */
    public function getColumnByUniqueId($id)
    {
        if (isset($this->columns[$id])) {
            return $this->columns[$id];
        }
        
        return null;
    }

    /**
     *
     * @param boolean $mode            
     */
    public function setUserFilterDisabled($mode = true)
    {
        $this->isUserFilterEnabled = (bool) ! $mode;
    }

    /**
     *
     * @return boolean
     */
    public function isUserFilterEnabled()
    {
        return (bool) $this->isUserFilterEnabled;
    }

    /**
     * Set the row click action - identity will be automatically appended!
     *
     * @param Column\Action\AbstractAction $action            
     */
    public function setRowClickAction(Column\Action\AbstractAction $action)
    {
        $this->rowClickAction = $action;
    }

    /**
     *
     * @return null Column\Action\AbstractAction
     */
    public function getRowClickAction()
    {
        return $this->rowClickAction;
    }

    /**
     *
     * @return boolean
     */
    public function hasRowClickAction()
    {
        if (is_object($this->rowClickAction)) {
            return true;
        }
        
        return false;
    }

    /**
     * Overwrite the render
     * F.x.
     * if you want to directly render a PDF
     *
     * @param string $name            
     */
    public function setRenderer($name = null)
    {
        $this->forceRenderer = $name;
    }

    /**
     * Get the current renderer name
     *
     * @return string
     */
    public function getRendererName()
    {
        $options = $this->getOptions();
        $parameterName = $options['generalParameterNames']['rendererType'];
        
        if ($this->forceRenderer !== null) {
            // A special renderer was given -> use is
            $rendererName = $this->forceRenderer;
        } else {
            // DEFAULT
            if ($this->getRequest() instanceof ConsoleRequest) {
                $rendererName = $options['settings']['default']['renderer']['console'];
            } else {
                $rendererName = $options['settings']['default']['renderer']['http'];
            }
        }
        
        // From request
        if ($this->getRequest() instanceof HttpRequest && $this->getRequest()->getQuery($parameterName) != '') {
            $rendererName = $this->getRequest()->getQuery($parameterName);
        }
        
        return $rendererName;
    }

    /**
     * Return the current renderer
     *
     * @return \ZfcDatagrid\Renderer\AbstractRenderer
     */
    public function getRenderer()
    {
        if ($this->renderer === null) {
            
            $options = $this->getOptions();
            
            $rendererName = 'zfcDatagrid.renderer.' . $this->getRendererName();
            
            if ($this->getServiceLocator()->has($rendererName) === true) {
                /* @var $renderer \ZfcDatagrid\Renderer\AbstractRenderer */
                $renderer = $this->getServiceLocator()->get($rendererName);
                if (! $renderer instanceof Renderer\AbstractRenderer) {
                    throw new \Exception('Renderer service must implement "ZfcDatagrid\Renderer\AbstractRenderer"');
                }
                $renderer->setOptions($this->getOptions());
                $renderer->setMvcEvent($this->getMvcEvent());
                if ($this->getToolbarTemplate() !== null) {
                    $renderer->setToolbarTemplate($this->getToolbarTemplate());
                }
                $renderer->setViewModel($this->getViewModel());
                $renderer->setTranslator($this->getTranslator());
                $renderer->setTitle($this->getTitle());
                $renderer->setColumns($this->getColumns());
                $renderer->setCacheId($this->getCacheId());
                $renderer->setCacheData($this->getCache()
                    ->getItem($this->getCacheId()));
                
                $this->renderer = $renderer;
            } else {
                throw new \Exception('Renderer service was not found, please register it: "' . $rendererName . '"');
            }
        }
        
        return $this->renderer;
    }

    public function isDataLoaded()
    {
        return (bool) $this->isDataLoaded;
    }

    /**
     * Load the data
     */
    public function loadData()
    {
        if ($this->isDataLoaded === true) {
            return true;
        }
        
        if ($this->isInit() !== true) {
            throw new \Exception('The init() method has to be called, before you can call loadData()!');
        }
        
        if ($this->hasDataSource() === false) {
            throw new \Exception('No datasource defined! Please call "setDataSource()" first"');
        }
        
        /**
         * Apply cache
         */
        $renderer = $this->getRenderer();
        
        /**
         * Step 1) Apply needed columns + filters + sort
         * - from Request (HTML View) -> and save in cache for export
         * - or from cache (Export PDF / Excel) -> same view like HTML (without LIMIT/Pagination)
         */
        {
            /**
             * Step 1.1) Only select needed columns (performance)
             */
            $this->getDataSource()->setColumns($this->getColumns());
            
            /**
             * Step 1.2) Sorting
             */
            foreach ($renderer->getSortConditions() as $condition) {
                $this->getDataSource()->addSortCondition($condition['column'], $condition['sortDirection']);
            }
            
            /**
             * Step 1.3) Filtering
             */
            foreach ($renderer->getFilters() as $filter) {
                $this->getDataSource()->addFilter($filter);
            }
        }
        
        /**
         * Save cache
         */
        if ($renderer->isExport() === false) {
            $cacheData = array(
                'sortConditions' => $renderer->getSortConditions(),
                'filters' => $renderer->getFilters()
            );
            $success = $this->getCache()->setItem($this->getCacheId(), $cacheData);
        }
        
        /**
         * Step 2) Load the data (Paginator)
         */
        {
            $this->getDataSource()->execute();
            $paginatorAdapter = $this->getDataSource()->getPaginatorAdapter();
            
            $this->paginator = new Paginator($paginatorAdapter);
            $this->paginator->setCurrentPageNumber($renderer->getCurrentPageNumber());
            $this->paginator->setItemCountPerPage($renderer->getItemsPerPage($this->getDefaultItemsPerPage()));
            
            /* @var $currentItems \ArrayIterator */
            $data = $this->paginator->getCurrentItems();
            if (! is_array($data)) {
                if ($data instanceof \Zend\Db\ResultSet\ResultSet) {
                    $data = $data->toArray();
                } elseif ($data instanceof ArrayIterator) {
                    $data = $data->getArrayCopy();
                } else {
                    $add = '';
                    if (is_object($data))
                        $add = get_class($data);
                    else
                        $add = '[no object]';
                    
                    throw new \Exception('The paginator returned an unknow result: ' . $add . ' (allowed: \ArrayIterator or a plain php array)');
                }
            }
        }
        
        /**
         * Step 3) Format the data
         * - Translate
         * - Replace
         * - Date / time / datetime
         * - Numbers
         * - ...
         */
        $prepareData = new PrepareData($data, $this->getColumns());
        $prepareData->setColumns($this->getColumns());
        $prepareData->setTranslator($this->getTranslator());
        $prepareData->prepare();
        $this->preparedData = $prepareData->getData();
        
        $this->isDataLoaded = true;
    }

    /**
     *
     * @deprecated use render() instead!
     */
    public function execute()
    {
        if ($this->isRendered() === false) {
            $this->render();
        }
    }

    /**
     * Render the grid
     */
    public function render()
    {
        if ($this->isDataLoaded() === false) {
            $this->loadData();
        }
        
        /**
         * Step 4) Render the data to the defined output format (HTML, PDF...)
         * - Styling the values based on column (and value)
         */
        $renderer = $this->getRenderer();
        $renderer->setTitle($this->getTitle());
        $renderer->setPaginator($this->getPaginator());
        $renderer->setData($this->getPreparedData());
        $renderer->prepareViewModel($this);
        
        $this->response = $renderer->execute();
        
        $this->isRendered = true;
    }

    /**
     * Is already rendered?
     *
     * @return boolean
     */
    public function isRendered()
    {
        return (bool) $this->isRendered;
    }

    /**
     *
     * @throws \Exception
     * @return Paginator
     */
    public function getPaginator()
    {
        if ($this->paginator === null) {
            throw new \Exception('Paginator is only available after calling "loadData()"');
        }
        
        return $this->paginator;
    }

    /**
     *
     * @return array
     */
    private function getPreparedData()
    {
        return $this->preparedData;
    }

    /**
     * Set the toolbar view template
     *
     * @param unknown $name            
     */
    public function setToolbarTemplate($name)
    {
        $this->toolbarTemplate = (string) $name;
    }

    public function getToolbarTemplate()
    {
        return $this->toolbarTemplate;
    }

    /**
     * Set a custom ViewModel...generally NOT necessary!
     *
     * @param ViewModel $viewModel            
     */
    public function setViewModel(ViewModel $viewModel)
    {
        if ($this->viewModel !== null) {
            throw new \Exception('A viewModel is already set (did you already called render()?)');
        }
        
        $this->viewModel = $viewModel;
    }

    /**
     *
     * @return ViewModel
     */
    public function getViewModel()
    {
        if ($this->viewModel === null) {
            $this->viewModel = new ViewModel();
        }
        
        return $this->viewModel;
    }

    /**
     *
     * @return Ambigous <\Zend\Stdlib\ResponseInterface, \Zend\Http\Response\Stream, \Zend\View\Model\ViewModel>
     */
    public function getResponse()
    {
        if (! $this->isRendered()) {
            $this->render();
        }
        
        return $this->response;
    }

    /**
     * Is this a HTML "init" response?
     * YES: loading the HTML for the grid
     * NO: AJAX loading OR it's an export
     *
     * @return boolean
     */
    public function isHtmlInitReponse()
    {
        if (! $this->getResponse() instanceof JsonModel && ! $this->getResponse() instanceof ResponseInterface) {
            return true;
        }
        
        return false;
    }
}
