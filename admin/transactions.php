<?php
require_once __DIR__ . '/../config/app.php'; require_admin();
$pageTitle='Kelola Transaksi';$error='';
$allowed=['pending','paid','processing','completed','cancelled'];
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$id=(int)($_POST['id']??0);$status=$_POST['status']??'';if(!in_array($status,$allowed,true))$error='Status tidak valid.';else{$q=$pdo->prepare('UPDATE transactions SET status=?, paid_at=IF(?="paid" AND paid_at IS NULL,NOW(),paid_at) WHERE id=?');$q->execute([$status,$status,$id]);set_flash('success','Status transaksi diperbarui.');redirect('/admin/transactions.php');}}
$items=$pdo->query('SELECT t.*,u.name,td.game_name,td.product_name FROM transactions t JOIN users u ON u.id=t.user_id JOIN transaction_details td ON td.transaction_id=t.id ORDER BY t.created_at DESC')->fetchAll();
require __DIR__.'/../includes/header.php';
?>
<div class="page-head"><div><span class="eyebrow">ADMIN / PESANAN</span><h1>Kelola Transaksi</h1></div><a class="text-link" href="<?=BASE_URL?>/admin/index.php">← Dashboard</a></div>
<?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<div class="panel table-wrap"><table><thead><tr><th>Invoice / User</th><th>Produk</th><th>Player ID</th><th>Total</th><th>Status</th><th>Ubah Status</th></tr></thead><tbody><?php foreach($items as $t):?><tr><td><?=e($t['invoice'])?><br><small><?=e($t['name'])?></small></td><td><?=e($t['game_name'])?><br><small><?=e($t['product_name'])?></small></td><td><?=e($t['player_id'])?></td><td><?=rupiah($t['total'])?></td><td><span class="status <?=e($t['status'])?>"><?=e(status_label($t['status']))?></span></td><td><form method="post" class="inline-form"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=(int)$t['id']?>"><select name="status"><?php foreach($allowed as $s):?><option value="<?=$s?>" <?=$t['status']===$s?'selected':''?>><?=e(status_label($s))?></option><?php endforeach;?></select><button class="btn btn-small">Update</button></form></td></tr><?php endforeach;?><?php if(!$items):?><tr><td colspan="6" class="center muted">Belum ada transaksi.</td></tr><?php endif;?></tbody></table></div>
<?php require __DIR__.'/../includes/footer.php'; ?>
