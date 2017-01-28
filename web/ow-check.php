<?php
include('mysql.php');
include('config.php');
$result = mysqli_query($conn, "SELECT * FROM list WHERE ow=false OR vac=false ORDER BY id DESC LIMIT 5000;");

$ids;

$count   = 0;
$counter = 1;
$rows = [];

while ($row = $result->fetch_array()) {
    $rows[] = $row;
}

$s = sizeof($rows);

foreach ($rows as $row) {
    
    $steamid = $row['steamid'];
    if ($count == 0) {
        $ids   = $steamid;
        $count = $count + 1;
    } else {
        
        if (strpos($ids, $steamid) !== false) {
            $s = $s - 1;
        } else {
            $counter = $counter + 1;
            $ids     = $ids . ',' . $steamid;
            $count   = $count + 1;
        }
    }
    // Abfrage
    if ($count > 10 || $counter == $s) {
        $key  = $steam_web_key;
        $link = file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerBans/v1/?key=' . $key . '&steamids=' . $ids . '&format=json');
        echo $link;
        $myarray = json_decode($link, true);
        
        
        
        for ($i = 0; $i < $count; $i++) {
            echo '+\n';
            if (!empty($myarray['players'][$i]['NumberOfGameBans'])) {
                $ow         = $myarray['players'][$i]['NumberOfGameBans'];
                $vac        = $myarray['players'][$i]['VACBanned'];
                $steamid    = $myarray['players'][$i]['SteamId'];
                $daylastban = $myarray['players'][$i]['DaysSinceLastBan'];
                if ($ow >= 1) {
                    if ($daylastban < 10) {
                        $result = mysqli_query($conn, "SELECT * FROM `banned` WHERE INSTR(`steamid`, '" . $steamid . "') > 0");
                        
                        $reportdate = mysqli_query($conn, "SELECT datum FROM list WHERE steamid='" . $steamid . "' LIMIT 1");
                        $reportdate = mysqli_fetch_assoc($reportdate);
                        
                        if (mysqli_num_rows($result) == 0) {
                            mysqli_query($conn, "INSERT INTO `banned` (`id`, `datum`, `steamid`, `ow`, `vac`, `datum-report`) VALUES (NULL, CURRENT_TIMESTAMP, '" . $steamid . "', 'No', 'No', '" . $reportdate['datum'] . "');");
                        } else {
                            mysqli_query($conn, "UPDATE `banned` SET `ow` = 'true' WHERE `steamid` = " . $steamid . ";");
                        }
                    }
                    mysqli_query($conn, "UPDATE `list` SET `ow` = 'true' WHERE `steamid` = " . $steamid . ";");
                } else {
                    mysqli_query($conn, "UPDATE `banned` SET `ow` = 'false' WHERE `steamid` = " . $steamid . ";");
                    mysqli_query($conn, "UPDATE `list` SET `ow` = 'false' WHERE `steamid` = " . $steamid . ";");
                }
                if ($vac == true) {
                    if ($daylastban < 10) {
                        $result = mysqli_query($conn, "SELECT * FROM `banned` WHERE INSTR(`steamid`, '" . $steamid . "') > 0");
                        
                        $reportdate = mysqli_query($conn, "SELECT datum FROM list WHERE steamid='" . $steamid . "' LIMIT 1");
                        $reportdate = mysqli_fetch_assoc($reportdate);
                        
                        if (mysqli_num_rows($result) == 0) {
                            mysqli_query($conn, "INSERT INTO `banned` (`id`, `datum`, `steamid`, `ow`, `vac`, `datum-report`) VALUES (NULL, CURRENT_TIMESTAMP, '" . $steamid . "', 'No', 'No', '" . $reportdate['datum'] . "');");
                        } else {
                            mysqli_query($conn, "UPDATE `banned` SET `vac` = 'true' WHERE `steamid` = " . $steamid . ";");
                        }
                    }
                    mysqli_query($conn, "UPDATE `list` SET `vac` = 'true' WHERE `steamid` = " . $steamid . ";");
                } else {
                    mysqli_query($conn, "UPDATE `banned` SET `vac` = 'false' WHERE `steamid` = " . $steamid . ";");
                    mysqli_query($conn, "UPDATE `list` SET `vac` = 'false' WHERE `steamid` = " . $steamid . ";");
                }
            }
        }
        $count = 0;
        $ids   = '';
    }
    $ow  = mysqli_query($conn, "SELECT COUNT(*) FROM banned WHERE ow='true';");
    $ow  = mysqli_fetch_array($ow);
    $vac = mysqli_query($conn, "SELECT COUNT(*) FROM banned WHERE vac='true';");
    $vac = mysqli_fetch_array($vac);
    $c   = mysqli_query($conn, "SELECT COUNT(*) FROM list;");
    $c   = mysqli_fetch_array($c);
    mysqli_query($conn, "UPDATE info SET ow=" . $ow[0]);
    mysqli_query($conn, "UPDATE info SET vac=" . $vac[0]);
    mysqli_query($conn, "UPDATE info SET checked=" . $c[0]);
}

echo $counter;
echo $s;

$d = date('d/m/Y H:i');
mysqli_query($conn, "UPDATE info SET lastcheck='" . $d . "'");
mysqli_close($conn);
?>
