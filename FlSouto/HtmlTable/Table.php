<?php

namespace FlSouto\HtmlTable;

use FlSouto\HtmlTable\Columns\Button;
use FlSouto\HtmlTable\Columns\Checkbox;
use FlSouto\HtmlTable\Columns\Combo;
use FlSouto\HtmlTable\Columns\Date;
use FlSouto\HtmlTable\Columns\Numeric;
use FlSouto\HtmlTable\Elements\Tr;

class Table
{

    public $tr;
    public $sorting;
    public $attrs;

    protected $data = [];
    protected $request;

    /**
     * @var Column[] $cols
     */
    protected $cols = [];
    protected static $idseq = 1;

    function __construct(array $attrs = [], array $request = null)
    {
        if (empty($attrs['id'])) {
            $attrs['id'] = self::$idseq;
            self::$idseq++;
        }
        $this->attrs = new Attrs();
        $this->attrs->merge($attrs);
        if (is_null($request)) {
            $request = $_REQUEST;
        }
        $this->request = $request;
        $this->sorting = new Sorting($this);
        $this->tr = new Tr($this, new Data);
    }

    protected function factory($key, $class = Column::class)
    {
        if (!isset($this->cols[$key])) {
            if ($key instanceof Column) {
                $col = $key;
                $key = $col->key;
            } else {
                $col = new $class($this, $key);
            }
            $this->cols[$key] = $col;
        }
        return $this->cols[$key];
    }

    function col($key): Column
    {
        return $this->factory($key, Column::class);
    }

    function cdate($key): Date
    {
        return $this->factory($key, Date::class);
    }

    function cnum($key): Numeric
    {
        return $this->factory($key, Numeric::class);
    }

    function cbtn($label, $url = null, $newtab = false): Button
    {
        /**
         * @var Button $col
         */
        $col = $this->factory($label, Button::class);
        $col->label($label);
        if ($url) {
            $col->url($url, $newtab);
        }
        return $col;
    }

    function ccheck($key): Checkbox
    {
        return $this->factory($key, Checkbox::class);
    }

    function ccombo($key): Combo
    {
        return $this->factory($key, Combo::class);
    }

    function data(array $data)
    {
        $this->data = $data;
        return $this;
    }

    function each(callable $callback)
    {
        array_map($callback, $this->cols);
        return $this;
    }

    function get($key)
    {
        return $this->$key;
    }

    protected function sort()
    {

        $col = null;

        foreach($this->cols as $c){
            if($c->get('sort_by') == $this->sorting->current()->col){
                $col = $c;
                break;
            }
        }

        if (!$col) {
            return;
        }

        uasort($this->data, $col->get('sorter'));

        if ($this->sorting->current()->ord == 'DESC') {
            $this->data = array_reverse($this->data);
        }

    }

    function __toString()
    {
        $str = "<table $this->attrs>";
        $str .= '<thead><tr>';
        foreach ($this->cols as $col) {
            $str .= $col->get('th');
        }
        $str .= "</tr></thead>";
        $str .= "<tbody>";
        $this->sort();
        foreach ($this->data as $i => $row) {
            $this->tr->data->replace($row);
            $this->tr->data->set('table.index', $i);
            $str .= $this->tr;
        }
        $str .= "</tbody>";
        $str .= "</table>";
        $str .= "<input type='hidden' name='htbl".$this->attrs->get('id')."' value='1' />";
        $str .= "<input type='hidden' name='htbl".$this->attrs->get('id')."_sort' value='".$this->sorting->current()."' />";
        return $str;
    }

}
