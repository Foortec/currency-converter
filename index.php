<?php
	require_once("data/config.php");
	require_once("php/templates.php");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<?php getMetadata(); ?>
		<script>
			function liveCalc(baseValue)
			{
				const baseCurrency = document.getElementById("baseCurrency").value;
				const targetValue = document.getElementById("targetValue");
				const targetCurrency = document.getElementById("targetCurrency").value;
				const roundDecimal = document.getElementById("roundDecimal").value;
				
				targetValue.placeholder = "Converting...";
				
				let xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if(this.readyState==4 && this.status==200)
					{
						targetValue.value = this.responseText;
						lastUpdate();
						lastUpdateLocal();
						targetValue.placeholder = "...";
					}
				}
				xmlhttp.open("GET", "php/convert.php?baseValue=" + baseValue + "&baseCurrency=" + baseCurrency + "&targetCurrency=" + targetCurrency + "&roundDecimal=" + roundDecimal, true);
				xmlhttp.send();
			}
			
			function lastUpdate()
			{
				let xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if(this.readyState==4 && this.status==200)
					{
						document.getElementById("lastUpdate").innerHTML = this.responseText;
					}
				}
				xmlhttp.open("GET", "php/lastUpdate.php", true);
				xmlhttp.send();
			}
			
			function flagChange(which, value)
			{
				document.getElementById(which + "Flag").src = "images/flags/" + value + ".svg";
				
				liveCalc(document.getElementById("baseValue").value);
			}
			
			function swapCurrencies()
			{
				document.getElementById("baseValue").value = document.getElementById("targetValue").value;
				
				let baseCurr = document.getElementById("baseCurrency").value;
				document.getElementById("baseCurrency").value = document.getElementById("targetCurrency").value;
				document.getElementById("targetCurrency").value = baseCurr;
				
				flagChange("base", document.getElementById("baseCurrency").value);
				flagChange("target", document.getElementById("targetCurrency").value);
				
				liveCalc(document.getElementById("baseValue").value);
			}
			
			function forceUpdate()
			{
				let xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if(this.readyState==4 && this.status==200)
					{
						document.getElementById("forceUpdateResult").innerHTML = this.responseText;
						lastUpdate();
						lastUpdateLocal();
					}
				}
				xmlhttp.open("GET", "php/forceUpdate.php", true);
				xmlhttp.send();
			}
			
			function lastUpdateLocal()
			{
				let xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if(this.readyState==4 && this.status==200)
					{
						document.getElementById("lastUpdateLocal").innerHTML = this.responseText;
					}
				}
				xmlhttp.open("GET", "php/lastUpdateLocal.php", true);
				xmlhttp.send();
			}
		</script>
	</head>
	<body onload="lastUpdate(); lastUpdateLocal();">
		<?php getHeader(); ?>
		<div id="shadow"></div>
		<section id="convertion">
			<div id="centered">
				<div id="baseContent">
					<div id="base">
						<input id="baseValue" type="number" placeholder="Insert amount..." onkeyup="liveCalc(this.value);"/>
						<select id="baseCurrency" onchange="flagChange('base', this.value)">
							<?php getSelectOptions("base"); ?>
						</select>
					</div>
					<img id="baseFlag" src="images/flags/<?=strtolower(htmlentities(DEFAULT_BASE_CURRENCY));?>.svg" alt="<?=htmlentities(DEFAULT_BASE_CURRENCY);?>"/>
				</div>
				<span id="arrow">&raquo;</span>
				<div id="targetContent">
					<img id="targetFlag" src="images/flags/<?=strtolower(htmlentities(DEFAULT_TARGET_CURRENCY));?>.svg" alt="<?=htmlentities(DEFAULT_TARGET_CURRENCY);?>"/>
					<div id="target">
						<input id="targetValue" type="text" placeholder="..." readonly/>
						<select id="targetCurrency" onchange="flagChange('target', this.value)">
							<?php getSelectOptions("target"); ?>
						</select>
					</div>
				</div>
			</div>
			<div id="options">
				<img id="settings-btn" src="images/settings.png" alt="Settings" onclick="document.getElementById('settings').style.display = 'block'; document.getElementById('info').style.display = 'none';" title="Settings"/>
				<img id="swap-btn" src="images/swap.png" alt="Swap" onclick="swapCurrencies();" title="Swap sides"/>
				<img id="info-btn" src="images/info.png" alt="Info" onclick="document.getElementById('info').style.display = 'block'; document.getElementById('settings').style.display = 'none';" title="Info"/>
			</div>
			<?php
				getSettings();
				getInfo();
			?>
		</section>
		<?php @getFooter(); ?>
	</body>
</html>