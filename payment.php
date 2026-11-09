<?php
require_once __DIR__ . '/config/app.php';
require_login();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare("SELECT t.*, pm.name AS payment_name, td.game_name, td.product_name
 FROM transactions t JOIN payment_methods pm ON pm.id=t.payment_method_id
 JOIN transaction_details td ON td.transaction_id=t.id
 WHERE t.id=? AND (t.user_id=? OR ?=1) LIMIT 1");
$stmt->execute([$id,current_user()['id'],is_admin()?1:0]);
$order = $stmt->fetch();
if (!$order) { http_response_code(404); exit('Pesanan tidak ditemukan.'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if ($order['status'] === 'pending') {
        $update = $pdo->prepare("UPDATE transactions SET status='paid', paid_at=NOW() WHERE id=? AND user_id=? AND status='pending'");
        $update->execute([$id,current_user()['id']]);
        set_flash('success','Pembayaran simulasi berhasil. Pesanan ditandai sebagai dibayar.');
    }
    redirect('/payment.php?id=' . $id);
}
$pageTitle = 'Pembayaran';
require __DIR__ . '/includes/header.php';
?>
<div class="narrow"><div class="panel">
  <span class="eyebrow">PEMBAYARAN SIMULASI</span><h1>Detail Pesanan</h1>
  <div class="invoice"><?= e($order['invoice']) ?></div>
  <div class="summary-row"><span>Game</span><b><?= e($order['game_name']) ?></b></div>
  <div class="summary-row"><span>Produk</span><b><?= e($order['product_name']) ?></b></div>
  <div class="summary-row"><span>Player ID</span><b><?= e($order['player_id']) ?></b></div>
  <div class="summary-row"><span>Metode pembayaran</span><b><?= e($order['payment_name']) ?></b></div>
  <div class="summary-row total"><span>Total</span><b><?= rupiah($order['total']) ?></b></div>
  <div class="summary-row"><span>Status</span><span class="status <?= e($order['status']) ?>"><?= e(status_label($order['status'])) ?></span></div>
  <?php if ($order['status']==='pending' && (int)$order['user_id']===(int)current_user()['id']): ?>
    <div class="notice">Klik tombol di bawah untuk menyimulasikan pembayaran berhasil. Tidak ada transaksi uang sungguhan.</div>
    <form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= $id ?>"><button class="btn full" type="submit">Simulasikan Pembayaran Berhasil</button></form>
  <?php else: ?><p class="muted">Status pesanan saat ini: <?= e(status_label($order['status'])) ?>.</p><?php endif; ?>
  <a class="btn btn-outline full mt" href="<?= BASE_URL ?>/orders.php">Lihat Pesanan Saya</a>
</div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
