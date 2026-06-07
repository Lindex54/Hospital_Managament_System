<?php

declare(strict_types=1);

function validate_required(array $data, array $fields): array
{
    $errors = [];

    foreach ($fields as $field) {
        $value = trim((string) ($data[$field] ?? ''));

        if ($value === '') {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }

    return $errors;
}

