<?php
// Doesn't work, you sadly need to update your php.ini.
//ini_set("phar.readonly", "Off");

$phar = new Phar("langwiz.phar");
$phar->buildFromIterator(
	new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator("src", FilesystemIterator::SKIP_DOTS)
	),
	"src"
);
$phar->setStub($phar->createDefaultStub("stub.php", "stub.php"));
