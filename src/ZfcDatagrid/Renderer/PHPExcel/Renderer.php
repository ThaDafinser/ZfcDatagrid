<?php
/**
 * Output as an excel file
 */
namespace ZfcDatagrid\Renderer\PHPExcel;

use ZfcDatagrid\Renderer\AbstractExport;
use PHPExcel;
use PHPExcel_Worksheet_PageSetup;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use PHPExcel_Style_Fill;
use Zend\Http\Response\Stream as ResponseStream;
use Zend\Http\Headers;

class Renderer extends AbstractExport
{
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

        if (true === $optionsRenderer['displayTitle']) {
            $sheet->setCellValue('A'.$optionsRenderer['rowTitle'], $this->getTitle());
            $sheet->getStyle('A'.$optionsRenderer['rowTitle'])
                ->getFont()
                ->setSize(15);
        }

        $this->calculateColumnWidth($this->getColumnsToExport());

        /*
         * Header
         */
        $xColumn = 0;
        $yRow = $optionsRenderer['startRowData'];
        foreach ($this->getColumnsToExport() as $col) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            $label = $this->getTranslator()->translate($col->getLabel());
            $sheet->setCellValueByColumnAndRow($xColumn, $yRow, $label);

            $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($xColumn))->setWidth($col->getWidth());

            $xColumn ++;
        }

        /*
         * Data
         */
        $yRow = $optionsRenderer['startRowData'] + 1;
        foreach ($this->getData() as $row) {
            $xColumn = 0;
            foreach ($this->getColumnsToExport() as $col) {
                $value = $row[$col->getUniqueId()];
                if (is_array($value)) {
                    $value = implode(PHP_EOL, $value);
                }

                /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                $currentColumn = PHPExcel_Cell::stringFromColumnIndex($xColumn);
                $sheet->getCell($currentColumn.$yRow)->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING);

                $columnStyle = $sheet->getStyle($currentColumn.$yRow);
                $columnStyle->getAlignment()->setWrapText(true);

                /*
                 * Styles
                 */
                $styles = array_merge($this->getRowStyles(), $col->getStyles());
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
                                        'rgb' => $style->getRgbHexString(),
                                    ),
                                ));
                                break;

                            default:
                                throw new \Exception('Not defined yet: "'.get_class($style).'"');

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
        $highest = $sheet->getHighestRowAndColumn();

        // Letzte Zeile merken

        // Autofilter + Freeze
        $sheet->setAutoFilter('A'.$optionsRenderer['startRowData'].':'.$highest['column'].$highest['row']);
        $freezeRow = $optionsRenderer['startRowData'] + 1;
        $sheet->freezePane('A'.$freezeRow);

        // repeat the data header for each page!
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($optionsRenderer['startRowData'], $optionsRenderer['startRowData']);

        // highlight header line
        $style = array(
            'font' => array(
                'bold' => true,
            ),

            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'argb' => PHPExcel_Style_Color::COLOR_BLACK,
                    ),
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'argb' => PHPExcel_Style_Color::COLOR_YELLOW,
                ),
            ),
        );
        $range = 'A'.$optionsRenderer['startRowData'].':'.$highest['column'].$optionsRenderer['startRowData'];
        $sheet->getStyle($range)->applyFromArray($style);

        // print borders
        $range = 'A'.$freezeRow.':'.$highest['column'].$highest['row'];
        $sheet->getStyle($range)->applyFromArray(array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        ));

        /*
         * Print settings
         */
        $this->setPrinting($phpExcel);

        /*
         * Save the file
         */
        $path = $optionsExport['path'];
        $saveFilename = $this->getCacheId().'.xlsx';

        $excelWriter = new \PHPExcel_Writer_Excel2007($phpExcel);
        $excelWriter->setPreCalculateFormulas(false);
        $excelWriter->save($path.'/'.$saveFilename);

        /*
         * Send the response stream
         */
        $response = new ResponseStream();
        $response->setStream(fopen($path.'/'.$saveFilename, 'r'));

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Type' => array(
                'application/force-download',
                'application/octet-stream',
                'application/download',
            ),
            'Content-Length' => filesize($path.'/'.$saveFilename),
            'Content-Disposition' => 'attachment;filename='.$this->getFilename().'.xlsx',
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 'Thu, 1 Jan 1970 00:00:00 GMT',
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
            if ('landscape' == $orientation) {
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

            // Margins
            $sheet->getPageMargins()->setTop(0.8);
            $sheet->getPageMargins()->setBottom(0.5);
            $sheet->getPageMargins()->setLeft(0.5);
            $sheet->getPageMargins()->setRight(0.5);

            $this->setHeaderFooter($sheet);
        }

        $phpExcel->setActiveSheetIndex(0);
    }

    protected function setHeaderFooter(\PHPExcel_Worksheet $sheet)
    {
        $translator = $this->getTranslator();

        $textRight = $translator->translate('Page').' &P / &N';

        $sheet->getHeaderFooter()->setOddHeader('&L&16&G '.$translator->translate($this->getTitle()));
        $sheet->getHeaderFooter()->setOddFooter('&R'.$textRight);
    }
}
