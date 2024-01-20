<?php
namespace Langwiz;

// https://store.crowdin.com/flex

class FlexWriter extends StringWriter
{
	final function kv(string $key, string $value): void
	{
		$this->out .= "$key=$value\n";
	}

	final function comment(string $msg): void
	{
		$this->out .= "#$msg\n";
	}
}

class FlexReader extends Reader
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
			if (substr($line, 0, 1) == "#")
			{
				$out->comment(substr($line, 1));
				continue;
			}
			$sep = strpos($line, "=");
			if ($sep === false)
			{
				throw new \UnexpectedValueException("Line does not fit flex format: $line");
			}
			$key = substr($line, 0, $sep);
			$value = substr($line, $sep + 1);
			$out->kv($key, $value);
		}
	}
}

class FlexFormat extends Format
{
	function getFileEndings(): array
	{
		return [".flex"];
	}

	function getWriter(): StringWriter
	{
		return new FlexWriter();
	}

	function getReader(): Reader
	{
		return new FlexReader();
	}
}

registerFormat("flex", "\Langwiz\FlexFormat");
