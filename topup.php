<?php
require_once __DIR__ . '/config/app.php';
require_login();
$gameId = (int)($_GET['game'] ?? $_POST['game_id'] ?? 0);
$gameStmt = $pdo->prepare('SELECT * FROM games WHERE id = ? AND is_active = 1');
$gameStmt->execute([$gameId]);
$game = $gameStmt->fetch();
if (!$game) { set_flash('error','Game tidak ditemukan.'); redirect('/index.php'); }
$productsStmt = $pdo->prepare('SELECT * FROM products WHERE game_id = ? AND is_active = 1 ORDER BY price');
$productsStmt->execute([$gameId]);
$products = $productsStmt->fetchAll();
$payments = $pdo->query('SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY name')->fetchAll();
$pageTitle = 'Top Up ' . $game['name'];
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $productId = (int)($_POST['product_id'] ?? 0);
    $paymentId = (int)($_POST['payment_method_id'] ?? 0);
    $playerId = trim($_POST['player_id'] ?? '');
    $serverId = trim($_POST['server_id'] ?? '');
    $p = $pdo->prepare('SELECT * FROM products WHERE id = ? AND game_id = ? AND is_active = 1');
    $p->execute([$productId,$gameId]);
    $product = $p->fetch();
    $pm = $pdo->prepare('SELECT id FROM payment_methods WHERE id = ? AND is_active = 1');
    $pm->execute([$paymentId]);
    if (!$product) $error = 'Pilih nominal top up yang tersedia.';
    elseif (!$pm->fetch()) $error = 'Pilih metode pembayaran yang tersedia.';
    elseif ($playerId === '' || strlen($playerId) > 80) $error = 'Player ID wajib diisi (maksimal 80 karakter).';
    elseif (strlen($serverId) > 40) $error = 'Server ID maksimal 40 karakter.';
    else {
        $pdo->beginTransaction();
        try {
            $invoice = make_invoice();
            $total = (int)$product['price'];
            $ins = $pdo->prepare("INSERT INTO transactions (invoice,user_id,payment_method_id,total,status,player_id,server_id) VALUES (?,?,?,?, 'pending',?,?)");
            $ins->execute([$invoice,current_user()['id'],$paymentId,$total,$playerId,$serverId ?: null]);
            $transactionId = (int)$pdo->lastInsertId();
            $detail = $pdo->prepare('INSERT INTO transaction_details (transaction_id,product_id,game_id,product_name,game_name,quantity,unit_price,subtotal) VALUES (?,?,?,?,?,1,?,?)');
            $detail->execute([$transactionId,$productId,$gameId,$product['name'],$game['name'],$total,$total]);
            $pdo->commit();
            redirect('/payment.php?id=' . $transactionId);
        } catch (Throwable $e) {
            $pdo->rollBack();
            $error = 'Pesanan gagal dibuat. Silakan coba kembali.';
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<div class="page-head"><div><span class="eyebrow">FORM PEMESANAN</span><h1>Top Up <?= e($game['name']) ?></h1><p class="muted">Masukkan data akun dan pilih nominal top up.</p></div><a href="<?= BASE_URL ?>/index.php" class="text-link">← Kembali</a></div>
<div class="two-col"><section class="panel">
  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <?php if (!$products): ?><div class="empty">Produk untuk game ini belum tersedia.</div><?php else: ?>
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="game_id" value="<?= $gameId ?>">
    <label>Player ID<input name="player_id" required maxlength="80" placeholder="Masukkan Player ID" value="<?= e($_POST['player_id'] ?? '') ?>"></label>
    <label>Server ID <span class="muted">(opsional)</span><input name="server_id" maxlength="40" placeholder="Masukkan Server ID jika ada" value="<?= e($_POST['server_id'] ?? '') ?>"></label>
    <label>Pilih nominal
      <select name="product_id" required><option value="">-- Pilih produk --</option>
      <?php foreach ($products as $product): ?><option value="<?= (int)$product['id'] ?>" <?= (int)($_POST['product_id'] ?? 0)===(int)$product['id']?'selected':'' ?>><?= e($product['name']) ?> — <?= rupiah($product['price']) ?></option><?php endforeach; ?>
      </select>
    </label>
    <label>Metode pembayaran<select name="payment_method_id" required><option value="">-- Pilih pembayaran --</option>
      <?php foreach ($payments as $payment): ?><option value="<?= (int)$payment['id'] ?>"><?= e($payment['name']) ?></option><?php endforeach; ?>
    </select></label>
    <div class="notice">Pembayaran pada aplikasi ini hanya simulasi. Tidak ada uang sungguhan yang diproses.</div>
    <button class="btn full" type="submit">Buat Pesanan</button>
  </form><?php endif; ?>
</section><aside class="panel side-note"><div class="big-icon"><?= e($game['icon'] ?: '🎮') ?></div><h3><?= e($game['name']) ?></h3><p><?= e($game['description']) ?></p><p class="muted small">Pastikan Player ID dan Server ID benar sebelum membuat pesanan.</p></aside></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
