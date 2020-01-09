<?php
	include_once('assets/scripts-home.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Election Result Reporting :: Electoral Commission</title>
		<link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
		<script src="https://kit.fontawesome.com/15deeaa0d3.js"></script>
		<script type="text/javascript" src="assets/extra.js"></script>
		<link href="assets/primary.css" rel="stylesheet">
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
			<div class="welcome-jumbo">
				<h1>Welcome!</h1>
				<p>
					This is the Election Result Reporting center, a service of the Electoral Commission. This tool
					allows you to search results of past elections conducted by the Commission, including races that may have just taken
					place.
				</p>
				<p>
					Use the box below to search for past races by date. Enjoy!
				</p>
			</div>
			
			<div class="notice-box">
				<h5>
					<i class="fas fa-exclamation-circle"></i> Status of results
				</h5>
				<p>
					All results shown on this page are considered <b>unofficial</b> until the results have been certified by canvass. Even if the "precincts reporting"
					or count status bar says "100% counted", the result is not final until certification.
				</p>
			</div>
			
			<div class="category-tool">
			
			<h2>Get results by date</h2>
				<form method="post">
					<select name="date" id="date">
						<option value="" disabled selected>Select a date...</option>
						<?php
							$query = mysqli_query(connToDB(), "SELECT DISTINCT date FROM race ORDER BY date DESC");
							
							while ($row = mysqli_fetch_assoc($query)) {
								echo "<option value='".$row['date']."'>".date('l, F j, Y', strtotime($row['date']))."</option>";
							}
						?>
					</select>
				</form>
			</div>
			
			<div class="search-result">
				<div id="results-box">
					
				</div>
			</div>
		</div>
		<div class="footer">
			<div class="copyright-msg">
				&copy; Electoral Commission. All rights reserved.
			</div>
			<div class="submsg">
				All results are unofficial until certified in the state canvass.
			</div>
		</div>
	</body>
</html>