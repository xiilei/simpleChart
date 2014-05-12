<?php

require __DIR__.'/../src/xDraw.class.php';
$xdraw = new \SimpleChart\xDraw(400, 300);

$xdraw->setShadow(true,array('X'=>10,'Y'=>10,'R'=>180,'G'=>180,'B'=>255,'Alpha'=>80));
$xdraw->drawPolygon(array(10,10,50,100,100,10),array('NoFill'=>true));
$xdraw->stroke();

