<?php

namespace FlSouto\HtmlTable;

abstract class Element
{

    /**
     * @var Attrs
     */
    public $attrs;

    /**
     * @var Data
     */
    public $data;

    protected $template;
    protected $internal = [];
    protected $callbacks = [];

    function __construct()
    {
        if (!$this->attrs) {
            $this->attrs = new Attrs($this);
        }
        if (!$this->data) {
            $this->data = new Data();
        }
        $this->init();
    }

    function __invoke($custom)
    {
        if (is_callable($custom)) {
            $this->callback($custom);
        } else {
            if (is_string($custom)) {
                $this->template($custom);
            } else {
                if (is_array($custom)) {
                    $this->attrs->merge($custom);
                }
            }
        }
        return $this;
    }

    protected function init()
    {
        $this->template = "<" . $this->tag() . " {attrs}>{content}</" . $this->tag() . ">";
    }

    abstract protected function tag();

    abstract function content();

    function __toString()
    {
        foreach ($this->callbacks as $cb) {
            if ($out = $cb($this) and is_string($out) and strstr($out, '<' . $this->tag())) {
                return $out;
            }
        }
        $this->internal['attrs'] = $this->attrs;
        $this->internal['attrs.style'] = $this->attrs->get('style');
        $this->internal['content'] = $this->content();
        return $this->evaluate($this->template);
    }

    function callback(callable $callback)
    {
        $this->callbacks[] = $callback;
        return $this;
    }

    function template($string)
    {
        if (!strstr($string, '<' . $this->tag())) {
            $string = "<" . $this->tag() . " {attrs}>$string</" . $this->tag() . ">";
        }
        $this->template = $string;
        return $this;
    }

    function evaluate($expr)
    {
        if (is_callable($expr)) {
            $output = $expr($this);
        } else {
            $output = $expr;
        }
        $data = array_merge($this->data->all(), $this->internal);
        foreach ($data as $k => $v) {
            if (!strstr($output, '{' . $k . '}') || $v === $this) {
                continue;
            }
            $output = str_replace('{' . $k . '}', $v, $output);
        }
        return $output;
    }

}
