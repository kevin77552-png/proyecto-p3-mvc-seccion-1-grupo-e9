<?php
// Script para migrar todas las tablas de SQLite a MySQL usando Laravel
// Requiere que el .env esté configurado para SQLite primero, luego para MySQL

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';

// Configuración manual de conexiones
$capsule = new Capsule;

// Conexión SQLite
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__.'/database/database.sqlite',
    'prefix' => '',
], 'sqlite');

// Conexión MySQL
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'laravel',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
], 'mysql');

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Obtener todas las tablas de SQLite
$tables = Capsule::connection('sqlite')->select('SELECT name FROM sqlite_master WHERE type="table" AND name NOT LIKE "sqlite_%"');

foreach ($tables as $tableObj) {
    $table = $tableObj->name;
    echo "Migrando tabla: $table\n";
    $rows = Capsule::connection('sqlite')->table($table)->get();
    foreach ($rows as $row) {
        $data = (array)$row;
        try {
            Capsule::connection('mysql')->table($table)->insert($data);
        } catch (Exception $e) {
            echo "Error en $table: ".$e->getMessage()."\n";
        }
    }
}

echo "Migración completada.\n";
