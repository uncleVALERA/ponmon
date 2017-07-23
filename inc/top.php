<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>BDCOM OLT Monitor:</title>
<link rel="stylesheet" type="text/css" href="css/css_style.css">
<link rel="stylesheet" type="text/css" href="css/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="css/themes/smoothness/dataTables.jqueryui.css">

<script type="text/javascript" language="javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="js/dataTables.jqueryui.js"></script>
</head>

<body bgcolor="#888">
<div>
<div class="container">

<script>
function gotolink(prm){
 document.menufrm.m.value=prm;
 document.menufrm.submit();
 return false;
}
</script>
<?php
if (empty($_POST["m"])) {
  $m = "dashboard";
}else{
  $m = $_POST["m"];
}
?>
<form method='POST' name=menufrm>
<input type="hidden" name="m" value="<?php echo $m;?>">
<div class="menu-1">
 <ul class="css-menu-1">
<?php

foreach ($conf_menu as $key => $name) { 
  echo "<li><a href='#".$key."' onclick=\"return gotolink('".$key."');\" ".($key == $m ? " class=selected " : "")." >".$name."</a></li>";
}
?>
 </ul>
</div>
<div class=sel_olt>
<?php echo $olt_list?>
</div>
</form>


