<?php
//error_reporting(E_ALL);
//ini_set('display_errors', true);

#include_once('../config.php');
require_once 'inc/telnetclass.php';


echo "<table border=1 width=100% align=center bgcolor='#ddd'>";

foreach ($conf_olt as $name => $olt) {

$tn = new Telnet($olt["host"], '23', '60', 'User Access Verification');
$tn->login($olt["user"],$olt["pass"]);
$tn->setPrompt('#');
$tn->exec('enable');
$tn->exec('terminal width 200');
$tn->exec('terminal length 512');


$preg_olt_info="/(^(Base|BDCOM).*$)|^.*uptime.*$/m";
preg_match_all($preg_olt_info, $tn->exec('show version'), $result);

echo "<tr><th colspan=2 bgcolor='#bbb'>".$name."</th></tr>";
echo "<tr><th>IP:</th><td>".$olt["host"]."</td></tr>";
echo "<tr><th>Info</th><td>".$result[0][0]."<br>".$result[0][1]."<br>".$result[0][2]."</td></tr>";
}

echo "</table>";

?>