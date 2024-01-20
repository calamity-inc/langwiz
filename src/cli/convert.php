<?php
if (empty($argv[2]) || empty($argv[3]))
{
	die("Syntax: php {$argv[0]} convert <from> <to>\n");
}

$from_format = Langwiz\formatByName($argv[2]);
if ($from_format === null)
{
	die("Unknown format: {$argv[2]}\n");
}

$to_format = Langwiz\formatByName($argv[3]);
if ($to_format === null)
{
	die("Unknown format: {$argv[3]}\n");
}

$langs = $from_format->getLangsInFolder(".");
$r = $from_format->getReader();
$w = $to_format->getWriter();
$ext = $to_format->getFileEndings()[0];
foreach ($langs as $code => $file)
{
	$r->read(file_get_contents($file), $w);
	file_put_contents($code.$ext, $w->out);
	$w->out = "";
}
