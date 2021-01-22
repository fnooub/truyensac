<?php

include 'functions.php';

$page = $_GET['page'] ?? 1;

$base_url = 'http://truyensac.herokuapp.com/665txt/';

$single_curl = single_curl('https://dichngay.com/translate?u=www.665txt.com/txtsort/9/' . $page . '.html');

$single_curl = html_entity_decode($single_curl);

preg_match('@<div class="l">(.+?)</div>@si', $single_curl, $new);
preg_match('@<div class="r">(.+?)</div>@si', $single_curl, $top);
preg_match('@<em id="pagestats">(\d+)/(\d+)</em>@si', $single_curl, $pageLinks);
	
preg_match_all('@<span class="s2">《<a href=".+?read%2F(\d+)%2F(\d+)%2F.+?" target="_parent">(.+?)</a>》</span>@si', $new[1], $links);
preg_match_all('@<a href=".+?read%2F(\d+)%2F(\d+)%2F.+?" target="_parent">(.+?)</a>@si', $top[1], $linkstop);
// top
for ($i=0; $i < count($linkstop[1]); $i++) { 
	echo '<p><a href="read.php?cat=' . $linkstop[1][$i] . '&bid=' . $linkstop[2][$i] . '">' . $linkstop[3][$i] . '</a></p>';
}

echo '<hr>';
// new
for ($i=0; $i < count($links[1]); $i++) { 
	echo '<p><a href="read.php?cat=' . $links[1][$i] . '&bid=' . $links[2][$i] . '">' . $links[3][$i] . '</a></p>';
}

?>
<hr>
<a href="?page=<?= $pageLinks[1]+1 ?>">Trang tiep</a> | 
<a href="?page=<?= $pageLinks[2] ?>">Trang cuoi</a>