<?php
/**
 * Output as an excel file
 */
namespace ZfcDatagrid\Renderer\PHPExcel;

use ZfcDatagrid\Renderer\AbstractRenderer;
use ZfcDatagrid\Column;
use PHPExcel;
use PHPExcel_Worksheet_PageSetup;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use Zend\Http\Response\Stream as ResponseStream;
use Zend\Http\Headers;

class Renderer extends AbstractRenderer
{

    public function getName ()
    {
        return 'PHPExcel';
    }

    public function isExport ()
    {
        return true;
    }

    public function isHtml ()
    {
        return false;
    }

    public function execute ()
    {
        $options = $this->getOptions();
        $rendererOptions = $this->getRendererOptions();
        
        $phpExcel = new PHPExcel();
        
        // Sheet 1
        $phpExcel->setActiveSheetIndex(0);
        $sheet = $phpExcel->getActiveSheet();
        $sheet->setTitle($this->getTranslator()
            ->translate($rendererOptions['sheetName']));
        
        if ($rendererOptions['displayTitle'] === true) {
            $sheet->setCellValue('A' . $rendererOptions['rowTitle'], $this->getTitle());
            $sheet->getStyle('A' . $rendererOptions['rowTitle'])
                ->getFont()
                ->setSize(15);
        }
        
        /*
         * Decide which columns we want to display DO NOT display HTML, actions, ... After we have all -> resize the width to the paper format
         */
        $columnsToExport = array();
        foreach ($this->getColumns() as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            if ($column instanceof Column\Standard && $column->isHidden() === false) {
                $columnsToExport[] = $column;
            }
        }
        $this->calculateColumnWidth($columnsToExport);
        
        $xColumn = 0;
        $yRow = $rendererOptions['startRowData'];
        foreach ($columnsToExport as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            $sheet->setCellValueByColumnAndRow($xColumn, $yRow, $column->getLabel());
            
            // $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($xColumn))->setCollapsed(true);
            $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($xColumn))->setWidth($column->getWidth());
            
            $xColumn ++;
        }
        
        /*
         * Data
         */
        $yRow = $rendererOptions['startRowData'] + 1;
        foreach ($this->getData() as $row) {
            
            $xColumn = 0;
            foreach ($columnsToExport as $column) {
                $currentColumn = PHPExcel_Cell::stringFromColumnIndex($xColumn);
                /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                $sheet->getCell($currentColumn . $yRow)->setValueExplicit($row[$column->getUniqueId()], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->getStyle($currentColumn . $yRow)
                    ->getAlignment()
                    ->setWrapText(true);
                
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
        $sheet->setAutoFilter('A' . $rendererOptions['startRowData'] . ':' . PHPExcel_Cell::stringFromColumnIndex($endColumn) . $endRow);
        $freezeRow = $rendererOptions['startRowData'] + 1;
        $sheet->freezePane('A' . $freezeRow);
       
        
        /*
         * Print settings
         */
        $this->setPrinting($phpExcel);
        
        /*
         * Save the file
         */
        $path = 'public/download';
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
    protected function calculateColumnWidth (array $columns)
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
     *
     * @return float
     */
    private function getPaperWidth ()
    {
        $options = $this->getOptions();
        $export = $options['settings']['export'];
        
        $papersize = $export['papersize'];
        $orientation = $export['orientation'];
        
        if (substr($papersize, 0, 1) != 'A') {
            throw new \Exception('Currently only "A" paper formats are supported!');
        }
        
        // calc from A0 to selected
        $divisor = substr($papersize, 1, 1);
        
        // A0 dimensions = 841 x 1189 mm
        $currentX = 841;
        $currentY = 1189;
        for ($i = 0; $i < $divisor; $i ++) {
            $tempY = $currentX;
            $tempX = floor($currentY / 2);
            
            $currentX = $tempX;
            $currentY = $tempY;
        }
        
        if ($orientation == 'landscape') {
            return $currentY;
        } else {
            return $currentX;
        }
    }

    /**
     * Set the printing options
     *
     * @param PHPExcel $phpExcel            
     */
    protected function setPrinting (PHPExcel $phpExcel)
    {
         $options = $this->getOptions();
        
        $phpExcel->getProperties()
            ->setCreator('https://github.com/ThaDafinser/ZfcDatagrid')
            ->setTitle($this->getTitle());
        
        /*
         * Printing setup
         */
        $papersize = $options['settings']['export']['papersize'];
        $orientation = $options['settings']['export']['orientation'];
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
