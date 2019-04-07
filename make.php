<?php
declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use Yahiru\CAFactory\Config;

function out(string $text, bool $break = true): void
{
    echo $text . ($break ? PHP_EOL : '');
}

function red(string $text): string
{
    return "\e[31m". $text ."\e[m";
}

function green(string $text): string
{
    return "\e[32m". $text ."\e[m";
}

function isOk(): bool
{
    $response = strtolower(trim(fgets(STDIN)));
    return in_array(
        ($response ?: 'yes'),
        ['y', 'yes'],
        true
    );
}

function getUseCase(Config $config): string
{
    do {
        out('Please entered UseCase.');
        out('example: User/CreateUser');

        out('UseCase : ', false);
        $useCase = trim(fgets(STDIN));

        out('');
        out('Are you ok? : '. $config->basePath.'/'.$useCase);
        out('('.green('Y/n').') : ', false);
    } while (!isOk());

    return $useCase;
}

function initialize(Config $config)
{
    /*
     * basePathの設定
     */
    do {
        out('Please entered full path for creating usecase.');
        out('example: /var/www/app/packages/UseCases');
        out('current value: '.($config->basePath ?: red('empty')).PHP_EOL);

        out('directory: ', false);
        $basePath = rtrim(trim(fgets(STDIN)), '/');

        out('');
        out('Are you ok? : '. $basePath);
        out('('.green('Y/n').') : ', false);
    } while (!isOk());

    /*
     * namespaceの設定
     */
    do {
        out('Please entered base namespace for usecase.');
        out('example: App\\Packages\\UseCases\\');
        out('current value: '.($config->namespace ?: red('empty')).PHP_EOL);

        out('namespace: ', false);
        $namespace = trim(trim(fgets(STDIN)), '\\').'\\';

        out('');
        out('Are you ok? : '. $namespace);
        out('('.green('Y/n').') : ', false);
    } while (!isOk());

    $stubConfig = file_get_contents(__DIR__.'/stub/Config');
    $stubConfig = str_replace('__BASE_PATH__', $basePath, $stubConfig);
    $stubConfig = str_replace('__NAMESPACE__', str_replace('\\', '\\\\', $namespace), $stubConfig);

    if (file_put_contents(__DIR__.'/config/user.php', $stubConfig) === false) {
        out(red('failed to create config file.'));
        exit;
    }

    out('set value success.'.PHP_EOL);
}

$config = Config::load();
if ( ! $config->isValid()) {
    initialize($config);
    $config = Config::load();
}

$useCase = $argv[1] ?? getUseCase($config);

$factory = new \Yahiru\CAFactory\UseCaseFactory($config);
$factory->make($useCase);
