<?php

include 'functions.php';

$id = $_GET['id'] ?? 726945;

$single_curl = single_curl('https://dichngay.com/translate?u=https://m.sinodan.cc/view/' . $id . '.html');

$single_curl = html_entity_decode($single_curl);

// tieude
preg_match('@<h1 class="page-title">(.+?)</h1>@si', $single_curl, $tieude);
// content
preg_match('@<div class="page-content font-large">(.+?)</div>@si', $single_curl, $con);

preg_match_all('/> *【 *(\d+) *】 *</', $con[1], $pages);
$nd = preg_replace('/<center class="chapterPages">.+?<\/center>/', '', $con[1]);
$nd = preg_replace('/<font color="blue">.+?<\/font>/', '', $nd);
$nd = str_replace('Mới nhất chương thỉnh phỏng vấn https://m.sinodan.cc<p>', '', $nd);

if (isset($_GET['list'])) {
	if (!empty($pages[1])) {
		foreach ($pages[1] as $page) {
			$urls[] = base_url() . 'view.php?id=' . $id . '_' . $page . '&txt';
		}
		echo multi_curl($urls);
	}
	exit;
}


if (isset($_GET['txt'])) {
	echo header("Content-Type: text/plain");
	echo "{$tieude[1]}\n";
	$nd = str_replace(". ", ".\n\n", wp_strip_all_tags($nd, true));
	echo normalize($nd);
	echo "\n\n";
} else {
	echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
	echo '<h1>' . $tieude[1] . '</h1>';
	if (!empty($pages[1])) {
		echo '<h2><a href="?id=' . $id . '&list">Tai ve</a></h2>';
		foreach ($pages[1] as $page) {
			echo '<a href="?id='.$id.'_'.$page.'">['.$page.']</a>';
		}
	}
	echo '<div>' . normalize($nd) . '</div>';
}

function normalize($text = '')
{
	$arr = [
		'《' => '⟨',
		'》' => '⟩',
		'　' => ' ',
		'ˉ' => '¯',
		'‥' => '¨',
		'‧' => '·',
		'•' => '·',
		'‵' => '`',
		'｀' => '`',
		'。' => '.',
		'﹒' => '.',
		'．' => '.',
		'﹐' => ',',
		'，' => ',',
		'﹑' => ',',
		'、' => ',',
		'︰' => ':',
		'∶' => ':',
		'﹔' => ';',
		'；' => ';',
		'﹕' => ':',
		'：' => ':',
		'﹖' => '?',
		'？' => '?',
		'﹗' => '!',
		'！' => '!',
		'﹙' => '(',
		'（' => '(',
		'﹚' => ')',
		'）' => ')',
		'﹛' => '{',
		'｛' => '{',
		'﹜' => '}',
		'｝' => '}',
		'【' => '[',
		'﹝' => '[',
		'［' => '[',
		'】' => ']',
		'﹞' => ']',
		'］' => ']',
		'＾' => '^',
		'﹟' => '#',
		'＃' => '#',
		'﹠' => '&',
		'＆' => '&',
		'﹡' => '*',
		'＊' => '*',
		'﹢' => '+',
		'＋' => '+',
		'﹣' => '-',
		'－' => '-',
		'﹤' => '<',
		'＜' => '<',
		'﹥' => '>',
		'＞' => '>',
		'﹦' => '=',
		'＝' => '=',
		'﹩' => '$',
		'＄' => '$',
		'﹪' => '%',
		'％' => '%',
		'﹫' => '@',
		'＠' => '@',
		'≒' => '≈',
		'≦' => '≤',
		'≧' => '≥',
		'︱' => '|',
		'｜' => '|',
		'︳' => '|',
		'︿' => '∧',
		'﹀' => '∨',
		'／' => '/',
		'＼' => '\\',
		'╴' => '_',
		'＿' => '_',
		'「' => '“',
		'」' => '”',
		'『' => '‘',
		'』' => '’',
		'＂' => '"',
		'～' => '~',
		'｟' => '(',
		'｠' => ')',
		'ａ' => 'a',
		'ｂ' => 'b',
		'ｃ' => 'c',
		'ｄ' => 'd',
		'ｅ' => 'e',
		'ｆ' => 'f',
		'ｇ' => 'g',
		'ｈ' => 'h',
		'ｉ' => 'i',
		'ｊ' => 'j',
		'ｋ' => 'k',
		'ｌ' => 'l',
		'ｍ' => 'm',
		'ｎ' => 'n',
		'ｏ' => 'o',
		'ｐ' => 'p',
		'ｑ' => 'q',
		'ｒ' => 'r',
		'ｓ' => 's',
		'ｔ' => 't',
		'ｕ' => 'u',
		'ｖ' => 'v',
		'ｗ' => 'w',
		'ｘ' => 'x',
		'ｙ' => 'y',
		'ｚ' => 'z',
		'Ａ' => 'A',
		'Ｂ' => 'B',
		'Ｃ' => 'C',
		'Ｄ' => 'D',
		'Ｅ' => 'E',
		'Ｆ' => 'F',
		'Ｇ' => 'G',
		'Ｈ' => 'H',
		'Ｉ' => 'I',
		'Ｊ' => 'J',
		'Ｋ' => 'K',
		'Ｌ' => 'L',
		'Ｍ' => 'M',
		'Ｎ' => 'N',
		'Ｏ' => 'O',
		'Ｐ' => 'P',
		'Ｑ' => 'Q',
		'Ｒ' => 'R',
		'Ｓ' => 'S',
		'Ｔ' => 'T',
		'Ｕ' => 'U',
		'Ｖ' => 'V',
		'Ｗ' => 'W',
		'Ｘ' => 'X',
		'Ｙ' => 'Y',
		'Ｚ' => 'Z',
		'１' => '1',
		'２' => '2',
		'３' => '3',
		'４' => '4',
		'５' => '5',
		'６' => '6',
		'７' => '7',
		'８' => '8',
		'９' => '9',
		'０' => '0',
	];

	foreach ($arr as $key => $value) {
		$text = str_replace($key, $value, $text);
	}

	return $text;

}