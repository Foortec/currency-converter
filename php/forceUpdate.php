<?php
require_once("classes.php");
$update = new DataSync();
$lastUpdate = $update->getLastUpdateXML();
$update->forceUpdate();
if($lastUpdate == $update->getLastUpdateXML())
	echo "Succeed. No new update found";
else
	echo "Succeed. The exchange rates has been updated.";