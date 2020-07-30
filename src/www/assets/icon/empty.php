<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
</head>
<body>
	<?php 
		$v =  $_GET['msg'];
		if( $v == 'work_in_progress_img') {
			?><div style="top:20px;height: 50px;width: 100%; text-align: left;"><img src="/assets/icon/loading_arrows.gif" height="20px" /></div><?php
		} else {
			echo $_GET['msg'] ;
		}
	?>
</body>
</html>