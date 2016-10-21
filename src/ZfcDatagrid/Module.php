<?php

namespace ZfcDatagrid;

class Module
{
    /**
     * @return array|mixed
     */
    public function getConfig()
    {
        $config = include __DIR__.'/../../config/module.config.php';
        if ($config['ZfcDatagrid']['renderer']['bootstrapTable']['daterange']['enabled'] === true) {
            $configNoCache = include __DIR__.'/../../config/daterange.config.php';

            $config = array_merge_recursive($config, $configNoCache);
        }

        return $config;
    }
}
