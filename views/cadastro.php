<section class="row justify-content-center">
    <div class="col-lg-7">
        <article class="card auth-card">
            <div class="card-body p-4 p-lg-5">
                <span class="eyebrow-pill mb-3">Cadastro</span>
                <h1 class="h2 mb-3">Criar conta</h1>
                <p class="text-muted mb-4">Cadastre-se para montar seu histórico de avaliações e participar da comunidade.</p>

                <form method="POST" action="<?= e(baseUrl('index.php?page=cadastro')) ?>" class="row g-3">
                    <input type="hidden" name="csrf_token" value="<?= e(gerarCsrf()) ?>">

                    <div class="col-12">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="col-md-6">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" minlength="6" required>
                    </div>

                    <div class="col-12">
                        <label for="confirmar_senha" class="form-label">Confirmar senha</label>
                        <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" minlength="6" required>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-gold">Cadastrar</button>
                    </div>
                </form>
            </div>
        </article>
    </div>
</section>
