<?php
// Connect to database
function connToDB() {
	return mysqli_connect('localhost', 'root', 'xt678Nu8x*', 'elec');
}

/* Return basic race information.
 *
 * Parameters:
 * raceid: The unique identifier for the specific race.
 * type: The type of information to be returned, this can include:
 *	- regvoters: Number of voters registered and entitled to vote in the race.
 *	- name: The name of the race.
 * 	- date: The date of the race.
 *	- isIRV: Is the race instant-runoff or plurality?
 *	- participating: The number of precincts participating in the race.
 */
function getRaceInfo($raceid, $type) {
	$conn = connToDB();
	
	if ($type == "regvoters") {
		$query = mysqli_query($conn, "SELECT regvoters FROM race WHERE raceid = '$raceid'");
		$row = mysqli_fetch_assoc($query);
		return $row['regvoters'];
	} else if ($type == "name") {
		$query = mysqli_query($conn, "SELECT name FROM race WHERE raceid = '$raceid'");
		$row = mysqli_fetch_assoc($query);
		return $row['name'];
	} else if ($type == "category") {
		$query = mysqli_query($conn, "SELECT category FROM race WHERE raceid = '$raceid'");
		$row = mysqli_fetch_assoc($query);
		return $row['category'];
	} else if ($type == "certified") {
		$query = mysqli_query($conn, "SELECT certified FROM race WHERE raceid = '$raceid'");
		$row = mysqli_fetch_assoc($query);
		return $row['certified'];
	} else if ($type == "date") {
		$query = mysqli_query($conn, "SELECT date FROM race WHERE raceid = '$raceid'");
		$row = mysqli_fetch_assoc($query);
		return $row['date'];
	} else if ($type == "isIRV") {
		$query = mysqli_query($conn, "SELECT isIRV FROM race WHERE raceid = '$raceid'");
		$row = mysqli_fetch_assoc($query);
		return $row['isIRV'];
	} else if ($type == "participating") {
		$query = mysqli_query($conn, "SELECT precs_participating FROM race WHERE raceid = '$raceid'");
		$row = mysqli_fetch_assoc($query);
		return $row['precs_participating'];
	}
}

/* Returns the number of votes cast in a specific race.
 * 
 * Parameters:
 * raceid: The unique identifier for the specific race.
 * tcp: Returns the number of votes counted in the final preference distribution.
 */
function getVotesCastByRace($raceid, $tcp) {
	$conn = connToDB();
	
	if ($tcp == 0) {
		$query = mysqli_query($conn, "SELECT election_day, postal, advance FROM results WHERE raceid = '$raceid' AND isTCP = 0");
	} else if ($tcp == 1) {
		$query = mysqli_query($conn, "SELECT election_day, postal, advance FROM results WHERE raceid = '$raceid' AND isTCP = 1");
	}
	
	$num_votes = 0;
	
	while ($row = mysqli_fetch_array($query)) {
		$num_votes += $row['election_day'] + $row['postal'] + $row['advance'];
	}
	
	return $num_votes;
}

/* Returns the number of precincts that have returned at least partial results.
 * 
 * Parameters:
 * raceid: The unique identifier for the specific race.
 * division: The specific division to get the information from. To get the total for a race, enter 0.
 */
function getReptPrecincts($raceid, $division) {
	$conn = connToDB();
	
	if ($division == 0) {
		$query = mysqli_query($conn, "SELECT SUM(precs_reporting) AS precs_reporting FROM (SELECT DISTINCT divid, precs_reporting FROM results WHERE raceid = '$raceid') AS precs");
		$row = mysqli_fetch_assoc($query);
		return $row['precs_reporting'];
	} else {
		$query = mysqli_query($conn, "SELECT DISTINCT precs_reporting FROM results WHERE raceid = '$raceid' AND divid = '$division'");
		$row = mysqli_fetch_assoc($query);
		return $row['precs_reporting'];
	}
}

