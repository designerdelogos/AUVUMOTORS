<?php
// admin/sections/products.php
// Observação: a função degra_upload_image() e o csrf_check() vêm de includes/uploads.php
// (já carregado pelo bootstrap). Não redeclare aqui.

// Actions
$action = $_POST['action'] ?? $_GET['action'] ?? '';
if ($action === 'save') {
    $id = $_POST['id'] ?: uid();
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']) ?: slugify($name);

    $existingImages = [];
    if (!empty($_POST['id'])) {
        try {
            $existingImages = q('SELECT image_url FROM product_images WHERE product_id=? ORDER BY sort_order ASC, id ASC', [$id])->fetchAll();
            $existingImages = array_values(array_map(fn($r) => $r['image_url'], $existingImages));
        } catch (Throwable $e) {
            $existingImages = [];
        }
    }
    if (!$existingImages && !empty($_POST['image_url'])) $existingImages[] = $_POST['image_url'];
    $removeImages = $_POST['remove_images'] ?? [];
    $keptImages = array_values(array_filter($existingImages, fn($img) => !in_array($img, $removeImages, true)));
    $newImages = degra_upload_images('gallery_images', 'products', max(0, 5 - count($keptImages)));
    $galleryImages = array_slice(array_merge($keptImages, $newImages), 0, 5);
    $image_url = $galleryImages[0] ?? ($_POST['image_url'] ?? '');

    $data = [
        $name, $slug, $_POST['short_description'] ?? '', $_POST['description'] ?? '',
        (float)($_POST['price'] ?? 0), ($_POST['previous_price'] ?? '') === '' ? null : (float)$_POST['previous_price'],
        $image_url,
        $_POST['make'] ?? '', $_POST['model'] ?? '', $_POST['year_model'] ?? '',
        ($_POST['mileage'] ?? '') === '' ? null : (int)$_POST['mileage'],
        $_POST['transmission'] ?? '', $_POST['fuel'] ?? '', $_POST['color'] ?? '',
        $_POST['category_id'] ?? null, $_POST['platform_id'] ?? null,
        $_POST['affiliate_url'] ?? '', $_POST['badge'] ?: null,
        !empty($_POST['featured']) ? 1 : 0, !empty($_POST['offer']) ? 1 : 0, !empty($_POST['active']) ? 1 : 0,
    ];
    $exists = q('SELECT id FROM products WHERE id=?', [$id])->fetch();
    if ($exists) {
        q('UPDATE products SET name=?,slug=?,short_description=?,description=?,price=?,previous_price=?,image_url=?,make=?,model=?,year_model=?,mileage=?,transmission=?,fuel=?,color=?,category_id=?,platform_id=?,affiliate_url=?,badge=?,featured=?,offer=?,active=? WHERE id=?', array_merge($data,[$id]));
        if (empty($_SESSION['flash'])) $_SESSION['flash'] = 'Veículo atualizado.';
    } else {
        q('INSERT INTO products (id,name,slug,short_description,description,price,previous_price,image_url,make,model,year_model,mileage,transmission,fuel,color,category_id,platform_id,affiliate_url,badge,featured,offer,active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', array_merge([$id],$data));
        if (empty($_SESSION['flash'])) $_SESSION['flash'] = 'Veículo criado.';
    }
    try {
        q('DELETE FROM product_images WHERE product_id=?', [$id]);
        foreach ($galleryImages as $order => $img) {
            q('INSERT INTO product_images (product_id,image_url,sort_order) VALUES (?,?,?)', [$id, $img, $order]);
        }
    } catch (Throwable $e) {
        $_SESSION['flash'] = 'Veículo salvo, mas a galeria não foi atualizada. Execute o instalador para criar product_images.';
    }
    redirect('admin/dashboard.php?section=products');
}
if ($action === 'delete' && !empty($_GET['id'])) {
    $row = q('SELECT image_url FROM products WHERE id=?', [$_GET['id']])->fetch();
    q('DELETE FROM products WHERE id=?', [$_GET['id']]);
    if ($row && strpos($row['image_url'] ?? '', '/uploads/products/') === 0) {
        $old = dirname(__DIR__, 2) . $row['image_url'];
        if (is_file($old)) @unlink($old);
    }
    $_SESSION['flash'] = 'Veículo excluído.';
    redirect('admin/dashboard.php?section=products');
}
if ($action === 'reset_clicks' && !empty($_GET['id'])) {
    q('UPDATE products SET clicks=0 WHERE id=?', [$_GET['id']]);
    $_SESSION['flash'] = 'Interesses deste veículo zerados.';
    redirect('admin/dashboard.php?section=products');
}
if ($action === 'reset_clicks_all') {
    q('UPDATE products SET clicks=0');
    $_SESSION['flash'] = 'Todos os interesses foram zerados.';
    redirect('admin/dashboard.php?section=products');
}
if ($action === 'duplicate' && !empty($_GET['id'])) {
    $src = q('SELECT * FROM products WHERE id=?', [$_GET['id']])->fetch();
    if ($src) {
        $newId = uid();
        q('INSERT INTO products (id,name,slug,short_description,description,price,previous_price,image_url,make,model,year_model,mileage,transmission,fuel,color,category_id,platform_id,affiliate_url,badge,featured,offer,active,clicks) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0)', [
            $newId, $src['name'].' (Cópia)', $src['slug'].'-copia-'.substr($newId,0,4),
            $src['short_description'],$src['description'],$src['price'],$src['previous_price'],$src['image_url'],
            $src['make'] ?? '',$src['model'] ?? '',$src['year_model'] ?? '',$src['mileage'] ?? null,$src['transmission'] ?? '',$src['fuel'] ?? '',$src['color'] ?? '',
            $src['category_id'],$src['platform_id'],$src['affiliate_url'],$src['badge'],
            $src['featured'],$src['offer'],0
        ]);
        foreach (product_images($src) as $order => $img) {
            q('INSERT INTO product_images (product_id,image_url,sort_order) VALUES (?,?,?)', [$newId, $img, $order]);
        }
        $_SESSION['flash'] = 'Veículo duplicado.';
    }
    redirect('admin/dashboard.php?section=products');
}

