<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin(): void
{
    requireLogin();

    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo 'Geen toegang. Alleen admins mogen deze pagina bekijken.';
        exit;
    }
}

function currentUserId(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function currentUserName(): string
{
    return $_SESSION['user_name'] ?? 'Gebruiker';
}

function currentUserRole(): string
{
    return $_SESSION['user_role'] ?? 'moderator';
}

function isAdmin(): bool
{
    return currentUserRole() === 'admin';
}

function isModerator(): bool
{
    return currentUserRole() === 'moderator';
}