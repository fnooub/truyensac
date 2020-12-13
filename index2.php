<?php

$data = json_decode(file_get_contents('data.json'), true);

if (isset($_GET['text'])) {
	$searchword = isset($_GET['text']) ? $_GET['text'] : null;
	// search
	$matches = array();
	foreach($data as $k => $v) {
		$data_s = $v['tieude'] . ' ' . $v['mota'];
		if(preg_match("/\b$searchword\b/iu", $data_s)) {
			$matches[$k] = array('tieude' => $v['tieude'], 'mota' => $v['mota'], 'drive_id' => $v['drive_id'], 'count_chapter' => $v['count_chapter'], 'size' => $v['size']);
		}
	}
	
} else {
	$matches = $data;
}


// pagination
include "Paginate.php";

$total = count($matches);
$keyword = isset($_GET['text']) ? '&text=' . $_GET['text'] : null;

$config['current_page'] = isset($_GET['trang']) ? $_GET['trang'] : 1;
$config['total_rows'] = $total;
$config['base_url'] = '?trang=(:num)' . $keyword;
$config['per_page'] = 20;
$config['num_links'] = 9;
$config['prev_link'] = '&laquo; Trước';
$config['next_link'] = 'Sau &raquo;';

$paginate = new Paginate();
$paginate->initialize($config);

$matches = $paginate->get_array($matches);

?>
<!DOCTYPE html>
<html>
<title>W3.CSS</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<body class="w3-light-gray">

<div class="w3-container">

<form action="" method="get">
	<input type="text" name="text" class="w3-input">
	<input type="submit" value="Search" class="w3-button w3-green">
</form>

<?php if (isset($matches)): ?>
	<h2><?= count($data) ?> truyện</h2>
	<?php $count = 1 ?>
	<?php foreach ($matches as $row): ?>
		
		<div class="w3-section w3-border w3-round w3-padding w3-white">
			<h3 class="w3-medium"><a href="https://docs.google.com/uc?id=<?= $row['drive_id'] ?>"><?= $row['tieude'] ?></a></h3>
			<span class="w3-tag w3-small" id="<?= $count ?>">#<?= $count ?></span>
			<span class="w3-tag w3-small"><?= $row['count_chapter'] ?> chương</span>
			<span class="w3-tag w3-small"><?= myfilesize($row['size']) ?></span>
			<p class="w3-small w3-text-gray"><?= $row['mota'] ?></p>
		</div>

		<?php $count++ ?>
	<?php endforeach ?>
<?php endif ?>

<?php echo $paginate->w3_create_links(); ?>

</div>

</body>
</html>

<?php

function myfilesize($size, $precision = 2) {
	static $units = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
	$step = 1024;
	$i = 0;
	while (($size / $step) > 0.9) {
		$size = $size / $step;
		$i++;
	}
	return round($size, $precision).$units[$i];
}
