<?php require __DIR__ . '/includes/bootstrap.php';
$__title = 'Sobre nos - Auto Store';
$__description = 'Conheca a Auto Store, uma vitrine digital para apresentar veiculos selecionados com fotos, detalhes e atendimento direto.';
require __DIR__ . '/includes/header.php'; ?>

<section class="mx-auto max-w-5xl px-4 py-16 lg:px-6">
  <div class="max-w-3xl">
    <p class="text-xs font-bold uppercase tracking-widest text-muted-foreground">Institucional</p>
    <h1 class="mt-3 text-4xl font-extrabold tracking-tight md:text-5xl">Sobre a Auto Store</h1>
    <p class="mt-5 text-lg leading-relaxed text-muted-foreground">
      A Auto Store e uma vitrine digital criada para apresentar veiculos de forma clara, organizada e objetiva. Nosso foco e facilitar a consulta de carros disponiveis, reunindo fotos, valores, descricoes e informacoes essenciais em um so lugar.
    </p>
  </div>

  <div class="mt-12 grid gap-5 md:grid-cols-3">
    <article class="rounded-2xl border border-border bg-card p-6">
      <h2 class="text-lg font-bold">Veiculos selecionados</h2>
      <p class="mt-3 text-sm leading-relaxed text-muted-foreground">Os anuncios sao organizados para destacar os principais dados de cada veiculo, como ano, quilometragem, cambio, combustivel, fotos e valor anunciado.</p>
    </article>
    <article class="rounded-2xl border border-border bg-card p-6">
      <h2 class="text-lg font-bold">Atendimento direto</h2>
      <p class="mt-3 text-sm leading-relaxed text-muted-foreground">Cada veiculo possui um canal de contato para que o interessado possa tirar duvidas, confirmar disponibilidade e seguir com a negociacao.</p>
    </article>
    <article class="rounded-2xl border border-border bg-card p-6">
      <h2 class="text-lg font-bold">Consulta simples</h2>
      <p class="mt-3 text-sm leading-relaxed text-muted-foreground">A navegacao foi pensada para encontrar rapidamente ofertas, categorias, detalhes dos veiculos e informacoes importantes antes do atendimento.</p>
    </article>
  </div>

  <div class="mt-12 grid gap-8 rounded-2xl border border-border bg-secondary/40 p-6 md:grid-cols-[1fr_1.2fr] md:p-8">
    <div>
      <h2 class="text-2xl font-bold">Nosso compromisso</h2>
      <p class="mt-3 text-sm leading-relaxed text-muted-foreground">
        Trabalhamos para manter uma apresentacao transparente, com informacoes bem distribuidas e imagens que ajudem o cliente a avaliar cada oportunidade antes de entrar em contato.
      </p>
    </div>
    <div class="space-y-4 text-sm leading-relaxed text-muted-foreground">
      <p>Os valores, disponibilidade, caracteristicas e condicoes dos veiculos podem mudar sem aviso previo. Por isso, todas as informacoes devem ser confirmadas diretamente no atendimento antes de qualquer negociacao.</p>
      <p>Ao receber os dados finais da loja, esta pagina podera incluir razao social, CNPJ, endereco, telefone, WhatsApp, e-mail e horario de atendimento.</p>
    </div>
  </div>

  <div class="mt-10 rounded-2xl border border-border bg-card p-6">
    <h2 class="text-xl font-bold">Contato da loja</h2>
    <p class="mt-3 text-sm leading-relaxed text-muted-foreground">
      As informacoes oficiais de contato serao adicionadas aqui: nome da loja, endereco, telefone, WhatsApp, e-mail e horario de atendimento.
    </p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
