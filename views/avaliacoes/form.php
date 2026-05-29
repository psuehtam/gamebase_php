<?php
$jogo = $jogo ?? ['id' => 0, 'titulo' => 'Jogo'];
$avaliacao = $avaliacao ?? null;
?>

<section class="row justify-content-center">
    <div class="col-lg-8">
        <article class="card auth-card">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                    <div>
                        <span class="eyebrow-pill mb-3"><?= $avaliacao !== null ? 'Edicao' : 'Nova avaliacao' ?></span>
                        <h1 class="h2 mb-1"><?= $avaliacao !== null ? 'Editar avaliação' : 'Avaliar jogo' ?></h1>
                        <p class="text-muted mb-0"><?= e($jogo['titulo']) ?></p>
                    </div>
                    <span class="badge badge-nota badge-nota-static">Nota de 1 a 10</span>
                </div>

                <form method="POST" action="<?= e(baseUrl('index.php?page=avaliar&jogo_id=' . (int) $jogo['id'])) ?>" class="row g-3">
                    <input type="hidden" name="csrf_token" value="<?= e(gerarCsrf()) ?>">
                    <input type="hidden" name="jogo_id" value="<?= (int) $jogo['id'] ?>">

                    <div class="col-md-3">
                        <label for="nota" class="form-label">Nota</label>
                        <input type="number" min="1" max="10" class="form-control" id="nota" name="nota" value="<?= e((string) ($avaliacao['nota'] ?? '')) ?>" required>
                    </div>

                    <div class="col-12">
                        <label for="comentario" class="form-label">Comentário</label>
                        <textarea class="form-control" id="comentario" name="comentario" rows="5" placeholder="Conte como foi sua experiência com o jogo."><?= e((string) ($avaliacao['comentario'] ?? '')) ?></textarea>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-gold"><?= $avaliacao !== null ? 'Atualizar avaliação' : 'Publicar avaliação' ?></button>
                        <a class="btn btn-outline-light" href="<?= e(baseUrl('index.php?page=detalhes&id=' . (int) $jogo['id'])) ?>">Cancelar</a>
                    </div>
                </form>
            </div>
        </article>
    </div>
</section>
