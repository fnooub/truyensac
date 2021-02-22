<?php

include 'functions.php';

$link = isset($_GET['link'] ? $_GET['link'] : 'https://thienvadia.info/diendan/threads/uong-nham-thuoc-ll.1312128/');

$single_curl = single_curl($link);

preg_match('@<a class="PageNavNext ">&rarr;</a>\s*<a href=".+?" class="">(\d+)</a>@si', $single_curl, $lastPage);

for ($i = 2; $i <= $lastPage[1]; $i++) {
	$urls[] = $link . 'page-' . $i;
}
array_unshift($urls, $link);

$multi_curl = multi_curl($urls);

preg_match_all('@<article>(.+?)</article>@si', $multi_curl, $listContent);

foreach ($listContent[1] as $key => $value) {
	if (preg_match('/bbCodeQuote/', $value)) {
		unset($listContent[1][$key]);
	}
	if (mb_strlen($value, 'UTF-8') < 1500) {
		unset($listContent[1][$key]);
	}
}

foreach ($listContent[1] as $value) {
	$value = preg_replace('@<ul class="samTextUnit samThreadPostMessageInside">.+?</ul>@si', '', $value);
	$value = strip_all_tags($value, true) . "\n\n";
	$data[] = $value;
}

file_put_contents(slug($link) . '.txt', $data);
