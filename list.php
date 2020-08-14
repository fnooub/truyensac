<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php

include 'functions.php';

$id = $_GET['id'] ?? 10577;
$base_url = 'https://truyensac.herokuapp.com/';

$single_curl = single_curl('https://dichngay.com/translate?u=https://m.sinodan.cc/list/' . $id . '.html');

$single_curl = html_entity_decode($single_curl);

preg_match('@<h1>(.*?)</h1>@si', $single_curl, $tieude);

// mota

preg_match('@<div class="mod book-intro">\s*<div class="bd">\s*(.*?)\s*</div>@si', $single_curl, $mota);


// list urls
preg_match_all('@<ul class="list">(.*?)</ul>@si', $single_curl, $list_urls);

//print_r($list_urls[1][1]);

$trang_tiep = isset($_GET['nextpg']) ? $list_urls[0][0] : $list_urls[1][1];

preg_match_all('@<a .+?(\d+).html.+?>\s*(.+?)\s*</a>@si', $trang_tiep, $links);

// nextpage
preg_match('@<a class="nextPage".+?list%2F(.+?).html.+?>@si', $single_curl, $nextpage);

// tai ve
if (isset($_GET['view'])) {
	$s = $_GET['s'] ?? 0;
	$e = $_GET['e'] ?? count($links[1]);
	for ($i = $s; $i < $e; $i++) { 
		echo file_get_contents($base_url . 'view.php?id=' . $links[1][$i]);
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
// tai ve
if (isset($_GET['download'])) {
	$s = $_GET['s'] ?? 0;
	$e = $_GET['e'] ?? count($links[1]);
	if (file_exists("$id.html")) {
		unlink("$id.html");
	}
	for ($i = $s; $i < $e; $i++) { 
		$file = "$id.html";
		$text = fopen($file, "a+") or die("Unable to open file!");
		fwrite($text, file_get_contents($base_url . 'view.php?id=' . $links[1][$i]));
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

//echo $nextpage[1];

echo '<h1>' . $tieude[1] . '</h1>';
echo '<p>' . $mota[1] . '</p>';
echo '<a href="list.php?id=' . $id . '&download">Tai ve</a>';
echo ' | <a href="list.php?id=' . $id . '&view">Xem</a>';
echo '<hr>';

for ($i=0; $i < count($links[1]); $i++) { 
	echo '<p><a href="view.php?id=' . $links[1][$i] . '">' . $links[2][$i] . '</a></p>';
}

if (isset($nextpage[1])) {
	echo '<a href="list.php?id=' . $nextpage[1] . '&nextpg">Trang tiep</a>';
}

