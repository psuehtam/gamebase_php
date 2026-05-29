<?php $jogos = $jogos ?? []; ?>

<section class="mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Gerenciar jogos</h1>
            <p class="text-muted mb-0">Visualize, edite e remova os jogos disponíveis na plataforma.</p>
        </div>
        <a class="btn btn-gold" href="<?= e(baseUrl('index.php?page=admin_jogos_form')) ?>">Novo jogo</a>
    </div>

    <section class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Título</th>
                        <th>Categoria</th>
                        <th>Ano</th>
                        <th>Média</th>
                        <th class="text-end">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($jogos === []): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">Nenhum jogo cadastrado.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($jogos as $jogo): ?>
                        <tr>
                            <td><?= e($jogo['titulo']) ?></td>
                            <td><?= e((string) ($jogo['categoria_nome'] ?? 'Sem categoria')) ?></td>
                            <td><?= e((string) ($jogo['ano'] ?? '-')) ?></td>
                            <td><span class="<?= e(classeNotaBadge((float) $jogo['media_nota'])) ?>"><?= e((string) $jogo['media_nota']) ?></span></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a class="btn btn-outline-light btn-sm" href="<?= e(baseUrl('index.php?page=admin_jogos_form&id=' . (int) $jogo['id'])) ?>">Editar</a>
                                    <form method="POST" action="<?= e(baseUrl('index.php?page=admin_jogos_excluir')) ?>">
                                        <input type="hidden" name="csrf_token" value="<?= e(gerarCsrf()) ?>">
                                        <input type="hidden" name="id" value="<?= (int) $jogo['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</section>
