<?php
require_once __DIR__ . '/config/app.php';
if (is_logged_in()) redirect('/index.php');
$pageTitle = 'Login';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id,name,email,password,role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        unset($user['password']);
        $_SESSION['user'] = $user;
        redirect($user['role'] === 'admin' ? '/admin/index.php' : '/index.php');
    }
    $error = 'Email atau password salah.';
}
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap"><form class="panel auth-panel" method="post">
  <span class="eyebrow">SELAMAT DATANG KEMBALI</span><h1>Login</h1><p class="muted">Masuk untuk melanjutkan transaksi.</p>
  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <label>Email<input type="email" name="email" required autofocus></label>
  <label>Password<input type="password" name="password" required></label>
  <button class="btn full" type="submit">Masuk</button><p class="muted center">Belum punya akun? <a href="<?= BASE_URL ?>/register.php">Daftar</a></p>
</form></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
