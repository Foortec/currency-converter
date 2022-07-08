<?php

/*
 * This is a configuration document.
 * Here you can customize some parts of the Converter.
 * I do not recommend to change "CURRENCIES", "ECBXML" and "LOCALXML" (anything from line 14 to the bottom of this document).
 * However, I recommend to change default currencies, so the Converter would be more suited to your requirements.
*/

define("DEFAULT_BASE_CURRENCY", "USD"); //Default currency that will be converted
define("DEFAULT_TARGET_CURRENCY", "EUR"); //Expected currency
define("DEFAULT_DECIMAL_NUMBERS", 2); //Default displayed decimal numbers of the output currency
define("UPDATE_BTN_DISPLAY", true); //The display of 'Manual update' button in the Settings section (display if true)
define("CURRENCIES", array(
    "EUR",
    "USD",
    "JPY",
	"BGN",
	"CZK",
	"DKK",
	"GBP",
	"HUF",
	"PLN",
	"RON",
	"SEK",
	"CHF",
	"ISK",
	"NOK",
	"HRK",
	"TRY",
	"AUD",
	"BRL",
	"CAD",
	"CNY",
	"HKD",
	"IDR",
	"ILS",
	"INR",
	"KRW",
	"MXN",
	"MYR",
	"NZD",
	"PHP",
	"SGD",
	"THB",
	"ZAR"
)); //All currencies supported by the Converter
define("ECBXML", "https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml"); //ECB XML file URL
define("LOCALXML", realpath("../data/currencyRates.xml")); //Local XML file path