/* Returns the votes cast by category and division for a specific candidate in a race.
 *
 * Parameters:
 * raceid: The unique identifier for the specific race.
 * candid: The unique identifier for the specific candidate.
 * type: The vote type (advance, postal, election_day). Enter "all" to get the total.
 * division: The specific division to get the information from. Enter 0 to get the total.
 * tcp: Returns TCP-specific results. "0" for primary votes, "1" for TCP.
 */
function getCandVotesByRaceDivision($raceid, $candid, $type, $division, $tcp) {
	$conn = connToDB();
	
	if ($tcp == 0) {
		if ($division == 0) {
			if ($type == "all") {
				$query = mysqli_query($conn, "SELECT SUM(election_day + postal + advance) AS sumvotes FROM results WHERE raceid = '$raceid' AND candid = '$candid' AND isTCP = 0");
				$row = mysqli_fetch_assoc($query);
				return $row['sumvotes'];
			} else {
				$query = mysqli_query($conn, "SELECT $type AS votes_by_type FROM results WHERE raceid = '$raceid' AND candid = '$candid' AND isTCP = 0");
				$row = mysqli_fetch_assoc($query);
				return $row['votes_by_type'];
			}
		} else {
			if ($type == "all") {
				$query = mysqli_query($conn, "SELECT SUM(election_day + postal + advance) AS sumvotes FROM results WHERE raceid = '$raceid' AND candid = '$candid' AND divid = '$division' AND isTCP = 0");
				$row = mysqli_fetch_assoc($query);
				return $row['sumvotes'];
			} else {
				$query = mysqli_query($conn, "SELECT $type AS votes_by_type FROM results WHERE raceid = '$raceid' AND candid = '$candid' AND divid = '$division' AND isTCP = 0");
				$row = mysqli_fetch_assoc($query);
				return $row['votes_by_type'];
			}
		}
	} else if ($tcp == 1) {
		if ($division == 0) {
			if ($type == "all") {
				$query = mysqli_query($conn, "SELECT SUM(election_day + postal + advance) AS sumvotes FROM results WHERE raceid = '$raceid' AND candid = '$candid' AND isTCP = 1");
				$row = mysqli_fetch_assoc($query);
				return $row['sumvotes'];
			} else {
				$query = mysqli_query($conn, "SELECT $type AS votes_by_type FROM results WHERE raceid = '$raceid' AND candid = '$candid' AND isTCP = 1");
				$row = mysqli_fetch_assoc($query);
				return $row['votes_by_type'];
			}
		} else {
			if ($type == "all") {
				$query = mysqli_query($conn, "SELECT SUM(election_day + postal + advance) AS sumvotes FROM results WHERE raceid = '$raceid' AND candid = '$candid' AND divid = '$division' AND isTCP = 1");
				$row = mysqli_fetch_assoc($query);
				return $row['sumvotes'];
			} else {
				$query = mysqli_query($conn, "SELECT $type AS votes_by_type FROM results WHERE raceid = '$raceid' AND candid = '$candid' AND divid = '$division' AND isTCP = 1");
				$row = mysqli_fetch_assoc($query);
				return $row['votes_by_type'];
			}
		}

	}
}

/* Returns the name of a division or its number of precincts for a specific race.
 * 
 * Parameters:
 * raceid: The unique identifier for the specific race.
 * divid: The unique identifier for the specific division.
 * toreturn: Toggle to determine what to return, "0" for the division name,
 * "1" for the number of precincts participating in the race.
 */
function getDivisionInfo($raceid, $divid, $toreturn) {
	$conn = connToDB();
	
	if ($toreturn == 0) {
		$query = mysqli_query($conn, "SELECT name FROM divisions WHERE divid = '$divid' ORDER BY name");
		$row = mysqli_fetch_assoc($query);
		return $row['name'];
	} else if ($toreturn == 1) {
		$query = mysqli_query($conn, "SELECT DISTINCT precs_participating, precs_reporting FROM results WHERE raceid = '$raceid' AND divid = '$divid'");
		$row = mysqli_fetch_assoc($query);
		return array($row['precs_participating'], $row['precs_reporting']);
	}
}

/* Returns the results for a particular race.
 * 
 * Parameters:
 * raceid: The unique identifier for the specific race.
 */
