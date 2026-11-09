<?php
require_once __DIR__ . '/../config/app.php'; require_admin();
$pageTitle='Kelola Game'; $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf(); $action=$_POST['action']??'';
 if ($action==='delete') {
   $id=(int)$_POST['id'];
   $q=$pdo->prepare('SELECT COUNT(*) FROM transaction_details WHERE game_id=?'); $q->execute([$id]);
   if ((int)$q->fetchColumn()>0) $error='Game sudah memiliki riwayat transaksi. Nonaktifkan, jangan hapus.';
   else { $q=$pdo->prepare('DELETE FROM games WHERE id=?'); $q->execute([$id]); set_flash('success','Game dihapus.'); redirect('/admin/games.php'); }
 } else {
   $id=(int)($_POST['id']??0); $name=trim($_POST['name']??''); $desc=trim($_POST['description']??''); $icon=trim($_POST['icon']??'🎮'); $color=trim($_POST['theme_color']??'#5936bd'); $active=isset($_POST['is_active'])?1:0;
   if ($name==='') $error='Nama game wajib diisi.';
   elseif (!preg_match('/^#[0-9a-fA-F]{6}$/',$color)) $error='Format warna harus hex, contoh #5936bd.';
   else if ($id) { $q=$pdo->prepare('UPDATE games SET name=?,description=?,icon=?,theme_color=?,is_active=? WHERE id=?'); $q->execute([$name,$desc,$icon,$color,$active,$id]); set_flash('success','Game diperbarui.'); redirect('/admin/games.php'); }
   else { $q=$pdo->prepare('INSERT INTO games(name,description,icon,theme_color,is_active) VALUES(?,?,?,?,?)'); $q->execute([$name,$desc,$icon,$color,$active]); set_flash('success','Game ditambahkan.'); redirect('/admin/games.php'); }
 }
}
$edit=null; if(isset($_GET['edit'])){$q=$pdo->prepare('SELECT * FROM games WHERE id=?');$q->execute([(int)$_GET['edit']]);$edit=$q->fetch();}
$games=$pdo->query('SELECT * FROM games ORDER BY id DESC')->fetchAll();
require __DIR__.'/../includes/header.php';
?>
<div class="page-head"><div><span class="eyebrow">ADMIN / KATALOG</span><h1>Kelola Game</h1></div><a class="text-link" href="<?= BASE_URL ?>/admin/index.php">← Dashboard</a></div>
<?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<div class="two-col admin-form-layout"><form class="panel" method="post"><h2><?= $edit?'Edit Game':'Tambah Game' ?></h2><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?= (int)($edit['id']??0) ?>">
<label>Nama game<input name="name" required value="<?=e($edit['name']??'')?>"></label><label>Deskripsi<textarea name="description" rows="3"><?=e($edit['description']??'')?></textarea></label><label>Ikon / Emoji<input name="icon" value="<?=e($edit['icon']??'🎮')?>"></label><label>Warna tema<input name="theme_color" value="<?=e($edit['theme_color']??'#5936bd')?>"></label><label class="check"><input type="checkbox" name="is_active" <?=(!isset($edit['is_active'])||(int)$edit['is_active']===1)?'checked':''?>> Aktif</label><button class="btn" name="action" value="save">Simpan</button><?php if($edit):?> <a href="<?=BASE_URL?>/admin/games.php">Batal</a><?php endif;?></form>
<div class="panel table-wrap"><h2>Daftar Game</h2><table><thead><tr><th>Game</th><th>Status</th><th>Aksi</th></tr></thead><tbody><?php foreach($games as $g):?><tr><td><?=e($g['icon'])?> <?=e($g['name'])?></td><td><?=$g['is_active']?'Aktif':'Nonaktif'?></td><td><a href="?edit=<?=(int)$g['id']?>">Edit</a><form class="inline-form" method="post" onsubmit="return confirm('Hapus game ini?')"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=(int)$g['id']?>"><button class="danger-link" name="action" value="delete">Hapus</button></form></td></tr><?php endforeach;?></tbody></table></div></div>
<?php require __DIR__.'/../includes/footer.php'; ?>
