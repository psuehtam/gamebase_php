<section class="row justify-content-center">
    <div class="col-lg-6">
        <article class="card auth-card">
            <div class="card-body p-4 p-lg-5">
                <span class="eyebrow-pill mb-3">Login</span>
                <h1 class="h2 mb-3">Entrar no GameBase</h1>
                <p class="text-muted mb-4">Acesse sua conta para avaliar jogos e acompanhar suas contribuições.</p>

                <form method="POST" action="<?= e(baseUrl('index.php?page=login')) ?>" class="vstack gap-3">
                    <input type="hidden" name="csrf_token" value="<?= e(gerarCsrf()) ?>">

                    <div>
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div>
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="lembrar" name="lembrar">
                        <label class="form-check-label" for="lembrar">Lembrar-me por 30 dias</label>
                    </div>

                    <button type="submit" class="btn btn-gold">Entrar</button>
                </form>
            </div>
        </article>
    </div>
</section>
