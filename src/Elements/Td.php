<?php

namespace FlSouto\HtmlTable\Elements;

use FlSouto\HtmlTable\Column;
use FlSouto\HtmlTable\Element;

class Td extends Element
{

    protected $col;

    function __construct(Column $col)
    {
        $this->col = $col;
        parent::__construct();
    }

    protected function tag()
    {
        return 'td';
    }

    function content()
    {
        $value = $this->data->get($this->col->key);
        if (is_null($value)) {
            $value = $this->col->get('blank');
        } else {
            if ($f = $this->col->get('formatter')) {
                $value = $f($value);
            }
        }
        return $value;
    }

}
