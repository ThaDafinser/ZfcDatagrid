<?php
namespace ZfcDatagrid;

use ZfcDatagrid\Column\Type;
use DateTime;
use DateTimeZone;
use IntlDateFormatter;
use NumberFormatter;
use Zend\I18n\Translator\Translator;

class PrepareData
{

    private $columns = array();

    private $data = array();

    /**
     *
     * @var Translator
     */
    private $translator;

    private $isPrepared = false;

    public function setColumns (array $columns)
    {
        $this->columns = $columns;
    }

    public function getColumns ()
    {
        return $this->columns;
    }

    public function setData (array $data)
    {
        $this->data = $data;
    }

    public function setTranslator (Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getTranslator ()
    {
        return $this->translator;
    }

    public function prepare ()
    {
        $data = $this->data;
        
        foreach ($data as $key => &$row) {
            $ids = array();
            
            foreach ($this->getColumns() as $column) {
                /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                
                if (isset($row[$column->getUniqueId()]) && $column->isIdentity() === true) {
                    $ids[] = $row[$column->getUniqueId()];
                }
                
                /**
                 * Maybe the data come not from another DataSource?
                 */
                if ($column->hasDataPopulation() === true) {
                    // @todo improve the interface...
                    $dataPopulation = $column->getDataPopulation();
                    if ($dataPopulation instanceof Column\DataPopulation\Object) {
                        
                        foreach ($dataPopulation->getParameters() as $parameter) {
                            $dataPopulation->setParameterValue($parameter['objectParameterName'], $row[$parameter['column']->getUniqueId()]);
                        }
                        $row[$column->getUniqueId()] = $dataPopulation->toString();
                    } else {
                        throw new \Exception('@todo');
                    }
                }
                
                if (! isset($row[$column->getUniqueId()])) {
                    $row[$column->getUniqueId()] = '';
                }
                
                /**
                 * Replace
                 */
                if ($column->hasReplaceValues() === true) {
                    $replaceValues = $column->getReplaceValues();
                    
                    if (isset($replaceValues[$row[$column->getUniqueId()]])) {
                        $row[$column->getUniqueId()] = $replaceValues[$row[$column->getUniqueId()]];
                    } elseif ($column->notReplacedGetEmpty() === true) {
                        $row[$column->getUniqueId()] = '';
                    }
                }
                
                /**
                 * Translate
                 */
                if ($column->isTranslationEnabled() === true) {
                    $row[$column->getUniqueId()] = $this->getTranslator()->translate($row[$column->getUniqueId()]);
                }
                
                /**
                 * Date / time / datetime
                 */
                $type = $column->getType();
                if ($type instanceof Type\Date) {
                    if ($row[$column->getUniqueId()] instanceof DateTime) {
                        $date = $row[$column->getUniqueId()];
                        $date->setTimezone(new DateTimeZone($type->getSourceTimezone()));
                        $date->setTimezone(new DateTimeZone($type->getOutputTimezone()));
                    } else {
                        $date = DateTime::createFromFormat($type->getSourceDateTimeFormat(), $row[$column->getUniqueId()], new DateTimeZone($type->getSourceTimezone()));
                        $date->setTimezone(new DateTimeZone($type->getOutputTimezone()));
                    }
                    $formatter = new IntlDateFormatter($type->getLocale(), $type->getOutputDateType(), $type->getOutputTimeType(), $type->getOutputTimezone(), IntlDateFormatter::GREGORIAN, $type->getOutputPattern());
                    $row[$column->getUniqueId()] = $formatter->format($date);
                } elseif ($type instanceof Type\Number) {
                    $formatter = new NumberFormatter($type->getLocale(), $type->getFormatStyle());
                    foreach ($type->getAttributes() as $attribute) {
                        $formatter->setAttribute($attribute['attribute'], $attribute['value']);
                    }
                    
                    $row[$column->getUniqueId()] = $type->getPrefix() . $formatter->format($row[$column->getUniqueId()], $type->getFormatType()) . $type->getSuffix();
                }
            }
            
            // Concat all identity columns
            if (count($ids) > 0) {
                $data[$key]['idConcated'] = implode('~', $ids);
            }
        }
        
        $this->data = $data;
        $this->isPrepared = true;
    }

    public function getData ()
    {
        if ($this->isPrepared === false) {
            $this->prepare();
        }
        
        return $this->data;
    }
}