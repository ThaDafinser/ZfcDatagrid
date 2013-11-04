<?php
/**
 * Output as an excel file
 */
namespace ZfcDatagrid\Renderer\TCPDF;

use ZfcDatagrid\Renderer\AbstractRenderer;
use Zend\Http\Response\Stream as ResponseStream;
use Zend\Http\Headers;
use TCPDF;

class Renderer extends AbstractRenderer
{

    private $allowedColumnTypes = array(
        'ZfcDatagrid\Column\Type\DateTime',
        'ZfcDatagrid\Column\Type\Image',
        'ZfcDatagrid\Column\Type\Number',
        'ZfcDatagrid\Column\Type\PhpArray',
        'ZfcDatagrid\Column\Type\String'
    );

    private $columnsToExport;

    private $columnsPositionX = array();

    /**
     *
     * @var TCPDF
     */
    private $pdf;

    public function getName()
    {
        return 'TCPDF';
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
        $this->initPdf();
        
        $pdf = $this->getPdf();
        
        // Check for PDF image header
        $headerData = $pdf->getHeaderData();
        if ($headerData['logo'] == '') {
            $pdf->setHeaderData('./tcpdf_logo.jpg');
        }
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
        
        foreach ($this->getData() as $row) {
            $rowHeight = $this->getRowHeight($row);
            $y = $pdf->GetY();
            
            $usedHeight = $y + $rowHeight;
            
            if ($usedHeight > $pageHeight) {
                // Height is more than the pageHeight -> create a new page
                if ($rowHeight < $pageHeight) {
                    // If the row height is more than the page height, than we would have a problem, if we add a new page
                    // because it will overflow anyway...
                    $pdf->AddPage();
                    
                    $this->printTableHeader();
                }
            }
            
            $pageBeforeRow = $pdf->getPage();
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

    public function initPdf()
    {
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
        
        $margins = $optionsRenderer['margins'];
        $pdf->SetMargins($margins['left'], $margins['top'], $margins['right']);
        $pdf->SetAutoPageBreak(true, $margins['bottom']);
        $pdf->setHeaderMargin($margins['header']);
        $pdf->setFooterMargin($margins['footer']);
        
        $header = $optionsRenderer['header'];
        $pdf->setHeaderFont(array(
            'Helvetica',
            '',
            13
        ));
        
        $pdf->setHeaderData($header['logo'], $header['logoWidth'], $this->getTitle());
        
        $this->pdf = $pdf;
    }

    /**
     *
     * @return TCPDF
     */
    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * Decide which columns we want to display DO NOT display HTML, actions
     * After we have all -> resize the width to the paper format
     *
     * @return multitype:\ZfcDatagrid\Column\AbstractColumn
     */
    public function getColumnsToExport()
    {
        if (is_array($this->columnsToExport)) {
            return $this->columnsToExport;
        }
        
        $columnsToExport = array();
        foreach ($this->getColumns() as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            
            if ($column->isHidden() === false && in_array(get_class($column->getType()), $this->allowedColumnTypes)) {
                $columnsToExport[] = $column;
            }
        }
        if (count($columnsToExport) === 0) {
            throw new \Exception('No columns to export available');
        }
        
        $this->columnsToExport = $columnsToExport;
        
        return $this->columnsToExport;
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

    /**
     *
     * @param array $row            
     * @return number
     */
    public function getRowHeight(array $row)
    {
        $options = $this->getOptions();
        $optionsRenderer = $this->getOptionsRenderer();
        $sizePoint = $optionsRenderer['style']['data']['size'];
        // Points to MM
        $size = $sizePoint / 2.83464566929134;
        
        $pdf = $this->getPdf();
        
        $rowHeight = $size + 4;
        foreach ($this->getColumnsToExport() as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            
            $height = 1;
            switch (get_class($column->getType())) {
                
                case 'ZfcDatagrid\Column\Type\Icon':
                    // "min" height for such a column
                    $height = 10;
                    break;
                
                case 'ZfcDatagrid\Column\Type\Image':
                    // "min" height for such a column
                    $height = 15;
                    break;
                
                default:
                    $value = $row[$column->getUniqueId()];
                    if (is_array($value)) {
                        $value = implode(PHP_EOL, $value);
                    }
                    
                    $height = $pdf->getStringHeight($column->getWidth(), $value);
                    
                    // include borders top/bottom
                    $height += 2;
                    break;
            }
            
            if ($height > $rowHeight) {
                $rowHeight = $height;
            }
        }
        
        return $rowHeight;
    }

    protected function printTableHeader()
    {
        $this->setFontHeader();
        
        $pdf = $this->getPdf();
        $currentPage = $pdf->getPage();
        $y = $pdf->GetY();
        foreach ($this->getColumnsToExport() as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            $x = $pdf->GetX();
            $pdf->setPage($currentPage);
            
            $this->columnsPositionX[$column->getUniqueId()] = $x;
            
            $label = $this->getTranslator()->translate($column->getLabel());
            
            // Do not wrap header labels, it will look very ugly, that's why max height is set to 7!
            $pdf->MultiCell($column->getWidth(), 7, $label, 1, 'L', true, 2, $x, $y, true, 0, false, true, 7);
        }
    }

    protected function printTableRow(array $row, $rowHeight)
    {
        $pdf = $this->getPdf();
        
        $currentPage = $pdf->getPage();
        $y = $pdf->GetY();
        foreach ($this->getColumnsToExport() as $column) {
            /* @var $column \ZfcDatagrid\Column\AbstractColumn */
            
            $pdf->setPage($currentPage);
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
            switch (get_class($column->getType())) {
                
                // case 'ZfcDatagrid\Column\Type\PhpArray':
                // print_r($row[$column->getUniqueId()]);
                // exit();
                // break;
                
                case 'ZfcDatagrid\Column\Type\Icon':
                    $text = '';
                    
                    $link = K_BLANK_IMAGE;
                    if ($column->getIconLink() != '') {
                        $link = $column->getIconLink();
                    }
                    $pdf->Image($link, $x + 1, $y + 1, 0, 0, '', '', 'L', true);
                    break;
                
                case 'ZfcDatagrid\Column\Type\Image':
                    $text = '';
                    
                    $link = K_BLANK_IMAGE;
                    if ($row[$column->getUniqueId()] != '') {
                        $link = $row[$column->getUniqueId()];
                        
                        if(is_array($link)){
                            $link = array_shift($link);
                        }
                    }
                    list ($width, $height) = $this->calcImageSize($pdf, $link, $column->getWidth() - 2, $rowHeight - 2);
                    // Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false, $alt=false, $altimgs=array()) {
                    $pdf->Image($link, $x + 1, $y + 1, $width, $height, '', '', 'L', true, 300, '', false, false, 0, false, false, true, false, array());
                    break;
                
                default:
                    $text = $row[$column->getUniqueId()];
                    break;
            }
            
            if(is_array($text)){
                $text = implode(PHP_EOL, $text);
            }
            
            // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false)
            $pdf->MultiCell($column->getWidth(), $rowHeight, $text, 1, 'L', false, 1, $x, $y, true, 0);
        }
    }

    /**
     *
     * @param TCPDF $pdf            
     * @param string $image            
     * @param number $maxWidth            
     * @param number $maxHeight            
     * @return array
     */
    private function calcImageSize(TCPDF $pdf, $image, $maxWidth, $maxHeight)
    {
        list ($width, $height) = getimagesize($image);
        
        $width = $pdf->pixelsToUnits($width);
        $height = $pdf->pixelsToUnits($height);
        $ratio = $width / $height;
        
        $widthDiff = $maxWidth - $width;
        $heightDiff = $maxHeight - $height;
        
        if ($widthDiff < ($heightDiff * $ratio)) {
            // resize based on width
            $widthPdf = $maxWidth;
            $heightPdf = $maxWidth % $ratio;
        } else {
            // resize based on height
            $widthPdf = $maxHeight % $ratio;
            $heightPdf = $maxHeight;
        }
        
        return array(
            $widthPdf,
            $heightPdf
        );
    }

    public function setFontHeader()
    {
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
        // "BOLD" fake
        $pdf->setTextRenderingMode(0.15, true, false);
    }

    public function setFontData()
    {
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

    public function setBold()
    {
        $pdf = $this->getPdf();
        $pdf->setTextRenderingMode(0.15, true, false);
    }

    public function setItalic()
    {
        $options = $this->getOptions();
        $optionsRenderer = $this->getOptionsRenderer();
        $style = $optionsRenderer['style']['data'];
        $font = $style['font'];
        $size = $style['size'];
        
        $pdf = $this->getPdf();
        $pdf->setFont($font . 'I', '', $size);
    }

    /**
     *
     * @param array $rgb            
     */
    public function setColor(array $rgb)
    {
        $pdf = $this->getPdf();
        $pdf->SetTextColor($rgb['red'], $rgb['green'], $rgb['blue']);
    }
}
