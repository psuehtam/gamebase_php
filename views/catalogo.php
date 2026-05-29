<?php
$categorias = $categorias ?? [];
$jogos = $jogos ?? [];
$buscaAtual = $buscaAtual ?? '';
$categoriaAtual = $categoriaAtual ?? null;
?>

<section class="mb-5">
    <div class="page-banner mb-4">
        <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-end gap-4">
            <div>
                <span class="eyebrow-pill mb-3">Catalogo</span>
                <h1 class="page-banner-title h2 mb-2">Catálogo de jogos</h1>
                <p class="page-banner-text mb-0">Pesquise titulos, filtre por categoria e encontre sua proxima aventura com uma interface mais imersiva.</p>
            </div>
            <div class="page-banner-stats">
                <div class="page-stat">
                    <span class="page-stat-value"><?= count($jogos) ?></span>
                    <span class="page-stat-label">resultados visiveis</span>
                </div>
                <div class="page-stat">
                    <span class="page-stat-value"><?= count($categorias) ?></span>
                    <span class="page-stat-label">categorias</span>
                </div>
            </div>
        </div>
    </div>

    <section class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= e(baseUrl('index.php')) ?>" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="catalogo">
                <div class="col-md-6">
                    <label for="busca" class="form-label">Buscar por nome</label>
                    <input type="text" class="form-control" id="busca" name="busca" value="<?= e((string) $buscaAtual) ?>" placeholder="Ex.: Hollow Knight">
                </div>
                <div class="col-md-4">
                    <label for="categoria" class="form-label">Categoria</label>
                    <select class="form-select" id="categoria" name="categoria">
                        <option value="">Todas</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= (int) $categoria['id'] ?>" <?= $categoriaAtual === (int) $categoria['id'] ? 'selected' : '' ?>>
                                <?= e($categoria['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-gold">Filtrar</button>
                </div>
            </form>
        </div>
    </section>

    <div class="row g-4">
        <?php if ($jogos === []): ?>
            <div class="col-12">
                <div class="card empty-state">
                    <div class="card-body text-center py-5">
                        <p class="mb-0 text-muted">Nenhum jogo encontrado com os filtros informados.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ($jogos as $jogo): ?>
            <div class="col-md-6 col-xl-4">
                <article class="card game-card h-100">
                    <?php if (!empty($jogo['capa_url'])): ?>
                        <img
                            src="<?= e($jogo['capa_url']) ?>"
                            alt="Capa de <?= e($jogo['titulo']) ?>"
                            class="game-card-cover"
                            loading="lazy"
                            onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="game-card-cover-placeholder" style="display:none">Sem imagem</div>
                    <?php else: ?>
                        <div class="game-card-cover-placeholder">Sem imagem</div>
                    <?php endif; ?>

                    <div class="card-body pt-4">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                            <h2 class="game-card-title h5 mb-0"><?= e($jogo['titulo']) ?></h2>
                            <span class="<?= e(classeNotaBadge((float) $jogo['media_nota'])) ?> flex-shrink-0"><?= e((string) $jogo['media_nota']) ?></span>
                        </div>
                        <p class="game-card-meta small mb-2">
                            <?= e((string) ($jogo['categoria_nome'] ?? 'Sem categoria')) ?>
                            <?php if (!empty($jogo['ano'])): ?>
                                &nbsp;•&nbsp;<?= e((string) $jogo['ano']) ?>
                            <?php endif; ?>
                        </p>
                        <p class="game-card-description mb-0">
                            <?= e(resumirTexto((string) ($jogo['descricao'] ?? 'Sem descrição disponível.'), 110)) ?>
                        </p>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <small class="meta-caption"><?= e((string) $jogo['total_avaliacoes']) ?> avaliação(ões)</small>
                        <a class="btn btn-outline-light btn-sm" href="<?= e(baseUrl('index.php?page=detalhes&id=' . (int) $jogo['id'])) ?>">Detalhes</a>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</section>