// Edit mode
$editing = null;
if (($_GET['edit'] ?? '') === 'new') $editing = ['id'=>'','name'=>'','slug'=>'','short_description'=>'','description'=>'','price'=>0,'previous_price'=>'','image_url'=>'','make'=>'','model'=>'','year_model'=>'','mileage'=>'','transmission'=>'','fuel'=>'','color'=>'','category_id'=>'','platform_id'=>'','affiliate_url'=>'','badge'=>'','featured'=>0,'offer'=>0,'active'=>1];
elseif (!empty($_GET['edit'])) $editing = q('SELECT * FROM products WHERE id=?',[$_GET['edit']])->fetch() ?: null;

$q_search = trim($_GET['q'] ?? '');
$rows = $q_search
  ? q('SELECT * FROM products WHERE name LIKE ? ORDER BY updated_at DESC',['%'.$q_search.'%'])->fetchAll()
  : q('SELECT * FROM products ORDER BY updated_at DESC')->fetchAll();
$cats = all_categories(false);
$plats = all_platforms(false);
?>
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
  <form method="get" class="flex gap-2">
    <input type="hidden" name="section" value="products">
    <input name="q" value="<?= e($q_search) ?>" placeholder="Buscar veículo…" class="rounded-lg border border-border bg-input px-3 py-2 text-sm">
    <button class="rounded-lg border border-border bg-secondary px-3 py-2 text-sm">Filtrar</button>
  </form>
  <div class="flex gap-2">
    <a href="?section=products&action=reset_clicks_all" onclick="return confirm('Zerar TODOS os interesses?')" class="rounded-lg border border-border px-3 py-2 text-sm hover:border-destructive hover:text-destructive">Zerar interesses</a>
    <a href="?section=products&edit=new" class="rounded-lg bg-neon px-4 py-2 text-sm font-bold text-neon-foreground">+ Novo veículo</a>
  </div>
</div>

