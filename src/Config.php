<?php
declare(strict_types=1);

namespace Yahiru\CAFactory;

final class Config
{
    const CONFIG_DIR = __DIR__.'/../config';
    const DEFAULT_STUB_DIR = __DIR__.'/../stub';

    const DUMMY_NAMESPACE = 'DummyNamespace';
    const DUMMY_USE_CASE = 'DummyUseCase';
    const DUMMY_REQUEST = 'DummyRequest';
    const DUMMY_RESPONSE = 'DummyResponse';
    const DUMMY_INTERACTOR = 'DummyInteractor';

    const REPLACE_STR = '__USE_CASE__';

    /** @var string */
    public $basePath;

    /** @var string */
    public $namespace;

    /** @var array */
    public $stub = [];

    /** @var array */
    public $class = [];

    public function __construct(string $basePath, string $namespace, array $stub = [], array $class = [])
    {
        $this->basePath = rtrim($basePath, '/');
        $this->namespace = trim($namespace, '\\').'\\';
        $this->setStub($stub);
        $this->setClass($class);
    }

    public function setStub(array $stub): void
    {
        foreach ($stub as $key => $val) {
            $this->stub[$key] = $val;
        }
    }

    public function setClass(array $class): void
    {
        foreach ($class as $key => $val) {
            $this->class[$key] = $val;
        }
    }

    private function hasBasePath(): bool
    {
        return !!$this->basePath;
    }

    private function hasNamespace(): bool
    {
        return !!$this->namespace;
    }

    public function isValid(): bool
    {
        return $this->hasBasePath()
            && $this->hasNamespace();
    }

    public static function load(): self
    {
        $config = require self::CONFIG_DIR.'/default.php';
        if (file_exists(self::CONFIG_DIR.'/user.php')) {
            $config = array_replace_recursive(
                $config,
                require self::CONFIG_DIR.'/user.php'
            );
        }

        return new self(
            $config['base_path'],
            $config['namespace'],
            $config['stub'],
            $config['class']
        );
    }
}
