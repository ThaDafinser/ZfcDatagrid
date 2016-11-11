<?php
/**
 * Output as an excel file.
 */
namespace ZfcDatagrid\Renderer\PHPExcel;

use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet_PageSetup;
use Zend\Http\Headers;
use Zend\Http\Response\Stream as ResponseStream;
use ZfcDatagrid\Column;
use ZfcDatagrid\Renderer\AbstractExport;

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
        $sheet->setTitle($this->translate($optionsRenderer['sheetName']));

        if (true === $optionsRenderer['displayTitle']) {
            $sheet->setCellValue('A'.$optionsRenderer['rowTitle'], $this->getTitle());
            $sheet->getStyle('A'.$optionsRenderer['rowTitle'])
                ->getFont()
                ->setSize(15);
        }

        /*
         * Print settings
         */
        $this->setPrinting($phpExcel);

        /*
         * Calculate column width
         */
        $this->calculateColumnWidth($sheet, $this->getColumnsToExport());

        /*
         * Header
         */
        $xColumn = 0;
        $yRow = $optionsRenderer['startRowData'];
        foreach ($this->getColumnsToExport() as $col) {
            /* @var $column Column\AbstractColumn */
            $sheet->setCellValueByColumnAndRow($xColumn, $yRow, $this->translate($col->getLabel()));

            $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($xColumn))->setWidth($col->getWidth());

            ++$xColumn;
        }

        /*
         * Data
         */
        $yRow = $optionsRenderer['startRowData'] + 1;
        foreach ($this->getData() as $row) {
            $xColumn = 0;
            foreach ($this->getColumnsToExport() as $col) {
                /* @var $col Column\AbstractColumn */

                $value = $row[$col->getUniqueId()];
                if (is_array($value)) {
                    $value = implode(PHP_EOL, $value);
                }

                /* @var $column Column\AbstractColumn */
                $currentColumn = PHPExcel_Cell::stringFromColumnIndex($xColumn);
                $cell = $sheet->getCell($currentColumn.$yRow);

                switch (get_class($col->getType())) {

                    case Column\Type\Number::class:
                        $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                        break;

                    case Column\Type\DateTime::class:
                        /* @var $dateType Column\Type\DateTime */
                        $dateType = $col->getType();

                        if (! $value instanceof \DateTime && is_scalar($value)) {
                            $value = \DateTime::createFromFormat($dateType->getSourceDateTimeFormat(), $value);
                            $value->setTimezone(new \DateTimeZone($dateType->getSourceTimezone()));
                        }

                        $value->setTimezone(new \DateTimeZone($dateType->getOutputTimezone()));
                        $cell->setValue(\PHPExcel_Shared_Date::PHPToExcel($value));

                        if ($dateType->getOutputPattern()) {
                            $outputPattern = $dateType->getOutputPattern();
                        } else {
                            $outputPattern = \PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME;
                        }

                        $cell->getStyle()
                            ->getNumberFormat()
                            ->setFormatCode($outputPattern);
                        break;

                    default:
                        $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING);
                        break;
                }

                $columnStyle = $sheet->getStyle($currentColumn.$yRow);
                $columnStyle->getAlignment()->setWrapText(true);

                /*
                 * Styles
                 */
                $styles = array_merge($this->getRowStyles(), $col->getStyles());
                foreach ($styles as $style) {
                    /* @var $style Column\Style\AbstractStyle */
                    if ($style->isApply($row) === true) {
                        switch (get_class($style)) {

                            case Column\Style\Bold::class:
                                $columnStyle->getFont()->setBold(true);
                                break;

                            case Column\Style\Italic::class:
                                $columnStyle->getFont()->setItalic(true);
                                break;

                            case Column\Style\Color::class:
                                $columnStyle->getFont()
                                    ->getColor()
                                    ->setRGB($style->getRgbHexString());
                                break;

                            case Column\Style\BackgroundColor::class:
                                $columnStyle->getFill()->applyFromArray([
                                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => [
                                        'rgb' => $style->getRgbHexString(),
                                    ],
                                ]);
                                break;

                            case Column\Style\Align::class:
                                switch ($style->getAlignment()) {
                                    case Column\Style\Align::$RIGHT:
                                        $columnStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                                        break;
                                    case Column\Style\Align::$LEFT:
                                        $columnStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                                        break;
                                    case Column\Style\Align::$CENTER:
                                        $columnStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                        break;
                                    case Column\Style\Align::$JUSTIFY:
                                        $columnStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
                                        break;
                                    default:
                                        //throw new \Exception('Not defined yet: "'.get_class($style->getAlignment()).'"');
                                        break;
                                }

                                break;

                            case Column\Style\Strikethrough::class:
                                $columnStyle->getFont()->setStrikethrough(true);
                                break;

                            case Column\Style\Html::class:
                                // @todo strip the html?
                                break;

                            default:
                                throw new \Exception('Not defined yet: "'.get_class($style).'"');
                                break;
                        }
                    }
                }

                ++$xColumn;
            }

            ++$yRow;
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
        $style = [
            'font' => [
                'bold' => true,
            ],

            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => [
                        'argb' => PHPExcel_Style_Color::COLOR_BLACK,
                    ],
                ],
            ],
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => [
                    'argb' => PHPExcel_Style_Color::COLOR_YELLOW,
                ],
            ],
        ];
        $range = 'A'.$optionsRenderer['startRowData'].':'.$highest['column'].$optionsRenderer['startRowData'];
        $sheet->getStyle($range)->applyFromArray($style);

        // print borders
        $range = 'A'.$freezeRow.':'.$highest['column'].$highest['row'];
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ],
            ],
        ]);

        /*
         * Save the file
         */
        $path = $optionsExport['path'];
        $saveFilename = date('Y-m-d_H-i-s').$this->getCacheId().'.xlsx';

        $excelWriter = new \PHPExcel_Writer_Excel2007($phpExcel);
        $excelWriter->setPreCalculateFormulas(false);
        $excelWriter->save($path.'/'.$saveFilename);

        /*
         * Send the response stream
         */
        $response = new ResponseStream();
        $response->setStream(fopen($path.'/'.$saveFilename, 'r'));

        $headers = new Headers();
        $headers->addHeaders([
            'Content-Type' => [
                'application/force-download',
                'application/octet-stream',
                'application/download',
            ],
            'Content-Length' => filesize($path.'/'.$saveFilename),
            'Content-Disposition' => 'attachment;filename='.$this->getFilename().'.xlsx',
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 'Thu, 1 Jan 1970 00:00:00 GMT',
        ]);

        $response->setHeaders($headers);

        return $response;
    }

    /**
     * Calculates the column width, based on the papersize and orientation.
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param array               $columns
     */
    protected function calculateColumnWidth(\PHPExcel_Worksheet $sheet, array $columns)
    {
        // First make sure the columns width is 100 "percent"
        $this->calculateColumnWidthPercent($columns);

        // width is in mm
        $paperWidth = $this->getPaperWidth();

        // remove margins (they are in inches!)
        $paperWidth -= $sheet->getPageMargins()->getLeft() / 0.0393700787402;
        $paperWidth -= $sheet->getPageMargins()->getRight() / 0.0393700787402;

        $paperWidth /= 2;

        $factor = $paperWidth / 100;
        foreach ($columns as $column) {
            /* @var $column Column\AbstractColumn */
            $column->setWidth($column->getWidth() * $factor);
        }
    }

    /**
     * Set the printing options.
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

    /**
     * @param \PHPExcel_Worksheet $sheet
     */
    protected function setHeaderFooter(\PHPExcel_Worksheet $sheet)
    {
        $textRight = $this->translate('Page').' &P / &N';

        $sheet->getHeaderFooter()->setOddHeader('&L&16&G '.$this->translate($this->getTitle()));
        $sheet->getHeaderFooter()->setOddFooter('&R'.$textRight);
    }
}
