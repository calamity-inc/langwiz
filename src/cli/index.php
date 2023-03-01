<?php
require __DIR__."/../all.php";

if (empty($argv[1]))
{
	die("Syntax: php {$argv[0]} update [format=axis] [source=en]\n");
}

if ($argv[1] == "update")
{
	require "update.php";
}
else
{
	die("Unknown tool: {$argv[1]}\n");
}