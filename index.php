<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/models/Usuario.php';
require_once BASE_PATH . '/models/Jogo.php';
require_once BASE_PATH . '/models/Categoria.php';
require_once BASE_PATH . '/models/Avaliacao.php';
require_once BASE_PATH . '/controllers/AuthController.php';
require_once BASE_PATH . '/controllers/JogoController.php';
require_once BASE_PATH . '/controllers/CategoriaController.php';
require_once BASE_PATH . '/controllers/AvaliacaoController.php';

function baseUrl(string $path = ''): string
{
    $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $base = $scriptName === '/' ? '' : rtrim($scriptName, '/');

    if ($path === '') {
        return $base !== '' ? $base : '.';
    }

    return ($base !== '' ? $base . '/' : '') . ltrim($path, '/');
}

function e(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function debugLog(string $runId, string $hypothesisId, string $location, string $message, array $data = []): void
{
    $payload = [
        'sessionId' => '90d1b0',
        'runId' => $runId,
        'hypothesisId' => $hypothesisId,
        'location' => $location,
        'message' => $message,
        'data' => $data,
        'timestamp' => (int) round(microtime(true) * 1000),
    ];

    file_put_contents(BASE_PATH . '/debug-90d1b0.log', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function resumirTexto(?string $texto, int $limite = 120): string
{
    $valor = trim((string) $texto);
    if ($valor === '') {
        return '';
    }

    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($valor, 0, $limite, '...');
    }

    return strlen($valor) > $limite ? substr($valor, 0, max(0, $limite - 3)) . '...' : $valor;
}

function classeNotaBadge(float $nota): string
{
    if ($nota >= 9.6) {
        return 'badge badge-nota badge-nota-masterpiece';
    }

    if ($nota >= 9.0) {
        return 'badge badge-nota badge-nota-amazing';
    }

    if ($nota >= 8.0) {
        return 'badge badge-nota badge-nota-great';
    }

    if ($nota >= 7.0) {
        return 'badge badge-nota badge-nota-good';
    }

    if ($nota >= 6.0) {
        return 'badge badge-nota badge-nota-average';
    }

    if ($nota >= 5.0) {
        return 'badge badge-nota badge-nota-bad';
    }

    return 'badge badge-nota badge-nota-trash';
}

function gerarCsrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function validarCsrf(): void
{
    $hasPostToken = isset($_POST['csrf_token']);
    $hasSessionToken = isset($_SESSION['csrf_token']);
    $isValid = $hasPostToken && $hasSessionToken && hash_equals((string) $_SESSION['csrf_token'], (string) $_POST['csrf_token']);

    debugLog('initial', 'H4', 'index.php:76', 'csrf validation checked', [
        'page' => (string) ($_GET['page'] ?? 'home'),
        'hasPostToken' => $hasPostToken,
        'hasSessionToken' => $hasSessionToken,
        'isValid' => $isValid,
        'method' => (string) ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN'),
    ]);

    if (!$isValid) {
        http_response_code(403);
        die('Token CSRF inválido.');
    }
}

function setFlash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'] = [
        'tipo' => $tipo,
        'mensagem' => $mensagem,
    ];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function atualizarUltimoAcessoCookie(): void
{
    setcookie('ultimo_acesso', date('d/m/Y H:i'), time() + 60 * 60 * 24 * 7, '/');
}

function autenticarUsuario(array $usuario): void
{
    $_SESSION['usuario_id'] = (int) $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_tipo'] = $usuario['tipo'];
    atualizarUltimoAcessoCookie();
}

function limparAutenticacao(): void
{
    unset($_SESSION['usuario_id'], $_SESSION['usuario_nome'], $_SESSION['usuario_tipo']);
}

function estaLogado(): bool
{
    return isset($_SESSION['usuario_id']);
}

function ehAdmin(): bool
{
    return estaLogado() && ($_SESSION['usuario_tipo'] ?? '') === 'admin';
}

function exigirLogin(): void
{
    if (!estaLogado()) {
        setFlash('warning', 'Faça login para acessar esta página.');
        redirectTo('login');
    }
}

function exigirAdmin(): void
{
    if (!ehAdmin()) {
        setFlash('danger', 'Acesso restrito à administração.');
        redirectTo('home');
    }
}

function redirectTo(string $page, array $params = []): void
{
    $query = http_build_query(array_merge(['page' => $page], $params));
    $location = baseUrl('index.php?' . $query);

    debugLog('initial', 'H3', 'index.php:158', 'redirecting request', [
        'targetPage' => $page,
        'params' => array_keys($params),
        'location' => $location,
        'currentPage' => (string) ($_GET['page'] ?? 'home'),
    ]);

    header('Location: ' . $location);
    exit;
}

function render(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $pageTitle = $data['pageTitle'] ?? 'GameBase';
    $viewPath = BASE_PATH . '/views/' . $view;

    include BASE_PATH . '/views/layout/header.php';
    include $viewPath;
    include BASE_PATH . '/views/layout/footer.php';
}

function restaurarLoginPorCookie(): void
{
    if (estaLogado() || empty($_COOKIE['lembrar'])) {
        return;
    }

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->buscarPorToken((string) $_COOKIE['lembrar']);

    if ($usuario !== null) {
        autenticarUsuario($usuario);
        return;
    }

    setcookie('lembrar', '', time() - 3600, '/');
}

restaurarLoginPorCookie();

$authController = new AuthController();
$jogoController = new JogoController();
$categoriaController = new CategoriaController();
$avaliacaoController = new AvaliacaoController();

$page = $_GET['page'] ?? 'home';

debugLog('initial', 'H3', 'index.php:215', 'dispatching route', [
    'page' => (string) $page,
    'method' => (string) ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN'),
    'loggedIn' => estaLogado(),
    'userType' => (string) ($_SESSION['usuario_tipo'] ?? 'guest'),
    'hasRememberCookie' => !empty($_COOKIE['lembrar']),
]);

switch ($page) {
    case 'home':
        $jogoController->home();
        break;

    case 'catalogo':
        $jogoController->catalogo();
        break;

    case 'detalhes':
        $jogoController->detalhes((int) ($_GET['id'] ?? 0));
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->login();
            break;
        }

        $authController->showLogin();
        break;

    case 'cadastro':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->cadastro();
            break;
        }

        $authController->showCadastro();
        break;

    case 'logout':
        $authController->logout();
        break;

    case 'perfil':
        exigirLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'excluir_avaliacao') {
            $avaliacaoController->excluirPeloPerfil();
            break;
        }

        $authController->perfil();
        break;

    case 'avaliar':
        exigirLogin();
        $jogoId = (int) ($_GET['jogo_id'] ?? $_POST['jogo_id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $avaliacaoController->salvar($jogoId);
            break;
        }

        $avaliacaoController->form($jogoId);
        break;

    case 'admin_dashboard':
        exigirAdmin();
        $jogoController->adminDashboard();
        break;

    case 'admin_jogos':
        exigirAdmin();
        $jogoController->adminJogos();
        break;

    case 'admin_jogos_form':
        exigirAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jogoController->salvarAdminJogo();
            break;
        }

        $jogoController->adminJogoForm((int) ($_GET['id'] ?? 0));
        break;

    case 'admin_jogos_excluir':
        exigirAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jogoController->excluirAdminJogo((int) ($_POST['id'] ?? 0));
            break;
        }

        redirectTo('admin_jogos');
        break;

    case 'admin_categorias':
        exigirAdmin();
        $categoriaController->adminCategorias();
        break;

    case 'admin_categorias_form':
        exigirAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoriaController->salvarAdminCategoria();
            break;
        }

        $categoriaController->adminCategoriaForm((int) ($_GET['id'] ?? 0));
        break;

    case 'admin_categorias_excluir':
        exigirAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoriaController->excluirAdminCategoria((int) ($_POST['id'] ?? 0));
            break;
        }

        redirectTo('admin_categorias');
        break;

    default:
        http_response_code(404);
        setFlash('warning', 'Página não encontrada.');
        redirectTo('home');
}