<div class="overflow-hidden rounded-2xl border border-border bg-card">
  <table class="w-full text-sm">
    <thead class="bg-secondary/50 text-left text-xs uppercase tracking-widest text-muted-foreground">
      <tr><th class="p-3">Veículo</th><th class="p-3">Categoria</th><th class="p-3">Origem</th><th class="p-3">Valor</th><th class="p-3">Interesses</th><th class="p-3">Status</th><th class="p-3 text-right">Ações</th></tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $p): $cat = $p['category_id']?find_category($p['category_id']):null; $plat = $p['platform_id']?find_platform($p['platform_id']):null; ?>
      <tr class="border-t border-border">
        <td class="p-3"><div class="flex items-center gap-3"><img src="<?= e(product_primary_image($p)) ?>" class="size-10 rounded object-cover"><div class="max-w-xs truncate font-medium"><?= e($p['name']) ?></div></div></td>
        <td class="p-3 text-muted-foreground"><?= e($cat['name'] ?? '—') ?></td>
        <td class="p-3 text-muted-foreground"><?= e($plat['name'] ?? '—') ?></td>
        <td class="p-3 font-semibold text-neon"><?= money((float)$p['price']) ?></td>
        <td class="p-3">
          <span><?= (int)$p['clicks'] ?></span>
          <?php if ((int)$p['clicks'] > 0): ?>
            <a href="?section=products&action=reset_clicks&id=<?= e($p['id']) ?>" onclick="return confirm('Zerar interesses deste veículo?')" class="ml-1 text-[10px] text-muted-foreground hover:text-destructive" title="Zerar interesses">↺</a>
          <?php endif; ?>
        </td>
        <td class="p-3"><span class="rounded-full px-2 py-0.5 text-xs <?= $p['active']?'bg-neon/15 text-neon':'bg-secondary text-muted-foreground' ?>"><?= $p['active']?'Ativo':'Inativo' ?></span></td>
        <td class="p-3 text-right">
          <a href="?section=products&edit=<?= e($p['id']) ?>" class="rounded-md border border-border px-2 py-1 text-xs hover:border-neon">Editar</a>
          <a href="?section=products&action=duplicate&id=<?= e($p['id']) ?>" class="rounded-md border border-border px-2 py-1 text-xs">Duplicar</a>
          <a href="?section=products&action=delete&id=<?= e($p['id']) ?>" onclick="return confirm('Excluir este veículo?')" class="rounded-md border border-destructive/60 px-2 py-1 text-xs text-destructive">Excluir</a>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="7" class="p-8 text-center text-muted-foreground">Nenhum veículo.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php if ($editing): ?>
