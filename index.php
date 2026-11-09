<?php
require_once __DIR__ . '/config/app.php';
$pageTitle = 'Top Up Game';
$games = $pdo->query("SELECT g.*, COUNT(p.id) AS product_count FROM games g
    LEFT JOIN products p ON p.game_id = g.id AND p.is_active = 1
    WHERE g.is_active = 1 GROUP BY g.id ORDER BY g.name")->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div>
    <span class="eyebrow">TOP UP CEPAT • AMAN • MUDAH</span>
    <h1>Level up permainanmu.<br><span>Top up tanpa ribet.</span></h1>
    <p>Pilih game favorit, tentukan nominal, lalu selesaikan pembayaran simulasi dalam beberapa langkah.</p>
    <a class="btn" href="#games">Jelajahi Game</a>
  </div>
  <div class="hero-art"><div class="orb orb-one"></div><div class="orb orb-two"></div><div class="controller">🎮</div><div class="hero-caption">GAME ON<br><b>TOP UP NOW</b></div></div>
</section>
<div class="section-heading" id="games"><div><span class="eyebrow">KATALOG</span><h2>Pilih Game Favorit</h2></div><p>Produk digital tersedia untuk demo pemesanan.</p></div>
<div class="game-grid">
<?php foreach ($games as $game): ?>
  <article class="game-card">
    <div class="game-cover" style="--cover: <?= e($game['theme_color'] ?: '#5837b7') ?>">
      <span class="game-symbol"><?= e($game['icon'] ?: '🎮') ?></span>
      <span class="cover-label">GAME TOP UP</span>
    </div>
    <div class="game-info">
      <div><h3><?= e($game['name']) ?></h3><p><?= e($game['description']) ?></p></div>
      <a class="btn btn-outline" href="<?= BASE_URL ?>/topup.php?game=<?= (int)$game['id'] ?>">Top Up</a>
    </div>
  </article>
<?php endforeach; ?>
</div>
<?php if (!$games): ?><div class="empty">Belum ada game aktif. Admin dapat menambahkan game melalui dashboard.</div><?php endif; ?>
<section class="steps">
  <div><b>01</b><h3>Pilih game</h3><p>Tentukan game yang ingin kamu top up.</p></div>
  <div><b>02</b><h3>Isi data akun</h3><p>Masukkan Player ID dan server jika diperlukan.</p></div>
  <div><b>03</b><h3>Bayar & selesai</h3><p>Gunakan pembayaran simulasi untuk menyelesaikan pesanan demo.</p></div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
