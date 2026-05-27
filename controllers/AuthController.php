<?php

declare(strict_types=1);

class AuthController
{
    private Usuario $usuarioModel;
    private Avaliacao $avaliacaoModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        $this->avaliacaoModel = new Avaliacao();
    }

    public function showLogin(): void
    {
        render('login.php', [
            'pageTitle' => 'Entrar',
        ]);
    }

    public function login(): void
    {
        validarCsrf();

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $senha = (string) ($_POST['senha'] ?? '');
        $lembrar = isset($_POST['lembrar']);

        if ($email === false || $senha === '') {
            setFlash('danger', 'Informe um e-mail válido e sua senha.');
            redirectTo('login');
        }

        $usuario = $this->usuarioModel->buscarPorEmail($email);

        // #region agent log
        debugLog('initial', 'H2', 'controllers/AuthController.php:39', 'login submission evaluated', [
            'emailIsValid' => $email !== false,
            'hasPassword' => $senha !== '',
            'rememberRequested' => $lembrar,
            'userFound' => $usuario !== null,
            'userType' => $usuario['tipo'] ?? 'missing',
        ]);
        // #endregion

        if ($usuario === null || !password_verify($senha, $usuario['senha'])) {
            setFlash('danger', 'Credenciais inválidas.');
            redirectTo('login');
        }

        session_regenerate_id(true);
        autenticarUsuario($usuario);

        if ($lembrar) {
            $token = bin2hex(random_bytes(32));
            $this->usuarioModel->atualizarToken((int) $usuario['id'], $token);
            setcookie('lembrar', $token, time() + 60 * 60 * 24 * 30, '/');
        } else {
            $this->usuarioModel->atualizarToken((int) $usuario['id'], null);
            setcookie('lembrar', '', time() - 3600, '/');
        }

        setFlash('success', 'Login realizado com sucesso.');
        redirectTo($usuario['tipo'] === 'admin' ? 'admin_dashboard' : 'perfil');
    }

    public function showCadastro(): void
    {
        render('cadastro.php', [
            'pageTitle' => 'Criar conta',
        ]);
    }

    public function cadastro(): void
    {
        validarCsrf();

        $nome = trim((string) ($_POST['nome'] ?? ''));
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $senha = (string) ($_POST['senha'] ?? '');
        $confirmacao = (string) ($_POST['confirmar_senha'] ?? '');

        if ($nome === '' || $email === false || strlen($senha) < 6) {
            setFlash('danger', 'Preencha nome, e-mail válido e uma senha com pelo menos 6 caracteres.');
            redirectTo('cadastro');
        }

        if ($senha !== $confirmacao) {
            setFlash('danger', 'A confirmação de senha não confere.');
            redirectTo('cadastro');
        }

        if ($this->usuarioModel->buscarPorEmail($email) !== null) {
            setFlash('warning', 'Já existe uma conta cadastrada com este e-mail.');
            redirectTo('cadastro');
        }

        $this->usuarioModel->inserir([
            'nome' => $nome,
            'email' => $email,
            'senha' => password_hash($senha, PASSWORD_DEFAULT),
            'tipo' => 'usuario',
        ]);

        setFlash('success', 'Cadastro realizado. Faça login para continuar.');
        redirectTo('login');
    }

    public function logout(): void
    {
        if (isset($_SESSION['usuario_id'])) {
            $this->usuarioModel->atualizarToken((int) $_SESSION['usuario_id'], null);
        }

        setcookie('lembrar', '', time() - 3600, '/');
        setcookie('ultimo_acesso', '', time() - 3600, '/');

        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        header('Location: ' . baseUrl('index.php?page=home'));
        exit;
    }

    public function perfil(): void
    {
        $usuario = $this->usuarioModel->buscarPorId((int) $_SESSION['usuario_id']);
        $avaliacoes = $this->avaliacaoModel->listarPorUsuario((int) $_SESSION['usuario_id']);

        render('perfil.php', [
            'pageTitle' => 'Meu perfil',
            'usuario' => $usuario,
            'avaliacoes' => $avaliacoes,
        ]);
    }
}
