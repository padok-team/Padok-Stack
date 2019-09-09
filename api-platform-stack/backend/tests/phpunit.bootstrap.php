<!--https://github.com/dmaicher/doctrine-test-bundle/blob/master/tests/phpunit.bootstrap.php-->
<?php

use App\Kernel;

require_once __DIR__.'/../vendor/autoload.php';

$kernel = new Kernel('test', true);
$kernel->boot();

$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
$application->setAutoExit(false);
$application->add(new \Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand());
$application->add(new \Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand());
$application->add(new \Doctrine\Bundle\DoctrineBundle\Command\Proxy\RunSqlDoctrineCommand());

$application->run(new \Symfony\Component\Console\Input\ArrayInput([
    'command' => 'doctrine:database:drop',
    '--if-exists' => '1',
    '--force' => '1',
]));

$application->run(new \Symfony\Component\Console\Input\ArrayInput([
    'command' => 'doctrine:database:create',
]));

$application->run(new \Symfony\Component\Console\Input\ArrayInput([
    'command' => 'doctrine:query:sql',
    'sql' => 'CREATE TABLE test (test VARCHAR(10))',
]));

$application->run(new \Symfony\Component\Console\Input\ArrayInput([
    'command' => 'doctrine:migrations:migrate',
    '--no-interaction' => '1',
]));

$kernel->shutdown();
