<?php

require __DIR__.'/../src/xDraw.class.php';
$xdraw = new \SimpleChart\xDraw(400, 300);

//$xdraw->Antialias=false;
$xdraw->drawPolygon(array(10,10,50,100,100,10));
$xdraw->stroke();

