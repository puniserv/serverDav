<?php
declare(strict_types=1);

namespace Src\Command;

use Src\Container;
use Src\Db;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Install extends Command
{
    private const SQL_FILES_PATH = '/sabre/dav/examples/sql/';

    protected function configure(): void
    {
        $this->setName('install');
        $this->setDescription('Import default database tables and records');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = $this->getDb();
        $db->connect();
        $pdo = $db->getPdo();
        try {
            foreach ($this->getSqlFiles() as $file) {
                $output->write("File: $file", true);
                $result = $pdo->exec(file_get_contents($file));
                $output->write("Result: $result", true);
            }
        } catch (\Throwable $throwable) {
            $output->write('Failed: ' . $throwable->getMessage(), true);
            return;
        }
        $pdo = null;
        $db->close();
        $output->write('Done', true);
    }

    private function getDb(): Db
    {
        return $this->getContainer()->getDb();
    }

    private function getVendorPath(): string
    {
        return $this->getAppDir() . '/vendor';
    }

    private function getContainer(): Container
    {
        return new Container((require $this->getAppDir() . '/config/main.php')['services'] ?? '');
    }

    private function getSqlFiles(): array
    {
        return glob($this->getVendorPath() . self::SQL_FILES_PATH . 'sqlite.*');
    }

    private function getAppDir(): string
    {
        return dirname(__DIR__, 2);
    }
}
