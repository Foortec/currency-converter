<?php
if(!isset($_GET['baseValue']) || $_GET['baseValue']==NULL || !isset($_GET['baseCurrency']) || !isset($_GET['targetCurrency']))
	return;

require_once("classes.php");
$baseValue = $_GET['baseValue'];
$baseCurrency = $_GET['baseCurrency'];
$targetCurrency = $_GET['targetCurrency'];
$roundDecimal = $_GET['roundDecimal'];

try
{
	$conv = new Convertion($baseValue, $roundDecimal, $baseCurrency, $targetCurrency);
	(new DataSync())->sync();
	if($conv->convert())
		echo $conv->getRoundedResult();
	else
		echo "Convertion issues";
}
catch(Exception $e)
{
	echo $e->getMessage();
}