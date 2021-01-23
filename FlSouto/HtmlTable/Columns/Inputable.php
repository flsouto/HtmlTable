<?php

namespace FlSouto\HtmlTable\Columns;

use FlSouto\HtmlTable\Column;
use FlSouto\HtmlTable\Data;
use FlSouto\HtmlTable\Table;

class Inputable
{

    protected $column;

    protected $request_key = '';
    protected $index_key = '';
    protected $data_key = '';

    function __construct(Column $col)
    {
        if (!preg_match("/(\w+)(?:\[(\w+)\])(?:\[(\w+)\])?/", $col->key, $m)) {
            throw new \InvalidArgumentException("Invalid inputable key: $col->key. Use: name[index] or name[index][key]");
        }
        $this->column = $col;
        $this->request_key = $m[1];
        $this->index_key = $m[2];
        $this->data_key = $m[3] ?? null;
    }

    function get($prop){
        return $this->$prop;
    }

    function name()
    {
        $expr = $this->request_key . "[{" . $this->index_key . "}]";
        if ($this->data_key) {
            $expr .= "[$this->data_key]";
        }
        return $expr;
    }

    function value(Data $data)
    {
        /**
         * @var Table $table
         */
        $table = $this->column->get('table');
        $request = $table->get('request');
        if (!$this->data_key) {
            return $request[$this->request_key][$data->get($this->index_key)] ?? null;
        }

        if (!isset($request['htbl'.$table->attrs->get('id')])) {
            return $data->get($this->data_key);
        } else {
            return $request[$this->request_key][$data->get($this->index_key)][$this->data_key] ?? null;
        }
    }

}
