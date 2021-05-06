<?php
if(isset($_POST['data'])){

$myfile = fopen("myJSON.json", "w") or die("Unable to open file!");
$txt = $_POST['data'];
fwrite($myfile, $txt);
fclose($myfile);


}
?>