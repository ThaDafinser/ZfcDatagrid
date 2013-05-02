<?php
namespace ZfcDatagrid;

use ZfcDatagrid\DataSource;
use ZfcDatagrid\Renderer;
use Zend\Mvc\MvcEvent;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Console\Request as ConsoleRequest;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\I18n\Translator\Translator;
use Doctrine\ORM\QueryBuilder;
use ArrayIterator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Cache;
use Zend\Session\Container as SessionContainer;
use Zend\Db\Sql\Select as ZendSelect;

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
     * @var Cache\Storage\StorageInterface
     */
    protected $cache;

    /**
     *
     * @var SessionContainer
     */
    protected $session;

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
     * @var Translator
     */
    protected $translator;

    protected $gridId;
    
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

    protected $itemsPerPage = 25;

    /**
     *
     * @var array
     */
    protected $columns = array();

    /**
     * 
     * @var string
     */
    protected $rowClickLink = '#';
    
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
    protected $sortConditions = array();

    /**
     *
     * @var boolean
     */
    protected $isUserSortActive = false;

    /**
     *
     * @var Paginator
     */
    protected $paginator = null;

    /**
     *
     * @var ViewModel
     */
    protected $viewModel = null;

    protected $isInit = false;

    protected $isExecuted = false;

    protected $forceRenderer = null;

    public function init ()
    {
        if ($this->getCache() === null) {
            $this->cache = Cache\StorageFactory::factory($this->getOptions()['cache']);
        }
        
        $this->isInit = true;
    }

    public function isInit ()
    {
        return (bool) $this->isInit;
    }

    public function setOptions (array $config)
    {
        $this->options = $config;
    }

    /**
     *
     * @return array
     */
    public function getOptions ()
    {
        return $this->options;
    }
    
    public function setGridId($id = null){
        if($id !== null){
            $this->gridId = (string)$id;
        }
    }
    public function getGridId(){
        if($this->gridId === null){
            $this->gridId = 'defaultGrid';
        }
        
        return $this->gridId;
    }

    public function setSession (SessionContainer $session)
    {
        $this->session = $session;
        if ($this->hash) {
            $this->initCsrfToken();
        }
        return $this;
    }

    /**
     * Get session container
     *
     * Instantiate session container if none currently exists
     *
     * @return SessionContainer
     */
    public function getSession ()
    {
        if (null === $this->session) {
            // Using fully qualified name, to ensure polyfill class alias is used
            $this->session = new SessionContainer($this->getGridId());
        }
        
        return $this->session;
    }

    public function setCache (Cache\Storage\StorageInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     *
     * @return Cache\Storage\StorageInterface
     */
    public function getCache ()
    {
        return $this->cache;
    }

    public function setCacheId ($id)
    {
        $this->cacheId = (string) $id;
    }

    public function getCacheId ()
    {
        if ($this->cacheId === null) {
            $this->cacheId = $this->getSession()->getManager()->getId().'_'.$this->getGridId();
        }
        
        return $this->cacheId;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator            
     */
    public function setServiceLocator (ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator ()
    {
        return $this->serviceLocator;
    }

    public function setMvcEvent (MvcEvent $mvcEvent)
    {
        $this->mvcEvent = $mvcEvent;
        $this->request = $mvcEvent->getRequest();
    }

    /**
     *
     * @return MvcEvent
     */
    public function getMvcEvent ()
    {
        return $this->mvcEvent;
    }

    /**
     *
     * @return HttpRequest
     */
    public function getRequest ()
    {
        return $this->request;
    }

    /**
     * Set the translator
     *
     * @param Translator $translator            
     */
    public function setTranslator (Translator $translator = null)
    {
        $this->translator = $translator;
    }

    /**
     *
     * @return Translator
     */
    public function getTranslator ()
    {
        return $this->translator;
    }

    /**
     *
     * @return boolean
     */
    public function hasTranslator ()
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
    public function setDataSource ($data)
    {
        if ($data instanceof DataSource\DataSourceInterface) {
            $this->dataSource = $data;
        } elseif ($data instanceof QueryBuilder) {
            $this->dataSource = new DataSource\Doctrine2($data);
        } elseif (is_array($data)) {
            $this->dataSource = new DataSource\PhpArray($data);
        } elseif ($data instanceof ZendSelect) {
            $args = func_get_args();
            if(count($args) === 1 || (!$args[1] instanceof \Zend\Db\Adapter\Adapter && !$args[1] instanceof \Zend\Db\Sql\Sql)){
                throw new \Exception('The $adapterOrSqlObject is missing');
            }
            $this->dataSource = new DataSource\ZendSelect($data);
            $this->dataSource->setAdapter($args[1]);
        } else {
            throw new \Exception('$data must implement the interface ZfcDatagrid\DataSource\DataSourceInterface');
        }
    }

    /**
     *
     * @return \ZfcDatagrid\DataSource\DataSourceInterface
     */
    public function getDataSource ()
    {
        return $this->dataSource;
    }

    public function hasDataSource ()
    {
        if ($this->dataSource !== null) {
            return true;
        }
        
        return false;
    }

    /**
     * Set items per page (-1 for unlimited)
     * @param integer $count
     */
    public function setItemsPerPage ($count = 25)
    {
        $this->itemsPerPage = (int) $count;
    }

    /**
     *
     * @return integer
     */
    public function getItemsPerPage ()
    {
        return (int) $this->itemsPerPage;
    }

    /**
     * Set the title
     *
     * @param string $title            
     */
    public function setTitle ($title)
    {
        $this->title = (string) $title;
    }

    public function getTitle ()
    {
        return $this->title;
    }

    /**
     * Add a column
     *
     * @param Column\AbstractColumn $col            
     */
    public function addColumn (Column\AbstractColumn $col)
    {
        $this->columns[] = $col;
    }

    public function getColumns ()
    {
        return $this->columns;
    }

    /**
     * Set active sortBy (from cache f.x.)
     *
     * @param array $sortBy            
     */
    private function setSortConditions (array $sortBy)
    {
        $this->sortConditions = $sortBy;
    }

    /**
     *
     * @return array
     */
    public function getSortConditions ()
    {
        if (count($this->sortConditions) > 0) {
            // set from cache! (for export)
            return $this->sortConditions;
        }
        
        $sortConditions = array();
        {
            // @todo Check for GET/POST/..
            $parameters = $this->getOptions()['parameters'];
            
            if ($this->getRequest() instanceof HttpRequest && $this->getRequest()->getQuery($parameters['sortColumn']) != '') {
                $sortColumn = $this->getRequest()->getQuery($parameters['sortColumn']);
                $sortDirection = $this->getRequest()->getQuery($parameters['sortDirection'], 'ASC');
                if ($sortDirection != 'ASC' && $sortDirection != 'DESC') {
                    $sortDirection = 'ASC';
                }
                
                foreach ($this->getColumns() as $column) {
                    /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                    if ($column->getUniqueId() == $sortColumn) {
                        $sortConditions[1] = array(
                            'sortDirection' => $sortDirection,
                            'column' => $column
                        );
                        
                        $column->setSortActive(true, $sortDirection);
                        
                        $this->setUserSortActive(true);
                    }
                }
            }
        }
        
        if (count($sortConditions) === 0) {
            foreach ($this->getColumns() as $column) {
                /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                if ($column->hasSortDefaults() === true) {
                    $sortDefaults = $column->getSortDefaults();
                    
                    $sortConditions[$sortDefaults['priority']] = array(
                        'sortDirection' => $sortDefaults['sortDirection'],
                        'column' => $column
                    );
                    
                    $column->setSortActive(true, $sortDefaults['sortDirection']);
                }
            }
            
            ksort($sortConditions);
        }
        
        return $sortConditions;
    }

    public function setUserSortActive ($mode = true)
    {
        $this->isUserSortActive = (bool) $mode;
    }

    public function isUserSortActive ()
    {
        return $this->isUserSortActive;
    }

    /**
     * Set the row click link - identity will be automatically appended!
     * 
     * @param string $link
     */
    public function setRowClickLink($link = '#'){
        $this->rowClickLink = (string)$link;
    }
    
    public function getRowClickLink(){
        return $this->rowClickLink;
    }
    
    /**
     *
     * @return array
     */
    private function getPreparedData ()
    {
        return $this->preparedData;
    }

    /**
     * Overwrite the render
     * F.x.
     * if you want to directly render a PDF
     *
     * @param string $name            
     */
    public function setRenderer ($name = null)
    {
        $this->forceRenderer = $name;
    }

    /**
     * Return the current renderer (PDF / Excel
     *
     * @return Renderer\RendererInterface
     */
    public function getRenderer ()
    {
        $options = $this->getOptions();
        $parameterName = $options['parameters']['rendererType'];
        
        if ($this->getRequest() instanceof ConsoleRequest) {
            $rendererName = $options['defaults']['renderer']['console'];
        } else {
            $rendererName = $options['defaults']['renderer']['http'];
        }
        if ($this->forceRenderer !== null) {
            $rendererName = $this->forceRenderer;
        } elseif ($this->getRequest() instanceof HttpRequest && $this->getRequest()->getQuery($parameterName) != '') {
            $rendererName = $this->getRequest()->getQuery($parameterName);
        }
        
        $rendererName = 'zfcDatagrid.renderer.' . $rendererName;
        
        if ($this->getServiceLocator()->has($rendererName) === true) {
            /* @var $renderer \Zend\Paginator\Paginator */
            $renderer = $this->getServiceLocator()->get($rendererName);
            if (! $renderer instanceof Renderer\AbstractRenderer) {
                throw new \Exception('Renderer service must implement "ZfcDatagrid\Renderer\AbstractRenderer"');
            }
            $renderer->setOptions($this->getOptions());
            $renderer->setMvcEvent($this->getMvcEvent());
            $renderer->setViewModel($this->getViewModel());
            $renderer->setTranslator($this->getTranslator());
            $renderer->setTitle($this->getTitle());
            
            return $renderer;
        } else {
            throw new \Exception('Renderer service was not found, please register it: "' . $rendererName . '"');
        }
    }

    /**
     * Prepare all variables for the view
     * - title
     * - data
     * - grid
     * - ...
     */
    public function execute ()
    {
        if ($this->isInit() !== true) {
            throw new \Exception('The init() method has to be called, before you can call execute()!');
        }
        
        if ($this->hasDataSource() === false) {
            throw new \Exception('No datasource defined! So no grid to display...');
        }
        
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
             * Step 1.2) Read cahe
             *
             * ONLY FOR EXPORT!
             */
            if ($renderer->isExport() === true) {
                $data = $this->getCache()->getItem($this->getCacheId(), $success);
                if (! $success) {
                    throw new \Exception('Cache for export is missing...');
                }
                $data = unserialize($data);
                
                $this->setSortConditions($data['sortConditions']);
            }
            
            /**
             * Step 1.3) Sorting
             */
            foreach ($this->getSortConditions() as $condition) {
                $this->getDataSource()->addSortCondition($condition['column'], $condition['sortDirection']);
            }
            
            /**
             * Step 1.4: Filtering
             */
            $this->getDataSource()->addFilter();
            
            /**
             * Step 1.5) Save cahe
             */
            if ($renderer->isExport() === false) {
                $data = array(
                    'sortConditions' => $this->getSortConditions()
                );
                $this->getCache()->setItem($this->getCacheId(), serialize($data));
            }
        }
        
        /**
         * Step 2) Load the data (Paginator)
         */
        {
            $this->getDataSource()->execute();
            $paginatorAdapter = $this->getDataSource()->getPaginatorAdapter();
            
            $this->paginator = new Paginator($paginatorAdapter);
            if ($renderer->isExport() === true) {
                // Export always all pages
                $this->paginator->setItemCountPerPage(- 1);
            } else {
                $this->paginator->setItemCountPerPage($this->getItemsPerPage());
            }
            $this->paginator->setCurrentPageNumber($this->getCurrentPage());
            
            $this->paginator->getCurrentItemCount();
            $this->paginator->getTotalItemCount();
            
            /* @var $currentItems \ArrayIterator */
            $data = $this->paginator->getCurrentItems();
            if (! is_array($data)) {
                if ($data instanceof \Zend\Db\ResultSet\ResultSet) {
                    $data = $data->toArray();
                }
                elseif ($data instanceof ArrayIterator) {
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
        $prepareData = new PrepareData();
        $prepareData->setColumns($this->getColumns());
        $prepareData->setData($data);
        $prepareData->setTranslator($this->getTranslator());
        $prepareData->prepare();
        $this->preparedData = $prepareData->getData();
        
        /**
         * Step 4) Render the data to the defined output format (HTML, PDF...)
         * - Styling the values based on column (and value)
         */
        $renderer->setColumns($this->getColumns());
        $renderer->setPaginator($this->getPaginator());
        $renderer->setData($this->getPreparedData());
        $renderer->prepareViewModel($this);
        
        $this->response = $renderer->execute();
    }

    public function isExecuted ()
    {
        return (bool) $this->isExecuted;
    }

    /**
     *
     * @throws \Exception
     * @return Paginator
     */
    public function getPaginator ()
    {
        if ($this->paginator === null) {
            throw new \Exception('Paginator is only available, after the grid has been executed!');
        }
        
        return $this->paginator;
    }

    public function getCurrentPage ()
    {
        $parameterName = $this->getOptions()['parameters']['currentPage'];
        if ($this->getRequest() instanceof HttpRequest) {
            return $this->getRequest()->getQuery($parameterName, 1);
        } else {
            return $this->getRequest()->getParam($parameterName, 1);
        }
        // return $this->getMvcEvent()->getRouteMatch()->getParam($parameterName, 1);
    }

    /**
     * Set a custom ViewModel...generally NOT necessary!
     *
     * @param ViewModel $viewModel            
     */
    public function setViewModel (ViewModel $viewModel = null)
    {
        if ($this->viewModel !== null) {
            throw new \Exception('A viewModel is already set (did you already called execute()?)');
        }
        
        if ($viewModel !== null) {
            $this->viewModel = $viewModel;
        }
    }

    /**
     *
     * @return ViewModel
     */
    public function getViewModel ()
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
    public function getResponse ()
    {
        return $this->response;
    }
}
