<?php
$pageTitle = $pageTitle ?? 'Beranda';
$flash = get_flash();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | <?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
</head>
<body>
<header class="topbar">
  <a class="brand" href="<?= BASE_URL ?>/index.php"><span class="brand-mark">G</span> Game<span>TopUp</span></a>
  <nav>
    <a href="<?= BASE_URL ?>/index.php">Beranda</a>
    <?php if (is_logged_in()): ?>
      <a href="<?= BASE_URL ?>/orders.php">Pesanan Saya</a>
      <?php if (is_admin()): ?><a href="<?= BASE_URL ?>/admin/index.php">Admin</a><?php endif; ?>
      <span class="nav-user"><?= e(current_user()['name']) ?></span>
      <form class="inline-form" method="post" action="<?= BASE_URL ?>/logout.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <button class="link-button" type="submit">Keluar</button>
      </form>
    <?php else: ?>
      <a href="<?= BASE_URL ?>/login.php">Masuk</a>
      <a class="btn btn-small" href="<?= BASE_URL ?>/register.php">Daftar</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
<?php if ($flash): ?>
  <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>
