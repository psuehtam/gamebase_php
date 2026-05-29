<?php
$jogo = $jogo ?? [
    'id' => 0,
    'titulo' => 'Jogo não encontrado',
    'descricao' => '',
    'ano' => null,
    'categoria_nome' => null,
    'capa_url' => null,
];
$mediaNota = $mediaNota ?? 0;
$avaliacoes = $avaliacoes ?? [];
?>

<section class="mb-4">
    <div class="page-banner mb-4">
        <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-end gap-4">
            <div>
                <span class="eyebrow-pill mb-3">Ficha do jogo</span>
                <h1 class="page-banner-title h2 mb-2"><?= e($jogo['titulo']) ?></h1>
                <p class="page-banner-text mb-0">Confira os detalhes do titulo, veja a nota media da comunidade e acompanhe as opinioes de outros jogadores.</p>
            </div>
            <div class="page-banner-stats">
                <div class="page-stat">
                    <span class="page-stat-value"><?= e((string) ($jogo['categoria_nome'] ?? 'Sem categoria')) ?></span>
                    <span class="page-stat-label">categoria</span>
                </div>
                <div class="page-stat">
                    <span class="page-stat-value"><?= e((string) ($jogo['ano'] ?? 'N/A')) ?></span>
                    <span class="page-stat-label">ano</span>
                </div>
                <div class="page-stat">
                    <span class="page-stat-value"><?= e((string) $mediaNota) ?></span>
                    <span class="page-stat-label">nota media</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <article class="card detail-card h-100">
                <div class="card-body">
                    <?php if (!empty($jogo['capa_url'])): ?>
                        <img src="<?= e($jogo['capa_url']) ?>" alt="Capa de <?= e($jogo['titulo']) ?>" class="img-fluid rounded mb-4 object-fit-cover cover-image">
                    <?php else: ?>
                        <div class="cover-placeholder rounded mb-4 d-flex align-items-center justify-content-center">
                            <span class="fw-semibold">Sem capa</span>
                        </div>
                    <?php endif; ?>

                    <span class="<?= e(classeNotaBadge((float) $mediaNota)) ?> mb-3">Nota média: <?= e((string) $mediaNota) ?></span>
                    <h2 class="h3"><?= e($jogo['titulo']) ?></h2>
                    <p class="detail-meta"><?= e((string) ($jogo['categoria_nome'] ?? 'Sem categoria')) ?> • <?= e((string) ($jogo['ano'] ?? 'Ano não informado')) ?></p>
                    <p class="mb-0"><?= e((string) ($jogo['descricao'] ?? 'Sem descrição disponível.')) ?></p>
                </div>
            </article>
        </div>

        <div class="col-lg-8">
            <section class="card action-panel mb-4">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h2 class="h4 mb-1">Avaliações da comunidade</h2>
                        <p class="text-muted mb-0">Veja o que outros jogadores acharam deste título.</p>
                    </div>

                    <?php if (estaLogado()): ?>
                        <a class="btn btn-gold" href="<?= e(baseUrl('index.php?page=avaliar&jogo_id=' . (int) $jogo['id'])) ?>">Avaliar jogo</a>
                    <?php else: ?>
                        <a class="btn btn-outline-light" href="<?= e(baseUrl('index.php?page=login')) ?>">Entre para avaliar</a>
                    <?php endif; ?>
                </div>
            </section>

            <section class="vstack gap-3">
                <?php if ($avaliacoes === []): ?>
                    <article class="card empty-state">
                        <div class="card-body">
                            <p class="mb-0">Ainda não há avaliações para este jogo.</p>
                        </div>
                    </article>
                <?php endif; ?>

                <?php foreach ($avaliacoes as $avaliacao): ?>
                    <article class="card review-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <h3 class="h5 mb-1"><?= e($avaliacao['usuario_nome']) ?></h3>
                                    <p class="text-muted small mb-0"><?= e(date('d/m/Y H:i', strtotime((string) $avaliacao['criado_em']))) ?></p>
                                </div>
                                <span class="<?= e(classeNotaBadge((float) $avaliacao['nota'])) ?>"><?= e((string) $avaliacao['nota']) ?>/10</span>
                            </div>
                            <p class="mb-0"><?= e((string) ($avaliacao['comentario'] ?: 'Sem comentário.')) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        </div>
    </div>
</section>
