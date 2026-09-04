<?php
$year = date('Y');
?>
<footer class="relative mt-20 border-t border-neon/20 bg-gradient-to-b from-card/40 to-black">
  <!-- Trust bar -->
  <div class="border-b border-border/50">
    <div class="mx-auto grid max-w-7xl gap-3 px-2 py-6 grid-cols-2 sm:gap-4 sm:px-4 sm:py-8 lg:grid-cols-4 lg:px-6">
      <div class="flex items-start gap-2 sm:gap-3 rounded-xl border border-border/60 bg-card/40 p-3 sm:p-4 transition hover:border-neon/40">
        <div class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-lg bg-neon/10 text-neon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
        </div>
        <div class="min-w-0">
          <p class="text-xs sm:text-sm font-semibold text-foreground">Atendimento direto</p>
          <p class="mt-0.5 text-[11px] sm:text-xs text-muted-foreground">Contato rápido para tirar dúvidas e negociar</p>
        </div>
      </div>
      <div class="flex items-start gap-2 sm:gap-3 rounded-xl border border-border/60 bg-card/40 p-3 sm:p-4 transition hover:border-neon/40">
        <div class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-lg bg-neon/10 text-neon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 11l1.8-4.2A2 2 0 0 1 8.65 5h6.7a2 2 0 0 1 1.85 1.8L19 11"/><path d="M4 11h16v6H4z"/><path d="M6 17v2"/><path d="M18 17v2"/><circle cx="7.5" cy="14" r="1"/><circle cx="16.5" cy="14" r="1"/></svg>
        </div>
        <div class="min-w-0">
          <p class="text-xs sm:text-sm font-semibold text-foreground">Veículos selecionados</p>
          <p class="mt-0.5 text-[11px] sm:text-xs text-muted-foreground">Anúncios organizados com fotos e detalhes essenciais</p>
        </div>
      </div>
      <div class="flex items-start gap-2 sm:gap-3 rounded-xl border border-border/60 bg-card/40 p-3 sm:p-4 transition hover:border-neon/40">
        <div class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-lg bg-neon/10 text-neon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
        </div>
        <div class="min-w-0">
          <p class="text-xs sm:text-sm font-semibold text-foreground">Estoque atualizado</p>
          <p class="mt-0.5 text-[11px] sm:text-xs text-muted-foreground">Destaques, disponibilidade e valores em um só lugar</p>
        </div>
      </div>
      <div class="flex items-start gap-2 sm:gap-3 rounded-xl border border-border/60 bg-card/40 p-3 sm:p-4 transition hover:border-neon/40">
        <div class="flex h-9 w-9 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-lg bg-neon/10 text-neon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <div class="min-w-0">
          <p class="text-xs sm:text-sm font-semibold text-foreground">Navegação Protegida</p>
          <p class="mt-0.5 text-[11px] sm:text-xs text-muted-foreground">Site com conexão criptografada (SSL)</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Main grid -->
  <div class="mx-auto max-w-7xl px-4 py-14 lg:px-6">
    <div class="grid gap-10 md:grid-cols-3 md:items-start">
      <div class="flex flex-col items-start text-left">
        <img src="<?= asset('logo_auvumotors.webp') ?>" alt="Auvu Motors" class="h-10 w-auto footer-logo-white">
        <p class="mt-5 max-w-sm text-sm leading-relaxed text-muted-foreground">
          A <span class="text-foreground font-semibold">Auto Store</span> é uma vitrine digital para apresentar veículos com fotos, detalhes, valores e atendimento direto.
        </p>
      </div>
      <div class="flex flex-col items-start text-left md:justify-self-center">
        <div>
          <h4 class="text-xs font-bold uppercase tracking-widest text-neon">Navegação</h4>
          <ul class="mt-4 space-y-2.5 text-sm text-muted-foreground">
            <li><a href="<?= url('index.php') ?>" class="transition hover:text-neon">Início</a></li>
            <li><a href="<?= url('produtos.php') ?>" class="transition hover:text-neon">Estoque</a></li>
            <li><a href="<?= url('ofertas.php') ?>" class="transition hover:text-neon">Destaques</a></li>
          </ul>
        </div>
      </div>
      <div class="flex flex-col items-start text-left">
        <h4 class="text-xs font-bold uppercase tracking-widest text-neon">Institucional</h4>
        <ul class="mt-4 space-y-2.5 text-sm text-muted-foreground">
          <li><a href="<?= url('sobre.php') ?>" class="transition hover:text-neon">Sobre nós</a></li>
          <li><a href="<?= url('privacidade.php') ?>" class="transition hover:text-neon">Política de Privacidade</a></li>
          <li><a href="<?= url('termos.php') ?>" class="transition hover:text-neon">Termos de Uso</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Bottom bar -->
  <div class="border-t border-border/60 bg-black/60">
    <div class="footer-bottom-grid mx-auto max-w-7xl px-4 py-6 text-xs text-muted-foreground lg:px-6">
      <div class="space-y-2">
        <p>© <?= $year ?> <span class="text-foreground font-semibold">Auto Store</span>. Todos os direitos reservados.</p>
        <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-white/70">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <span>Site protegido por SSL</span>
        </div>
      </div>
      <p class="max-w-xl leading-relaxed text-white/60 md:text-center">
        Informações, valores e disponibilidade dos veículos podem mudar sem aviso prévio. Confirme todos os dados no atendimento antes da negociação.
      </p>
      <p class="rounded-full border border-white/10 bg-white/5 px-3 py-2 text-[11px] text-white/55 md:justify-self-end md:text-right">
        Desenvolvido por
        <a href="https://www.montpage.com.br/" target="_blank" rel="noopener noreferrer" class="font-semibold text-white/80 transition hover:text-white">Montpage</a>
      </p>
    </div>
  </div>
<script src="<?= asset('js/site.js') ?>?v=20260904-hero-infinite" defer></script>
</footer>
