<?php
require_once __DIR__ . '/config/app.php';
if (is_logged_in()) redirect('/index.php');
$pageTitle = 'Daftar';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    if ($name === '' || strlen($name) > 100) $errors[] = 'Nama wajib diisi (maksimal 100 karakter).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
    if (strlen($password) < 8) $errors[] = 'Password minimal 8 karakter.';
    if (!$errors) {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) $errors[] = 'Email sudah terdaftar.';
        else {
            $stmt = $pdo->prepare('INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)');
            $stmt->execute([$name,$email,password_hash($password, PASSWORD_DEFAULT),'user']);
            set_flash('success', 'Registrasi berhasil. Silakan login.');
            redirect('/login.php');
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap"><form class="panel auth-panel" method="post">
  <span class="eyebrow">BUAT AKUN BARU</span><h1>Daftar GameTopUp</h1><p class="muted">Buat akun untuk mulai melakukan pemesanan.</p>
  <?php foreach ($errors as $error): ?><div class="alert error"><?= e($error) ?></div><?php endforeach; ?>
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <label>Nama lengkap<input name="name" maxlength="100" required value="<?= e($_POST['name'] ?? '') ?>"></label>
  <label>Email<input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
  <label>Password<input type="password" name="password" minlength="8" required></label>
  <button class="btn full" type="submit">Buat Akun</button><p class="muted center">Sudah punya akun? <a href="<?= BASE_URL ?>/login.php">Login</a></p>
</form></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
