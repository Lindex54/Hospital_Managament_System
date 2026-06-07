<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config/app.php';
require_once dirname(__DIR__) . '/app/helpers/functions.php';

$appName = config('app_name', 'Hospital Management System');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?= e($appName); ?></title>
    <link rel="stylesheet" href="<?= e(asset_url('css/output.css')); ?>">
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <main class="flex min-h-screen items-center justify-center px-4">
        <section class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg">
            <h1 class="text-2xl font-bold text-slate-900"><?= e($appName); ?></h1>
            <p class="mt-2 text-sm text-slate-600">Login screen placeholder. Authentication will be added module by module.</p>

            <form class="mt-6 space-y-4" action="#" method="post">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700" for="email">Email</label>
                    <input class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-brand-600 focus:outline-none" id="email" name="email" type="email" placeholder="name@example.com">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700" for="password">Password</label>
                    <input class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-brand-600 focus:outline-none" id="password" name="password" type="password" placeholder="********">
                </div>

                <button class="w-full rounded-lg bg-brand-600 px-4 py-2 font-semibold text-white transition hover:bg-brand-700" type="submit">
                    Sign In
                </button>
            </form>
        </section>
    </main>
</body>
</html>

