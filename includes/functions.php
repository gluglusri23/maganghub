<?php
declare(strict_types=1);

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function redirect(string $path): never {
    header('Location: ' . BASE_URL . $path);
    exit;
}
function is_logged_in(): bool {
    return isset($_SESSION['user']);
}
function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}
function is_admin(): bool {
    return is_logged_in() && (current_user()['role'] ?? '') === 'admin';
}
function require_login(): void {
    if (!is_logged_in()) {
        set_flash('error', 'Silakan login terlebih dahulu.');
        redirect('/login.php');
    }
}
function require_admin(): void {
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        exit('403 - Anda tidak memiliki akses admin.');
    }
}
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function get_flash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit('Token keamanan tidak valid. Muat ulang halaman dan coba lagi.');
    }
}
function rupiah(int|float|string $amount): string {
    return 'Rp ' . number_format((float)$amount, 0, ',', '.');
}
function make_invoice(): string {
    return 'GT' . date('ymd') . strtoupper(bin2hex(random_bytes(3)));
}
function status_label(string $status): string {
    return match ($status) {
        'pending' => 'Menunggu Pembayaran',
        'paid' => 'Dibayar',
        'processing' => 'Diproses',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        default => ucfirst($status),
    };
}
