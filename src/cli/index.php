<?php
require __DIR__."/../all.php";

if (empty($argv[1]))
{
	die("To update localisations from source: php {$argv[0]} update [format=axis] [source=en]\n"
		."To convert localisation format: php {$argv[0]} convert <from> <to>\n"
		."\n"
		."Available formats: ".join(", ", array_keys($format_registry))
	);
}

if ($argv[1] == "update")
{
	require "update.php";
}
else if ($argv[1] == "convert")
{
	require "convert.php";
}
else
{
	die("Unknown tool: {$argv[1]}\n");
}
