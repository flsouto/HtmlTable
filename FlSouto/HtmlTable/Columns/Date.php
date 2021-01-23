<?php

namespace FlSouto\HtmlTable\Columns;

use FlSouto\HtmlTable\Column;

class Date extends Column
{

    protected $datef = 'd/Y H:i';

    function init()
    {
        $this->sorter = function ($a, $b) {
            $a = $a[$this->key] ?? null;
            $b = $b[$this->key] ?? null;
            return $a > $b;
        };
        $this->sort_by = $this->key;
        $this->formatter = function ($value) {
            return date($this->datef, strtotime($value));
        };
        $this->blank = '----';
    }

    function datef($format)
    {
        $this->datef = $format;
        return $this;
    }


}
