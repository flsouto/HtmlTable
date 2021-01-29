<?php

namespace FlSouto\HtmlTable\Columns;

use FlSouto\HtmlTable\Column;
use FlSouto\HtmlTable\Data;
use FlSouto\HtmlTable\Elements\In;

class Checkbox extends Column
{

    /**
     * @var In $input
     */
    protected $input;

    /**
     * @var Inputable
     */
    protected $inputable;

    function init()
    {
        $this->inputable = new Inputable($this);

        $cb_class = $this->table->attrs->get('id').'_'.str_replace(['[',']'],['_',''],$this->key);

        $this->input = new In;
        $this->input->attrs->merge([
            'type' => 'checkbox',
            'name' => $this->inputable->name(),
            'class' => $cb_class,
            'checked' => function () {
                return !empty($this->inputable->value($this->td->data));
            }
        ]);

        $this->td->template('{checkbox.element}');

        $this->sorter = null;
        $this->sortable = false;

        $js = "document.querySelectorAll('input[class=\'$cb_class\']:checked').length > 0 ? [...document.getElementsByClassName('$cb_class')].map( (el) => { el.checked = this.checked } ) : [...document.getElementsByClassName('$cb_class')].map( (el) => { el.checked = this.checked } )";
        $this->heading = '<input autocomplete="off" type="checkbox" onClick="'.$js.'" />';

    }


    function render(Data $data)
    {
        $data->set('checkbox.element', $this->input);

        $this->td->data = $data;
        $this->input->data = $data;

        return $this->td->__toString();
    }


}
