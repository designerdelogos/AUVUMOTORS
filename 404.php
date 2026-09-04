<?php require __DIR__ . '/includes/bootstrap.php';
http_response_code(404);
$__title = 'Pagina nao encontrada - Auto Store';
$__description = 'A pagina solicitada nao foi encontrada na Auto Store.';
require __DIR__ . '/includes/header.php'; ?>

<section class="mx-auto grid min-h-[58vh] max-w-5xl place-items-center px-4 py-16 text-center lg:px-6">
  <div class="max-w-2xl">
    <p class="text-sm font-bold uppercase tracking-[0.3em] text-muted-foreground">Erro 404</p>
    <h1 class="mt-4 text-4xl font-extrabold tracking-tight md:text-6xl">Pagina nao encontrada</h1>
    <p class="mt-5 text-base leading-relaxed text-muted-foreground md:text-lg">
      O endereco acessado nao existe, foi removido ou esta temporariamente indisponivel. Voce pode voltar para o inicio ou consultar o estoque de veiculos.
    </p>
    <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
      <a href="<?= url('index.php') ?>" class="inline-flex items-center justify-center rounded-lg bg-black px-5 py-3 text-sm font-bold text-white transition hover:opacity-85">Voltar ao inicio</a>
      <a href="<?= url('produtos.php') ?>" class="inline-flex items-center justify-center rounded-lg border border-border px-5 py-3 text-sm font-bold transition hover:border-black hover:bg-black hover:text-white">Ver estoque</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
