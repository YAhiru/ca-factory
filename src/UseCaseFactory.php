<?php
declare(strict_types=1);

namespace Yahiru\CAFactory;

use Yahiru\CAFactory\Exception\DomainException;
use Yahiru\CAFactory\Exception\RuntimeException;

final class UseCaseFactory
{
    /** @var Config */
    private $config;

    private const VISIBILITY = 0755;

    public function __construct(Config $config)
    {
        if ( ! $config->isValid()) {
            throw new DomainException('config is invalid.');
        }
        $this->config= $config;
    }

    public function make(string $path): void
    {
        $this->createUseCase($path);
        $this->createRequest($path);
        $this->createResponse($path);
        $this->createInteractor($path);
    }

    private function createUseCase(string $path)
    {
        $content = $this->getContent(
            $this->config->stub['use_case'],
            $useCaseName = $this->getClassFromPath($path),
            $this->getNamespaceFromPath($path)
        );
        $useCase = str_replace(Config::REPLACE_STR, $useCaseName, $this->config->class['use_case']);
        $this->put($path."/$useCase.php", $content);
    }

    private function createInteractor(string $path)
    {
        $content = $this->getContent(
            $this->config->stub['interactor'],
            $useCaseName = $this->getClassFromPath($path),
            $this->getNamespaceFromPath($path)
        );
        $interactor = str_replace(Config::REPLACE_STR, $useCaseName, $this->config->class['interactor']);
        $this->put($path."/$interactor.php", $content);
    }

    private function createRequest(string $path)
    {
        $content = $this->getContent(
            $this->config->stub['request'],
            $useCaseName = $this->getClassFromPath($path),
            $this->getNamespaceFromPath($path)
        );
        $request = str_replace(Config::REPLACE_STR, $useCaseName, $this->config->class['request']);
        $this->put($path."/$request.php", $content);
    }

    private function createResponse(string $path)
    {
        $content = $this->getContent(
            $this->config->stub['response'],
            $useCaseName = $this->getClassFromPath($path),
            $this->getNamespaceFromPath($path)
        );
        $response = str_replace(Config::REPLACE_STR, $useCaseName, $this->config->class['response']);
        $this->put($path."/$response.php", $content);
    }

    private function getClassFromPath(string $path): string
    {
        return basename($path);
    }

    private function getNamespaceFromPath(string $path): string
    {
        return $this->config->namespace.trim(str_replace('/', '\\', $path), '\\');
    }

    private function put(string $path, string $content)
    {
        $path = $this->getFullPath($path);
        $dir = dirname($path);
        if ( ! file_exists($dir) && ! mkdir($dir, self::VISIBILITY, true)) {
            throw new RuntimeException('Failed to create a directory at '.$dir);
        }

        file_put_contents($path, $content);
    }

    public function delete(string $path): bool
    {
        $path = $this->getFullPath($path);
        if ( ! file_exists($path) || is_dir($path)) {
            return false;
        }

        return unlink($path);
    }

    public function getFullPath(string $path): string
    {
        return $this->config->basePath.'/'.ltrim($path, '/');
    }

    public function getContent(string $stubPath, string $useCaseName, string $namespace): string
    {
        $useCase = str_replace(Config::REPLACE_STR, $useCaseName, $this->config->class['use_case']);
        $request = str_replace(Config::REPLACE_STR, $useCaseName, $this->config->class['request']);
        $response = str_replace(Config::REPLACE_STR, $useCaseName, $this->config->class['response']);
        $interactor = str_replace(Config::REPLACE_STR, $useCaseName, $this->config->class['interactor']);

        $content = file_get_contents($stubPath);

        if ($content === false) {
            throw new RuntimeException('failed to read file at '.$stubPath);
        }

        $content = str_replace(Config::DUMMY_NAMESPACE, $namespace, $content);

        $content = str_replace(Config::DUMMY_USE_CASE, $useCase, $content);
        $content = str_replace(Config::DUMMY_REQUEST, $request, $content);
        $content = str_replace(Config::DUMMY_RESPONSE, $response, $content);
        $content = str_replace(Config::DUMMY_INTERACTOR, $interactor, $content);

        return $content;
    }
}
