<?php
namespace ZfcDatagrid\ServiceManager;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\InitializableInterface;

/**
 * Plugin manager implementation for form elements.
 *
 * Enforces that elements retrieved are instances of ElementInterface.
 */
class DatagirdManager extends AbstractPluginManager
{

    /**
     * Default set of helpers
     *
     * @var array
     */
    protected $invokableClasses = array(
        'button' => 'Zend\Form\Element\Button'
    );

    /**
     * Don't share form elements by default
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     *
     * @param ConfigInterface $configuration            
     */
    public function __construct (ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
        
        $this->addInitializer(array(
            $this,
            'injectFactory'
        ));
    }

    /**
     * Inject the factory to any element that implements FormFactoryAwareInterface
     *
     * @param
     *            $element
     */
    public function injectFactory ($element)
    {
        if ($element instanceof FormFactoryAwareInterface) {
            $factory = $element->getFormFactory();
            $factory->setFormElementManager($this);
            
            if ($this->serviceLocator instanceof ServiceLocatorInterface && $this->serviceLocator->has('InputFilterManager')) {
                $inputFilters = $this->serviceLocator->get('InputFilterManager');
                $factory->getInputFilterFactory()->setInputFilterManager($inputFilters);
            }
        }
    }

    /**
     * Validate the plugin
     *
     * Checks that the element is an instance of ElementInterface
     *
     * @param mixed $plugin            
     * @throws Exception\InvalidElementException
     * @return void
     */
    public function validatePlugin ($plugin)
    {
        // Hook to perform various initialization, when the element is not created through the factory
        if ($plugin instanceof InitializableInterface) {
            $plugin->init();
        }
        
        if ($plugin instanceof ElementInterface) {
            return; // we're okay
        }
        
        throw new Exception\InvalidElementException(sprintf('Plugin of type %s is invalid; must implement Zend\Form\ElementInterface', (is_object($plugin) ? get_class($plugin) : gettype($plugin))));
    }
}
