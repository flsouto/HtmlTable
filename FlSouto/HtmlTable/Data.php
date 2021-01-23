<?php

namespace FlSouto\HtmlTable;

class Data
{
    protected $data = [];

    function set($key, $value)
    {
        if (method_exists($this, $key)) {
            $this->$key($value);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    function get($key)
    {
        return $this->data[$key] ?? null;
    }

    function merge(array $data)
    {
        foreach ($data as $k => $v) {
            $this->set($k, $v);
        }
        return $this;
    }

    function replace(array $data)
    {
        $this->data = $data;
        return $this;
    }

    function all()
    {
        return $this->data;
    }

}
