<?php
	$raceid = $_GET['race'];
	include_once('assets/scripts.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Election Result Reporting :: Electoral Commission</title>
		<link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
		<link href="assets/primary.css" rel="stylesheet">
		<script src="https://kit.fontawesome.com/15deeaa0d3.js"></script>
	</head>
	<body>
		<div class="header">
			<div class="container">
				<a href="index.html"><img src="assets/img/EClogo.png" /></a>
			</div>
		</div>
		<div class="container">
			<div class="menu">
			
			</div>
			
			<?php getResults($raceid); ?>
		</div>
		<div class="footer">
			<div class="copyright-msg">
				&copy; Electoral Commission . All rights reserved.
			</div>
			<div class="submsg">
				All results are unofficial until certified in the state canvass.
			</div>
		</div>
	</body>
</html>