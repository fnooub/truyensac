<?php

include 'functions.php';

$cat = $_GET['cat'] ?? 32;
$bid = $_GET['bid'] ?? 32976;
$base_url = 'http://truyensac.herokuapp.com/665txt/';

$single_curl = single_curl('https://dichngay.com/translate?u=www.665txt.com/read/' . $cat . '/' . $bid . '/');

$single_curl = html_entity_decode($single_curl);

preg_match('@<h1>(.*?)</h1>@si', $single_curl, $tieude);

// mota
preg_match('@<div id="intro">\s*(.*?)\s*</div>@si', $single_curl, $mota);


// list urls
preg_match('@<div id="list">(.*?)</div>@si', $single_curl, $list_urls);

preg_match_all('@<a href=".+?(\d+).html.+?" target="_parent">(.+?)</a>@si', $list_urls[1], $out);

// xem
if (isset($_GET['txt'])) {
	$s = $_GET['s'] ?? 0;
	$e = $_GET['e'] ?? count($out[1]);
	echo header("Content-Type: text/plain");
	for ($i = $s; $i < $e; $i++) { 
		echo file_get_contents($base_url . 'chapter.php?cat=' . $cat. '&bid=' . $bid . '&cid=' . $out[1][$i] . '&txt');
	}
	exit;
}

// output
echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>a { text-decoration: none }</style>';
echo '<h1>' . $tieude[1] . '</h1>';
echo '<p>' . $mota[1] . '</p>';
echo '<a href="?txt&cat=' . $cat . '&bid=' . $bid . '">TXT</a>';
echo ' <span style="color:red">them hau to s=0&e=10 de cat chuong</span>';
echo '<hr>';

for ($i = 0; $i < count($out[1]); $i++) {
	echo '<p>[' . $i . '] <a href="chapter.php?cat=' . $cat. '&bid=' . $bid . '&cid=' . $out[1][$i] . '">' . $out[2][$i] . '</a></p>';
}

