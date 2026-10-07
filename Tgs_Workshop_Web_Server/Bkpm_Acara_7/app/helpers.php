<?php

// Escape output agar aman dari XSS saat menampilkan data dari database
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
