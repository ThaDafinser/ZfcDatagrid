<?php

namespace ZfcDatagrid;

use ArrayIterator;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Interop\Container\ContainerInterface;
use Zend\Cache;
use Zend\Console\Request as ConsoleRequest;
use Zend\Db\Sql\Select as ZendSelect;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\MvcEvent;
use Zend\Paginator\Paginator;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container as SessionContainer;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use ZfcDatagrid\Column\Style;

class Datagrid
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var SessionContainer
     */
    protected $session;

    /**
     * @var Cache\Storage\StorageInterface
     */
    protected $cache;

    /**
     * @var string
     */
    protected $cacheId;

    /**
     * @var MvcEvent
     */
    protected $mvcEvent;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var mixed
     */
    protected $url;

    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * View or Response.
     *
     * @var \Zend\Http\Response\Stream|\Zend\View\Model\ViewModel
     */
    protected $response;

    /**
     * @var Renderer\AbstractRenderer
     */
    private $renderer;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var string
     */
    protected $id;

    /**
     * The grid title.
     *
     * @var string
     */
    protected $title = '';

    /**
     * @var DataSource\DataSourceInterface
     */
    protected $dataSource = null;

    /**
     * @var int
     */
    protected $defaulItemsPerPage = 25;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var Style\AbstractStyle[]
     */
    protected $rowStyles = [];

    /**
     * @var Column\Action\AbstractAction
     */
    protected $rowClickAction;

    /**
     * @var Action\Mass
     */
    protected $massActions = [];

    /**
     * The prepared data.
     *
     * @var array
     */
    protected $preparedData = [];

    /**
     * @var array
     */
    protected $isUserFilterEnabled = true;

    /**
     * @var Paginator
     */
    protected $paginator = null;

    /**
     * @var array
     */
    protected $exportRenderers;

    /**
     * @var string|null
     */
    protected $toolbarTemplate;

    /**
     * @var array
     */
    protected $toolbarTemplateVariables = [];

    /**
     * @var ViewModel
     */
    protected $viewModel;

    /**
     * @var bool
     */
    protected $isInit = false;

    /**
     * @var bool
     */
    protected $isDataLoaded = false;

    /**
     * @var bool
     */
    protected $isRendered = false;

    /**
     * @var string
     */
    protected $forceRenderer;

    /**
     * @var Renderer\AbstractRenderer
     */
    private $rendererService;

    /**
     * @var array
     */
    private $specialMethods = [
        'filterSelectOptions',
        'rendererParameter',
        'replaceValues',
        'select',
        'sortDefault',
    ];

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    /**
     * Init method is called automatically with the service creation.
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
     * @return bool
     */
    public function isInit()
    {
        return (bool) $this->isInit;
    }

    /**
     * Set the options from config.
     *
     * @param array $config
     */
    public function setOptions(array $config)
    {
        $this->options = $config;
    }

    /**
     * Get the config options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the grid id.
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
     * Get the grid id.
     *
     * @return string
     */
    public function getId()
    {
        if (null === $this->id) {
            $this->id = 'defaultGrid';
        }

        return $this->id;
    }

    /**
     * Set the session.
     *
     * @param \Zend\Session\Container $session
     */
    public function setSession(SessionContainer $session)
    {
        $this->session = $session;
    }

    /**
     * Get session container.
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

    /**
     * @param Cache\Storage\StorageInterface $cache
     */
    public function setCache(Cache\Storage\StorageInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return Cache\Storage\StorageInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set the cache id.
     *
     * @param string $id
     */
    public function setCacheId($id)
    {
        $this->cacheId = (string) $id;
    }

    /**
     * Get the cache id.
     *
     * @return string
     */
    public function getCacheId()
    {
        if (null === $this->cacheId) {
            $this->cacheId = md5($this->getSession()
                ->getManager()
                ->getId().'_'.$this->getId());
        }

        return $this->cacheId;
    }

    /**
     * @param MvcEvent $mvcEvent
     */
    public function setMvcEvent(MvcEvent $mvcEvent)
    {
        $this->mvcEvent = $mvcEvent;
        $this->request = $mvcEvent->getRequest();
    }

    /**
     * @return MvcEvent
     */
    public function getMvcEvent()
    {
        return $this->mvcEvent;
    }

    /**
     * @return HttpRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set the translator.
     *
     * @param Translator $translator
     *
     * @throws \InvalidArgumentException
     */
    public function setTranslator($translator = null)
    {
        if (!$translator instanceof Translator && !$translator instanceof \Zend\I18n\Translator\TranslatorInterface) {
            throw new \InvalidArgumentException('Translator must be an instanceof "Zend\I18n\Translator\Translator" or "Zend\I18n\Translator\TranslatorInterface"');
        }

        $this->translator = $translator;
    }

    /**
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return bool
     */
    public function hasTranslator()
    {
        if ($this->translator !== null) {
            return true;
        }

        return false;
    }

    /**
     * Set the data source.
     *
     * @param mixed $data
     *
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
            if (count($args) === 1 || (!$args[1] instanceof \Zend\Db\Adapter\Adapter && !$args[1] instanceof \Zend\Db\Sql\Sql)) {
                throw new \InvalidArgumentException('For "Zend\Db\Sql\Select" also a "Zend\Db\Adapter\Sql" or "Zend\Db\Sql\Sql" is needed.');
            }
            $this->dataSource = new DataSource\ZendSelect($data);
            $this->dataSource->setAdapter($args[1]);
        } elseif ($data instanceof Collection) {
            $args = func_get_args();
            if (count($args) === 1 || !$args[1] instanceof \Doctrine\ORM\EntityManager) {
                throw new \InvalidArgumentException('If providing a Collection, also the Doctrine\ORM\EntityManager is needed as a second parameter');
            }
            $this->dataSource = new DataSource\Doctrine2Collection($data);
            $this->dataSource->setEntityManager($args[1]);
        } else {
            throw new \InvalidArgumentException('$data must implement the interface ZfcDatagrid\DataSource\DataSourceInterface');
        }
    }

    /**
     * @return \ZfcDatagrid\DataSource\DataSourceInterface
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * Datasource defined?
     *
     * @return bool
     */
    public function hasDataSource()
    {
        if ($this->dataSource !== null) {
            return true;
        }

        return false;
    }

    /**
     * Set default items per page (-1 for unlimited).
     *
     * @param int $count
     */
    public function setDefaultItemsPerPage($count = 25)
    {
        $this->defaulItemsPerPage = (int) $count;
    }

    /**
     * @return int
     */
    public function getDefaultItemsPerPage()
    {
        return (int) $this->defaulItemsPerPage;
    }

    /**
     * Set the title.
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add a external parameter.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function addParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * These parameters are handled to the view + over all grid actions.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Has parameters?
     *
     * @return bool
     */
    public function hasParameters()
    {
        return (bool) $this->getParameters();
    }

    /**
     * Set the base url.
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the export renderers (overwrite the config).
     *
     * @param array $renderers
     */
    public function setExportRenderers(array $renderers = [])
    {
        $this->exportRenderers = $renderers;
    }

    /**
     * Get the export renderers.
     *
     * @return array
     */
    public function getExportRenderers()
    {
        if (null === $this->exportRenderers) {
            $options = $this->getOptions();
            $this->exportRenderers = $options['settings']['export']['formats'];
        }

        return $this->exportRenderers;
    }

    /**
     * Create a column from array instanceof.
     *
     * @param array $config
     *
     * @return Column\AbstractColumn
     */
    private function createColumn($config)
    {
        if ($config instanceof Column\AbstractColumn) {
            return $config;
        }

        if (!is_array($config) && !$config instanceof Column\AbstractColumn) {
            throw new \InvalidArgumentException('createColumn() supports only a config array or instanceof Column\AbstractColumn as a parameter');
        }

        $colType = isset($config['colType']) ? $config['colType'] : 'Select';
        if (class_exists($colType, true)) {
            $class = $colType;
        } elseif (class_exists('ZfcDatagrid\\Column\\'.$colType, true)) {
            $class = 'ZfcDatagrid\\Column\\'.$colType;
        } else {
            throw new \InvalidArgumentException(sprintf('Column type: "%s" not found!', $colType));
        }

        if ('ZfcDatagrid\\Column\\Select' == $class) {
            if (!isset($config['select']['column'])) {
                throw new \InvalidArgumentException('For "ZfcDatagrid\Column\Select" the option select[column] must be defined!');
            }
            $table = isset($config['select']['table']) ? $config['select']['table'] : null;

            $instance = new $class($config['select']['column'], $table);
        } else {
            $instance = new $class();
        }

        foreach ($config as $key => $value) {
            $method = 'set'.ucfirst($key);
            if (method_exists($instance, $method)) {
                if (in_array($key, $this->specialMethods)) {
                    if (!is_array($value)) {
                        $value = [
                            $value,
                        ];
                    }
                    call_user_func_array([
                        $instance,
                        $method,
                    ], $value);
                } else {
                    call_user_func([
                        $instance,
                        $method,
                    ], $value);
                }
            }
        }

        return $instance;
    }

    /**
     * Set multiple columns by array (willoverwrite all existing).
     *
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        $useColumns = [];

        foreach ($columns as $col) {
            $col = $this->createColumn($col);
            $useColumns[$col->getUniqueId()] = $col;
        }

        $this->columns = $useColumns;
    }

    /**
     * Add a column by array config or instanceof Column\AbstractColumn.
     *
     * @param array|Column\AbstractColumn $col
     */
    public function addColumn($col)
    {
        $col = $this->createColumn($col);
        $this->columns[$col->getUniqueId()] = $col;
    }

    /**
     * @return \ZfcDatagrid\Column\AbstractColumn[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param string $id
     *
     * @return Column\AbstractColumn null
     */
    public function getColumnByUniqueId($id)
    {
        if (isset($this->columns[$id])) {
            return $this->columns[$id];
        }

        return;
    }

    /**
     * @param Style\AbstractStyle $style
     */
    public function addRowStyle(Style\AbstractStyle $style)
    {
        $this->rowStyles[] = $style;
    }

    /**
     * @return Style\AbstractStyle[]
     */
    public function getRowStyles()
    {
        return $this->rowStyles;
    }

    /**
     * @return bool
     */
    public function hasRowStyles()
    {
        return (bool) $this->rowStyles;
    }

    /**
     * If disabled, the toolbar filter will not be shown to the user.
     *
     * @param bool $mode
     */
    public function setUserFilterDisabled($mode = true)
    {
        $this->isUserFilterEnabled = (bool) !$mode;
    }

    /**
     * @return bool
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

    public function getRowClickAction()
    {
        return $this->rowClickAction;
    }

    /**
     * @return bool
     */
    public function hasRowClickAction()
    {
        if (is_object($this->rowClickAction)) {
            return true;
        }

        return false;
    }

    /**
     * Add a mass action.
     *
     * @param Action\Mass $action
     */
    public function addMassAction(Action\Mass $action)
    {
        $this->massActions[] = $action;
    }

    /**
     * @return Action\Mass[]
     */
    public function getMassActions()
    {
        return $this->massActions;
    }

    /**
     * @return bool
     */
    public function hasMassAction()
    {
        return (bool) $this->massActions;
    }

    /**
     * Overwrite the render
     * F.x.
     * if you want to directly render a PDF.
     *
     * @param string $name
     */
    public function setRendererName($name = null)
    {
        $this->forceRenderer = $name;
    }

    /**
     * Get the current renderer name.
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
     * Return the current renderer.
     *
     * @throws \Exception
     *
     * @return \ZfcDatagrid\Renderer\AbstractRenderer
     */
    public function getRenderer()
    {
        if (null === $this->renderer) {
            if (isset($this->rendererService)) {
                $renderer = $this->rendererService;
                if (!$renderer instanceof Renderer\AbstractRenderer) {
                    throw new \Exception('Renderer service must implement "ZfcDatagrid\Renderer\AbstractRenderer"');
                }
                $renderer->setOptions($this->getOptions());
                $renderer->setMvcEvent($this->getMvcEvent());
                if ($this->getToolbarTemplate() !== null) {
                    $renderer->setToolbarTemplate($this->getToolbarTemplate());
                }
                $renderer->setToolbarTemplateVariables($this->getToolbarTemplateVariables());
                $renderer->setViewModel($this->getViewModel());
                if ($this->hasTranslator()) {
                    $renderer->setTranslator($this->getTranslator());
                }
                $renderer->setTitle($this->getTitle());
                $renderer->setColumns($this->getColumns());
                $renderer->setRowStyles($this->getRowStyles());
                $renderer->setCache($this->getCache());
                $renderer->setCacheId($this->getCacheId());

                $this->renderer = $renderer;
            } else {
                throw new \Exception(sprintf('Renderer service was not found, please register it: "zfcDatagrid.renderer.%s"', $this->getRendererName()));
            }
        }

        return $this->renderer;
    }

    /**
     * @return bool
     */
    public function isDataLoaded()
    {
        return (bool) $this->isDataLoaded;
    }

    /**
     * Load the data.
     */
    public function loadData()
    {
        if (true === $this->isDataLoaded) {
            return true;
        }

        if ($this->isInit() !== true) {
            throw new \Exception('The init() method has to be called, before you can call loadData()!');
        }

        if ($this->hasDataSource() === false) {
            throw new \Exception('No datasource defined! Please call "setDataSource()" first"');
        }

        /**
         * Apply cache.
         */
        $renderer = $this->getRenderer();

        /*
         * Step 1) Apply needed columns + filters + sort
         * - from Request (HTML View) -> and save in cache for export
         * - or from cache (Export PDF / Excel) -> same view like HTML (without LIMIT/Pagination)
         */
        {
            /*
             * Step 1.1) Only select needed columns (performance)
             */
            $this->getDataSource()->setColumns($this->getColumns());

            /*
             * Step 1.2) Sorting
             */
            foreach ($renderer->getSortConditions() as $condition) {
                $this->getDataSource()->addSortCondition($condition['column'], $condition['sortDirection']);
            }

            /*
             * Step 1.3) Filtering
             */
            foreach ($renderer->getFilters() as $filter) {
                $this->getDataSource()->addFilter($filter);
            }
        }

        /*
         * Step 2) Load the data (Paginator)
         */
        {
            $this->getDataSource()->execute();
            $paginatorAdapter = $this->getDataSource()->getPaginatorAdapter();

            \Zend\Paginator\Paginator::setDefaultScrollingStyle('Sliding');

            $this->paginator = new Paginator($paginatorAdapter);
            $this->paginator->setCurrentPageNumber($renderer->getCurrentPageNumber());
            $this->paginator->setItemCountPerPage($renderer->getItemsPerPage($this->getDefaultItemsPerPage()));

            /* @var $currentItems \ArrayIterator */
            $data = $this->paginator->getCurrentItems();
            if (!is_array($data)) {
                if ($data instanceof \Zend\Db\ResultSet\ResultSet) {
                    $data = $data->toArray();
                } elseif ($data instanceof ArrayIterator) {
                    $data = $data->getArrayCopy();
                } else {
                    if (is_object($data)) {
                        $add = get_class($data);
                    } else {
                        $add = '[no object]';
                    }
                    throw new \Exception(
                        sprintf('The paginator returned an unknown result: %s (allowed: \ArrayIterator or a plain php array)', $add)
                    );
                }
            }
        }

        /*
         * check if the export is enabled
         * Save cache
         */
        if ($this->getOptions()['settings']['export']['enabled'] && $renderer->isExport() === false) {
            $cacheData = [
                'sortConditions' => $renderer->getSortConditions(),
                'filters' => $renderer->getFilters(),
                'currentPage' => $this->getPaginator()->getCurrentPageNumber(),
            ];
            $success = $this->getCache()->setItem($this->getCacheId(), $cacheData);
            if ($success !== true) {
                /** @var \Zend\Cache\Storage\Adapter\FilesystemOptions $options */
                $options = $this->getCache()->getOptions();
                throw new \Exception(
                    sprintf(
                        'Could not save the datagrid cache. Does the directory "%s" exists and is writeable? CacheId: %s',
                        $options->getCacheDir(),
                        $this->getCacheId()
                    )
                );
            }
        }

        /*
         * Step 3) Format the data - Translate - Replace - Date / time / datetime - Numbers - ...
         */
        $prepareData = new PrepareData($data, $this->getColumns());
        $prepareData->setRendererName($this->getRendererName());
        if ($this->hasTranslator()) {
            $prepareData->setTranslator($this->getTranslator());
        }
        $prepareData->prepare();
        $this->preparedData = $prepareData->getData();

        $this->isDataLoaded = true;
    }

    /**
     * Render the grid.
     */
    public function render()
    {
        if ($this->isDataLoaded() === false) {
            $this->loadData();
        }

        /**
         * Step 4) Render the data to the defined output format (HTML, PDF...)
         * - Styling the values based on column (and value).
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
     * @return bool
     */
    public function isRendered()
    {
        return (bool) $this->isRendered;
    }

    /**
     * @throws \Exception
     *
     * @return Paginator
     */
    public function getPaginator()
    {
        if (null === $this->paginator) {
            throw new \Exception('Paginator is only available after calling "loadData()"');
        }

        return $this->paginator;
    }

    /**
     * @return array
     */
    private function getPreparedData()
    {
        return $this->preparedData;
    }

    /**
     * Set the toolbar view template.
     *
     * @param string $name
     */
    public function setToolbarTemplate($name)
    {
        $this->toolbarTemplate = (string) $name;
    }

    /**
     * Get the toolbar template name
     * Return null if nothing custom set.
     *
     * @return string|null
     */
    public function getToolbarTemplate()
    {
        return $this->toolbarTemplate;
    }

    /**
     * Set the toolbar view template variables.
     *
     * @param array $variables
     */
    public function setToolbarTemplateVariables(array $variables)
    {
        $this->toolbarTemplateVariables = $variables;
    }

    /**
     * Get the toolbar template variables.
     *
     * @return array
     */
    public function getToolbarTemplateVariables()
    {
        return $this->toolbarTemplateVariables;
    }

    /**
     * Set a custom ViewModel...generally NOT necessary!
     *
     * @param ViewModel $viewModel
     *
     * @throws \Exception
     */
    public function setViewModel(ViewModel $viewModel)
    {
        if ($this->viewModel !== null) {
            throw new \Exception('A viewModel is already set. Did you already called $grid->render() or $grid->getViewModel() before?');
        }

        $this->viewModel = $viewModel;
    }

    /**
     * @return ViewModel
     */
    public function getViewModel()
    {
        if (null === $this->viewModel) {
            $this->viewModel = new ViewModel();
        }

        return $this->viewModel;
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface|\Zend\Http\Response\Stream|\Zend\View\Model\ViewModel
     */
    public function getResponse()
    {
        if (!$this->isRendered()) {
            $this->render();
        }

        return $this->response;
    }

    /**
     * Is this a HTML "init" response?
     * YES: loading the HTML for the grid
     * NO: AJAX loading OR it's an export.
     *
     * @return bool
     */
    public function isHtmlInitReponse()
    {
        if (!$this->getResponse() instanceof JsonModel && !$this->getResponse() instanceof ResponseInterface) {
            return true;
        }

        return false;
    }

    /**
     * @param Renderer\AbstractRenderer $rendererService
     *
     * @return self
     */
    public function setRendererService(Renderer\AbstractRenderer $rendererService)
    {
        $this->rendererService = $rendererService;

        return $this;
    }

    /**
     * Set service locator.
     *
     * @param ContainerInterface $serviceLocator
     *
     * @return mixed
     */
    public function setServiceLocator(ContainerInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get service locator.
     *
     * @return ContainerInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
