<?php
/**
 * Output as an excel file
 */
namespace ZfcDatagrid\Renderer\PHPExcel;

use ZfcDatagrid\Renderer\AbstractRenderer;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Style;
use PHPExcel;
use PHPExcel_Worksheet_PageSetup;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use Zend\Http\Response\Stream as ResponseStream;
use Zend\Http\Headers;

class Renderer extends AbstractRenderer
{

    private $allowedColumnTypes = array(
        'ZfcDatagrid\Column\Type\DateTime',
        'ZfcDatagrid\Column\Type\Number',
        'ZfcDatagrid\Column\Type\PhpArray',
        'ZfcDatagrid\Column\Type\String'
    );

    public function getName()
    {
        return 'PHPExcel';
    }

    public function isExport()
    {
        return true;
    }

    public function isHtml()
    {
        return false;
    }

    public function execute()
    {
        $options = $this->getOptions();
        $optionsExport = $options['settings']['export'];
        
        $optionsRenderer = $this->getOptionsRenderer();
        
        $phpExcel = new PHPExcel();
        
        // Sheet 1
        $phpExcel->setActiveSheetIndex(0);
        $sheet = $phpExcel->getActiveSheet();
        $sheet->setTitle($this->getTranslator()
            ->translate($optionsRenderer['sheetName']));
        
        if ($optionsRenderer['displayTitle'] === true) {
            $sheet->setCellValue('A' . $optionsRenderer['rowTitle'], $this->getTitle());
            $sheet->getStyle('A' . $optionsRenderer['rowTitle'])
                ->getFont()
                ->setSize(15);
        }
        
        /*
         * Decide which columns we want to display 
         * DO NOT display HTML, actions, ... After we have all -> resize the width to the paper format
         */
        $columnsToExport = array();
        foreach ($this->getColumns() as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            if (! $column instanceof Column\Action && $column->isHidden() === false && in_array(get_class($column->getType()), $this->allowedColumnTypes)) {
                $columnsToExport[] = $column;
            }
        }
        if (count($columnsToExport) === 0) {
            throw new \Exception('No columns to export available');
        }
        $this->calculateColumnWidth($columnsToExport);
        
        $xColumn = 0;
        $yRow = $optionsRenderer['startRowData'];
        foreach ($columnsToExport as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            $label = $this->getTranslator()->translate($column->getLabel());
            $sheet->setCellValueByColumnAndRow($xColumn, $yRow, $label);
            
            // $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($xColumn))->setCollapsed(true);
            $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($xColumn))->setWidth($column->getWidth());
            
            $xColumn ++;
        }
        
        /*
         * Data
         */
        $yRow = $optionsRenderer['startRowData'] + 1;
        foreach ($this->getData() as $row) {
            
            $xColumn = 0;
            foreach ($columnsToExport as $column) {
                /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                $currentColumn = PHPExcel_Cell::stringFromColumnIndex($xColumn);
                $sheet->getCell($currentColumn . $yRow)->setValueExplicit($row[$column->getUniqueId()], PHPExcel_Cell_DataType::TYPE_STRING);
                
                $columnStyle = $sheet->getStyle($currentColumn . $yRow);
                $columnStyle->getAlignment()->setWrapText(true);
                
                /*
                 * Styles
                 */
                $styles = array_merge($this->getRowStyles(), $column->getStyles());
                foreach ($styles as $style) {
                    /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
                    if ($style->isApply($row) === true) {
                        switch (get_class($style)) {
                            
                            case 'ZfcDatagrid\Column\Style\Bold':
                                $columnStyle->getFont()->setBold(true);
                                break;
                            
                            case 'ZfcDatagrid\Column\Style\Italic':
                                $columnStyle->getFont()->setItalic(true);
                                break;
                            
                            case 'ZfcDatagrid\Column\Style\Color':
                                $columnStyle->getFont()
                                    ->getColor()
                                    ->setRGB($style->getRgbHexString());
                                break;
                            
                            case 'ZfcDatagrid\Column\Style\BackgroundColor':
                                $columnStyle->getFill()->applyFromArray(array(
                                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array(
                                        'rgb' => $style->getRgbHexString()
                                    )
                                ));
                                break;
                            
                            default:
                                throw new \Exception('Not defined yet: "' . get_class($style) . '"');
                                
                                break;
                        }
                    }
                }
                
                $xColumn ++;
            }
            
            $yRow ++;
        }
        
        /*
         * Autofilter, freezing, ...
         */
        // Letzte Zeile merken
        $endRow = $yRow - 1;
        $endColumn = count($columnsToExport) - 1;
        
        // Autofilter + Freeze
        $sheet->setAutoFilter('A' . $optionsRenderer['startRowData'] . ':' . PHPExcel_Cell::stringFromColumnIndex($endColumn) . $endRow);
        $freezeRow = $optionsRenderer['startRowData'] + 1;
        $sheet->freezePane('A' . $freezeRow);
        
        /*
         * Print settings
         */
        $this->setPrinting($phpExcel);
        
        /*
         * Save the file
         */
        $path = $optionsExport['path'];
        $saveFilename = $this->getCacheId() . '.xlsx';
        
        $excelWriter = new \PHPExcel_Writer_Excel2007($phpExcel);
        $excelWriter->setPreCalculateFormulas(false);
        $excelWriter->save($path . '/' . $saveFilename);
        
        /*
         * Send the response stream
         */
        $response = new ResponseStream();
        $response->setStream(fopen($path . '/' . $saveFilename, 'r'));
        
        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Type' => array(
                'application/force-download',
                'application/octet-stream',
                'application/download'
            ),
            'Content-Length' => filesize($path . '/' . $saveFilename),
            'Content-Disposition' => 'attachment;filename=' . $this->getFilename() . '.xlsx',
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 'Thu, 1 Jan 1970 00:00:00 GMT'
        ));
        
        $response->setHeaders($headers);
        
        return $response;
    }

    /**
     * Calculates the column width, based on the papersize and orientation
     *
     * @param array $columns            
     */
    protected function calculateColumnWidth(array $columns)
    {
        // First make sure the columns width is 100 "percent"
        $this->calculateColumnWidthPercent($columns);
        
        $paperWidth = $this->getPaperWidth();
        $paperWidth /= 2.29;
        
        $factor = $paperWidth / 100;
        foreach ($columns as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            $column->setWidth($column->getWidth() * $factor);
        }
    }

    /**
     * Set the printing options
     *
     * @param PHPExcel $phpExcel            
     */
    protected function setPrinting(PHPExcel $phpExcel)
    {
        $options = $this->getOptions();
        $optionsRenderer = $this->getOptionsRenderer();
        
        $phpExcel->getProperties()
            ->setCreator('https://github.com/ThaDafinser/ZfcDatagrid')
            ->setTitle($this->getTitle());
        
        /*
         * Printing setup
         */
        $papersize = $optionsRenderer['papersize'];
        $orientation = $optionsRenderer['orientation'];
        foreach ($phpExcel->getAllSheets() as $sheet) {
            /* @var $sheet \PHPExcel_Worksheet */
            if ($orientation == 'landscape') {
                $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            } else {
                $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
            }
            
            switch ($papersize) {
                
                case 'A5':
                    $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A5);
                    break;
                
                case 'A4':
                    $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                    break;
                
                case 'A3':
                    $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A3);
                    break;
                
                case 'A2':
                    $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A2);
                    break;
            }
        }
        
        $phpExcel->setActiveSheetIndex(0);
    }
}
