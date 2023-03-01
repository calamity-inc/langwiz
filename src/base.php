<?php
namespace Langwiz;

// Representation of languages

abstract class LangBase
{
	public array $strings = [];

	final function hasString(string $key): bool
	{
		return array_key_exists($key, $this->strings);
	}

	function getString(string $key): string
	{
		return $this->strings[$key];
	}
}

class Source extends LangBase
{
}

class Localisation extends LangBase
{
	public Source $source;

	public function __construct(Source $source)
	{
		$this->source = $source;
	}

	final function getString(string $key): string
	{
		return $this->strings[$key] ?? $this->source->strings[$key];
	}

	final function getTranslatedPercentage(): float
	{
		return count($this->strings) / count($this->source->strings) * 100;
	}
}

// Base Types

abstract class Writer
{
	abstract function kv(string $key, string $value): void;
	function untranslatedKv(string $key, string $value): void {}
	function comment(string $msg): void {}
	function space(): void {}
}

abstract class StringWriter extends Writer
{
	public string $out = "";
}

abstract class Reader
{
	abstract static function read(string $in, Writer $out): void;

	final static function loadSource(string $in): Source
	{
		$src = new Source();
		$w = new MemoryWriter($src);
		static::read($in, $w);
		return $src;
	}

	final static function loadLocalisation(Source $source, string $in): Localisation
	{
		$loc = new Localisation($source);
		$w = new MemoryWriter($loc);
		static::read($in, $w);
		return $loc;
	}
}

abstract class Format
{
	abstract function getFileEndings(): array;
	abstract function getWriter(): StringWriter;
	abstract function getReader(): Reader;

	final function getLangsInFolder(string $folder): array
	{
		$langs = [];
		foreach (scandir($folder) as $file)
		{
			foreach ($this->getFileEndings() as $ending)
			{
				if (substr($file, -strlen($ending)) == $ending)
				{
					$langs[substr($file, 0, -strlen($ending))] = $file;
					continue 2;
				}
			}
		}
		return $langs;
	}
}

// Basic Writer

class MemoryWriter extends Writer
{
	public LangBase $out;

	public function __construct(LangBase $out)
	{
		$this->out = $out;
	}

	final function kv(string $key, string $value): void
	{
		$this->out->strings[$key] = $value;
	}
}

// Format registry

$format_registry = [];

function registerFormat(string $name, string $clazz)
{
	global $format_registry;
	$format_registry[$name] = $clazz;
}

function formatByName(string $name): ?Format
{
	global $format_registry;
	if (array_key_exists($name, $format_registry))
	{
		return new $format_registry[$name];
	}
	return null;
}
