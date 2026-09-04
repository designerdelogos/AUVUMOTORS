<?php
$action = $_POST['action'] ?? $_GET['action'] ?? '';
if ($action==='save') {
    $id = $_POST['id'] ?: uid();
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']) ?: slugify($name);
    $color = $_POST['color'] ?: '#111111';
    $active = !empty($_POST['active'])?1:0;
    $exists = q('SELECT id FROM platforms WHERE id=?',[$id])->fetch();
    if ($exists) q('UPDATE platforms SET name=?,slug=?,color=?,active=? WHERE id=?',[$name,$slug,$color,$active,$id]);
    else q('INSERT INTO platforms (id,name,slug,color,active) VALUES (?,?,?,?,?)',[$id,$name,$slug,$color,$active]);
    $_SESSION['flash']='Plataforma salva.';
    redirect('admin/dashboard.php?section=platforms');
}
if ($action==='delete' && !empty($_GET['id'])) {
    $used = q('SELECT COUNT(*) c FROM products WHERE platform_id=?',[$_GET['id']])->fetch()['c'];
    if ($used > 0) { $_SESSION['flash']='Não é possível excluir: há veículos usando essa origem.'; }
    else { q('DELETE FROM platforms WHERE id=?',[$_GET['id']]); $_SESSION['flash']='Plataforma excluída.'; }
    redirect('admin/dashboard.php?section=platforms');
}
$editing = null;
if (($_GET['edit']??'')==='new') $editing = ['id'=>'','name'=>'','slug'=>'','color'=>'#111111','active'=>1];
elseif (!empty($_GET['edit'])) $editing = q('SELECT * FROM platforms WHERE id=?',[$_GET['edit']])->fetch() ?: null;
$rows = q('SELECT p.*, (SELECT COUNT(*) FROM products x WHERE x.platform_id=p.id) as used FROM platforms p ORDER BY name ASC')->fetchAll();
?>
<div class="flex items-center justify-between mb-4">
  <h2 class="text-base font-bold">Origens (<?= count($rows) ?>)</h2>
  <a href="?section=platforms&edit=new" class="rounded-lg bg-neon px-4 py-2 text-sm font-bold text-neon-foreground">+ Nova origem</a>
</div>
<div class="overflow-hidden rounded-2xl border border-border bg-card">
  <table class="w-full text-sm">
    <thead class="bg-secondary/50 text-left text-xs uppercase tracking-widest text-muted-foreground">
      <tr><th class="p-3">Nome</th><th class="p-3">Slug</th><th class="p-3">Cor</th><th class="p-3">Veículos</th><th class="p-3">Status</th><th class="p-3 text-right">Ações</th></tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $c): ?>
      <tr class="border-t border-border">
        <td class="p-3 font-medium"><?= e($c['name']) ?></td>
        <td class="p-3 text-muted-foreground"><?= e($c['slug']) ?></td>
        <td class="p-3"><span class="inline-block size-6 rounded" style="background:<?= e($c['color']) ?>"></span></td>
        <td class="p-3"><?= (int)$c['used'] ?></td>
        <td class="p-3"><span class="rounded-full px-2 py-0.5 text-xs <?= $c['active']?'bg-neon/15 text-neon':'bg-secondary text-muted-foreground' ?>"><?= $c['active']?'Ativa':'Inativa' ?></span></td>
        <td class="p-3 text-right">
          <a href="?section=platforms&edit=<?= e($c['id']) ?>" class="rounded-md border border-border px-2 py-1 text-xs">Editar</a>
          <a href="?section=platforms&action=delete&id=<?= e($c['id']) ?>" onclick="return confirm('Excluir?')" class="rounded-md border border-destructive/60 px-2 py-1 text-xs text-destructive">Excluir</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php if ($editing): ?>
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
  <form method="post" class="w-full max-w-md rounded-2xl border border-border bg-card p-6">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= e($editing['id']) ?>">
    <h3 class="mb-4 text-lg font-bold"><?= $editing['id']?'Editar origem':'Nova origem' ?></h3>
    <div class="grid gap-3">
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Nome</div><input name="name" required value="<?= e($editing['name']) ?>" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Slug</div><input name="slug" value="<?= e($editing['slug']) ?>" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Cor</div><input type="color" name="color" value="<?= e($editing['color']) ?>" class="h-10 w-full rounded-lg border border-border bg-input px-2"></label>
      <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="active" <?= $editing['active']?'checked':'' ?>> Ativa</label>
    </div>
    <div class="mt-5 flex justify-end gap-2">
      <a href="?section=platforms" class="rounded-lg border border-border px-4 py-2 text-sm">Cancelar</a>
      <button class="rounded-lg bg-neon px-4 py-2 text-sm font-bold text-neon-foreground">Salvar</button>
    </div>
  </form>
</div>
<?php endif; ?>
