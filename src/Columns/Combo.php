<?php

namespace FlSouto\HtmlTable\Columns;

use FlSouto\HtmlTable\Column;
use FlSouto\HtmlTable\Data;
use FlSouto\HtmlTable\Element;
use FlSouto\HtmlTable\Table;

class Combo extends Column
{

    /**
     * @var Element $select
     */
    protected $select;
    protected $options = [];
    protected $caption = "";

    function init()
    {
        $this->select = new class() extends Element{
            /**
             * @var Inputable
             */
            var $inputable;

            /**
             * @var Table
             */
            var $table;

            function tag(){
                return 'select';
            }

            function content(){
                $str = "<option value=''>{combo.caption}</option>";
                foreach($this->data->get('combo.options') as $k => $v){
                    $selected = '';
                    if($this->inputable->value($this->data) == $k){
                        $selected = 'selected';
                    }
                    $str .= "<option value='$k' $selected>$v</option>";
                }
                return $str;
            }
        };

        $this->select->table = $this->table;
        $this->select->inputable = new Inputable($this);

        $this->select->attrs->merge([
            'name' => $this->select->inputable->name(),
        ]);

        $this->td->template('{combo.element}');
        $this->heading = $this->th::pretty($this->select->inputable->get('data_key')?:$this->select->inputable->get('request_key'));
        $this->sort_by = $this->select->inputable->get('data_key');

        $this->sorter = function ($a, $b) {
            $a = $a[$this->sort_by] ?? null;
            $b = $b[$this->sort_by] ?? null;

            $a = self::unaccent($this->options[$a] ?? $a);
            $b = self::unaccent($this->options[$b] ?? $b);

            return $a > $b;
        };

    }

    function element($custom){
        call_user_func($this->select, $custom);
        return $this;
    }

    function caption($caption){
        $this->caption = $caption;
        return $this;
    }

    function options(array $options){
        $this->options = $options;
        return $this;
    }

    function render(Data $data)
    {
        $data->set('combo.element', $this->select);
        $data->set('combo.options', $this->options);
        $data->set('combo.caption', $this->caption);

        $this->td->data = $data;
        $this->select->data = $data;

        return $this->td->__toString();
    }


}
