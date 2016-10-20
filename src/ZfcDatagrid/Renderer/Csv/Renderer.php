<?php
/**
 * Render datagrid as CSV.
 */
namespace ZfcDatagrid\Renderer\Csv;

use Zend\Http\Headers;
use Zend\Http\Response\Stream as ResponseStream;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Renderer\AbstractExport;

class Renderer extends AbstractExport
{
    public function getName()
    {
        return 'csv';
    }

    public function isExport()
    {
        return true;
    }

    public function isHtml()
    {
        return false;
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function execute()
    {
        $optionsRenderer = $this->getOptionsRenderer();

        $delimiter = ',';
        if (isset($optionsRenderer['delimiter'])) {
            $delimiter = $optionsRenderer['delimiter'];
        }
        $enclosure = '"';
        if (isset($optionsRenderer['enclosure'])) {
            $enclosure = $optionsRenderer['enclosure'];
        }

        $options = $this->getOptions();
        $optionsExport = $options['settings']['export'];

        $path = $optionsExport['path'];
        $saveFilename = date('Y-m-d_H-i-s').$this->getCacheId().'.csv';

        $fp = fopen($path.'/'.$saveFilename, 'w');
        // Force UTF-8 for CSV rendering in EXCEL.
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

        /*
         * Save the file
         */
        // header
        if (isset($optionsRenderer['header']) && true === $optionsRenderer['header']) {
            $header = [];
            foreach ($this->getColumnsToExport() as $col) {
                $header[] = $this->translate($col->getLabel());
            }
            fputcsv($fp, $header, $delimiter, $enclosure);
        }

        // data
        foreach ($this->getData() as $row) {
            $csvRow = [];
            foreach ($this->getColumnsToExport() as $col) {
                $value = $row[$col->getUniqueId()];

                if ($col->getType() instanceof Type\PhpArray || $col->getType() instanceof Type\Image) {
                    $value = implode(',', $value);
                }

                $csvRow[] = $value;
            }
            fputcsv($fp, $csvRow, $delimiter, $enclosure);
        }
        fclose($fp);

        /*
         * Return the file
         */
        $response = new ResponseStream();
        $response->setStream(fopen($path.'/'.$saveFilename, 'r'));

        $headers = new Headers();
        $headers->addHeaders([
            'Content-Type' => [
                'application/force-download',
                'application/octet-stream',
                'application/download',
                'text/csv; charset=utf-8',
            ],
            'Content-Length' => filesize($path.'/'.$saveFilename),
            'Content-Disposition' => 'attachment;filename='.$this->getFilename().'.csv',
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 'Thu, 1 Jan 1970 00:00:00 GMT',
        ]);

        $response->setHeaders($headers);

        return $response;
    }
}
