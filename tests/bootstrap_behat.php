<?php

declare(strict_types=1);

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

set_time_limit(0);

$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = 'test';

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

$commands = [
    [
        'command' => 'cache:clear',
        '--no-optional-warmers' => true,
        '-n' => true,
    ],
    [
        'command' => 'doctrine:database:drop',
        '-f' => true,
        '--if-exists' => true,
        '-n' => true,
    ],
    [
        'command' => 'doctrine:database:create',
        '-n' => true,
    ],
    [
        'command' => 'doctrine:migrations:migrate',
        '--allow-no-migration' => true,
        '-n' => true,
        '-q' => true,
    ],
];

$kernel = new Kernel('test', (bool) $_SERVER['APP_DEBUG']);
$application = new Application($kernel);
$application->setAutoExit(false);
foreach ($commands as $command) {
    $application->run(new ArrayInput($command));
}
$kernel->shutdown();
