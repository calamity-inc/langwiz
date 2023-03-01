<?php
class SourceToLocalisationWriter extends Langwiz\Writer
{
	public Langwiz\StringWriter $w;
	public Langwiz\Localisation $loc;

	public function __construct(Langwiz\StringWriter $w)
	{
		$this->w = $w;
	}

	final function kv(string $key, string $value): void
	{
		if ($this->loc->hasString($key))
		{
			$this->w->kv($key, $this->loc->getString($key));
		}
		else
		{
			$this->w->untranslatedKv($key, $value);
		}
	}

	final function untranslatedKv(string $key, string $value): void
	{
		throw new LogicException("Untranslated kv in source?");
	}

	final function comment(string $msg): void
	{
		$this->w->comment($msg);
	}

	final function space(): void
	{
		$this->w->space();
	}
}

$format = Langwiz\formatByName($argv[2] ?? "axis");
if ($format === null)
{
	die("Unknown format: {$argv[2]}\n");
}

$langs = [];
foreach (scandir(".") as $file)
{
	foreach ($format->getFileEndings() as $ending)
	{
		if (substr($file, -strlen($ending)) == $ending)
		{
			$langs[substr($file, 0, -strlen($ending))] = $file;
			continue 2;
		}
	}
}
$source_code = ($argv[3] ?? "en");
if (!array_key_exists($source_code, $langs))
{
	die("Did not find file for source language $source_code\n");
}

$r = $format->getReader();
$w = new SourceToLocalisationWriter($format->getWriter());

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
