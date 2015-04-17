<?php
return [
    'ZfcDatagrid' => [
        'renderer' => [

            'bootstrapTable' => [
                // Daterange bootstrapTable filter configuration example
                'daterange' => [
                    'enabled' => false,
                    'options' => [
                        'ranges' => [
                            'Today'        => new \Zend\Json\Expr("[moment().startOf('day'), moment().endOf('day')]"),
                            'Yesterday'    => new \Zend\Json\Expr("[moment().subtract('days', 1), moment().subtract('days', 1)]"),
                            'Last 7 Days'  => new \Zend\Json\Expr("[moment().subtract('days', 6), moment()]"),
                            'Last 30 Days' => new \Zend\Json\Expr("[moment().subtract('days', 29), moment()]"),
                            'This Month'   => new \Zend\Json\Expr("[moment().startOf('month'), moment().endOf('month')]"),
                            'Last Month'   => new \Zend\Json\Expr("[moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]"),
                        ],
                        'locale' => \Locale::getDefault(),
                        'format' => 'DD/MM/YY HH:mm:ss',
                    ],
                ],
            ],
        ],
    ],
];
