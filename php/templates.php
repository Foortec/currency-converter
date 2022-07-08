<?php
function getMetadata() : void
{
	echo<<<meta
		<meta charset="UTF-8"/>
		<meta name="author" content="Piotr Czajka"/>
		<meta name="application-name" content="Currency Converter"/>
		<meta name="description" content="Currency Converter based on ECB exchange rates"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<meta name="theme-color" content="#fafad2"/>
		<link rel="Shortcut icon" href="images/coin.png"/>
		<link rel="Stylesheet" href="css/style.css"/>
meta;
}

function getHeader() : void
{
	echo<<<header
		<header>
			<h1 id="site-title">Currency Converter</h1>
			<p id="site-subtitle">based on European Central Bank exchange rates</p>
		</header>
header;
}

function getSelectOptions(string $for) : void
{
	$for = strtolower($for);
	if($for != "base" && $for != "target")
		return;
	
	$currenciesCopy = CURRENCIES;
	asort($currenciesCopy, SORT_STRING);
	if($for == "base")
		$selectedCurr = DEFAULT_BASE_CURRENCY;
	else
		$selectedCurr = DEFAULT_TARGET_CURRENCY;
	
	foreach($currenciesCopy as $currency)
	{
		if($currency == $selectedCurr)
			echo '<option value="' . strtolower($currency) . '" selected>' . $currency . '</option>';
		else
			echo '<option value="' . strtolower($currency) . '">' . $currency . '</option>';
	}
}

function getFooter() : void
{
	$date = date("Y");
	echo<<<footer
		<footer>
			<p id="copyright">&copy; {$date} <a href="https://github.com/Foortec">Foortec</a></p>
		</footer>
footer;
}

function getSettings() : void
{
	$decimalDefault = htmlentities(DEFAULT_DECIMAL_NUMBERS);
	if(UPDATE_BTN_DISPLAY)
		 $forceUpdateBtn = '<input id="forceUpdate-btn" type="button" value="Manual update" onclick="forceUpdate();" title="The exchange rates are being updated automatically, but you can force the update manually."/>';
	else
		$forceUpdateBtn = "";
	echo<<<settings
		<div id="settings" class="popup">
			<img id="cancel" src="images/cancel.png" alt="X" onclick="document.getElementById('settings').style.display = 'none'; document.getElementById('forceUpdateResult').innerHTML = '';"/>
			<h3>Settings</h3>
			<hr/>
			
			<table style="width: 100%;">
				<tr>
					<td style="text-align: right; width: 50%;">
						<label for="roundDecimal">Decimal numbers: <span id="rangeValue">{$decimalDefault}</span></label>
					</td>
					<td style="text-align: left;">
						<input id="roundDecimal" type="range" min="0" max="10" value="{$decimalDefault}" onchange="document.getElementById('rangeValue').innerHTML = this.value; liveCalc(document.getElementById('baseValue').value)"/>
					</td>
				</tr>
				<tr>
					<td style="text-align: right; width: 50%;">
					{$forceUpdateBtn}
					</td>
					<td style="text-align: left;">
						<span id="forceUpdateResult"></span>
					</td>
				</tr>
			</table>
		</div>
settings;
}

function getInfo() : void
{
	echo<<<info
		<div id="info" class="popup">
			<img id="cancel" src="images/cancel.png" alt="X" onclick="document.getElementById('info').style.display = 'none';"/>
			<h3>Info</h3>
			<hr/>
			
			<table style="width: 100%;">
				<tr>
					<td style="text-align: right; width: 50%; vertical-align: top; font-weight: bold;">
						Last local update/check for update:
					</td>
					<td style="text-align: left;">
						<span id="lastUpdateLocal"></span>
					</td>
				</tr>
				<tr>
					<td style="text-align: right; width: 50%; vertical-align: top; font-weight: bold;">
						Last ECB update:
					</td>
					<td style="text-align: left;">
						<span id="lastUpdate"></span> around 16:00 CET
					</td>
				</tr>
				<tr>
					<td style="text-align: right; width: 50%; vertical-align: top; font-weight: bold;">
						Icons taken from Flaticon:
					</td>
					<td style="text-align: left;">
						<a href="https://www.flaticon.com/free-icons/settings" target="_blank" title="settings icons">Settings icon by Freepik</a> <br/>
						<a href="https://www.flaticon.com/free-icons/close" target="_blank" title="close icons">Close icon by dmitri13</a> <br/>
						<a href="https://www.flaticon.com/free-icons/swap" target="_blank" title="swap icons">Swap icon by Smashicons</a> <br/>
						<a href="https://www.flaticon.com/free-icons/info" target="_blank" title="info icons">Info icon by Freepik</a>
					</td>
				</tr>
				<tr>
					<td style="text-align: right; width: 50%; vertical-align: top; font-weight: bold;">
						European Central Bank website:
					</td>
					<td style="text-align: left;">
						<a href="https://www.ecb.europa.eu/home/html/index.en.html" target="_blank" title="ECB">[LINK]</a>
					</td>
				</tr>
				<tr>
					<td style="text-align: right; width: 50%; vertical-align: top; font-weight: bold;">
						Converter version:
					</td>
					<td style="text-align: left;">
						2022.04.v1
					</td>
				</tr>
			</table>
		</div>
info;
}