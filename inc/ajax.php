<?php
//error_reporting(E_ALL);
//ini_set('display_errors', true);

include_once('../config.php');
require_once '../inc/telnetclass.php';

//Проверяем ajax_req на допустимые символы
#$_POST = array_map('pg_escape_string', $_POST);
#$_GET = array_map('pg_escape_string', $_GET);
if (isset($_GET['ajax_req'])) {
 $ajax_req = $_GET['ajax_req'];
 $olt = $_GET['olt_name'];
} elseif (isset($_POST['ajax_req'])) {
 $ajax_req = $_POST['ajax_req'];
 $olt = $_POST['olt_name'];
} else {
 $ajax_req = "get_onu_list";
}

if (!preg_match("/^[0-9a-zA-Z_]+$/",$ajax_req)) { echo "Обля! \n"; exit(); }

$preg_epon_all_info = '/((EPON0\/[\d]{1,2}:[\d]{1,2})\s*(([A-Za-z]{3,4})\s*([0-9a-zA-Z]{3,4})){0,1}\s*([a-f0-9]{4}.[a-f0-9]{4}.[a-f0-9]{4})[\w\W]*(deregistered|auto_configured|auto-configured|lost)[\w\W]*(N\/A|power\soff|power-off|wire\sdown|unknow))+/iU';
$preg_epon_optical_from_onu = '/((EPON0\/[\d]{1,2}:[\d]{1,2})\s*(-\d{1,2}\.\d))+/iU';
$preg_epon_optical_to_onu = '/(( received power\(DBm\):)\s*(-\d{1,2}\.\d))+/iU';

#exit;
$tn = new Telnet($conf_olt[$olt]["host"], '23', '60', 'User Access Verification');
$tn->login($conf_olt[$olt]["user"],$conf_olt[$olt]["pass"]);
$tn->setPrompt('#');
$tn->exec('enable');
$tn->exec('terminal width 200');
$tn->exec('terminal length 512');

switch ($ajax_req) {
  case 'get_onu_list': 
    preg_match_all($preg_epon_all_info, $tn->exec('show epon onu-information'), $result);
    foreach ($result[0] as $i => $value) {
      $responce->rows[$i]["iface"]  = $result[2][$i];
      $responce->rows[$i]["model"]  = $result[5][$i];
      $responce->rows[$i]["mac"]    = $result[6][$i];
      if ( ($result[7][$i] == "auto_configured") || ($result[7][$i] == "auto-configured") ) {
        $responce->rows[$i]["status"] = "<font color=green>online</font>";
      }else{
        $responce->rows[$i]["status"] = "<font color=red>offline</font>";
      }
//$responce->rows[$i]["status"] = $result[7][$i];
    }
    $responce->page = 1;
    $responce->total = 1;
    $responce->records = $i;
    echo json_encode($responce);
    break;
  case 'get_onu_info':
    preg_match_all($preg_epon_optical_from_onu, $tn->exec('show epon optical-transceiver-diagnosis interface '.$_GET['iface']), $result);
    $signal_rx = $result[3][0];
    preg_match_all($preg_epon_optical_to_onu, $tn->exec('show epon interface '.$_GET['iface'].' onu ctc optical-transceiver-diagnosis'), $result);
    $signal_tx = $result[3][0];
    $onu_cfg = preg_replace("/[\n\r]+/s","<br/>", preg_replace("/(show).*(onu-configuration\r)/s","", $tn->exec('show running-config interface '.$_GET['iface'])));
    $onu_port_state = preg_replace("/[\n\r]+/s","<br/>", preg_replace("/(show).*(state\r)/s","", $tn->exec('show epon interface '.$_GET['iface'].' onu port 1 state')));
    $onu_mac_table = preg_replace("/[\n\r]+/s","<br/>", preg_replace("/(show).*(table)/s","", $tn->exec('show epon interface '.$_GET['iface'].' onu mac address-table')));
    $onu_info = preg_replace("/[\n\r]+/s","<br/>", preg_replace("/(show).*(info)/s","", $tn->exec('show epon interface '.$_GET['iface'].' onu ctc basic-info')));
#    $signal_rx = $_GET['iface'];
    $conntent = "<table>";
    $conntent.="<tr><th>Signal:</th><th>Port 1 state:</th><th>ONU MAC-address table:</th><th>Configuration ONU:</th><th>ONU info:</th></tr>";
    $conntent.="<tr><td>from ONU: ".$signal_rx."<br> to ONU: ".$signal_tx."</td><td>"
               .$onu_port_state."</td><td>".$onu_mac_table."</td><td>".$onu_cfg."</td><td>".$onu_info."</td></tr>";
echo $conntent;
#    echo "<b><u>Signal:</u></b><br> from ONU: ".$signal_rx."<br> to ONU: ".$signal_tx .
#         "<p><b><u>Port 1 state:</u></b><br>".$onu_port_state.
#         "<p><b><u>ONU MAC-address table:</u></b><br>".$onu_mac_table.
#         "<p><b><u>Configuration ONU:</u></b><br>".$onu_cfg.""
    ;
    break;
  case 'get_report_optical':
    preg_match_all($preg_epon_optical_from_onu, $tn->exec('show epon optical-transceiver-diagnosis'), $result);
    foreach ($result[0] as $i => $value) {
      $responce->rows[$i]["iface"]  = $result[2][$i];
      $responce->rows[$i]["rxpower"]  = $result[3][$i];
    }
    $responce->page = 1;
    $responce->total = 1;
    $responce->records = $i;
    echo json_encode($responce);
    break;
}

?>