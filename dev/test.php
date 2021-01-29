<?php

require(__DIR__."/../vendor/autoload.php");

$tbl = new FlSouto\HtmlTable\Table(['id'=>'blah']);

$tbl->ccheck('selection[id]');
$tbl->col('id');
$tbl->col('name');
$tbl->cbtn('Teste','?rm=1')->confirm("'Sure?'");

$tbl->data([
    ['id' =>1, 'name'=>'blah'],
    ['id' =>2, 'name'=>'blah'],
    ['id' =>3, 'name'=>'blah'],
]);

echo $tbl;
