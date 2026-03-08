<?php

/**
 * @ingroup MetaBase
 **/
class MetaNamespace
{
	private $namePathSeparator = '\\';
	private $dirPathSeparator = DIRECTORY_SEPARATOR;
	private $name;
	private $baseSpace = null;
	private $baseDir = PATH_CLASSES;
	private $pathMap = [
		'dao' => 'DAOs',
		'business' => 'Business',
		'proto' => 'Proto',
	];

	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setBaseSpace(?string $baseSpace): self
	{
		$this->baseSpace = $baseSpace;
		return $this;
	}

	public function getBaseSpace(): ?string
	{
		return $this->baseSpace;
	}

	public function setBaseDir(string $baseDir): self
	{
		$this->baseDir = $baseDir;
		return $this;
	}

	public function getBaseDir(): string
	{
		return $this->baseDir;
	}

	public function setPathMap(array $pathMap): self
	{
		$this->pathMap = $pathMap;
		return $this;
	}

	public function getPathMap(): array
	{
		return $this->pathMap;
	}

	public function setPathForType(string $type, string $path): self
	{
		$this->pathMap[$type] = $path;
		return $this;
	}

	/**
	 * Create MetaNamespace from XML attributes array.
	 * Expected keys: 'namespace' (full namespace), 'base-space' (optional), 'base-dir' (optional).
	 */
	public static function fromXmlArray(array $attributes): self
	{
		$ns = new self();
		if (isset($attributes['namespace'])) {
			$ns->setName((string) $attributes['namespace']);
		}
		if (isset($attributes['base-space'])) {
			$ns->setBaseSpace((string) $attributes['base-space']);
		}
		if (isset($attributes['base-dir'])) {
			$ns->setBaseDir((string) $attributes['base-dir']);
		}
		// pathMap cannot be set from XML for now
		return $ns;
	}

	public function buildFullName(string $type, bool $auto = false)
	{
		$path = $this->buildPath($type, $auto);
		return implode($this->namePathSeparator, $path);
	}

	public function buildFilePath(string $type, bool $auto = false)
	{
		$path = $this->buildPath($type, $auto);
		return implode($this->dirPathSeparator, $path) . $this->dirPathSeparator;
	}

	private function buildPath(string $type, bool $auto = false)
	{
		$path = [];
		if ($this->baseSpace) {
			$path[] = $this->baseSpace;
		}
		if ($this->name) {
			$path = array_merge($path, explode($this->namePathSeparator, $this->name));
		}
		if ($auto) {
			$path[] = 'Auto';
		}
		$path[] = $this->pathMap[$type];
		return $path;
	}
}
