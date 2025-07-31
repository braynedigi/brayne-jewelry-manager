<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExportDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:export {--file= : The output file name} {--format=sqlite : Export format (sqlite or mysql)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export database structure and data for cPanel migration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = $this->option('file') ?? 'database_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $format = $this->option('format') ?? 'sqlite';
        
        $this->info("Exporting database to {$filename} in {$format} format...");
        
        try {
            $connection = DB::connection();
            $database = $connection->getDatabaseName();
            
            // Get all tables
            $tables = [];
            if ($connection->getDriverName() === 'sqlite') {
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            } else {
                $tables = Schema::getAllTables();
            }
            
            $sql = "-- Database Export for Laravel Jewelry Manager\n";
            $sql .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Database: {$database}\n\n";
            
            // Define table order to handle foreign key dependencies
            $tableOrder = [
                'users',
                'password_reset_tokens', 
                'sessions',
                'cache',
                'cache_locks',
                'jobs',
                'job_batches',
                'failed_jobs',
                'distributors',
                'customers',
                'products',
                'couriers',
                'product_categories',
                'product_metals',
                'product_stones',
                'product_fonts',
                'ring_sizes',
                'order_templates',
                'orders',
                'order_product',
                'order_status_histories',
                'settings',
                'notifications',
                'email_templates'
            ];
            
            // First pass: Create tables in proper order
            foreach ($tableOrder as $tableName) {
                // Find the table in our list
                $table = null;
                foreach ($tables as $t) {
                    $tName = $t->name ?? $t->TABLE_NAME;
                    if ($tName === $tableName) {
                        $table = $t;
                        break;
                    }
                }
                
                if (!$table) {
                    continue; // Skip if table not found
                }
                
                $this->info("Exporting table structure: {$tableName}");
                
                // Get table structure
                if ($connection->getDriverName() === 'sqlite') {
                    $createTable = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name='{$tableName}'")[0];
                    $createStatement = $createTable->sql;
                    
                    if ($format === 'mysql') {
                        // Convert SQLite syntax to MySQL syntax
                        $createStatement = $this->convertSqliteToMysql($createStatement, $tableName);
                    }
                } else {
                    $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
                    $createStatement = $createTable->{'Create Table'} ?? $createTable->{'Create View'};
                }
                
                $sql .= "-- Table structure for table `{$tableName}`\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createStatement . ";\n\n";
            }
            
            // Second pass: Insert data
            foreach ($tableOrder as $tableName) {
                // Find the table in our list
                $table = null;
                foreach ($tables as $t) {
                    $tName = $t->name ?? $t->TABLE_NAME;
                    if ($tName === $tableName) {
                        $table = $t;
                        break;
                    }
                }
                
                if (!$table) {
                    continue; // Skip if table not found
                }
                
                // Get table data
                $rows = DB::table($tableName)->get();
                
                if ($rows->count() > 0) {
                    $this->info("Exporting data: {$tableName}");
                    $sql .= "-- Data for table `{$tableName}`\n";
                    
                    foreach ($rows as $row) {
                        $values = [];
                        foreach ((array) $row as $value) {
                            if ($value === null) {
                                $values[] = 'NULL';
                            } elseif (is_numeric($value)) {
                                $values[] = $value;
                            } else {
                                $values[] = "'" . addslashes($value) . "'";
                            }
                        }
                        
                        $sql .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $sql .= "\n";
                }
            }
            
            // Add foreign key constraints after all tables are created
            if ($format === 'mysql') {
                $sql .= "-- Adding foreign key constraints\n";
                $sql .= $this->getForeignKeyConstraints();
            }
            
            // Write to file
            file_put_contents($filename, $sql);
            
            $this->info("✅ Database exported successfully to {$filename}");
            $this->info("📊 Total tables exported: " . count($tables));
            
        } catch (\Exception $e) {
            $this->error("❌ Error exporting database: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    /**
     * Convert SQLite CREATE TABLE statement to MySQL syntax
     */
    private function convertSqliteToMysql($sqliteStatement, $tableName)
    {
        // Replace double quotes with backticks for identifiers
        $mysqlStatement = preg_replace('/"([^"]+)"/', '`$1`', $sqliteStatement);
        
        // Convert SQLite data types to MySQL
        $mysqlStatement = str_replace([
            'integer primary key autoincrement',
            'integer',
            'varchar',
            'datetime',
            'text',
            'real',
            'blob',
            'boolean'
        ], [
            'int auto_increment primary key',
            'int',
            'varchar(255)',
            'datetime',
            'text',
            'double',
            'longblob',
            'tinyint(1)'
        ], $mysqlStatement);
        
        // Remove SQLite-specific constraints
        $mysqlStatement = preg_replace('/check\s*\([^)]+\)/i', '', $mysqlStatement);
        
        // Fix role column constraint issue
        $mysqlStatement = preg_replace('/`role` varchar\(255\) \)/', '`role` varchar(255)', $mysqlStatement);
        
        // Remove foreign key constraints for now (we'll add them back later)
        $mysqlStatement = preg_replace('/,\s*foreign key\s*\([^)]+\)\s*references\s*[^)]+\)/i', '', $mysqlStatement);
        
        // Remove any remaining foreign key constraint fragments from column definitions
        $mysqlStatement = preg_replace('/\s+on\s+delete\s+cascade\s*/i', '', $mysqlStatement);
        $mysqlStatement = preg_replace('/\s+on\s+delete\s+set\s+null\s*/i', '', $mysqlStatement);
        $mysqlStatement = preg_replace('/\s+on\s+update\s+no\s+action\s*/i', '', $mysqlStatement);
        $mysqlStatement = preg_replace('/on\s+delete\s+cascade/i', '', $mysqlStatement);
        $mysqlStatement = preg_replace('/on\s+delete\s+set\s+null/i', '', $mysqlStatement);
        $mysqlStatement = preg_replace('/on\s+update\s+no\s+action/i', '', $mysqlStatement);
        
        // Clean up extra commas
        $mysqlStatement = preg_replace('/,\s*\)/', ')', $mysqlStatement);
        
        // Add MySQL-specific options
        $mysqlStatement = str_replace(');', ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;', $mysqlStatement);
        
        return $mysqlStatement;
    }
    
    /**
     * Generate foreign key constraints for MySQL
     */
    private function getForeignKeyConstraints()
    {
        $constraints = "";
        
        // Add foreign key constraints in the correct order
        $constraints .= "ALTER TABLE `distributors` ADD CONSTRAINT `distributors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;\n";
        $constraints .= "ALTER TABLE `customers` ADD CONSTRAINT `customers_distributor_id_foreign` FOREIGN KEY (`distributor_id`) REFERENCES `distributors` (`id`) ON DELETE CASCADE;\n";
        $constraints .= "ALTER TABLE `order_templates` ADD CONSTRAINT `order_templates_distributor_id_foreign` FOREIGN KEY (`distributor_id`) REFERENCES `distributors` (`id`) ON DELETE CASCADE;\n";
        $constraints .= "ALTER TABLE `orders` ADD CONSTRAINT `orders_distributor_id_foreign` FOREIGN KEY (`distributor_id`) REFERENCES `distributors` (`id`) ON DELETE CASCADE;\n";
        $constraints .= "ALTER TABLE `orders` ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;\n";
        $constraints .= "ALTER TABLE `orders` ADD CONSTRAINT `orders_courier_id_foreign` FOREIGN KEY (`courier_id`) REFERENCES `couriers` (`id`) ON DELETE SET NULL;\n";
        $constraints .= "ALTER TABLE `orders` ADD CONSTRAINT `orders_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `order_templates` (`id`) ON DELETE SET NULL;\n";
        $constraints .= "ALTER TABLE `order_product` ADD CONSTRAINT `order_product_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;\n";
        $constraints .= "ALTER TABLE `order_product` ADD CONSTRAINT `order_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;\n";
        $constraints .= "ALTER TABLE `order_status_histories` ADD CONSTRAINT `order_status_histories_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;\n";
        $constraints .= "ALTER TABLE `order_status_histories` ADD CONSTRAINT `order_status_histories_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;\n";
        $constraints .= "ALTER TABLE `notifications` ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;\n";
        $constraints .= "ALTER TABLE `product_categories` ADD CONSTRAINT `product_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE;\n";
        
        return $constraints;
    }
} 