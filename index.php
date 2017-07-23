<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
global $messages;
include_once('config.php');

if (empty($_POST["m"])) {
  $m = "dashboard";
}else{
  $m = $_POST["m"];
}

$olt_list = "";
if ($m != "dashboard" ){
if ( !empty($_POST['olt_name']) && array_key_exists($_POST['olt_name'],$conf_olt )){
 $olt = $_POST['olt_name'];
}else{
 foreach ($conf_olt as $i => $value) {
  $olt = $i;
  break;
 }
}

# Create SELECT list of OLT 
$olt_list = "
<select align=right id=olt_name name=olt_name>";
foreach ($conf_olt as $i => $value) {
  $olt_list.="<option value=".$i." ".($i == $olt ? "selected" : "") .">".$i." (".$conf_olt[$i]["host"].")</option>";
}
$olt_list.= "</select>
<input type=submit name=update value='Select/Update'>
";
}


include_once('inc/top.php');

switch ($m) {
  case 'dashboard':
    include_once('inc/dashboard.php');
    break;
  case 'list_onu':
    include_once('inc/onu_table.php');
    break;
  case 'report_optical':
    include_once('inc/report_optical.php');
    break;
}

include_once('html/bottom.html');

?>