function getResults($raceid) {
	$conn = connToDB();
	
	// Show an error page if the race ID does not exist.
	if (!getRaceInfo($raceid, "regvoters")) {
		echo "<h1>Error</h1>";
		echo "<p>The page does not exist.</p>";
		return;
	}
	
	// Important local variables.
	$regvoters = getRaceInfo($raceid, "regvoters");
	$category = getRaceInfo($raceid, "category");
	$date = strtotime(getRaceInfo($raceid, "date"));
	$certified = getRaceInfo($raceid, "certified");
	$num_rptg_precincts = getReptPrecincts($raceid, 0);
	$num_ptcp_precincts = getRaceInfo($raceid, "participating");
	$votes_cast = getVotesCastByRace($raceid, 0);
	
	// If this race's results have been certified by the ECA, display a message here.
	if ($certified) {
		echo "<div class='certified-msg'>";
		echo "<i class='fas fa-check'></i> This race's results have been <b>certified</b> by the Electoral Commission.";
		echo "</div>";
	}
	
	// Display the name, category, and date of the race.
	echo "<p style='margin-bottom: 2px; font-size: 16px; font-weight: 600;' >$category</p>";
	echo "<div class='race-name-header'><h1>".getRaceInfo($raceid, "name")."</h1></div>";
	echo "<p style='margin: 0; margin-bottom: 28px; font-size: 20px;' ><b>".date('l, F j, Y', $date)."</b></p>";
	
	// Display turnout and precinct report information.
	echo "<div class='result-table'>";
	
	echo "<table class='overview'>";
	echo "<tr>";
		echo "<td style='padding: 0 6px; width: 50%;'><b>Turnout</b><br>";
			echo "<div style='border: 1px solid #000; margin: 12px 0; padding: 8px 0; position: relative;'>";
			echo "<div style='margin-left: 8px;'>".number_format($votes_cast)."/".number_format($regvoters)." voters (".number_format(100 * ($votes_cast / $regvoters), 2)."%)</div>";
			echo "<div style='position: absolute; height: 100%; width: ".number_format(100 * ($votes_cast / $regvoters), 2)."%; z-index: -200; background-color: #E8E8E8; top: 0;'></div>";
			echo "</div>";
		echo "</td>";
		
		echo "<td style='padding: 0 6px; width: 50%;'><b>Precincts reporting*</b><br>";
			echo "<div style='border: 1px solid #000; margin: 12px 0; padding: 8px 0; position: relative;'>";
			echo "<div style='margin-left: 8px;'>".number_format($num_rptg_precincts)."/".number_format($num_ptcp_precincts)." precincts (".number_format(100 * ($num_rptg_precincts / $num_ptcp_precincts), 2)."%)</div>";
			echo "<div style='position: absolute; height: 100%; width: ".number_format(100 * ($num_rptg_precincts / $num_ptcp_precincts), 2)."%; z-index: -200; background-color: #E8E8E8; top: 0;'></div>";
			echo "</div>";
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td colspan='2' style='font-size: 14px;'>* In instant-runoff races, this bar only indicates how many precincts have reported first preference (primary) vote figures.</td>";
	echo "</tr>";
	echo "</table>";
	
	echo "<hr>";
	
	// If this race is conducted under IRV, TCP values will be displayed here.
	if (getRaceInfo($raceid, "isIRV") == 1) {
		$tcp_votes_cast = getVotesCastByRace($raceid, 1);
		
		if ($tcp_votes_cast == 0) {
			
			echo "<h2>Two-candidate preferred vote</h2>";
			
			echo "<p>Two-candidate preferred totals are not currently available.</p>";
			
		} else {
		
			// Get candidate information, including name, party, and number of votes.
			$tcp_cand_query = mysqli_query($conn, "SELECT candidate.candid, candidate.name AS candname, party.name AS partyname, party.color AS color, incumbent, candidate.raceid FROM candidate INNER JOIN party ON candidate.partyid = party.partyid INNER JOIN results On results.candid WHERE candidate.candid = results.candid AND isTCP = 1 AND results.raceid = '$raceid' ORDER BY candname");
			
			// Display TCP vote figures.
			echo "<h2>Two-candidate preferred vote</h2>";
			
			echo "<table class='tcp-result'>";
			
			while ($row = mysqli_fetch_array($tcp_cand_query)) {
				$total_votes = getCandVotesByRaceDivision($raceid, $row['candid'], "all", 0, 1);
				
				echo "<tr>";
					echo "<td style='font-size: 16px; width: 100%;'>";
					if ($row['incumbent'] == 1) {
						echo "<b>".$row['candname']." (inc.)</b> <small>".$row['partyname']."</small>";
					} else {
						echo "<b>".$row['candname']."</b> <small>".$row['partyname']."</small>";
					}
					echo "<div style='border: 1px solid #000; margin: 12px 0; padding: 6px 0; position: relative;'>";
					echo "<div style='margin-left: 8px; font-size: 14px; color: #fff;'>".number_format($total_votes)." votes (".number_format(100 * ($total_votes / $tcp_votes_cast), 2)."%)</div>";
					echo "<div style='position: absolute; height: 100%; width: ".number_format(100 * ($total_votes / $tcp_votes_cast), 2)."%; z-index: -200; background-color: ".$row['color']."; top: 0;'></div>";
					echo "</div>";
				echo "</tr>";
			}
			
			echo "</table>";
			
			// Display TCP vote figures by division.
			echo "<h3>By division</h3>";
	
			echo "<p>Please note that only those divisions that have reported results will display here.</p>";
			
			$div_query = mysqli_query($conn, "SELECT DISTINCT divid FROM results WHERE raceid = '$raceid' AND isTCP = 1");
	
			echo "<div class='division-card-cont'>";
			
			while ($divrow = mysqli_fetch_array($div_query)) {
				
					$div_id = $divrow['divid'];
				
					echo "<div class='division-card'>";
					
					echo "<div class='division-info'>";
						echo "<h4>".strtoupper(getDivisionInfo($raceid, $divrow['divid'], 0))."</h4>";
						echo "<div style='color: #585858;'> ".getDivisionInfo($raceid, $div_id, 1)[1]."/".getDivisionInfo($raceid, $div_id, 1)[0]." precincts reporting TCP vote</div>";
					echo "</div>";
					
					echo "<table class='division-result'>";
						echo "<tr>";
							echo "<td></td>";
							echo "<td class='heading'>Election day</td>";
							echo "<td class='heading'>Advance</td>";
							echo "<td class='heading'>Postal</td>";
							echo "<td class='heading'>Total</td>";
						echo "</tr>";
						
						$cand_div = mysqli_query($conn, "SELECT election_day, postal, advance, name, results.raceid, candidate.candid AS candid FROM results INNER JOIN candidate ON results.candid = candidate.candid WHERE divid = '$div_id' AND results.raceid = '$raceid' AND isTCP = 1 ORDER BY name");
						while ($row = mysqli_fetch_array($cand_div)) {
							echo "<tr>";
								echo "<td class='cand-name'>".$row['name']."</td>";
								echo "<td>".number_format(getCandVotesByRaceDivision($raceid, $row['candid'], "election_day", $div_id, 1))."</td>";
								echo "<td>".number_format(getCandVotesByRaceDivision($raceid, $row['candid'], "advance", $div_id, 1))."</td>";
								echo "<td>".number_format(getCandVotesByRaceDivision($raceid, $row['candid'], "postal", $div_id, 1))."</td>";
								echo "<td><b>".number_format(getCandVotesByRaceDivision($raceid, $row['candid'], "all", $div_id, 1))."</b></td>";
							echo "</tr>";
						}
					
					echo "</table>";
					
					echo "</div>";
			}
			
			echo "</div>";
		
		}
		
	}
	
	if ($votes_cast == 0) {
		echo "<h2>Primary vote</h2>";
			
		echo "<p>Vote totals are not currently available.</p>";
		
	} else {
	
		// Get candidate information, including name, party, and number of votes.
		$cand_query = mysqli_query($conn, "SELECT candid, candidate.name AS candname, party.name AS partyname, party.color AS color, incumbent, raceid FROM candidate INNER JOIN party ON candidate.partyid = party.partyid AND raceid = '$raceid' ORDER BY candname");
		
		// Display primary vote figures.
		echo "<h2>Primary vote</h2>";
		
		echo "<table class='primary-result'>";
		
		while ($row = mysqli_fetch_array($cand_query)) {
			$total_votes = getCandVotesByRaceDivision($raceid, $row['candid'], "all", 0, 0);
			
			echo "<tr>";
				if ($row['incumbent'] == 1) {
					echo "<td style='padding: 4px 0; width: 30%;'><b>".$row['candname']." (inc.) </b><br><small>".$row['partyname']."</small></td>";
				} else {
					echo "<td style='padding: 4px 0; width: 30%;'><b>".$row['candname']."</b><br><small>".$row['partyname']."</small></td>";
				}
				echo "<td style='border-left: 4px solid ".$row['color']."; width: 80%; position: relative;'>";
				if ($total_votes == 0) {
					echo "<div style='margin-left: 8px;'>0 votes (0.00%)</div>";
					echo "<div style='position: absolute; height: 100%; width: 0%; z-index: -200; background-color: #E8E8E8; top: 0; left: 0;'></div>";
				} else {
					echo "<div style='margin-left: 8px;'>".number_format($total_votes)." votes (".number_format(100 * ($total_votes / $votes_cast), 2)."%)</div>";
					echo "<div style='position: absolute; height: 100%; width: ".number_format(100 * ($total_votes / $votes_cast), 2)."%; z-index: -200; background-color: #E8E8E8; top: 0; left: 0;'></div>";
				}
				echo "</td>";
			echo "</tr>";
		}
		
		echo "</table>";
		
		// Display primary vote figures by division.
		echo "<h3>By division</h3>";
		
		echo "<p>Please note that only those divisions that have reported results will display here.</p>";
		
		$div_query = mysqli_query($conn, "SELECT DISTINCT divid FROM results WHERE raceid = '$raceid'");
		
		echo "<div class='division-card-cont'>";
		
		while ($divrow = mysqli_fetch_array($div_query)) {
			
				$div_id = $divrow['divid'];
			
				echo "<div class='division-card'>";
				
				echo "<div class='division-info'>";
					echo "<h4>".strtoupper(getDivisionInfo($raceid, $divrow['divid'], 0))."</h4>";
					echo "<div style='color: #585858;'> ".getDivisionInfo($raceid, $div_id, 1)[1]."/".getDivisionInfo($raceid, $div_id, 1)[0]." precincts reporting</div>";
				echo "</div>";
				
				echo "<table class='division-result'>";
					echo "<tr>";
						echo "<td></td>";
						echo "<td class='heading'>Election day</td>";
						echo "<td class='heading'>Advance</td>";
						echo "<td class='heading'>Postal</td>";
						echo "<td class='heading'>Total</td>";
					echo "</tr>";
					
					$cand_div = mysqli_query($conn, "SELECT election_day, postal, advance, name, results.raceid, candidate.candid AS candid FROM results INNER JOIN candidate ON results.candid = candidate.candid WHERE divid = '$div_id' AND results.raceid = '$raceid' AND isTCP = 0 ORDER BY name");
					while ($row = mysqli_fetch_array($cand_div)) {
						echo "<tr>";
							echo "<td class='cand-name'>".$row['name']."</td>";
							echo "<td>".number_format(getCandVotesByRaceDivision($raceid, $row['candid'], "election_day", $div_id, 0))."</td>";
							echo "<td>".number_format(getCandVotesByRaceDivision($raceid, $row['candid'], "advance", $div_id, 0))."</td>";
							echo "<td>".number_format(getCandVotesByRaceDivision($raceid, $row['candid'], "postal", $div_id, 0))."</td>";
							echo "<td><b>".number_format(getCandVotesByRaceDivision($raceid, $row['candid'], "all", $div_id, 0))."</b></td>";
						echo "</tr>";
					}
				
				echo "</table>";
				
				echo "</div>";
		}
		
		echo "</div>";
		
	}
	
	echo "</div>";
}

?>