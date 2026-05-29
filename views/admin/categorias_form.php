<?php $categoria = $categoria ?? null; ?>

<section class="row justify-content-center">
    <div class="col-lg-7">
        <article class="card">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                    <div>
                        <h1 class="h2 mb-1"><?= $categoria !== null ? 'Editar categoria' : 'Cadastrar categoria' ?></h1>
                        <p class="text-muted mb-0">Defina um nome claro para facilitar os filtros do catálogo.</p>
                    </div>
                    <a class="btn btn-outline-light" href="<?= e(baseUrl('index.php?page=admin_categorias')) ?>">Voltar</a>
                </div>

                <form method="POST" action="<?= e(baseUrl('index.php?page=admin_categorias_form')) ?>" class="vstack gap-3">
                    <input type="hidden" name="csrf_token" value="<?= e(gerarCsrf()) ?>">
                    <input type="hidden" name="id" value="<?= (int) ($categoria['id'] ?? 0) ?>">

                    <div>
                        <label for="nome" class="form-label">Nome da categoria</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?= e((string) ($categoria['nome'] ?? '')) ?>" required>
                    </div>

                    <button type="submit" class="btn btn-gold align-self-start"><?= $categoria !== null ? 'Salvar alterações' : 'Cadastrar categoria' ?></button>
                </form>
            </div>
        </article>
    </div>
</section>
