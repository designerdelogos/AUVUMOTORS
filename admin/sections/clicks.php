<?php
$rows = q('SELECT * FROM click_logs ORDER BY id DESC LIMIT 200')->fetchAll();
$total = (int) q('SELECT COUNT(*) c FROM click_logs')->fetch()['c'];
?>
<div class="rounded-2xl border border-border bg-card">
  <div class="border-b border-border p-4"><h3 class="font-bold">Registro de interesses (<?= $total ?>)</h3><p class="text-xs text-muted-foreground mt-1">Exibindo os 200 cliques de contato mais recentes.</p></div>
  <?php if (!$rows): ?>
    <div class="p-10 text-center text-sm text-muted-foreground">Sem interesses ainda.</div>
  <?php else: ?>
    <table class="w-full text-sm">
      <thead class="bg-secondary/50 text-left text-xs uppercase tracking-widest text-muted-foreground">
        <tr><th class="p-3">Veículo</th><th class="p-3">Origem</th><th class="p-3">Dispositivo</th><th class="p-3">Data/hora</th></tr>
      </thead>
      <tbody>
      <?php foreach ($rows as $c):
        $pn = q('SELECT name FROM products WHERE id=?',[$c['product_id']])->fetch()['name'] ?? '—';
        $plat = $c['platform_id']?find_platform($c['platform_id']):null; ?>
        <tr class="border-t border-border">
          <td class="p-3"><?= e($pn) ?></td>
          <td class="p-3"><?= e($plat['name'] ?? '—') ?></td>
          <td class="p-3"><?= e($c['device']) ?></td>
          <td class="p-3 text-muted-foreground"><?= e(date('d/m/Y H:i', strtotime($c['created_at']))) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
