<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/data.php';
require_once dirname(__DIR__) . '/config/database.php';

function ensure_session_started(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function is_logged_in(): bool
{
    return getCurrentUser() !== null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect(base_url('login.php'));
    }
}

/**
 * Returns the authenticated user stored in the session.
 *
 * Until the real login flow is implemented, development mode falls back to
 * a demo Administrator so the protected scaffold remains accessible locally.
 */
function getCurrentUser(): ?array
{
    ensure_session_started();

    if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
        return $_SESSION['user'];
    }

    if (config('environment') === 'development') {
        return [
            'id' => 0,
            'username' => 'demo-admin',
            'email' => 'admin@hospital.test',
            'role_id' => 0,
            'role_name' => 'Administrator',
            'is_demo_user' => true,
        ];
    }

    return null;
}

/**
 * Loads the permissions granted to a user through role assignments.
 *
 * Results are cached per request and in the session for regular users so the
 * sidebar and page guards do not keep repeating the same joins.
 */
function getUserPermissions(int $userId): array
{
    static $permissionCache = [];

    $currentUser = getCurrentUser();

    if ($currentUser === null) {
        return [];
    }

    if (($currentUser['is_demo_user'] ?? false) === true) {
        return ['*'];
    }

    if (isset($permissionCache[$userId])) {
        return $permissionCache[$userId];
    }

    ensure_session_started();

    if (isset($_SESSION['permissions'][$userId]) && is_array($_SESSION['permissions'][$userId])) {
        $permissionCache[$userId] = $_SESSION['permissions'][$userId];
        return $permissionCache[$userId];
    }

    $pdo = database_connection();
    $statement = $pdo->prepare(
        'SELECT p.name
         FROM users u
         INNER JOIN role_permissions rp ON rp.role_id = u.role_id
         INNER JOIN permissions p ON p.id = rp.permission_id
         WHERE u.id = :user_id'
    );
    $statement->execute(['user_id' => $userId]);

    $permissions = $statement->fetchAll(PDO::FETCH_COLUMN);
    $permissions = array_values(array_unique(array_map('strval', $permissions)));

    $_SESSION['permissions'][$userId] = $permissions;
    $permissionCache[$userId] = $permissions;

    return $permissions;
}

/**
 * Checks whether the current user holds a given permission string.
 */
function hasPermission(string $permission): bool
{
    $currentUser = getCurrentUser();

    if ($currentUser === null) {
        return false;
    }

    $permissions = getUserPermissions((int) ($currentUser['id'] ?? 0));

    return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
}

/**
 * Stops the request with a 403 response if the current user lacks access.
 *
 * This should be used by protected pages in addition to hiding navigation.
 */
function requirePermission(string $permission): void
{
    if (hasPermission($permission)) {
        return;
    }

    http_response_code(403);
    require dirname(__DIR__) . '/views/errors/403.php';
    exit;
}
