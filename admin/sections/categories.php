<?php
$action = $_POST['action'] ?? $_GET['action'] ?? '';
if ($action === 'save') {
    $id = $_POST['id'] ?: uid();
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']) ?: slugify($name);
    $order = (int)($_POST['order'] ?? 0);
    $icon = $_POST['icon'] ?? '';
    $desc = $_POST['description'] ?? '';
    $active = !empty($_POST['active']) ? 1 : 0;
    $exists = q('SELECT id FROM categories WHERE id=?',[$id])->fetch();
    if ($exists) q('UPDATE categories SET name=?,slug=?,`order`=?,icon=?,description=?,active=? WHERE id=?',[$name,$slug,$order,$icon,$desc,$active,$id]);
    else q('INSERT INTO categories (id,name,slug,`order`,icon,description,active) VALUES (?,?,?,?,?,?,?)',[$id,$name,$slug,$order,$icon,$desc,$active]);
    $_SESSION['flash']='Categoria salva.';
    redirect('admin/dashboard.php?section=categories');
}
if ($action === 'delete' && !empty($_GET['id'])) {
    q('UPDATE products SET category_id=NULL WHERE category_id=?',[$_GET['id']]);
    q('DELETE FROM categories WHERE id=?',[$_GET['id']]);
    $_SESSION['flash']='Categoria excluída.';
    redirect('admin/dashboard.php?section=categories');
}
$editing = null;
if (($_GET['edit'] ?? '')==='new') $editing = ['id'=>'','name'=>'','slug'=>'','order'=>0,'icon'=>'','description'=>'','active'=>1];
elseif (!empty($_GET['edit'])) $editing = q('SELECT * FROM categories WHERE id=?',[$_GET['edit']])->fetch() ?: null;
$rows = q('SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id=c.id) as products_count FROM categories c ORDER BY `order` ASC, name ASC')->fetchAll();
?>
<div class="flex items-center justify-between mb-4">
  <h2 class="text-base font-bold">Categorias (<?= count($rows) ?>)</h2>
  <a href="?section=categories&edit=new" class="rounded-lg bg-neon px-4 py-2 text-sm font-bold text-neon-foreground">+ Nova categoria</a>
</div>
<div class="overflow-hidden rounded-2xl border border-border bg-card">
  <table class="w-full text-sm">
    <thead class="bg-secondary/50 text-left text-xs uppercase tracking-widest text-muted-foreground">
      <tr><th class="p-3">Nome</th><th class="p-3">Slug</th><th class="p-3">Ordem</th><th class="p-3">Veículos</th><th class="p-3">Status</th><th class="p-3 text-right">Ações</th></tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $c): ?>
      <tr class="border-t border-border">
        <td class="p-3 font-medium"><?= e($c['name']) ?></td>
        <td class="p-3 text-muted-foreground"><?= e($c['slug']) ?></td>
        <td class="p-3"><?= (int)$c['order'] ?></td>
        <td class="p-3"><?= (int)$c['products_count'] ?></td>
        <td class="p-3"><span class="rounded-full px-2 py-0.5 text-xs <?= $c['active']?'bg-neon/15 text-neon':'bg-secondary text-muted-foreground' ?>"><?= $c['active']?'Ativa':'Inativa' ?></span></td>
        <td class="p-3 text-right">
          <a href="?section=categories&edit=<?= e($c['id']) ?>" class="rounded-md border border-border px-2 py-1 text-xs">Editar</a>
          <a href="?section=categories&action=delete&id=<?= e($c['id']) ?>" onclick="return confirm('Excluir? Veículos ficarão sem categoria.')" class="rounded-md border border-destructive/60 px-2 py-1 text-xs text-destructive">Excluir</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php if ($editing): ?>
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
  <form method="post" class="w-full max-w-lg rounded-2xl border border-border bg-card p-6">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= e($editing['id']) ?>">
    <h3 class="mb-4 text-lg font-bold"><?= $editing['id']?'Editar categoria':'Nova categoria' ?></h3>
    <div class="grid gap-3">
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Nome</div><input name="name" required value="<?= e($editing['name']) ?>" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Slug</div><input name="slug" value="<?= e($editing['slug']) ?>" placeholder="auto" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Ordem</div><input type="number" name="order" value="<?= (int)$editing['order'] ?>" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Ícone</div><input name="icon" value="<?= e($editing['icon']) ?>" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Descrição</div><textarea name="description" rows="3" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"><?= e($editing['description'] ?? '') ?></textarea></label>
      <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="active" <?= $editing['active']?'checked':'' ?>> Ativa</label>
    </div>
    <div class="mt-5 flex justify-end gap-2">
      <a href="?section=categories" class="rounded-lg border border-border px-4 py-2 text-sm">Cancelar</a>
      <button class="rounded-lg bg-neon px-4 py-2 text-sm font-bold text-neon-foreground">Salvar</button>
    </div>
  </form>
</div>
<?php endif; ?>
