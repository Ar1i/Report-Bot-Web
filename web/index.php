<?php
require_once "recaptchalib.php";
require('mysql.php');
if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
	  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}
 
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content=""> 

    <title>Private Report Bot.</title>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
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
	  <?php include ("header.php"); ?> 
      <div class="row marketing">
		<?php
		if (!empty($_POST['pw'])) {
			if ($_POST['pw'] == $pw) {
				// empty response
				$response = null;
				 
				// check secret key
				$reCaptcha = new ReCaptcha($secret);
				
				// if submitted check response
				if ($_POST["g-recaptcha-response"]) {
					$response = $reCaptcha->verifyResponse(
						$_SERVER["REMOTE_ADDR"],
						$_POST["g-recaptcha-response"]
					);
				}
				
				if ($response != null && $response->success) {
					
					if (!empty($_POST['steamid'])) {
						$steamid = $_POST['steamid'];
						if (strlen($steamid) != 17) {
							echo '<br><font color=red>Error: SteamID not Valid?</font>';
						} else {
							if (!ctype_digit($steamid)) {
								echo '<br><font color=red>Error: SteamID not Valid?</font>';
							} else {
								if ((strpos($steamid, "765") !== false)) {
									$result = mysqli_query($conn,"SELECT * FROM whitelist WHERE steamid = '".$steamid."'");
									$row = mysqli_fetch_array($result);
									if (!in_array($steamid, $row)) {
										$link = 'http://steamcommunity.com/profiles/'.$steamid.'?xml=1';
										$xml = simplexml_load_file(rawurlencode($link));
										$error = $xml->error;
										if ($error == 'The specified profile could not be found.') 
										{
											echo '<br><font color=red>Error: Cant find this Steam Account?</font>';
										} else {
											$sql = "INSERT INTO `list` (`id`, `datum`, `steamid`, `ow`, `vac`, `ip`) VALUES (NULL, CURRENT_TIMESTAMP, '".$steamid."', 'false', 'false', '".$_SERVER['REMOTE_ADDR']."');"; 
											$conn->query($sql);
											mysqli_close($conn);
											exec('cd '.$script_path.' && node bot-report-web.js '.$steamid.' > '.$script_log_path.''.$steamid.'.txt &');
											header('Location: ?l='.$steamid);	
										}
									} else {
										
										echo '<br><font color=red>Error: Account probably on the Whitelist.</font>';
									}
								} else {
									echo '<br><font color=red>Error: SteamID not Valid?</font>';
								}
							}
						}
					}
				} else {
					echo '<br><font color=red>Error: Captcha?</font>';
				}
			} else {
				echo '<br><font color=red>Error: Password wrong?</font>';
			}
		}
        if (!empty($_GET['l'])) {
            $steamid = $_GET['l'];
            if (strlen($steamid) != 17) {
                echo '<br><font color=red>Error: SteamID not Valid?</font>';
            } else {
                if (!ctype_digit($steamid)) {
                    echo '<br><font color=red>Error: SteamID not Valid?</font>';
                } else {
					echo '<h2>Output of "'.$steamid.'"</h2><br>';
                    $filename = $script_log_path.$steamid.'.txt';
                    if (file_exists($filename)) {
                        if (is_readable($filename)) {
                            $handle = fopen($filename, 'r');
                            if (filesize($filename) > 0) {
                                $contents = fread($handle, filesize($filename));
                                fclose($handle);
                                $contents = str_replace('\r\n', "\r\n", $contents);
                                echo ' <textarea data-autoresize class="form-control vresize" id="info" rows="20">'.$contents.'</textarea>';    
                            } else {
								 echo ' <textarea data-autoresize class="form-control vresize" id="info" rows="20"></textarea>'; 
							}    
                        }
                    } else {
                        echo '<br><font color=red>Cant get any Logs for SteamID: "'.$steamid.'"</font>';
                    }
                }
            }
        } else {
			?>
			<h1 class="display-3">Report:</h1>
			<p class="lead">
			  <form action = "<?php $_PHP_SELF ?>" method = "POST">
				SteamID 64: 
				<input type = "text" placeholder="SteamID 64" name = "steamid" /><br><br>
				MatchID (Not needed):
				<input type = "text" placeholder="MatchID" name = "matchid" /><br><br>
				<input type = "checkbox" onclick = "document.getElementById('buttplug').disabled=false;" id="checky"> I understand that the Player needs to be in my game.<br><br>
				Password:
				<input type = "text" placeholder="Password" name = "pw" /><br><br>
			    <div class="g-recaptcha" data-sitekey="<?php echo $gcaptchasidekey?>"></div><br>
				<input class="btn btn-lg btn-success" value="Report!" disabled="true" id="buttplug" type = "submit" />
			  </form>
			  <br>
			</p>

			<?php
		}
    ?>
      </div>
	
	</center>
	
	<script src="js/jquery-3.0.0.min.js"></script>
    <script src="js/ie10-viewport-bug-workaround.js"></script>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<script>
	var textarea = document.getElementById('info');
	var w = 'Thanks for using this Service';
	var textValue=textarea.value; 
	if (textValue.indexOf(w)!=-1)
	{
	} else {
		 setTimeout(function () { location.reload(true); }, 3000);
	}
	</script>
	
      <footer class="footer">
        <p>Script by &copy; Radat. @High-Minded.net 2016</p>
      </footer>

    </div> <!-- /container -->
	</body>
</html>
