<?php

namespace FlSouto\HtmlTable\Elements;

use FlSouto\HtmlTable\Column;
use FlSouto\HtmlTable\Element;
use FlSouto\HtmlTable\Table;
use FlSouto\HtmlTable\Data;

class Tr extends Element
{

    public $data;
    protected $table;

    function __construct(Table $table, Data $data)
    {
        $this->data = $data;
        $this->table = $table;
        parent::__construct();
    }

    protected function tag()
    {
        return 'tr';
    }

    function content()
    {

        $content = '';

        foreach ($this->table->get('cols') as $col) {
            /**
             * @var Column $col
             */
            $content .= $col->render($this->data);
        }

        return $content;

    }

}
