<?php

include "functions.php";
$file = single_curl('https://dichngay.com/translate?u=' . $_GET['link']);

//$file = nl2br($file);

preg_match('/&lt;body&gt;(.+?)&lt;\/body&gt;/s', $file, $out);

header("Content-Type: text/plain");
echo $out[1];
