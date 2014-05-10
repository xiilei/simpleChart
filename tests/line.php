<?php

require __DIR__.'/../src/xDraw.class.php';
$xdraw = new \SimpleChart\xDraw(400, 300);

//$xdraw->Antialias=false;
$xdraw->useAlpha(false);
$xdraw->drawLine(250, 10, 50, 100);

$xdraw->stroke();
