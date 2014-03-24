<?php
namespace ZfcDatagrid\Column\DataPopulation;

interface DataPopulationInterface
{
    
    /**
     * Return the result
     * 
     * @return string
     */
    public function toString();
    
    /**
     * @return array
     */
    public function getParameters();
}