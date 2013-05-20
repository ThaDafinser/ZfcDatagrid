<?php
namespace ZfcDatagrid\Renderer;

use ZfcDatagrid\Datagrid;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\Stdlib\RequestInterface as Request;

interface RendererInterface
{

    /**
     *
     * @return array
     */
    public function getSortConditions ();

    /**
     *
     * @return array
     */
    public function getFilters ();

    /**
     * Determine if the renderer is for export
     *
     * @return boolean
     */
    public function isExport ();

    /**
     * Determin if the renderer is HTML
     * It can be export + html -> f.x.
     * printing for HTML
     *
     * @return boolean
     */
    public function isHtml ();

    /**
     * Execute all...
     *
     * @return ViewModel Response\Stream
     */
    public function execute ();
}
