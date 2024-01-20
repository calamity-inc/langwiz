<?php
namespace Langwiz;

/* Axis is our own localisation format, it looks like this:

KEY=I'm the string for KEY.

! I'm a comment with information about UNTRANSLATED_KEY.
# UNTRANSLATED_KEY=何ですか

*/

class AxisWriter extends StringWriter
{
	final function kv(string $key, string $value): void
	{
		$this->out .= "$key=$value\n";
	}

	final function untranslatedKv(string $key, string $value): void
	{
		$this->out .= "# $key=$value\n";
	}

	final function comment(string $msg): void
	{
		$this->out .= "!$msg\n";
	}

	final function space(): void
	{
		$this->out .= "\n";
	}
}

class AxisReader extends Reader
{
	static function read(string $in, Writer $out): void
	{
		$in = str_replace("\r", "", $in);
		$lines = explode("\n", $in);
		if ($lines[count($lines) - 1] == "")
		{
			array_pop($lines);
		}
		foreach ($lines as $line)
		{
			if ($line == "")
			{
				$out->space();
				continue;
			}
			if (substr($line, 0, 1) == "!")
			{
				$out->comment(substr($line, 1));
				continue;
			}
			$untranslated = false;
			if (substr($line, 0, 2) == "# ")
			{
				$untranslated = true;
				$line = substr($line, 2);
			}
			$sep = strpos($line, "=");
			if ($sep === false)
			{
				throw new \UnexpectedValueException("Line does not fit axis format: $line");
			}
			$key = substr($line, 0, $sep);
			$value = substr($line, $sep + 1);
			if ($untranslated)
			{
				$out->untranslatedKv($key, $value);
			}
			else
			{
				$out->kv($key, $value);
			}
		}
	}
}

class AxisFormat extends Format
{
	function getFileEndings(): array
	{
		return [".axis"];
	}

	function getWriter(): StringWriter
	{
		return new AxisWriter();
	}

	function getReader(): Reader
	{
		return new AxisReader();
	}
}

registerFormat("axis", "\Langwiz\AxisFormat");
