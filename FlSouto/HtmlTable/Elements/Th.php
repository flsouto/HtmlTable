<?php

namespace FlSouto\HtmlTable\Elements;

use FlSouto\HtmlTable\Column;
use FlSouto\HtmlTable\Element;
use FlSouto\HtmlTable\Table;

class Th extends Element
{

    protected $col;

    function __construct(Column $col)
    {
        $this->col = $col;
        parent::__construct();
    }

    protected function tag()
    {
        return 'th';
    }

    function link($content)
    {
        /**
         * @var Table $table
         */
        $table = $this->col->get('table');
        $content = '<a href="' . $table->sorting->url($this->col->get('sort_by')) . '">' . $content . '</a>';
        if ($table->sorting->current()->col == $this->col->get('sort_by')) {
            $content .= '&nbsp;' . ($table->sorting->current()->ord == 'DESC' ? '&darr;' : '&uarr;');
        }
        return $content;
    }

    static function pretty($col){
        return ucwords(str_replace('_', ' ', $col));
    }

    function content()
    {
        $heading = $this->col->get('heading');

        if (empty($heading) || $heading == $this->col->key) {
            $heading = self::pretty($this->col->key);
        }

        if ($this->col->get('sortable')) {
            return $this->link($heading);
        }

        return $heading;
    }

}
