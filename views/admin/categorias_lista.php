<?php $categorias = $categorias ?? []; ?>

<section class="mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Gerenciar categorias</h1>
            <p class="text-muted mb-0">Crie e organize as categorias disponíveis para classificação dos jogos.</p>
        </div>
        <a class="btn btn-gold" href="<?= e(baseUrl('index.php?page=admin_categorias_form')) ?>">Nova categoria</a>
    </div>

    <section class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th class="text-end">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($categorias === []): ?>
                        <tr>
                            <td colspan="3" class="text-center py-4">Nenhuma categoria cadastrada.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?= (int) $categoria['id'] ?></td>
                            <td><?= e($categoria['nome']) ?></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a class="btn btn-outline-light btn-sm" href="<?= e(baseUrl('index.php?page=admin_categorias_form&id=' . (int) $categoria['id'])) ?>">Editar</a>
                                    <form method="POST" action="<?= e(baseUrl('index.php?page=admin_categorias_excluir')) ?>">
                                        <input type="hidden" name="csrf_token" value="<?= e(gerarCsrf()) ?>">
                                        <input type="hidden" name="id" value="<?= (int) $categoria['id'] ?>">
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
