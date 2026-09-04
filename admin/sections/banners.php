<?php
$action = $_POST['action'] ?? $_GET['action'] ?? '';
if ($action==='save') {
    $id = $_POST['id'] ?: uid();
    $alt = $_POST['alt'] ?? '';
    $active = !empty($_POST['active'])?1:0;

    $exists = q('SELECT * FROM banners WHERE id=?',[$id])->fetch();
    $current_img = $exists['image_url'] ?? null;

    $uploaded = degra_upload_image('banners', $current_img);
    $image_url = $uploaded ?: $current_img;

    if (!$image_url) {
        $_SESSION['flash'] = 'Envie uma imagem para o banner.';
        redirect('admin/dashboard.php?section=banners');
    }

    if ($exists) q('UPDATE banners SET image_url=?,alt=?,active=? WHERE id=?',[$image_url,$alt,$active,$id]);
    else q('INSERT INTO banners (id,image_url,alt,active) VALUES (?,?,?,?)',[$id,$image_url,$alt,$active]);
    $_SESSION['flash']='Banner salvo.';
    redirect('admin/dashboard.php?section=banners');
}
if ($action==='delete' && !empty($_GET['id'])) {
    $row = q('SELECT image_url FROM banners WHERE id=?',[$_GET['id']])->fetch();
    if ($row && strpos($row['image_url'] ?? '', '/uploads/banners/') === 0) {
        @unlink(dirname(__DIR__, 2) . $row['image_url']);
    }
    q('DELETE FROM banners WHERE id=?',[$_GET['id']]);
    $_SESSION['flash']='Banner excluído.';
    redirect('admin/dashboard.php?section=banners');
}
$editing = null;
if (($_GET['edit']??'')==='new') $editing = ['id'=>'','image_url'=>'','alt'=>'','active'=>1];
elseif (!empty($_GET['edit'])) $editing = q('SELECT * FROM banners WHERE id=?',[$_GET['edit']])->fetch() ?: null;
$rows = q('SELECT * FROM banners ORDER BY id ASC')->fetchAll();
?>
<div class="flex items-center justify-between mb-4">
  <h2 class="text-base font-bold">Banners (<?= count($rows) ?>)</h2>
  <a href="?section=banners&edit=new" class="rounded-lg bg-neon px-4 py-2 text-sm font-bold text-neon-foreground">+ Novo banner</a>
</div>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
<?php foreach ($rows as $b): ?>
  <div class="overflow-hidden rounded-2xl border border-border bg-card">
    <div class="w-full bg-secondary" style="aspect-ratio:16/6;overflow:hidden;"><img src="<?= e($b['image_url']) ?>" style="width:100%;height:100%;object-fit:cover;display:block;"></div>
    <div class="p-3">
      <div class="text-xs text-muted-foreground line-clamp-1"><?= e($b['alt'] ?? '') ?></div>
      <div class="mt-2 flex items-center justify-between text-xs">
        <span class="rounded-full px-2 py-0.5 <?= $b['active']?'bg-neon/15 text-neon':'bg-secondary text-muted-foreground' ?>"><?= $b['active']?'Ativo':'Inativo' ?></span>
        <div class="flex gap-1">
          <a href="?section=banners&edit=<?= e($b['id']) ?>" class="rounded-md border border-border px-2 py-1">Editar</a>
          <a href="?section=banners&action=delete&id=<?= e($b['id']) ?>" onclick="return confirm('Excluir?')" class="rounded-md border border-destructive/60 px-2 py-1 text-destructive">Excluir</a>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>
<?php if (!$rows): ?><p class="col-span-full text-muted-foreground">Sem banners.</p><?php endif; ?>
</div>
<?php if ($editing): ?>
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
  <form method="post" enctype="multipart/form-data" class="w-full max-w-lg rounded-2xl border border-border bg-card p-6">
    <input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= e($editing['id']) ?>">
    <h3 class="mb-4 text-lg font-bold"><?= $editing['id']?'Editar banner':'Novo banner' ?></h3>
    <div class="grid gap-4">
      <div>
        <div class="mb-2 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Imagem do banner</div>
        <div class="flex items-center gap-3">
          <div style="width:160px;height:60px;border:1px solid #262626;border-radius:8px;overflow:hidden;background:#0a0a0a;flex-shrink:0;display:grid;place-items:center;">
            <?php if (!empty($editing['image_url'])): ?>
              <img id="bnrPrev" src="<?= e($editing['image_url']) ?>" style="width:100%;height:100%;object-fit:cover;display:block;">
              <span id="bnrPh" style="display:none;color:#666;font-size:11px;">Sem imagem</span>
            <?php else: ?>
              <img id="bnrPrev" src="" style="width:100%;height:100%;object-fit:cover;display:none;">
              <span id="bnrPh" style="color:#666;font-size:11px;">Sem imagem</span>
            <?php endif; ?>
          </div>
          <label class="cursor-pointer rounded-lg border border-border bg-secondary px-3 py-2 text-xs font-semibold hover:border-neon">
            Enviar imagem
            <input type="file" name="image_file" accept="image/*" class="hidden" onchange="(function(inp){var f=inp.files&&inp.files[0];if(!f)return;var r=new FileReader();r.onload=function(e){var img=document.getElementById('bnrPrev');img.src=e.target.result;img.style.display='block';document.getElementById('bnrPh').style.display='none';};r.readAsDataURL(f);})(this)">
          </label>
        </div>
        <p class="mt-2 text-[11px] text-muted-foreground">Recomendado: 1920×720 (proporção 16:6), JPG/PNG/WebP.</p>
      </div>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Texto alternativo</div><input name="alt" value="<?= e($editing['alt']) ?>" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="active" <?= $editing['active']?'checked':'' ?>> Ativo</label>
    </div>
    <div class="mt-5 flex justify-end gap-2">
      <a href="?section=banners" class="rounded-lg border border-border px-4 py-2 text-sm">Cancelar</a>
      <button class="rounded-lg bg-neon px-4 py-2 text-sm font-bold text-neon-foreground">Salvar</button>
    </div>
  </form>
</div>
<?php endif; ?>
