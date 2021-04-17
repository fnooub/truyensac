<?php

function base_url($uri = '')
{
	return 'http://truyensac.herokuapp.com/' . $uri;
}

function d04($int)
{
	return sprintf( "%04d", $int );
}

/*
crawl nội dung một trang
*/

function single_curl($link)
{
	// Tạo mới một cURL
	$ch = curl_init();

	// Cấu hình cho cURL
	curl_setopt($ch, CURLOPT_URL, $link); // Chỉ định địa chỉ lấy dữ liệu
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36'); // Giả tên trình duyệt $_SERVER['HTTP_USER_AGENT']
	curl_setopt($ch, CURLOPT_HEADER, 0); // Không kèm header của HTTP Reponse trong nội
	curl_setopt($ch, CURLOPT_TIMEOUT, 600); // Định timeout khi curl
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Trả kết quả về ở hàm curl_exec
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // Không xác nhận chứng chì ssl

	// Thực thi cURL
	$result = curl_exec($ch);

	// Ngắt cURL, giải phóng
	curl_close($ch);

	return $result;

}

/*
crawl nội dung nhiều trang
*/
function multi_curl($links){
	$mh = curl_multi_init();
	foreach($links as $k => $link) {
		$ch[$k] = curl_init();
		curl_setopt($ch[$k], CURLOPT_URL, $link);
		curl_setopt($ch[$k], CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36');
		curl_setopt($ch[$k], CURLOPT_HEADER, 0);
		curl_setopt($ch[$k], CURLOPT_TIMEOUT, 0);
		curl_setopt($ch[$k], CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch[$k], CURLOPT_SSL_VERIFYPEER, 0);
		curl_multi_add_handle($mh, $ch[$k]);
	}
	$running = null;
	do {
		curl_multi_exec($mh, $running);
	} while($running > 0);
	foreach($links as $k => $link) {
		$result[$k] = curl_multi_getcontent($ch[$k]);
		curl_multi_remove_handle($mh, $ch[$k]);
	}
	curl_multi_close($mh);
	return join('', $result);

}

/*
lấy nội dung
*/
function get_match($bd, $kt, $str, $all = false)
{
	$bd = preg_quote($bd, '/');
	$kt = preg_quote($kt, '/');
	if ($all) {
		preg_match_all('@'.$bd.'\s*(.*?)\s*'.$kt.'@siu', $str, $result);
	} else {
		preg_match('@'.$bd.'\s*(.*?)\s*'.$kt.'@siu', $str, $result);
	}
	return $result[1];
}

function get_links($str, $all = false)
{
	if ($all) {
		preg_match_all('@href=(["\'])\s*(.*?)\s*\1@si', $str, $result);
	} else {
		preg_match('@href=(["\'])\s*(.*?)\s*\1@si', $str, $result);
	}
	return $result[2];
}

/*
xoá thẻ html
*/
function remove_all_tags( $string ) {
	$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
	$string = strip_tags( $string, '<br><p>' );
	$string = preg_replace( '/\s\s+/', ' ', $string );
	$string = preg_replace( '/(<\/?p>)|(<br\s*\/?>)/i', "\n", $string );

	return trim( $string );
}

function wp_strip_all_tags( $string, $remove_breaks = false ) {
	$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
	$string = strip_tags( $string );
 
	if ( $remove_breaks ) {
		$string = preg_replace( '/[\r\n\t ]+/', ' ', $string );
	}
 
	return trim( $string );
}

/*
tải lại nội dung từ link json
*/
function get_links_content($jsonLinks, $time = 0, $redirect = false)
{
	// check exists
	if (!file_exists('count.txt')) {
		file_put_contents('count.txt', 0);
	}
	// get data
	$urls = json_decode(file_get_contents($jsonLinks), true);
	$total = count($urls);
	$count = file_get_contents("count.txt");
	if ($count == $total) {
		if ($redirect) {
			header('location: ' . $readdir);
		}
		exit('Xong');
	}
	header("Refresh: " . $time);
	$dem = $count+1;
	// url
	$link = $urls[$count];

	// tang them +1 vao file count.txt
	$file = fopen("count.txt", "w");
	fwrite($file, $dem);
	fclose($file);

	// show
	return array('link' => $link, 'count' => $count, 'total' => $total);
}

function save_content($data, $path = false)
{
	if ($path) {
		if (!is_dir($path)) {
			mkdir($path, 0777, true);
		}
	}
	$count = file_get_contents('count.txt');
	$d04 = sprintf( "%04d", $count );
	$filename = "$path/$d04.txt";
	file_put_contents($filename, $data);
	return true;
}

/*
lọc thẻ p vào nội dung văn bản
*/
function nl2p($string, $nl2br = true)
{
	// Normalise new lines
	$string = str_replace(array("\r\n", "\r"), "\n", $string);

	// Extract paragraphs
	$parts = explode("\n", $string);

	// Put them back together again
	$string = '';

	foreach ($parts as $part) {
		$part = trim($part);
		if ($part) {
			if ($nl2br) {
				// Convert single new lines to <br />
				$part = nl2br($part);
			}
			$string .= "<p>$part</p>\n";
		}
	}

	return $string;
}

/*
sửa tiêu đề chung
*/
function get_title($title, $mb = false) {
	$title = strip_tags($title);
	$title = preg_replace('/\s+/', ' ', $title);
	$title = trim($title);
	if ($mb) {
		$title = mb_convert_case($title, MB_CASE_TITLE, "UTF-8");
	}
	return html_entity_decode($title);
}

/*
slug chuỗi
*/
function slug($link)
{
	$a_str = array('ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'á', 'à', 'ả', 'ã', 'ạ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ');
	$d_str = array('đ', 'Đ');
	$e_str = array('é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ');
	$o_str = array('ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ');
	$i_str = array('í', 'ì', 'ỉ', 'ị', 'ĩ', 'Í', 'Ì', 'Ỉ', 'Ị', 'Ĩ');
	$u_str = array('ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ữ', 'ử', 'ự', 'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự');
	$y_str = array('ý', 'ỳ', 'ỷ', 'ỵ', 'ỹ', 'Ý', 'Ỳ', 'Ỷ', 'Ỵ', 'Ỹ');

	$link = str_replace($a_str, 'a', $link);
	$link = str_replace($d_str, 'd', $link);
	$link = str_replace($e_str, 'e', $link);
	$link = str_replace($o_str, 'o', $link);
	$link = str_replace($i_str, 'i', $link);
	$link = str_replace($u_str, 'u', $link);
	$link = str_replace($y_str, 'y', $link);

	$link = strtolower($link); //chuyển tất cả sang chữ thường
	$link = preg_replace('/[^a-z0-9]/', ' ', $link); //ngoài a-z0-9 thì chuyển sang khoảng trắng
	$link = preg_replace('/\s\s+/', ' ', $link); //2 khoảng trắng trở lên thì chỉ lấy 1
	$link = trim($link); //loại bỏ khoảng trắng đầu cuối
	$link = str_replace(' ', '_', $link); //chuyển khoảng trắng sang gạch ngang (-)
	return $link;
}

/*
lọc string cho app google text to speech
*/
function loc($word)
{
	$word = html_entity_decode($word);
	// loc chu
	$word = preg_replace(array('/\bria\b/iu', '/\bsum\b/iu', '/\bboa\b/iu', '/\bmu\b/iu', '/\bah\b/iu', '/\buh\b/iu', '/\bcm\b/iu', '/\bkm\b/iu', '/\bkg\b/iu', '/\bcmn\b/iu', '/\bgay go\b/iu'), array('dia', 'xum', 'bo', 'mư', 'a', 'ư', 'xen ti mét', 'ki lô mét', 'ki lô gam', 'con mẹ nó', 'khó khăn'), $word);
	// loc ki tu dac biet
	$word = preg_replace('/…/', '...', $word);
	$word = preg_replace('/\.(?:\s*\.)+/', '...', $word);
	$word = preg_replace('/,(?:\s*,)+/', ',', $word);
	$word = preg_replace('/-(?:\s*-)+/', '', $word);
	$word = preg_replace('/_(?:\s*_)+/', '', $word);
	$word = preg_replace('/-*o\s*(0|O)\s*o-*/', '...', $word);
	$word = preg_replace('/ +(\.|\?|!|,)/', '$1', $word);
	// thay the
	//$word = str_replace('"..."', '"Lặng!"', $word);
	return $word;
}