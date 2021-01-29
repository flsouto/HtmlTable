<?php

namespace FlSouto\HtmlTable\Columns;

use FlSouto\HtmlTable\Column;
use FlSouto\HtmlTable\Data;
use FlSouto\HtmlTable\Elements\In;

class Button extends Column
{

    /**
     * @var In $input
     */
    protected $input;
    protected $label;
    protected $url;
    protected $newtab = false;
    protected $confirm = '';

    function init()
    {
        $this->sorter = null;
        $this->sortable = false;
        $this->heading = $this->key;
        $this->input = new In;
        $this->input->attrs->merge([
            'type' => 'button',
            'value' => '{button.label}'
        ]);
        $this->td->template('{button.element}');
    }

    function label($label)
    {
        $this->label = $label;
        return $this;
    }

    function url($url, $newtab = false)
    {
        $this->url = $url;
        $this->newtab = $newtab;
        return $this;
    }

    function blank($blank)
    {
        $this->newtab = (bool)$blank;
        return $this;
    }

    function element($custom)
    {
        $input = $this->input;
        $input($custom);
        return $this;
    }

    function confirm($message){
        $this->confirm = $message;
        return $this;
    }

    function render(Data $data)
    {
        if ($this->url) {
            if ($this->newtab) {
                $js = "window.open('$this->url','_blank')";
            } else {
                $js = "window.location.href='$this->url'";
            }
            if($this->confirm){
                $confirm = str_replace(['"',"'"],['&quot;',"\\'"],$this->confirm);
                $js = "if(confirm('$confirm')){ $js }";
            }
            $this->input->attrs->set('onClick', $js);
        }
        $data->set('button.element', $this->input);
        $data->set('button.label', $this->label);
        $this->td->data = $data;
        $this->input->data = $data;
        return $this->td->__toString();
    }


}
