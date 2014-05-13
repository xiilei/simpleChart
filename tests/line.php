<?php

require __DIR__.'/../src/xDraw.class.php';
$xdraw = new \SimpleChart\xDraw(400, 300);

$xdraw->setShadow(array('X'=>10,'Y'=>10,array('Color'=>array('R'=>210,'G'=>210,'B'=>210,'Alpha'=>60))));
$xdraw->drawLine(250, 10, 50, 100);

$xdraw->stroke();
