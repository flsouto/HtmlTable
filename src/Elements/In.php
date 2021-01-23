<?php

namespace FlSouto\HtmlTable\Elements;

use FlSouto\HtmlTable\Data;
use FlSouto\HtmlTable\Element;

class In extends Element
{

    function __construct(Data $data = null)
    {
        $this->data = $data;
        parent::__construct();
    }

    protected function init()
    {
        $this->template = "<input {attrs} />";
    }

    protected function tag()
    {
        return 'input';
    }

    function content()
    {
        return '';
    }

}
