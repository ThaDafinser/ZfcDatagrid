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
        
        $papersize = $options['settings']['export']['papersize'];
        $orientation = $options['settings']['export']['orientation'];
        if ($orientation == 'landscape') {
            $orientation = 'L';
        } else {
            $orientation = 'P';
        }
        
        $pdf = new TCPDF($orientation, 'mm', $papersize);
        $pdf->AddPage();
        
        /*
         * Save the file
         */
        $path = 'public/download';
        $saveFilename = $this->getCacheId() . '.xlsx';
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
            'Content-Disposition' => 'attachment;filename=' . $this->getFilename() . '.xlsx',
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 'Thu, 1 Jan 1970 00:00:00 GMT'
        ));
        
        $response->setHeaders($headers);
        
        return $response;
    }
}
