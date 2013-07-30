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

    private $columnsToExport;
    
    private $columnsPositionX = array();
    
    /**
     * 
     * @var TCPDF
     */
    private $pdf;
    
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
        $this->initPdf();
        
        $pdf = $this->getPdf();
        $pdf->AddPage();
        
        $columns = $this->getColumnsToExport();
        $this->calculateColumnWidth($columns);
        
        /*
         * Display used filters etc...
         */
        
        /*
         * Print the header
         */
        $this->printTableHeader();
        
        /*
         * Write data
         */
        $pageHeight = $pdf->getPageHeight();
        $pageHeight -= 10;
        
        foreach($this->getData() as $row){
            $rowHeight = $this->getRowHeight($row);
            $y = $pdf->GetY();
            
            $usedHeight = $y + $rowHeight;
            
            if ($usedHeight > $pageHeight) {
                //Height is more than the pageHeight -> create a new page
                if($rowHeight < $pageHeight){
                    //If the row height is more than the page height, than we would have a problem, if we add a new page
                    //because it will overflow anyway...
                    $pdf->AddPage();
                    
                    $this->printTableHeader();
                }
            }
            
            $this->printTableRow($row, $rowHeight);
        }
        
        /*
         * Save the file
         */
        $options = $this->getOptions();
        $optionsExport = $options['settings']['export'];
        
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


    public function initPdf(){
        $options = $this->getOptions();
        $optionsRenderer = $this->getOptionsRenderer();
        
        $papersize = $optionsRenderer['papersize'];
        $orientation = $optionsRenderer['orientation'];
        if ($orientation == 'landscape') {
            $orientation = 'L';
        } else {
            $orientation = 'P';
        }
        
        $pdf = new TCPDF($orientation, 'mm', $papersize);
        
        $margins  = $optionsRenderer['margins'];
        $pdf->SetMargins($margins['left'], $margins['top'], $margins['right']);
        $pdf->SetAutoPageBreak(true, $margins['bottom']);
        $pdf->setHeaderMargin($margins['header']);
        $pdf->setFooterMargin($margins['footer']);
        
        $header = $optionsRenderer['header'];
        $pdf->setHeaderFont(array('Helvetica', '', 13));
        $pdf->setHeaderData('/'.$header['logo'], $header['logoWidth'], $this->getTitle());
        
        $this->pdf = $pdf;
    }
    
    /**
     *
     * @return TCPDF
     */
    public function getPdf(){
        return $this->pdf;
    }
    
    /**
     * Decide which columns we want to display DO NOT display HTML, actions, ... After we have all -> resize the width to the paper format
     * 
     * @return multitype:\ZfcDatagrid\Column\AbstractColumn
     */
    public function getColumnsToExport(){
        if(is_array($this->columnsToExport)){
            return $this->columnsToExport;
        }
        
        $columnsToExport = array();
        foreach ($this->getColumns() as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            if($column->isHidden() === false){
                
                switch(get_class($column)){
                    
                    case 'ZfcDatagrid\Column\Standard':
                        $columnsToExport[] = $column;
                        break;
                        
                    case 'ZfcDatagrid\Column\Icon':
                        $columnsToExport[] = $column;
                        break;
                        
                }
            }
            
        }
        
        $this->columnsToExport = $columnsToExport;
        
        return $this->columnsToExport;
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
        
        $options = $this->getOptions();
        $optionsRenderer = $this->getOptionsRenderer();
        $margins = $optionsRenderer['margins'];
        
        $paperWidth = $this->getPaperWidth();
        $paperWidth -= ($margins['left'] + $margins['right']);
        
        $factor = $paperWidth / 100;
        foreach ($columns as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            $column->setWidth($column->getWidth() * $factor);
        }
    }
    
    public function getRowHeight(array $row){
        
        $options = $this->getOptions();
        $optionsRenderer = $this->getOptionsRenderer();
        $sizePoint = $optionsRenderer['style']['data']['size'];
        //Points to MM
        $size = $sizePoint / 2.83464566929134;
        
        $pdf = $this->getPdf();
        
        $rowHeight = $size + 4;
        foreach($this->getColumnsToExport() as $column){
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            
            $height = 1;
            switch(get_class($column)){
            
                case 'ZfcDatagrid\Column\Standard':
                    if($row[$column->getUniqueId()] != ''){
                        $count = $pdf->getNumLines($row[$column->getUniqueId()], $column->getWidth());
                        
                        $height = $count * $size + 4;
                    }
                    break;
            
                case 'ZfcDatagrid\Column\Icon':
                    $height = 10;
                    break;
            
            }
            
            
            if($height > $rowHeight){
                $rowHeight = $height;
            }
        }
        
        return $rowHeight;
    }

    protected function printTableHeader(){
        
        $this->setFontHeader();
        
        $pdf = $this->getPdf();
        
        $y = $pdf->GetY();
        foreach ($this->getColumnsToExport() as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            $x = $pdf->GetX();
            $this->columnsPositionX[$column->getUniqueId()] = $x;

            $label = $this->getTranslator()->translate($column->getLabel());
            
            //Do not wrap header labels, it will look very ugly, that's why max height is set to 7!
            $pdf->MultiCell($column->getWidth(), 7, $label, 1, 'L', true, 2, $x, $y, true, 0, false, true, 7);
        }
    }
    
    protected function printTableRow(array $row, $rowHeight){
        
        
        $pdf = $this->getPdf();
        
        $y = $pdf->GetY();
        foreach ($this->getColumnsToExport()  as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            
            $x = $this->columnsPositionX[$column->getUniqueId()];
            
            $this->setFontData();
            
            /*
             * Styles
            */
            if ($column->hasStyles() === true) {
                foreach ($column->getStyles() as $style) {
                    /* @var $style \ZfcDatagrid\Column\Style\AbstractStyle */
                    if ($style->isApply($row) === true) {
                        switch (get_class($style)) {
            
                            case 'ZfcDatagrid\Column\Style\Bold':
                                $this->setBold();
                                break;
            
                            case 'ZfcDatagrid\Column\Style\Italic':
                                $this->setItalic();
                                break;
            
                            case 'ZfcDatagrid\Column\Style\Color':
                                $this->setColor($style->getRgbArray());
                                break;
            
                            default:
                                throw new \Exception('Not defined yet: "' . get_class($style) . '"');
            
                                break;
                        }
                    }
                }
            }
            
            
            $text = '';
            switch(get_class($column)){
                
                case 'ZfcDatagrid\Column\Standard':
                    $text = $row[$column->getUniqueId()];
                    break;
                    
                case 'ZfcDatagrid\Column\Icon':
                    $text = '';
                    
                    $link = K_BLANK_IMAGE;
                    if($column->getIconLink() != ''){
                        $link = $column->getIconLink();
                    }
                    $pdf->Image($link, $x + 1, $y + 1, 0, 0, '', '', 'L', true);
                    break;
                    
            }
            
            $pdf->MultiCell($column->getWidth(), $rowHeight, $text, 1, 'L', true, 2,  $x, $y, true, 0);
            
        }
    }
    
    public function setFontHeader(){
        $options = $this->getOptions();
        $optionsRenderer = $this->getOptionsRenderer();
        $style = $optionsRenderer['style']['header'];
        
        $font = $style['font'];
        $size = $style['size'];
        $color = $style['color'];
        $background = $style['background-color'];
    
        $pdf = $this->getPdf();
        $pdf->setFont($font, '', $size);
        $pdf->SetTextColor($color[0], $color[1], $color[2]);
        $pdf->SetFillColor($background[0], $background[1], $background[2]);
        //"BOLD" fake
        $pdf->setTextRenderingMode(0.15, true, false);
    }
    
    public function setFontData(){
        $options = $this->getOptions();
        $optionsRenderer = $this->getOptionsRenderer();
        $style = $optionsRenderer['style']['data'];
        
        $font = $style['font'];
        $size = $style['size'];
        $color = $style['color'];
        $background = $style['background-color'];
    
        $pdf = $this->getPdf();
        $pdf->setFont($font, '', $size);
        $pdf->SetTextColor($color[0], $color[1], $color[2]);
        $pdf->SetFillColor($background[0], $background[1], $background[2]);
        $pdf->setTextRenderingMode();
    }
    
    public function setBold(){
        $pdf = $this->getPdf();
        $pdf->setTextRenderingMode(0.15, true, false);
    }
    
    public function setItalic(){
        $options = $this->getOptions();
        $optionsRenderer = $this->getOptionsRenderer();
        $style = $optionsRenderer['style']['data'];
        $font = $style['font'];
        $size = $style['size'];
        
        $pdf = $this->getPdf();
        $pdf->setFont($font.'I', '', $size);
    }
    
    /**
     * 
     * @param array $rgb
     */
    public function setColor(array $rgb){
        $pdf = $this->getPdf();
        $pdf->SetTextColor($rgb['red'], $rgb['green'], $rgb['blue']);
    }
}
