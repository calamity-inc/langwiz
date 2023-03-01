<?php
if (!empty($argv) && substr($argv[0], -5) == ".phar")
{
	require "cli/index.php";
}
else
{
	require "all.php";
}
