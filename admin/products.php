<?php
require_once __DIR__ . '/../config/app.php'; require_admin();
$pageTitle='Kelola Produk'; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$action=$_POST['action']??'';
 if($action==='delete'){$id=(int)$_POST['id'];$q=$pdo->prepare('SELECT COUNT(*) FROM transaction_details WHERE product_id=?');$q->execute([$id]);if((int)$q->fetchColumn())$error='Produk memiliki riwayat transaksi. Nonaktifkan produk agar riwayat tetap aman.';else{$q=$pdo->prepare('DELETE FROM products WHERE id=?');$q->execute([$id]);set_flash('success','Produk dihapus.');redirect('/admin/products.php');}}
 else{$id=(int)($_POST['id']??0);$game=(int)($_POST['game_id']??0);$name=trim($_POST['name']??'');$price=(int)($_POST['price']??0);$active=isset($_POST['is_active'])?1:0;if(!$game||$name===''||$price<1)$error='Game, nama produk, dan harga wajib valid.';else{if($id){$q=$pdo->prepare('UPDATE products SET game_id=?,name=?,price=?,is_active=? WHERE id=?');$q->execute([$game,$name,$price,$active,$id]);}else{$q=$pdo->prepare('INSERT INTO products(game_id,name,price,is_active) VALUES(?,?,?,?)');$q->execute([$game,$name,$price,$active]);}set_flash('success','Produk disimpan.');redirect('/admin/products.php');}}
}
$edit=null;if(isset($_GET['edit'])){$q=$pdo->prepare('SELECT * FROM products WHERE id=?');$q->execute([(int)$_GET['edit']]);$edit=$q->fetch();}
$games=$pdo->query('SELECT id,name FROM games ORDER BY name')->fetchAll();
$items=$pdo->query('SELECT p.*,g.name game_name FROM products p JOIN games g ON g.id=p.game_id ORDER BY p.id DESC')->fetchAll();
require __DIR__.'/../includes/header.php';
?>
<div class="page-head"><div><span class="eyebrow">ADMIN / KATALOG</span><h1>Kelola Produk Top Up</h1></div><a class="text-link" href="<?=BASE_URL?>/admin/index.php">← Dashboard</a></div>
<?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<div class="two-col admin-form-layout"><form class="panel" method="post"><h2><?= $edit?'Edit Produk':'Tambah Produk'?></h2><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=(int)($edit['id']??0)?>">
<label>Game<select name="game_id" required><option value="">Pilih game</option><?php foreach($games as $g):?><option value="<?=(int)$g['id']?>" <?=((int)($edit['game_id']??0)===(int)$g['id'])?'selected':''?>><?=e($g['name'])?></option><?php endforeach;?></select></label>
<label>Nama nominal<input name="name" required placeholder="Contoh: 86 Diamonds" value="<?=e($edit['name']??'')?>"></label><label>Harga (Rupiah)<input type="number" min="1" name="price" required value="<?=e((string)($edit['price']??''))?>"></label><label class="check"><input type="checkbox" name="is_active" <?=(!isset($edit['is_active'])||(int)$edit['is_active']===1)?'checked':''?>> Aktif</label><button class="btn" name="action" value="save">Simpan Produk</button><?php if($edit):?> <a href="<?=BASE_URL?>/admin/products.php">Batal</a><?php endif;?></form>
<div class="panel table-wrap"><h2>Daftar Produk</h2><table><thead><tr><th>Game / Produk</th><th>Harga</th><th>Status</th><th>Aksi</th></tr></thead><tbody><?php foreach($items as $p):?><tr><td><?=e($p['game_name'])?><br><small><?=e($p['name'])?></small></td><td><?=rupiah($p['price'])?></td><td><?=$p['is_active']?'Aktif':'Nonaktif'?></td><td><a href="?edit=<?=(int)$p['id']?>">Edit</a><form class="inline-form" method="post" onsubmit="return confirm('Hapus produk ini?')"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=(int)$p['id']?>"><button class="danger-link" name="action" value="delete">Hapus</button></form></td></tr><?php endforeach;?></tbody></table></div></div>
<?php require __DIR__.'/../includes/footer.php'; ?>
