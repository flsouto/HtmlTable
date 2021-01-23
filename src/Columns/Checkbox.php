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

        $this->input = new In;
        $this->input->attrs->merge([
            'type' => 'checkbox',
            'name' => $this->inputable->name(),
            'checked' => function () {
                return !empty($this->inputable->value($this->td->data));
            }
        ]);

        $this->td->template('{checkbox.element}');

        $this->sorter = null;
        $this->sortable = false;
        $this->heading = '&nbsp;';

    }

    function render(Data $data)
    {
        $data->set('checkbox.element', $this->input);

        $this->td->data = $data;
        $this->input->data = $data;

        return $this->td->__toString();
    }


}
