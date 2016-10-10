<?php

namespace ZfcDatagrid\Column\Formatter;

class Link extends HtmlTag
{
    /**
     * @var array
     */
    protected $attributes = [
        'href' => '',
    ];

    /**
     * @var string
     */
    protected $name = 'a';
}
