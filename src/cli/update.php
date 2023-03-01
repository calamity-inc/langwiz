<?php
$format = Langwiz\formatByName($argv[2] ?? "axis");
if ($format === null)
{
	die("Unknown format: {$argv[2]}\n");
}

$langs = $format->getLangsInFolder(".");

$source_code = ($argv[3] ?? "en");
if (!array_key_exists($source_code, $langs))
{
	die("Did not find file for source language $source_code\n");
}

$r = $format->getReader();
$w = new Langwiz\SourceToLocalisationWriter($format->getWriter());

$src_cont = file_get_contents($langs[$source_code]);
$src = $r->loadSource($src_cont);

$data = [];

foreach ($langs as $code => $file)
{
	if ($code == $source_code)
	{
		continue;
	}

	$w->loc = $r->loadLocalisation($src, file_get_contents($file));

	$r->read($src_cont, $w);
	file_put_contents($file, $w->w->out);
	$w->w->out = "";

	$data[$code] = [
		"translated" => $w->loc->getTranslatedPercentage()
	];
}

if (file_exists("translations.json"))
{
	file_put_contents("translations.json", json_encode($data, JSON_PRETTY_PRINT));
}
