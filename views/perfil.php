<?php
$usuario = $usuario ?? [];
$avaliacoes = $avaliacoes ?? [];
?>

<section class="mb-4">
    <div class="page-banner mb-4">
        <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-end gap-4">
            <div>
                <span class="eyebrow-pill mb-3">Perfil</span>
                <h1 class="page-banner-title h2 mb-2">Meu perfil</h1>
                <p class="page-banner-text mb-0">Acompanhe seus dados e revise todas as avaliacoes publicadas na plataforma.</p>
            </div>
            <div class="page-banner-stats">
                <div class="page-stat">
                    <span class="page-stat-value"><?= count($avaliacoes) ?></span>
                    <span class="page-stat-label">avaliacoes feitas</span>
                </div>
                <div class="page-stat">
                    <span class="page-stat-value text-capitalize"><?= e((string) ($usuario['tipo'] ?? 'usuario')) ?></span>
                    <span class="page-stat-label">nivel de acesso</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <article class="card h-100">
                <div class="card-body">
                    <h2 class="h4 mb-3">Meu perfil</h2>
                    <dl class="mb-0">
                        <dt class="text-muted">Nome</dt>
                        <dd class="mb-3"><?= e((string) ($usuario['nome'] ?? '')) ?></dd>

                        <dt class="text-muted">E-mail</dt>
                        <dd class="mb-3"><?= e((string) ($usuario['email'] ?? '')) ?></dd>

                        <dt class="text-muted">Tipo de acesso</dt>
                        <dd class="mb-0 text-capitalize"><?= e((string) ($usuario['tipo'] ?? 'usuario')) ?></dd>
                    </dl>
                </div>
            </article>
        </div>

        <div class="col-lg-8">
            <section class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="h4 mb-0">Minhas avaliações</h2>
                        <a class="btn btn-outline-light btn-sm" href="<?= e(baseUrl('index.php?page=catalogo')) ?>">Avaliar mais jogos</a>
                    </div>

                    <div class="vstack gap-3">
                        <?php if ($avaliacoes === []): ?>
                            <article class="empty-state p-3">
                                <p class="mb-0">Você ainda não avaliou nenhum jogo.</p>
                            </article>
                        <?php endif; ?>

                        <?php foreach ($avaliacoes as $avaliacao): ?>
                            <article class="profile-review-item p-3">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
                                    <div>
                                        <h3 class="h5 mb-1"><?= e($avaliacao['jogo_titulo']) ?></h3>
                                        <p class="mb-1"><span class="<?= e(classeNotaBadge((float) $avaliacao['nota'])) ?>"><?= e((string) $avaliacao['nota']) ?>/10</span></p>
                                        <p class="text-muted small mb-2"><?= e(date('d/m/Y H:i', strtotime((string) $avaliacao['criado_em']))) ?></p>
                                        <p class="mb-0"><?= e((string) ($avaliacao['comentario'] ?: 'Sem comentário.')) ?></p>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <a class="btn btn-outline-light btn-sm" href="<?= e(baseUrl('index.php?page=avaliar&jogo_id=' . (int) $avaliacao['jogo_id'])) ?>">Editar</a>
                                        <form method="POST" action="<?= e(baseUrl('index.php?page=perfil')) ?>">
                                            <input type="hidden" name="csrf_token" value="<?= e(gerarCsrf()) ?>">
                                            <input type="hidden" name="action" value="excluir_avaliacao">
                                            <input type="hidden" name="avaliacao_id" value="<?= (int) $avaliacao['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>
