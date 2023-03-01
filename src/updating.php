<?php
namespace Langwiz;

class SourceToLocalisationWriter extends Writer
{
	public StringWriter $w;
	public Localisation $loc;

	public function __construct(StringWriter $w)
	{
		$this->w = $w;
	}

	final function kv(string $key, string $value): void
	{
		if ($this->loc->hasString($key))
		{
			$this->w->kv($key, $this->loc->getString($key));
		} else
		{
			$this->w->untranslatedKv($key, $value);
		}
	}

	final function untranslatedKv(string $key, string $value): void
	{
		throw new \LogicException("Untranslated kv in source?");
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
