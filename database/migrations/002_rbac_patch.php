<?php

declare(strict_types=1);

/**
 * RBAC patch runner
 *
 * This script updates an existing Hospital Management System database with the
 * role-based access control tables and seed data introduced after the initial
 * schema. It is intentionally idempotent so it can be re-run safely while the
 * project is still under active setup.
 */

require_once dirname(__DIR__, 2) . '/app/config/database.php';
require_once dirname(__DIR__, 2) . '/app/helpers/data.php';

/**
 * Returns true when a table already exists in the configured database.
 */
function table_exists(PDO $pdo, string $tableName): bool
{
    $statement = $pdo->prepare(
        'SELECT COUNT(*)
         FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = :table_name'
    );
    $statement->execute(['table_name' => $tableName]);

    return (int) $statement->fetchColumn() > 0;
}

/**
 * Returns true when a column already exists on a table.
 */
function column_exists(PDO $pdo, string $tableName, string $columnName): bool
{
    $statement = $pdo->prepare(
        'SELECT COUNT(*)
         FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = :table_name
           AND COLUMN_NAME = :column_name'
    );
    $statement->execute([
        'table_name' => $tableName,
        'column_name' => $columnName,
    ]);

    return (int) $statement->fetchColumn() > 0;
}

/**
 * Returns true when a named index already exists on a table.
 */
function index_exists(PDO $pdo, string $tableName, string $indexName): bool
{
    $statement = $pdo->prepare(
        'SELECT COUNT(*)
         FROM information_schema.STATISTICS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = :table_name
           AND INDEX_NAME = :index_name'
    );
    $statement->execute([
        'table_name' => $tableName,
        'index_name' => $indexName,
    ]);

    return (int) $statement->fetchColumn() > 0;
}

/**
 * Returns true when a foreign key constraint already exists on a table.
 */
function foreign_key_exists(PDO $pdo, string $tableName, string $constraintName): bool
{
    $statement = $pdo->prepare(
        'SELECT COUNT(*)
         FROM information_schema.TABLE_CONSTRAINTS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = :table_name
           AND CONSTRAINT_NAME = :constraint_name
           AND CONSTRAINT_TYPE = \'FOREIGN KEY\''
    );
    $statement->execute([
        'table_name' => $tableName,
        'constraint_name' => $constraintName,
    ]);

    return (int) $statement->fetchColumn() > 0;
}

/**
 * Creates the RBAC tables that may not exist yet on earlier installs.
 */
function ensure_rbac_tables(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS permissions (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            label VARCHAR(150) NOT NULL,
            module VARCHAR(100) NOT NULL,
            description TEXT DEFAULT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_permissions_module (module)
        ) ENGINE=InnoDB'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS role_permissions (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            role_id BIGINT UNSIGNED NOT NULL,
            permission_id BIGINT UNSIGNED NOT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_role_permissions_role
                FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
            CONSTRAINT fk_role_permissions_permission
                FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
            UNIQUE KEY uq_role_permission (role_id, permission_id)
        ) ENGINE=InnoDB'
    );
}

/**
 * Aligns the users table with the RBAC model by ensuring role_id exists and
 * points back to the roles table.
 */
function ensure_users_role_column(PDO $pdo): void
{
    if (!column_exists($pdo, 'users', 'role_id')) {
        $pdo->exec('ALTER TABLE users ADD COLUMN role_id BIGINT UNSIGNED NULL AFTER id');
    }

    if (!index_exists($pdo, 'users', 'fk_users_role')) {
        // MySQL automatically creates a supporting index for the foreign key.
    }

    if (!foreign_key_exists($pdo, 'users', 'fk_users_role')) {
        $pdo->exec('ALTER TABLE users ADD CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)');
    }
}

/**
 * Adds the expanded role list without removing any existing user data.
 */
function seed_roles(PDO $pdo, array $roles): void
{
    $statement = $pdo->prepare(
        'INSERT INTO roles (name, description)
         VALUES (:name, :description)
         ON DUPLICATE KEY UPDATE description = VALUES(description)'
    );

    foreach ($roles as $role) {
        $statement->execute([
            'name' => $role['name'],
            'description' => $role['description'],
        ]);
    }
}

/**
 * Inserts or refreshes the permission catalog used by the sidebar and route
 * guards.
 */
function seed_permissions(PDO $pdo, array $permissions): void
{
    $statement = $pdo->prepare(
        'INSERT INTO permissions (name, label, module, description)
         VALUES (:name, :label, :module, :description)
         ON DUPLICATE KEY UPDATE
            label = VALUES(label),
            module = VALUES(module),
            description = VALUES(description)'
    );

    foreach ($permissions as $permission) {
        $statement->execute($permission);
    }
}

/**
 * Seeds the role-to-permission assignments declared in app/data/rbac.php.
 */
function seed_role_permissions(PDO $pdo, array $rolePermissions): void
{
    $roleLookup = $pdo->query('SELECT name, id FROM roles')->fetchAll(PDO::FETCH_KEY_PAIR);
    $permissionLookup = $pdo->query('SELECT name, id FROM permissions')->fetchAll(PDO::FETCH_KEY_PAIR);

    $insertStatement = $pdo->prepare(
        'INSERT IGNORE INTO role_permissions (role_id, permission_id)
         VALUES (:role_id, :permission_id)'
    );

    foreach ($rolePermissions as $roleName => $permissionNames) {
        if (!isset($roleLookup[$roleName])) {
            continue;
        }

        $roleId = (int) $roleLookup[$roleName];
        $permissionsToAssign = $permissionNames;

        if ($permissionNames === ['*']) {
            $permissionsToAssign = array_keys($permissionLookup);
        }

        foreach ($permissionsToAssign as $permissionName) {
            if (!isset($permissionLookup[$permissionName])) {
                continue;
            }

            $insertStatement->execute([
                'role_id' => $roleId,
                'permission_id' => (int) $permissionLookup[$permissionName],
            ]);
        }
    }
}

$pdo = database_connection();
$rbac = load_app_data('rbac.php');

try {
    ensure_rbac_tables($pdo);
    ensure_users_role_column($pdo);
    seed_roles($pdo, $rbac['roles']);
    seed_permissions($pdo, $rbac['permissions']);
    seed_role_permissions($pdo, $rbac['role_permissions']);

    echo "RBAC patch applied successfully.\n";
} catch (Throwable $exception) {
    fwrite(STDERR, "RBAC patch failed: {$exception->getMessage()}\n");
    exit(1);
}
