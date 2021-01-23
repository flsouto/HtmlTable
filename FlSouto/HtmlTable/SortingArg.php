<?php

namespace FlSouto\HtmlTable;

class SortingArg
{

    var $col;
    var $ord = 'DESC';

    function sql($alias)
    {
        return $alias . '.' . $this->__toString();
    }

    function __toString()
    {
        return $this->col . ' ' . $this->ord;
    }

}
