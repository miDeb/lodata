<?php

declare(strict_types=1);

namespace Flat3\Lodata\Drivers\SQL\PDO;

use Doctrine\DBAL\Connection as DoctrineConnection;
use Doctrine\DBAL\Driver;
use Exception;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Database\SqlServerConnection;

trait Doctrine
{
    protected function getDoctrineSchemaManager()
    {
        $connection = $this->getDoctrineConnection();

        return $connection->createSchemaManager();
    }

    /**
     * Get the Doctrine DBAL database connection instance.
     *
     * @return DoctrineConnection
     */
    protected function getDoctrineConnection()
    {
        $driver = $this->getDoctrineDriver();

        $connection = $this->getConnection();

        return new DoctrineConnection(array_filter([
            'pdo' => $connection->getPdo(),
            'dbname' => $connection->getDatabaseName(),
            'driver' => $driver->getName(),
            'serverVersion' => $connection->getConfig('server_version'),
        ]), $driver);
    }

    protected function getDoctrineDriver(): Driver
    {
        switch (true) {
            case $this->getConnection() instanceof PostgresConnection:
                return new PostgresDriver;

            case $this->getConnection() instanceof MySqlConnection:
                return new MySqlDriver;

            case $this->getConnection() instanceof SQLiteConnection:
                return new SQLiteDriver;

            case $this->getConnection() instanceof SqlServerConnection:
                return new SqlServerDriver;
        }

        throw new Exception('Connection not known');
    }
}