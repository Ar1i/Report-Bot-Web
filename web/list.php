<?php
require_once "recaptchalib.php";
require('mysql.php');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content=""> 

    <title>Report Cheaters in CSGO.</title>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/narrow-jumbotron.css" rel="stylesheet">
  </head>

  <body>
    <div class="container">
      <div class="header clearfix">
        <nav>
          <ul class="nav nav-pills pull-xs-right">
            <li class="nav-item active">
              <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="list.php">Reported List</a>
            </li>
			<li class="nav-item">
              <a class="nav-link" href="banned.php">Banned List</a>
            </li>
          </ul>
        </nav>
        <h3 class="text-muted">Report Cheaters in CSGO.</h3>
      </div>
	 
	<center>
      <div class="row marketing">
		<?php include ("header.php"); 
		$r = mysqli_query($conn,"SELECT COUNT(*) FROM list;");
		$r = mysqli_fetch_array($r);
		echo '	<p>This site is limited by the last 2000 entrys. Current entrys: <big>'.$r[0].'</big></p>';
		$result = mysqli_query($conn,"SELECT * FROM `info`;");
		$row = mysqli_fetch_array($result);
		echo '<div class="alert alert-success"><p><strong>Ban Info:</strong><br>Last Check: <strong>'.$row['lastcheck'].'</strong> | OW Banned: <strong>'.$row['ow'].'</strong> | VAC Banned: <strong>'.$row['vac'].'</strong> | Checked Accounts: <strong>'.$row['checked'].'</strong></p></div>';
		
		?>
		<?php
			$result = mysqli_query($conn,"SELECT * FROM list ORDER BY id DESC LIMIT 2000;");

			echo '<table class="table tablesorter" id="list_table">
			<thead>
			<tr>
			<th>ID</th>
			<th>Date</th>
			<th>SteamID</th>
			<th>Report Log</th>
			</tr>
			</thead>';

			echo '<tbody>';
			while($row = mysqli_fetch_array($result))
			{
			echo '<tr>';
			echo '<td>' . $row['id'] . '</td>';
			echo '<td>' . $row['datum'] . '</td>';
			echo '<td><a href="http://steamcommunity.com/profiles/'.$row['steamid'].'" target="_blank">' . $row['steamid'] . '</a></td>';
			echo '<td><a href="index.php?l='.$row['steamid'].'" target="_blank">Report Log</a></td>';
			echo '</tr>';
			}
			echo '</tbody></table>';
			mysqli_close($conn);
			?>
      </div>
	</center>
      <footer class="footer">
                <p>Script by &copy; Radat. @High-Minded.net 2016</p>
      </footer>

    </div> <!-- /container -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
	<script src="js/jquery-latest.js"></script>
	<script src="js/jquery.tablesorter.js"></script>
	<script>
	$(document).ready(function() 
    { 
        $("#list_table").tablesorter(); 
    } 
	); 
		
	</script>
	</body>
</html>
