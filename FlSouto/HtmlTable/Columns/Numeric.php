<?php

namespace FlSouto\HtmlTable\Columns;

use FlSouto\HtmlTable\Column;

class Numeric extends Column
{

    protected $point = ',';
    protected $thousands = ' ';
    protected $precision = 2;

    function init()
    {
        $this->sort_by = $this->key;
        $this->sorter = function ($a, $b) {
            $a = $a[$this->key] ?? null;
            $b = $b[$this->key] ?? null;
            return (float)$a > (float)$b;
        };
        $this->formatter = function ($value) {
            $result = number_format((float)$value, $this->precision, $this->point, $this->thousands);
            $result = preg_replace("/[{$this->point}][0]+$/", "", $result);
            return $result;
        };
        $this->blank = '';
    }

    function precision($n)
    {
        $this->precision = $n;
        return $this;
    }

    function thousands($separator)
    {
        $this->thousands = $separator;
    }

    function point($separator)
    {
        $this->point = $separator;
        return $this;
    }


}
