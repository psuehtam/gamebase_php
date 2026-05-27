<?php

$pageTitle = $pageTitle ?? 'GameBase';
$flash = getFlash();
$ultimoAcesso = $_COOKIE['ultimo_acesso'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | GameBase</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Orbitron:wght@500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= e(baseUrl('assets/css/style.css')) ?>">
</head>
<body class="app-shell">
<header>
    <nav class="navbar app-navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= e(baseUrl('index.php?page=home')) ?>">
                <div class="navbar-logo-wrap">
                    <img src="<?= e(baseUrl('assets/img/logo.png')) ?>" alt="Logo GameBase" class="navbar-logo">
                </div>
                <span>
                    <strong class="navbar-brand-name">GameBase</strong>
                    <small class="d-block text-gold navbar-brand-slogan">Descubra. Avalie. Jogue.</small>
                </span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#gamebaseNav" aria-controls="gamebaseNav" aria-expanded="false" aria-label="Alternar navegação">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="gamebaseNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?= e(baseUrl('index.php?page=home')) ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= e(baseUrl('index.php?page=catalogo')) ?>">Catálogo</a></li>
                    <?php if (ehAdmin()): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= e(baseUrl('index.php?page=admin_dashboard')) ?>">Admin</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= e(baseUrl('index.php?page=admin_jogos')) ?>">Jogos</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= e(baseUrl('index.php?page=admin_categorias')) ?>">Categorias</a></li>
                    <?php endif; ?>
                </ul>

                <div class="navbar-user-panel d-flex flex-column flex-lg-row align-items-lg-center gap-2 gap-lg-3 text-lg-end">
                    <?php if (estaLogado()): ?>
                        <div class="small text-light">
                            <div>Olá, <strong><?= e((string) ($_SESSION['usuario_nome'] ?? '')) ?></strong></div>
                            <?php if ($ultimoAcesso !== null): ?>
                                <div class="text-muted">Último acesso: <?= e($ultimoAcesso) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex gap-2">
                            <a class="btn btn-outline-light btn-sm" href="<?= e(baseUrl('index.php?page=perfil')) ?>">Meu perfil</a>
                            <a class="btn btn-gold btn-sm" href="<?= e(baseUrl('index.php?page=logout')) ?>">Sair</a>
                        </div>
                    <?php else: ?>
                        <div class="d-flex gap-2">
                            <a class="btn btn-outline-light btn-sm" href="<?= e(baseUrl('index.php?page=login')) ?>">Entrar</a>
                            <a class="btn btn-gold btn-sm" href="<?= e(baseUrl('index.php?page=cadastro')) ?>">Cadastrar</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>

<main class="app-main py-4">
    <div class="container">
        <?php if ($flash !== null): ?>
            <div class="alert alert-<?= e($flash['tipo']) ?> mb-4" role="alert">
                <?= e($flash['mensagem']) ?>
            </div>
        <?php endif; ?>
