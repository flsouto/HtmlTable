<?php

namespace FlSouto\HtmlTable;

class Style extends Attrs
{

    protected $equal = ':';
    protected $separator = ';';
    protected $wrapper = '';

    protected function init()
    {
    }

    function parse($str)
    {
        parse_str(str_replace([':', ';'], ['=', '&'], $str), $attrs);
        $this->merge($attrs);
        return $this;
    }

}
