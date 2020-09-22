<?php

$data = json_decode(file_get_contents('sactxt_data.json'));

?>
<!DOCTYPE html>
<html>
<title>W3.CSS</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<body class="w3-light-gray">

<div class="w3-container">
	
	<?php foreach ($data as $row): ?>
		
		<div class="w3-section w3-border w3-round w3-padding w3-white">
			<h3 class="w3-medium"><a href="https://docs.google.com/uc?id=<?= $row->drive_id ?>"><?= $row->tieude ?></a></h3>
			<p class="w3-small w3-text-gray"><?= $row->mota ?></p>
		</div>

	<?php endforeach ?>

</div>

</body>
</html>