<style>
  .degra-modal-form select option,
  .degra-modal-form select optgroup { background-color:#fff; color:#111; }
  .degra-modal-form select { color-scheme: light; }
</style>
<div class="fixed inset-0 z-50 flex items-start justify-center bg-black/70 p-4" style="overflow:hidden;">
  <form method="post" enctype="multipart/form-data" class="degra-modal-form w-full max-w-3xl rounded-2xl border border-border bg-card my-6 flex flex-col" style="max-height:calc(100vh - 3rem);">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= e($editing['id']) ?>">
    <div class="flex items-center justify-between px-6 pt-6 pb-3 border-b border-border">
    <h3 class="text-lg font-bold"><?= $editing['id'] ? 'Editar veículo' : 'Novo veículo' ?></h3>
      <a href="?section=products" class="text-muted-foreground hover:text-foreground">✕</a>
    </div>
    <div class="px-6 py-4 overflow-y-auto" style="flex:1 1 auto; min-height:0;">
    <div class="grid gap-4 md:grid-cols-2">
      <label class="md:col-span-2"><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Título do anúncio</div><input name="name" required value="<?= e($editing['name']) ?>" placeholder="Honda Civic Touring 1.5 Turbo 2021" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Slug</div><input name="slug" value="<?= e($editing['slug']) ?>" placeholder="gerado automaticamente" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Marca</div><input name="make" value="<?= e($editing['make'] ?? '') ?>" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Modelo / versão</div><input name="model" value="<?= e($editing['model'] ?? '') ?>" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Ano</div><input name="year_model" value="<?= e($editing['year_model'] ?? '') ?>" placeholder="2021/2022" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Quilometragem</div><input type="number" name="mileage" value="<?= e($editing['mileage'] ?? '') ?>" placeholder="45000" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Câmbio</div><input name="transmission" value="<?= e($editing['transmission'] ?? '') ?>" placeholder="Automático" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Combustível</div><input name="fuel" value="<?= e($editing['fuel'] ?? '') ?>" placeholder="Flex" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Cor</div><input name="color" value="<?= e($editing['color'] ?? '') ?>" placeholder="Preto" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>

      <div class="md:col-span-2 rounded-xl border border-dashed border-border bg-secondary/30 p-4">
        <div class="mb-2 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Fotos do veículo (até 5)</div>
        <div class="flex flex-wrap items-center gap-4">
          <div class="grid flex-1 gap-3 sm:grid-cols-5">
            <?php $gallery = $editing['id'] ? product_images($editing) : []; ?>
            <?php foreach ($gallery as $img): ?>
              <label class="block overflow-hidden rounded-lg border border-border bg-secondary text-xs">
                <img src="<?= e($img) ?>" style="width:100%;aspect-ratio:1;object-fit:cover;">
                <span class="flex items-center gap-1 p-2"><input type="checkbox" name="remove_images[]" value="<?= e($img) ?>"> Remover</span>
              </label>
            <?php endforeach; ?>
            <?php for ($i=count($gallery); $i<5; $i++): ?>
              <div class="grid place-items-center rounded-lg border border-dashed border-border bg-secondary/60 text-xs text-muted-foreground" style="aspect-ratio:1;">Foto <?= $i + 1 ?></div>
            <?php endfor; ?>
          </div>
          <div class="basis-full space-y-2">
            <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-border bg-input px-3 py-2 text-sm hover:border-neon">
              <span>Enviar fotos do computador</span>
              <input type="file" name="gallery_images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple class="hidden">
            </label>
            <p class="text-[11px] text-muted-foreground">JPG, PNG, WEBP ou GIF. Até 6MB por foto. A primeira foto aparece como capa.</p>
            <div>
              <div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Imagem de capa por URL (opcional)</div>
              <input name="image_url" value="<?= e($editing['image_url']) ?>" placeholder="https://…" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm">
            </div>
          </div>
        </div>
      </div>

      <label class="md:col-span-2"><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Descrição curta</div><input name="short_description" value="<?= e($editing['short_description']) ?>" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label class="md:col-span-2"><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Descrição completa</div><textarea name="description" rows="4" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"><?= e($editing['description']) ?></textarea></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Valor</div><input type="number" step="0.01" name="price" required value="<?= e($editing['price']) ?>" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Valor anterior</div><input type="number" step="0.01" name="previous_price" value="<?= e($editing['previous_price']) ?>" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Categoria</div>
        <select name="category_id" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm">
          <?php foreach ($cats as $c): ?><option value="<?= e($c['id']) ?>" <?= $c['id']===$editing['category_id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
        </select>
      </label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Origem</div>
        <select name="platform_id" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm">
          <?php foreach ($plats as $c): ?><option value="<?= e($c['id']) ?>" <?= $c['id']===$editing['platform_id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
        </select>
      </label>
      <label class="md:col-span-2"><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Link de contato</div><input name="affiliate_url" required value="<?= e($editing['affiliate_url']) ?>" placeholder="https://wa.me/55..." class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <label><div class="mb-1 text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Selo</div><input name="badge" value="<?= e($editing['badge']) ?>" placeholder="Único dono, IPVA pago…" class="w-full rounded-lg border border-border bg-input px-3 py-2 text-sm"></label>
      <div class="md:col-span-2 flex flex-wrap gap-4 pt-2 text-sm">
        <label class="flex items-center gap-2"><input type="checkbox" name="featured" <?= $editing['featured']?'checked':'' ?>> Destaque</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="offer" <?= $editing['offer']?'checked':'' ?>> Oferta</label>
        <label class="flex items-center gap-2"><input type="checkbox" name="active" <?= $editing['active']?'checked':'' ?>> Ativo</label>
      </div>
    </div>
    </div>
    <div class="flex justify-end gap-2 px-6 py-4 border-t border-border bg-card">
      <a href="?section=products" class="rounded-lg border border-border px-4 py-2 text-sm">Cancelar</a>
      <button class="rounded-lg bg-neon px-4 py-2 text-sm font-bold text-neon-foreground">Salvar</button>
    </div>
  </form>
</div>
<?php endif; ?>
