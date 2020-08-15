<?php

include 'functions.php';

$id = $_GET['id'] ?? 726945;

$single_curl = single_curl('https://dichngay.com/translate?u=https://m.sinodan.cc/view/' . $id . '.html');

$single_curl = html_entity_decode($single_curl);

// tieude
preg_match('@<h1 class="page-title">(.+?)</h1>@si', $single_curl, $tieude);
// content
preg_match('@<div class="page-content font-large">(.+?)</div>@si', $single_curl, $con);

if (isset($_GET['txt'])) {
	echo header("Content-Type: text/plain");
	echo "{$tieude[1]}\n";
	echo str_replace(". ", ".\n", wp_strip_all_tags($con[1], true));
	echo "\n\n";
} else {
	echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
	echo '<h1>' . $tieude[1] . '</h1>';
	echo '<div>' . $con[1] . '</div>';
}