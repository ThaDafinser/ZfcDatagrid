<?php
/**
 * Output as an excel file
 */
namespace ZfcDatagrid\Renderer\TCPDF;

use ZfcDatagrid\Renderer\AbstractRenderer;
use ZfcDatagrid\Column;
use TCPDF;
use Zend\Http\Response\Stream as ResponseStream;
use Zend\Http\Headers;

class Renderer extends AbstractRenderer
{

    private $columnsPositionX = array();
    
    public function getName ()
    {
        return 'TCPDF';
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
        $optionsExport = $options['settings']['export'];
        
        $papersize = $optionsExport['papersize'];
        $orientation = $optionsExport['orientation'];
        if ($orientation == 'landscape') {
            $orientation = 'L';
        } else {
            $orientation = 'P';
        }
        
        $pdf = new TCPDF($orientation, 'mm', $papersize);
        $pdf->AddPage();
        
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
        
        /*
         * Print the header
         */
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(255, 255, 200);
        $pdf->setTextRenderingMode(0.15, true, false);
        
        $y = $pdf->GetY();
        foreach ($columnsToExport as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            $x = $pdf->GetX();
            $this->columnsPositionX[$column->getUniqueId()] = $x;
            //Do not wrap header labels, it will look very ugly, that's why max height is set to 7!
            $pdf->MultiCell($column->getWidth(), 7, $column->getLabel(), 1, 'L', true, 2, $x, $y, true, 0, false, true, 7);
        }
        
        foreach($this->getData() as $row){
            
            $y = $pdf->GetY();
            foreach ($columnsToExport as $column) {
                /* @var $column \ZfcDatagrid\Column\AbstractColumn */
                $pdf->MultiCell($column->getWidth(), 7, $row[$column->getUniqueId()], 1, 'L', true, 2,  $this->columnsPositionX[$column->getUniqueId()], $y, true, 0);
            }
        }
        
        /*
         * Save the file
         */
        $path = $optionsExport['path'];
        $saveFilename = $this->getCacheId() . '.pdf';
        $pdf->Output($path . '/' . $saveFilename, 'F');
        
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
            'Content-Disposition' => 'attachment;filename=' . $this->getFilename() . '.pdf',
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
        $paperWidth -= 15;
        
        $factor = $paperWidth / 100;
        foreach ($columns as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            $column->setWidth($column->getWidth() * $factor);
        }
    }
}
