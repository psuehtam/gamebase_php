<?php
$jogo = $jogo ?? null;
$categorias = $categorias ?? [];
?>

<section class="row justify-content-center">
    <div class="col-xl-9">
        <article class="card">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                    <div>
                        <h1 class="h2 mb-1"><?= $jogo !== null ? 'Editar jogo' : 'Cadastrar jogo' ?></h1>
                        <p class="text-muted mb-0">Preencha os dados do jogo para exibição no catálogo público.</p>
                    </div>
                    <a class="btn btn-outline-light" href="<?= e(baseUrl('index.php?page=admin_jogos')) ?>">Voltar</a>
                </div>

                <form method="POST" action="<?= e(baseUrl('index.php?page=admin_jogos_form')) ?>" class="row g-3">
                    <input type="hidden" name="csrf_token" value="<?= e(gerarCsrf()) ?>">
                    <input type="hidden" name="id" value="<?= (int) ($jogo['id'] ?? 0) ?>">

                    <div class="col-md-8">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" value="<?= e((string) ($jogo['titulo'] ?? '')) ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label for="ano" class="form-label">Ano</label>
                        <input type="number" class="form-control" id="ano" name="ano" value="<?= e((string) ($jogo['ano'] ?? '')) ?>" min="1970" max="2100">
                    </div>

                    <div class="col-md-6">
                        <label for="categoria_id" class="form-label">Categoria</label>
                        <select class="form-select" id="categoria_id" name="categoria_id">
                            <option value="">Selecione</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= (int) $categoria['id'] ?>" <?= (int) ($jogo['categoria_id'] ?? 0) === (int) $categoria['id'] ? 'selected' : '' ?>>
                                    <?= e($categoria['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="capa_url" class="form-label">URL da capa</label>
                        <input type="url" class="form-control" id="capa_url" name="capa_url" value="<?= e((string) ($jogo['capa_url'] ?? '')) ?>" placeholder="https://...">
                    </div>

                    <div class="col-12">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="6"><?= e((string) ($jogo['descricao'] ?? '')) ?></textarea>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-gold"><?= $jogo !== null ? 'Salvar alterações' : 'Cadastrar jogo' ?></button>
                    </div>
                </form>
            </div>
        </article>
    </div>
</section>
