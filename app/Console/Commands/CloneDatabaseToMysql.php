<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CloneDatabaseToMysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clone-to-mysql
                            {--sqlite-path= : Ruta al archivo SQLite (por defecto database/database.sqlite)}
                            {--fresh : Ejecutar migraciones en MySQL antes de copiar (crea tablas vacías)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clona la base de datos SQLite actual a MySQL (útil para exportar a XAMPP)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sqlitePath = $this->option('sqlite-path') ?? database_path('database.sqlite');

        if (! file_exists($sqlitePath)) {
            $this->error("No se encontró el archivo SQLite en: {$sqlitePath}");
            $this->info('Crea la base SQLite con: php artisan migrate');
            return self::FAILURE;
        }

        if (config('database.default') !== 'mysql') {
            $this->warn('Tu .env tiene DB_CONNECTION=' . config('database.default') . '.');
            $this->info('Para clonar a XAMPP, configura temporalmente en .env:');
            $this->line('  DB_CONNECTION=mysql');
            $this->line('  DB_HOST=127.0.0.1');
            $this->line('  DB_PORT=3306');
            $this->line('  DB_DATABASE=crm_nv');
            $this->line('  DB_USERNAME=root');
            $this->line('  DB_PASSWORD=');
            if (! $this->confirm('¿Continuar usando la conexión MySQL actual del .env?', true)) {
                return self::FAILURE;
            }
        }

        config(['database.connections.clone_sqlite' => [
            'driver' => 'sqlite',
            'database' => $sqlitePath,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]]);

        try {
            DB::connection('mysql')->getPdo();
        } catch (\Throwable $e) {
            $this->error('No se pudo conectar a MySQL. Revisa XAMPP (MySQL en ejecución) y tu .env: ' . $e->getMessage());
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->info('Ejecutando migraciones en MySQL...');
            $this->call('migrate', ['--database' => 'mysql', '--force' => true]);
        }

        $tables = $this->getTableNamesFromSqlite();
        if (empty($tables)) {
            $this->warn('No hay tablas en el archivo SQLite.');
            return self::SUCCESS;
        }

        $this->info('Clonando ' . count($tables) . ' tablas de SQLite a MySQL...');

        DB::connection('mysql')->getPdo()->exec('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $table) {
            $this->cloneTable($table);
        }

        DB::connection('mysql')->getPdo()->exec('SET FOREIGN_KEY_CHECKS=1');

        $this->newLine();
        $this->info('Base de datos clonada correctamente a MySQL (XAMPP).');
        return self::SUCCESS;
    }

    /**
     * Obtiene los nombres de tablas del SQLite (excluyendo sqlite_sequence).
     */
    protected function getTableNamesFromSqlite(): array
    {
        $names = DB::connection('clone_sqlite')
            ->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");

        return array_map(fn ($row) => $row->name, $names);
    }

    /**
     * Copia una tabla desde SQLite a MySQL.
     */
    protected function cloneTable(string $table): void
    {
        $count = DB::connection('clone_sqlite')->table($table)->count();
        if ($count === 0) {
            $this->line("  [{$table}] (vacía)");
            return;
        }

        if (! Schema::connection('mysql')->hasTable($table)) {
            $this->warn("  [{$table}] no existe en MySQL (¿ejecutaste migraciones? usa --fresh). Omitiendo.");
            return;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->setFormat("  [{$table}] %current%/%max%");
        $bar->start();

        $chunkSize = 100;
        $query = DB::connection('clone_sqlite')->table($table);
        if (Schema::connection('clone_sqlite')->hasColumn($table, 'id')) {
            $query->orderBy('id');
        }
        $query->chunk($chunkSize, function ($rows) use ($table, $bar) {
            $inserts = $rows->map(fn ($row) => (array) $row)->toArray();
            foreach ($inserts as $row) {
                try {
                    DB::connection('mysql')->table($table)->insert($row);
                } catch (\Throwable $e) {
                    $this->newLine();
                    $this->warn("  Error en {$table}: " . $e->getMessage());
                }
            }
            $bar->advance($rows->count());
        });

        $bar->finish();
        $this->newLine();
    }
}
