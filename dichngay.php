<?php

if (isset($_POST['submit'])) {
	include "functions.php";
	$file = single_curl('https://dichngay.com/translate?u=' . $_POST['link']);

	//$file = nl2br($file);

	preg_match('/&lt;body&gt;(.+?)&lt;\/body&gt;/s', $file, $out);

	header("Content-Type: text/plain");

	if (isset($_POST['line'])) {
		$nd = preg_replace('/\n+/', ' ', $out[1]);
		$nd = str_replace(". ", ".\n\n", $nd);
		echo $nd;
	} else {
		echo $out[1];
	}
	exit;
}
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<form action="" method="post">
	<input type="text" name="link"><br>
	<input type="radio" id="line" name="line" value="line">
	<label for="line">line</label><br>
	<input type="submit" name="submit">
</form>
