<?php
require_once __DIR__ . '/config/app.php';
require_login();
$stmt = $pdo->prepare("SELECT t.*, td.game_name, td.product_name FROM transactions t
 JOIN transaction_details td ON td.transaction_id=t.id WHERE t.user_id=? ORDER BY t.created_at DESC");
$stmt->execute([current_user()['id']]);
$orders = $stmt->fetchAll();
$pageTitle = 'Pesanan Saya';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head"><div><span class="eyebrow">AKUN</span><h1>Pesanan Saya</h1><p class="muted">Riwayat transaksi top up Anda.</p></div><a class="btn" href="<?= BASE_URL ?>/index.php">Top Up Lagi</a></div>
<div class="panel table-wrap"><table><thead><tr><th>Invoice</th><th>Game / Produk</th><th>Total</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($orders as $o): ?><tr><td><?= e($o['invoice']) ?></td><td><?= e($o['game_name']) ?><br><small><?= e($o['product_name']) ?></small></td><td><?= rupiah($o['total']) ?></td><td><?= e($o['created_at']) ?></td><td><span class="status <?= e($o['status']) ?>"><?= e(status_label($o['status'])) ?></span></td><td><a href="<?= BASE_URL ?>/payment.php?id=<?= (int)$o['id'] ?>">Detail</a></td></tr><?php endforeach; ?>
<?php if (!$orders): ?><tr><td colspan="6" class="center muted">Belum ada transaksi.</td></tr><?php endif; ?>
</tbody></table></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
