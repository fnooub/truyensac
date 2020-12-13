<?php

include 'functions.php';

$id = $_GET['id'] ?? 10577;
$book = $_GET['book'] ?? '8418_1';

$base_url = 'http://truyensac.herokuapp.com/';

$single_curl = single_curl('https://dichngay.com/translate?u=https://m.sinodan.cc/book/' . $book . '.html');

$single_curl = html_entity_decode($single_curl);

preg_match_all('@<a class="name" href=".+?(\d+).html.+?" target="_parent">(.+?)</a>@si', $single_curl, $links);

// trang tiep
preg_match('@<a class="nextPage" href=".+?book%2F(.+?).html.+?" target="_parent">@si', $single_curl, $nextpage);
// trang tiep
preg_match('@<a class="endPage" href=".+?book%2F(.+?).html.+?" target="_parent">@si', $single_curl, $endpage);

// in ra
echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<a href="index.php?book=8418_1">Mới đổi</a>';
echo ' | <a href="index.php?book=1_8418_1">Xem nhiều</a>';
echo ' | <a href="index.php?book=2_8418_1">Xem nhiều tháng</a>';
echo ' | <a href="index.php?book=3_8418_1">Sách mới</a>';
echo ' | <a href="index.php?book=4_8418_1">Số lượng chữ</a>';
echo '<hr>';

for ($i=0; $i < count($links[1]); $i++) { 
	echo '<p><a href="list.php?id=' . $links[1][$i] . '">' . $links[2][$i] . '</a></p>';
}

if (isset($nextpage[1])) {
	echo '<hr>';
	echo '<a href="index.php?book=' . $nextpage[1] . '">Trang tiep</a>';
	echo ' | <a href="index.php?book=' . $endpage[1] . '">Trang cuoi</a>';
}
