<?php
// ============================================================
// DEGRA STORE — shim de compatibilidade
// Este arquivo existia como monolito. Agora só reencaminha para
// os módulos separados. Mantido para não quebrar uploads parciais
// ou includes antigos.
// ============================================================
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/fallbacks.php';
require_once __DIR__ . '/data.php';
