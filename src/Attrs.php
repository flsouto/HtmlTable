<?php

namespace FlSouto\HtmlTable;

class Attrs extends Data
{

    protected $element;
    protected $equal = '=';
    protected $separator = ' ';
    protected $wrapper = '"';

    function __construct(Element $element = null)
    {
        $this->element = $element;
        $this->init();
    }

    protected function init()
    {
        $this->data['style'] = new Style($this->element);
    }

    function style($style)
    {
        if (is_string($style)) {
            $this->data['style']->parse($style);
        } else {
            if (is_array($style)) {
                $this->data['style']->merge($style);
            }
        }
        return $this;
    }

    function parse($str)
    {
        parse_str(str_replace(' ', '&', $str), $attrs);
        $this->merge($attrs);
        return $this;
    }

    function __toString()
    {
        $arr = [];
        foreach ($this->data as $k => $v) {
            if ($this->element) {
                $v = $this->element->evaluate($v);
            }
            if ($v === true) {
                $arr[] = $k; // ex: checked
                continue;
            }
            $v = "$v";
            if (empty($v)) {
                continue;
            }
            $arr[] = $k . $this->equal . $this->wrapper . $v . $this->wrapper;
        }
        return implode($this->separator, $arr);
    }

}
