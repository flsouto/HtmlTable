<?php

namespace FlSouto\HtmlTable;

use FlSouto\HtmlTable\Elements\Td;
use FlSouto\HtmlTable\Elements\Th;

class Column
{

    public $key;

    protected $td;
    protected $table;
    protected $heading;
    protected $th;
    protected $sorter;
    protected $formatter;
    protected $blank;
    protected $sortable = true;
    protected $sort_by = '';

    function __construct(Table $table, $key)
    {
        $this->table = $table;
        $this->key = $key;
        $this->td = new Td($this);
        $this->th = new Th($this);
        $this->init();
    }

    function init()
    {
        $this->sorter = function ($a, $b) {
            $a = $a[$this->key] ?? null;
            $b = $b[$this->key] ?? null;
            return mb_strtolower(self::unaccent($a)) > mb_strtolower(self::unaccent($b));
        };
        $this->formatter = function ($value) {
            return $value;
        };
        $this->sort_by = $this->key;
        $this->blank = '';
    }

    static protected function unaccent($string)
    {
        return preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i',
            '$1',
            htmlentities($string, ENT_QUOTES, 'UTF-8'));
    }

    function blank($value)
    {
        $this->blank = $value;
        return $this;
    }

    function align($value)
    {
        $style = [
            'text-align' => $value
        ];
        $this->th->attrs->style($style);
        $this->td->attrs->style($style);
        return $this;
    }

    function width($value)
    {
        $style = [
            'width' => $value
        ];
        $this->th->attrs->style($style);
        $this->td->attrs->style($style);
        return $this;
    }

    function td($tpl_attrs_or_callback)
    {
        $td = $this->td;
        $td($tpl_attrs_or_callback);
        return $this;
    }

    function th($tpl_attrs_or_callback)
    {
        $th = $this->th;
        $th($tpl_attrs_or_callback);
        return $this;
    }

    function sortable(bool $bool)
    {
        $this->sortable = $bool;
        return $this;
    }

    function heading($heading)
    {
        $this->heading = $heading;
        return $this;
    }

    function get($prop)
    {
        return $this->$prop;
    }

    function render(Data $data)
    {
        $this->td->data = $data;
        return $this->td->__toString();
    }

}
