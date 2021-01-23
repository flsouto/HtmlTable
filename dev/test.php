<?php

require(__DIR__."/../vendor/autoload.php");

$tbl = new FlSouto\HtmlTable\Table();

$tbl->col('id');
$tbl->col('name');

$tbl->data([
    ['id' =>1, 'name'=>'blah']
]);

echo $tbl;
