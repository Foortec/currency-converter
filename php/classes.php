<?php
require_once("../data/config.php");
date_default_timezone_set("Europe/Brussels");
class Convertion
{
	public float $baseValue;
	public float $targetValue = 0;
	public int $roundDecimal;
	public string $baseCurrency;
	public string $targetCurrency;
	
	public function __construct(float $baseValue, int $roundDecimal = DEFAULT_DECIMAL_NUMBERS, string $baseCurrency = DEFAULT_BASE_CURRENCY, string $targetCurrency = DEFAULT_TARGET_CURRENCY)
	{
		$this->baseValue = $baseValue;
		$this->roundDecimal = $roundDecimal;
		$baseCurrency = strtoupper($baseCurrency);
		$targetCurrency = strtoupper($targetCurrency);
		foreach(CURRENCIES as $currency)
		{
			if($baseCurrency == $currency)
				$this->baseCurrency = $baseCurrency;
			if($targetCurrency == $currency)
				$this->targetCurrency = $targetCurrency;
		}
		if(!isset($this->baseCurrency) || !isset($this->targetCurrency))
			throw new Exception("Incorrect currency");
	}
	
	public function convert() : bool
	{
		if($this->baseCurrency == $this->targetCurrency)
		{
			$this->targetValue = $this->baseValue;
			return true;
		}
		
		$localXML = new DOMDocument();
		if(!$localXML->load(LOCALXML))
			return false;
		
		$localElements = $localXML->getElementsByTagName("Cube");
		$localAmount = $localElements->length;
		
		if($localAmount == 0)
			return false;
		
		for($i=2; $i<$localAmount; $i++)
			$currencies[$localElements->item($i)->attributes->item(0)->nodeValue] = $localElements->item($i)->attributes->item(1)->nodeValue;
		
		if($this->baseCurrency != "EUR" && $this->targetCurrency != "EUR")
			$this->targetValue = ($this->baseValue / $currencies[$this->baseCurrency]) * $currencies[$this->targetCurrency];
		elseif($this->baseCurrency == "EUR" && $this->targetCurrency != "EUR")
		{
			$this->targetValue = $this->baseValue * $currencies[$this->targetCurrency];
		}
		elseif($this->baseCurrency != "EUR" && $this->targetCurrency == "EUR")
		{
			$this->targetValue = $this->baseValue / $currencies[$this->baseCurrency];
		}
		return true;
	}
	
	function getRoundedResult() : float
	{
		return round($this->targetValue, $this->roundDecimal);
	}
}

class DataSync
{
	public string $lastUpdateLocal;
	public string $currentDate;
	
	public function __construct()
	{
		$this->currentDate = date("Y-m-d");
	}
	
	public function currentTimeInSeconds() : int
	{
		list($H, $i, $s) = explode(":", date("H:i:s"));
		return (int)$s + ((int)$i * 60) + ((int)$H * 3600);
	}
	
	public function forceUpdate() : void
	{
		if(!file_exists("../data/sync"))
			return;
		unlink("../data/sync");
		$this->sync();
	}
	
	public function getLastUpdateXML() : string
	{
		$xml = new DOMDocument();
		$xml->load(LOCALXML);
		return $xml->getElementsByTagName("Cube")->item(1)->attributes->item(0)->nodeValue;
	}
	
	public function getLastUpdateLocal() : string
	{
		$lastUpdate = fopen("../data/lastUpdateLocal", "r") or die("---");
		$out = fread($lastUpdate, filesize("../data/lastUpdateLocal"));
		fclose($lastUpdate);
		return $out;
	}
	
	public function fileCreate() : void
	{
		fclose(fopen("../data/sync", "w"));
	}
	
	public function syncDateCorrect() : bool
	{
		$sync = fopen("../data/sync", "r");
		list($year, $month, $day, $time) = explode("-", fread($sync, filesize("../data/sync")));
		fclose($sync);
		
		list($currentYear, $currentMonth, $currentDay) = explode("-", $this->currentDate);
		
		if((int)$currentYear <= (int)$year && (int)$currentMonth <= (int)$month && (int)$currentDay <= (int)$day)
			if($this->currentTimeInSeconds() <= $time)
				return true;
		return false;
	}
	
	public function syncDateUpdate(int $option = 1) : void
	{
		if($option == 1) //till 4pm tomorrow
		{
			list($year, $month, $day) = explode("-", $this->currentDate);
			$day = (int)$day + 1;
			$time = $year . "-" . $month . "-" . $day . "-57600";
		}
		elseif($option == 2) //till 4:15pm today
		{
			list($year, $month, $day) = explode("-", $this->currentDate);
			$time = $year . "-" . $month . "-" . $day . "-58500";
		}
		elseif($option == 3) //till the midnight
		{
			list($year, $month, $day) = explode("-", $this->currentDate);
			$time = $year . "-" . $month . "-" . $day . "-86400";
		}
		
		$sync = fopen("../data/sync", "w");
		fwrite($sync, $time);
		fclose($sync);
	}
	
	public function weekend() : bool
	{
		return date("l") == "Saturday" || date("l") == "Sunday";
	}
	
	public function before4_15pm() : bool
	{
		return $this->currentTimeInSeconds() < 58500;
	}
	
	public function updateLastUpdate() : void
	{
		$lastUpdateLocal = fopen("../data/lastUpdateLocal", "w");
		fwrite($lastUpdateLocal, $this->currentDate . " " . date("H:i:s") . " CET");
		fclose($lastUpdateLocal);
	}
	
	public function updateLocal() : void
	{
		$docECB = new DOMDocument();
		$docECB->load(ECBXML);

		$elementsECB = $docECB->getElementsByTagName("Cube");

		$count = $elementsECB->length;

		$this->lastUpdateLocal = $elementsECB->item(1)->attributes->item(0)->nodeValue;

		for($i=2; $i<$count; $i++)
			$currencies[$elementsECB->item($i)->attributes->item(0)->nodeValue] = $elementsECB->item($i)->attributes->item(1)->nodeValue;

		$docLocal = fopen(LOCALXML, "w");
		fwrite($docLocal, '<Cube>' . PHP_EOL);
		fwrite($docLocal, '<Cube time="' . $this->lastUpdateLocal . '">' . PHP_EOL);

		foreach($currencies as $code => $value)
			fwrite($docLocal, '<Cube currency="' . $code . '" rate="' . $value . '"/>' . PHP_EOL);

		fwrite($docLocal, '</Cube>' . PHP_EOL);
		fwrite($docLocal, '</Cube>');
		fclose($docLocal);
		
		$this->updateLastUpdate();
	}
	
	public function sync() : void
	{
		if(file_exists("../data/sync") && $this->syncDateCorrect())
			return;
		
		if(!file_exists("../data/sync"))
			$this->fileCreate();
		
		$this->updateLocal();
		
		if($this->lastUpdateLocal == $this->currentDate)
		{
			$this->syncDateUpdate();
			return;
		}
		
		if($this->weekend())
		{
			$this->syncDateUpdate();
			return;
		}
		
		if($this->before4_15pm())
		{
			$this->syncDateUpdate(2);
			return;
		}
		
		$this->syncDateUpdate(3);
	}
}