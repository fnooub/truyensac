<?php

include 'functions.php';

$id = $_GET['id'] ?? 10577;
$base_url = 'http://truyensac.herokuapp.com/';

$single_curl = single_curl('https://dichngay.com/translate?u=https://m.sinodan.cc/list/' . $id . '.html');

$single_curl = html_entity_decode($single_curl);

preg_match('@<h1>(.*?)</h1>@si', $single_curl, $tieude);

// mota

preg_match('@<div class="mod book-intro">\s*<div class="bd">\s*(.*?)\s*</div>@si', $single_curl, $mota);


// list urls
preg_match_all('@<ul class="list">(.*?)</ul>@si', $single_curl, $list_urls);

//print_r($list_urls[1][1]);

$trang_tiep = isset($_GET['nextpg']) ? $list_urls[0][0] : $list_urls[1][1];
$nextpg = isset($_GET['nextpg']) ? '&nextpg' : null;


preg_match_all('@<a .+?(\d+).html.+?>\s*(.+?)\s*</a>@si', $trang_tiep, $links);

// nextpage
preg_match('@<a class="nextPage".+?list%2F(.+?).html.+?>@si', $single_curl, $nextpage);

// xem
if (isset($_GET['get'])) {
	$s = $_GET['s'] ?? 0;
	$e = $_GET['e'] ?? count($links[1]);
	if (isset($_GET['txt'])) {
		echo header("Content-Type: text/plain");
		$txt = '&txt';
	} else {
		echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
		$txt = null;
	}
	for ($i = $s; $i < $e; $i++) { 
		$urls[] = $base_url . 'view.php?id=' . $links[1][$i] . $txt;
	}
	$content = multi_curl($urls);
	echo $content;
	exit;
}
// xem
if (isset($_GET['view'])) {
	$s = $_GET['s'] ?? 0;
	$e = $_GET['e'] ?? count($links[1]);
	if (isset($_GET['txt'])) {
		echo header("Content-Type: text/plain");
		$txt = '&txt';
	} else {
		echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
		$txt = null;
	}
	for ($i = $s; $i < $e; $i++) { 
		echo file_get_contents($base_url . 'view.php?id=' . $links[1][$i] . $txt);
	}
	exit;
}
// tai ve
if (isset($_GET['download'])) {
	$s = $_GET['s'] ?? 0;
	$e = $_GET['e'] ?? count($links[1]);
	$file = "$id.txt";
	if (file_exists($file)) {
		unlink($file);
	}

	for ($i = $s; $i < $e; $i++) { 
		$text = fopen($file, "a+") or die("Unable to open file!");
		fwrite($text, file_get_contents($base_url . 'view.php?id=' . $links[1][$i] . '&txt'));
		fclose($text);
	}
	// download
	header('Content-Description: File Transfer');
	header('Content-Disposition: attachment; filename=' . basename($file));
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file));
	header("Content-Type: text/plain");
	readfile($file);
	exit('ok');
}

if (isset($_GET['list_download']) && $id) {
	echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
	if (count($links[1]) > 10) {
		$count = ceil(count($links[1]) / 10);
	} else {
		$count = 1;
	}
	if ($count > 1) {
		$tong = count($links[1]);
		for ($i = 1; $i <= $count; $i++) { 
			$start = ($i - 1) * 10 + 1; //10 la so khoang cach giua bd va kt
			$bd = ($i == 1) ? $start - 1 : $start;
			$kt = ($i == $count) ? $tong : $start + 9;

			echo '<p><a href="list.php?id=' . $id . $nextpg . '&download&s=' . $bd . '&e=' . $kt . '">' . $bd . ' - ' . $kt . '</a> ~> <a href="list.php?id=' . $id . $nextpg . '&get&txt&s=' . $bd . '&e=' . $kt . '">GET</a></p>';
		}
	} else {
		echo "<p>Ít chương tải ngoài trang chủ</p>";
	}
	exit;
}

if (isset($_GET['merge'])) {
	header("Content-Type: text/plain");
	//header('Content-Disposition: attachment; filename=' . $id . '.txt');
	for ($i=0; $i < count($links[1]); $i++) { 
		$urls[] = base_url() . 'view.php?id=' . $links[1][$i] . '&list';
	}
	echo multi_curl($urls);
	exit();
}

echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>a { text-decoration: none }</style>';
echo '<h1>' . $tieude[1] . '</h1>';
echo '<p>' . $mota[1] . '</p>';
echo '<a href="list.php?id=' . $id . $nextpg . '&download">Tai ve TXT</a>';
echo ' | <a href="list.php?id=' . $id . $nextpg . '&list_download">Tai ve list</a>';
echo ' | <a href="list.php?id=' . $id . $nextpg . '&view">Xem HTML</a>';
echo ' | <a href="list.php?id=' . $id . $nextpg . '&view&txt">Xem TXT</a>';
echo ' | <a href="list.php?id=' . $id . $nextpg . '&get&txt">Get TXT</a>';
echo ' | <a href="ts_' . $id . '.txt">Get TXT2</a>';
echo '<hr>';

for ($i=0; $i < count($links[1]); $i++) { 
	echo '<p>[' . $i .'] <a href="view.php?id=' . $links[1][$i] . '">' . $links[2][$i] . '</a></p>';
}

if (isset($nextpage[1])) {
	echo '<a href="list.php?id=' . $nextpage[1] . '&nextpg">Trang tiep</a>';
}
