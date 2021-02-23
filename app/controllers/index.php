<?php

use RedBeanPHP\R;

echo 'In Controller';

db_connect();

$book = R::dispense('book');
$book->name = 'Some Book';
$id = R::store($book);

dd($id);
