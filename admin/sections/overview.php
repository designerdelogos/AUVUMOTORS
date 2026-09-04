<?php
$total = (int) q('SELECT COUNT(*) c FROM products')->fetch()['c'];
$active = (int) q('SELECT COUNT(*) c FROM products WHERE active=1')->fetch()['c'];
$offers = (int) q('SELECT COUNT(*) c FROM products WHERE offer=1 AND active=1')->fetch()['c'];
$clicks = (int) q('SELECT COALESCE(SUM(clicks),0) c FROM products')->fetch()['c'];
$catCount = (int) q('SELECT COUNT(*) c FROM categories WHERE active=1')->fetch()['c'];
$platCount = (int) q('SELECT COUNT(*) c FROM platforms WHERE active=1')->fetch()['c'];
$top = q('SELECT * FROM products ORDER BY clicks DESC LIMIT 5')->fetchAll();
$recent = q('SELECT * FROM click_logs ORDER BY id DESC LIMIT 8')->fetchAll();
?>
<div class="grid gap-4 md:grid-cols-3">
  <?php foreach ([
    ['Veículos ativos', $active . ' / ' . $total, 'Total cadastrados'],
    ['Destaques ativos', $offers, 'Marcados como destaque'],
    ['Interesses totais', $clicks, 'Cliques em contato'],
    ['Categorias', $catCount, 'Ativas'],
    ['Plataformas', $platCount, 'Parceiros'],
    ['Administradores', (int) q('SELECT COUNT(*) c FROM admins')->fetch()['c'], 'Contas ativas'],
  ] as [$l,$v,$s]): ?>
    <div class="rounded-2xl border border-border bg-card p-5">
      <div class="text-[11px] font-bold uppercase tracking-widest text-muted-foreground"><?= e($l) ?></div>
      <div class="mt-2 text-3xl font-extrabold text-neon"><?= e((string)$v) ?></div>
      <div class="mt-1 text-xs text-muted-foreground"><?= e($s) ?></div>
    </div>
  <?php endforeach; ?>
</div>

<div class="mt-6 grid gap-4 lg:grid-cols-2">
  <div class="rounded-2xl border border-border bg-card">
    <div class="border-b border-border p-4"><h3 class="font-bold">Mais procurados</h3></div>
    <table class="w-full text-sm">
      <?php foreach ($top as $p): ?>
        <tr class="border-t border-border">
          <td class="p-3"><div class="flex items-center gap-3"><img src="<?= e(product_primary_image($p)) ?>" class="size-10 rounded object-cover"><span class="line-clamp-1"><?= e($p['name']) ?></span></div></td>
          <td class="p-3 text-right font-bold text-neon"><?= (int)$p['clicks'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
  <div class="rounded-2xl border border-border bg-card">
    <div class="border-b border-border p-4"><h3 class="font-bold">Últimos cliques</h3></div>
    <?php if (!$recent): ?><div class="p-6 text-center text-sm text-muted-foreground">Sem cliques ainda.</div><?php else: ?>
    <table class="w-full text-sm">
      <?php foreach ($recent as $c): $prod = q('SELECT name FROM products WHERE id=?',[$c['product_id']])->fetch(); ?>
        <tr class="border-t border-border">
          <td class="p-3 line-clamp-1"><?= e($prod['name'] ?? '—') ?></td>
          <td class="p-3 text-xs text-muted-foreground"><?= e($c['device']) ?></td>
          <td class="p-3 text-xs text-muted-foreground"><?= e(date('d/m H:i', strtotime($c['created_at']))) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
    <?php endif; ?>
  </div>
</div>
