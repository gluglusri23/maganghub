<?php
require_once __DIR__ . '/../config/app.php';
require_admin();
$pageTitle = 'Dashboard Admin';
$stats = [
 'users' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn(),
 'games' => (int)$pdo->query("SELECT COUNT(*) FROM games")->fetchColumn(),
 'products' => (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
 'orders' => (int)$pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn(),
];
$latest = $pdo->query("SELECT t.*,u.name,td.game_name FROM transactions t JOIN users u ON u.id=t.user_id JOIN transaction_details td ON td.transaction_id=t.id ORDER BY t.created_at DESC LIMIT 8")->fetchAll();
require __DIR__ . '/../includes/header.php';
?>
<div class="page-head"><div><span class="eyebrow">PANEL ADMINISTRATOR</span><h1>Dashboard</h1><p class="muted">Kelola katalog dan transaksi GameTopUp.</p></div></div>
<div class="stat-grid"><?php foreach (['users'=>'Pengguna','games'=>'Game','products'=>'Produk','orders'=>'Transaksi'] as $key=>$label): ?><div class="panel stat"><span><?= $label ?></span><b><?= $stats[$key] ?></b></div><?php endforeach; ?></div>
<div class="admin-links">
<a class="panel admin-link" href="<?= BASE_URL ?>/admin/games.php"><b>🎮 Kelola Game</b><span>Tambah, ubah, hapus katalog game</span></a>
<a class="panel admin-link" href="<?= BASE_URL ?>/admin/products.php"><b>💎 Kelola Produk</b><span>Atur nominal dan harga top up</span></a>
<a class="panel admin-link" href="<?= BASE_URL ?>/admin/payments.php"><b>💳 Metode Pembayaran</b><span>Atur opsi pembayaran simulasi</span></a>
<a class="panel admin-link" href="<?= BASE_URL ?>/admin/users.php"><b>👥 Kelola Pengguna</b><span>Lihat, ubah, dan hapus akun user</span></a>
<a class="panel admin-link" href="<?= BASE_URL ?>/admin/transactions.php"><b>🧾 Kelola Transaksi</b><span>Lihat transaksi dan ubah status</span></a>
</div>
<div class="section-heading"><h2>Transaksi Terbaru</h2></div>
<div class="panel table-wrap"><table><thead><tr><th>Invoice</th><th>Pengguna</th><th>Game</th><th>Total</th><th>Status</th></tr></thead><tbody>
<?php foreach ($latest as $o): ?><tr><td><?= e($o['invoice']) ?></td><td><?= e($o['name']) ?></td><td><?= e($o['game_name']) ?></td><td><?= rupiah($o['total']) ?></td><td><span class="status <?= e($o['status']) ?>"><?= e(status_label($o['status'])) ?></span></td></tr><?php endforeach; ?>
<?php if (!$latest): ?><tr><td colspan="5" class="center muted">Belum ada transaksi.</td></tr><?php endif; ?>
</tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
