<?php
namespace ZfcDatagrid;

use ZfcDatagrid\Column;
use Zend\I18n\Translator\Translator;

class PrepareData
{

    /**
     *
     * @var array
     */
    private $columns = array();

    /**
     *
     * @var array
     */
    private $data = array();

    /**
     *
     * @var array null
     */
    private $dataPrepared;

    private $rendererName;

    /**
     *
     * @var Translator
     */
    private $translator;

    /**
     *
     * @param array $data            
     * @param array $columns            
     */
    public function __construct(array $data, array $columns)
    {
        $this->setData($data);
        $this->setColumns($columns);
    }

    /**
     *
     * @param array $columns            
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     *
     * @param array $data            
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     *
     * @param boolean $raw            
     * @return array
     */
    public function getData($raw = false)
    {
        if ($raw === true) {
            return $this->data;
        }
        
        $this->prepare();
        
        return $this->dataPrepared;
    }

    public function setRenderer($name = null)
    {
        $this->rendererName = $name;
    }

    public function getRendererName()
    {
        return $this->rendererName;
    }

    /**
     *
     * @param Translator $translator            
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     *
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     *
     * @throws \Exception
     * @return void
     */
    public function prepare()
    {
        if (is_array($this->dataPrepared)) {
            return;
        }
        
        $data = $this->data;
        
        foreach ($data as $key => &$row) {
            $ids = array();
            
            foreach ($this->getColumns() as $column) {
                /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                
                if (isset($row[$column->getUniqueId()]) && $column->isIdentity() === true) {
                    $ids[] = $row[$column->getUniqueId()];
                }
                
                /*
                 * Maybe the data come not from another DataSource?
                 */
                if ($column instanceof Column\ExternalData) {
                    // @todo improve the interface...
                    $dataPopulation = $column->getDataPopulation();
                    
                    foreach ($dataPopulation->getParameters() as $parameter) {
                        $dataPopulation->setParameterValue($parameter['objectParameterName'], $row[$parameter['column']->getUniqueId()]);
                    }
                    $row[$column->getUniqueId()] = $dataPopulation->toString();
                }
                
                if (! isset($row[$column->getUniqueId()])) {
                    $row[$column->getUniqueId()] = '';
                }
                
                /*
                 * Replace
                 */
                if ($column->hasReplaceValues() === true) {
                    $replaceValues = $column->getReplaceValues();
                    
                    if (is_array($row[$column->getUniqueId()])) {
                        foreach ($row[$column->getUniqueId()] as &$value) {
                            if (isset($replaceValues[$value])) {
                                $value = $replaceValues[$value];
                            } elseif ($column->notReplacedGetEmpty() === true) {
                                $value = '';
                            }
                        }
                    } else {
                        if (isset($replaceValues[$row[$column->getUniqueId()]])) {
                            $row[$column->getUniqueId()] = $replaceValues[$row[$column->getUniqueId()]];
                        } elseif ($column->notReplacedGetEmpty() === true) {
                            $row[$column->getUniqueId()] = '';
                        }
                    }
                }
                
                /*
                 * Type converting
                 */
                $row[$column->getUniqueId()] = $column->getType()->getUserValue($row[$column->getUniqueId()]);
                
                /*
                 * Translate (nach typ convertierung -> PhpArray...)
                 */
                if ($column->isTranslationEnabled() === true) {
                    if (is_array($row[$column->getUniqueId()])) {
                        foreach ($row[$column->getUniqueId()] as &$value) {
                            $value = $this->getTranslator()->translate($value);
                        }
                    } else {
                        $row[$column->getUniqueId()] = $this->getTranslator()->translate($row[$column->getUniqueId()]);
                    }
                }
                
                /*
                 * Trim the values
                 */
                if (is_array($row[$column->getUniqueId()])) {
                    array_walk_recursive($row[$column->getUniqueId()], function (&$value)
                    {
                        $value = trim($value);
                    });
                } else {
                    $row[$column->getUniqueId()] = trim($row[$column->getUniqueId()]);
                }
                
                /*
                 * Custom formatter
                 */
                if ($column->hasFormatter($this->getRendererName()) === true) {
                    /* @var $formatter \ZfcDatagrid\Column\Formatter\AbstractFormatter */
                    $formatter = $column->getFormatter($this->getRendererName());
                    $formatter->setColumns($this->getColumns());
                    $formatter->setRowData($row);
                    $formatter->setRenderer($this->getRendererName());
                    
                    $row[$column->getUniqueId()] = $formatter->format($column);
                }
            }
            
            // Concat all identity columns
            if (count($ids) > 0) {
                $data[$key]['idConcated'] = implode('~', $ids);
            }
        }
        
        $this->dataPrepared = $data;
    }
}
