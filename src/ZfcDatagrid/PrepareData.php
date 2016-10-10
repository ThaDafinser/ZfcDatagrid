<?php

namespace ZfcDatagrid;

use Zend\I18n\Translator\Translator;

class PrepareData
{
    /**
     * @var array
     */
    private $columns = [];

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array null
     */
    private $dataPrepared;

    private $rendererName;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @param array $data
     * @param array $columns
     */
    public function __construct(array $data, array $columns)
    {
        $this->setData($data);
        $this->setColumns($columns);
    }

    /**
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param bool $raw
     *
     * @return array
     */
    public function getData($raw = false)
    {
        if (true === $raw) {
            return $this->data;
        }

        $this->prepare();

        return $this->dataPrepared;
    }

    /**
     * @param string $name
     */
    public function setRendererName($name = null)
    {
        $this->rendererName = $name;
    }

    /**
     * @return string
     */
    public function getRendererName()
    {
        return $this->rendererName;
    }

    /**
     * @param Translator $translator
     *
     * @throws \InvalidArgumentException
     */
    public function setTranslator($translator)
    {
        if (!$translator instanceof Translator && !$translator instanceof \Zend\I18n\Translator\TranslatorInterface) {
            throw new \InvalidArgumentException('Translator must be an instanceof "Zend\I18n\Translator\Translator" or "Zend\I18n\Translator\TranslatorInterface"');
        }

        $this->translator = $translator;
    }

    /**
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Return true if preparing executed, false if already done!
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function prepare()
    {
        if (is_array($this->dataPrepared)) {
            return false;
        }

        $data = $this->data;

        foreach ($data as $key => &$row) {
            $row = (array) $row;

            $ids = [];

            foreach ($this->getColumns() as $col) {
                /* @var $col \ZfcDatagrid\Column\AbstractColumn */

                if (isset($row[$col->getUniqueId()]) && $col->isIdentity() === true) {
                    $ids[] = $row[$col->getUniqueId()];
                }

                /*
                 * Maybe the data come not from another DataSource?
                 */
                if ($col instanceof Column\ExternalData) {
                    /* @var $col \ZfcDatagrid\Column\ExternalData */
                    // @todo improve the interface...
                    $dataPopulation = $col->getDataPopulation();

                    foreach ($dataPopulation->getObjectParametersColumn() as $parameter) {
                        $dataPopulation->setObjectParameter($parameter['objectParameterName'], $row[$parameter['column']->getUniqueId()]);
                    }
                    $row[$col->getUniqueId()] = $dataPopulation->toString();
                }

                if (!isset($row[$col->getUniqueId()])) {
                    $row[$col->getUniqueId()] = '';
                }

                /*
                 * Replace
                 */
                if ($col->hasReplaceValues() === true) {
                    $replaceValues = $col->getReplaceValues();

                    if (is_array($row[$col->getUniqueId()])) {
                        foreach ($row[$col->getUniqueId()] as &$value) {
                            if (isset($replaceValues[$value])) {
                                $value = $replaceValues[$value];
                            } elseif ($col->notReplacedGetEmpty() === true) {
                                $value = '';
                            }
                        }
                    } else {
                        if (isset($replaceValues[$row[$col->getUniqueId()]])) {
                            $row[$col->getUniqueId()] = $replaceValues[$row[$col->getUniqueId()]];
                        } elseif ($col->notReplacedGetEmpty() === true) {
                            $row[$col->getUniqueId()] = '';
                        }
                    }
                }

                /*
                 * Type converting
                 */
                if ($this->getRendererName() != 'PHPExcel') {
                    $row[$col->getUniqueId()] = $col->getType()->getUserValue($row[$col->getUniqueId()]);
                }

                /*
                 * Translate (nach typ convertierung -> PhpArray...)
                 */
                if ($col->isTranslationEnabled() === true) {
                    if (is_array($row[$col->getUniqueId()])) {
                        foreach ($row[$col->getUniqueId()] as &$value) {
                            if (is_array($value)) {
                                continue;
                            }
                            $value = $this->getTranslator()->translate($value);
                        }
                    } else {
                        $row[$col->getUniqueId()] = $this->getTranslator()->translate($row[$col->getUniqueId()]);
                    }
                }

                /*
                 * Trim the values
                 */
                if (is_array($row[$col->getUniqueId()])) {
                    array_walk_recursive($row[$col->getUniqueId()], function (&$value) {
                        if (!is_object($value)) {
                            $value = trim($value);
                        }
                    });
                } elseif (!is_object($row[$col->getUniqueId()])) {
                    $row[$col->getUniqueId()] = trim($row[$col->getUniqueId()]);
                }

                /*
                 * Custom formatter
                 */
                if ($col->hasFormatters() === true) {
                    foreach ($col->getFormatters() as $formatter) {
                        $formatter->setRowData($row);
                        $formatter->setRendererName($this->getRendererName());

                        $row[$col->getUniqueId()] = $formatter->format($col);
                    }
                }
            }

            // Concat all identity columns
            if ($ids) {
                $data[$key]['idConcated'] = implode('~', $ids);
            }
        }

        $this->dataPrepared = $data;

        return true;
    }
}
