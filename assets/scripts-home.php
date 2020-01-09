<?php
// Connect to database
function connToDB() {
	return mysqli_connect('localhost', 'root', 'xt678Nu8x*', 'elec');
}

// Get list of divisions.
function listDivisions() {
	$conn = connToDB();
	
	$query = mysqli_query($conn, "SELECT divid, name FROM divisions WHERE name != 'Province'");
	return $query;
}

/* Returns the number of precincts that have returned at least partial results.
 * 
 * Parameters:
 * raceid: The unique identifier for the specific race.
 */
function getReptPrecincts($raceid) {
	$conn = connToDB();
	
	$query = mysqli_query($conn, "SELECT SUM(precs_reporting) AS precs_reporting FROM (SELECT DISTINCT divid, precs_reporting FROM results WHERE raceid = '$raceid') AS precs");
	$row = mysqli_fetch_assoc($query);
	return $row['precs_reporting'];
}

// Returns the results of the end user's search based on dropdown menu on the home page.
if (isset($_POST['query'])) {
	$usr_query = $_POST['query'];
	$conn = connToDB();
	
	// Get the list of categories for races.
	$cat_query = mysqli_query($conn, "SELECT DISTINCT category FROM race WHERE date = '$usr_query' ORDER BY category");
	
	while($cat_row = mysqli_fetch_array($cat_query)) {
		$category = $cat_row['category'];
		
		echo "<h4>$category</h4>";
		
		$query = mysqli_query($conn, "SELECT raceid, name, certified, precs_participating FROM race WHERE date = '$usr_query' AND category = '$category' ORDER BY name ASC");
		
		echo "<table class='cat-result'>";
		while ($row = mysqli_fetch_array($query)) {
			echo "<tr>";
				echo "<td id='position'><a href='racedetail-".$row['raceid'].".html'>".$row['name']."</a></td>";
				if ($row['certified']) {
					echo "<td class='extra-info' id='certified'>";
						echo "<span id='status'><i class='fas fa-check'></i> <span id='status-text'>Certified result</span></span>";
					echo "</td>";
				} else {
					echo "<td class='extra-info' id='counting'>";
						echo "<span id='status'><span id='status-text'>".number_format(100 * (getReptPrecincts($row['raceid']) / $row['precs_participating']), 2)."% counted";
					echo "</td>";
				}
			echo "</tr>";
		}
		echo "</table>";
	}
}
?>