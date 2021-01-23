<?php

namespace FlSouto\HtmlTable;

class Sorting
{

    protected $table;
    protected $default_col = '';
    protected $default_ord = 'DESC';
    protected $current = null;

    function __construct(Table $table)
    {
        $this->table = $table;
    }

    function defaults($col, $ord = 'DESC')
    {
        $this->default_col = $col;
        $this->default_ord = $ord;
        return $this;
    }

    protected function getRequestKey()
    {
        $id = $this->table->get('attrs')->get('id');
        return "htbl{$id}_sort";
    }

    function current()
    {
        if (is_null($this->current)) {
            $request = $this->table->get('request');
            $parts = explode(' ', $request[$this->getRequestKey()] ?? '');
            $this->current = new SortingArg;
            $this->current->col = $parts[0] ?: $this->default_col;
            $this->current->ord = $parts[1] ?? $this->default_ord;
            $col = null;
            foreach($this->table->get('cols') as $c){
                /**
                 * @var Column $c
                 */
                if($c->get('sort_by') == $this->current->col){
                    $col = $c;
                    break;
                }
            }
            if (!$col) {
                $this->current->col = $this->default_col;
            }
            if (!in_array($this->current()->ord, ['ASC', 'DESC'])) {
                $this->current->ord = $this->default_ord;
            }

        }
        return $this->current;
    }

    function url($col)
    {
        $order = $this->default_ord;
        if ($this->current()->col == $col) {
            $order = $this->current()->ord == 'DESC' ? 'ASC' : 'DESC';
        }
        $request = $this->table->get('request');
        $request[$this->getRequestKey()] = $col . ' ' . $order;
        return '?' . http_build_query($request);
    }

}
