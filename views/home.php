<?php $destaques = $destaques ?? []; ?>

<section class="page-banner mb-5">
    <div class="row align-items-center g-4">
        <div class="col-lg-8">
            <span class="eyebrow-pill mb-3">GameBase</span>
            <h1 class="page-banner-title h2 mb-3">Descubra jogos, veja notas da comunidade e encontre o que jogar.</h1>
            <p class="page-banner-text mb-0">
                Explore o catalogo, abra a ficha de cada titulo e acompanhe as avaliacoes para decidir
                sua proxima jogatina com mais facilidade.
            </p>
        </div>
        <div class="col-lg-4">
            <div class="d-grid gap-2 d-sm-flex d-lg-grid justify-content-sm-start">
                <a class="btn btn-gold btn-lg" href="<?= e(baseUrl('index.php?page=catalogo')) ?>">Explorar catálogo</a>
                <?php if (!estaLogado()): ?>
                    <a class="btn btn-outline-light btn-lg" href="<?= e(baseUrl('index.php?page=cadastro')) ?>">Criar conta grátis</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="section-heading">
    <div>
        <span class="eyebrow-pill mb-3">Destaques</span>
        <h2 class="h3 mb-1 text-white">Top jogos melhor avaliados</h2>
        <p class="section-copy">Os titulos que mais chamaram a atencao da comunidade GameBase.</p>
    </div>
    <div>
        <a class="link-gold fw-semibold" href="<?= e(baseUrl('index.php?page=catalogo')) ?>">Ver todos -></a>
    </div>
</section>

<section class="mb-5">
    <div class="row g-4">
        <?php if ($destaques === []): ?>
            <div class="col-12">
                <div class="card empty-state">
                    <div class="card-body text-center py-5">
                        <p class="mb-0 text-muted">Nenhum jogo cadastrado ainda. <a class="link-gold" href="<?= e(baseUrl('index.php?page=catalogo')) ?>">Ver catálogo</a></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ($destaques as $jogo): ?>
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
                            <h3 class="game-card-title h5 mb-0"><?= e($jogo['titulo']) ?></h3>
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
                    <div class="card-footer">
                        <a class="btn btn-gold w-100 btn-sm" href="<?= e(baseUrl('index.php?page=detalhes&id=' . (int) $jogo['id'])) ?>">Ver detalhes</a>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</section>
