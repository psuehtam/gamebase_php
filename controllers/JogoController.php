<?php

declare(strict_types=1);

class JogoController
{
    private Jogo $jogoModel;
    private Categoria $categoriaModel;
    private Usuario $usuarioModel;
    private Avaliacao $avaliacaoModel;

    public function __construct()
    {
        $this->jogoModel = new Jogo();
        $this->categoriaModel = new Categoria();
        $this->usuarioModel = new Usuario();
        $this->avaliacaoModel = new Avaliacao();
    }

    public function home(): void
    {
        render('home.php', [
            'pageTitle' => 'Home',
            'destaques' => $this->jogoModel->listarDestaques(),
        ]);
    }

    public function catalogo(): void
    {
        $categoriaId = isset($_GET['categoria']) && $_GET['categoria'] !== ''
            ? (int) $_GET['categoria']
            : null;
        $busca = trim((string) ($_GET['busca'] ?? ''));

        render('catalogo.php', [
            'pageTitle' => 'Catálogo',
            'categorias' => $this->categoriaModel->listarTodas(),
            'jogos' => $this->jogoModel->listarTodos($busca !== '' ? $busca : null, $categoriaId),
            'buscaAtual' => $busca,
            'categoriaAtual' => $categoriaId,
        ]);
    }

    public function detalhes(int $id): void
    {
        if ($id <= 0) {
            setFlash('warning', 'Jogo não encontrado.');
            redirectTo('catalogo');
        }

        $jogo = $this->jogoModel->buscarPorId($id);

        if ($jogo === null) {
            setFlash('warning', 'Jogo não encontrado.');
            redirectTo('catalogo');
        }

        render('detalhes.php', [
            'pageTitle' => $jogo['titulo'],
            'jogo' => $jogo,
            'mediaNota' => $this->jogoModel->mediaNota($id),
            'avaliacoes' => $this->avaliacaoModel->listarPorJogo($id),
        ]);
    }

    public function adminDashboard(): void
    {
        render('admin/dashboard.php', [
            'pageTitle' => 'Dashboard Admin',
            'totalJogos' => $this->jogoModel->contarTodos(),
            'totalUsuarios' => $this->usuarioModel->contarTodos(),
            'totalAvaliacoes' => $this->avaliacaoModel->contarTodos(),
        ]);
    }

    public function adminJogos(): void
    {
        render('admin/jogos_lista.php', [
            'pageTitle' => 'Gerenciar jogos',
            'jogos' => $this->jogoModel->listarTodos(),
        ]);
    }

    public function adminJogoForm(int $id = 0): void
    {
        $jogo = $id > 0 ? $this->jogoModel->buscarPorId($id) : null;

        if ($id > 0 && $jogo === null) {
            setFlash('warning', 'Jogo não encontrado.');
            redirectTo('admin_jogos');
        }

        render('admin/jogos_form.php', [
            'pageTitle' => $id > 0 ? 'Editar jogo' : 'Novo jogo',
            'jogo' => $jogo,
            'categorias' => $this->categoriaModel->listarTodas(),
        ]);
    }

    public function salvarAdminJogo(): void
    {
        validarCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $titulo = trim((string) ($_POST['titulo'] ?? ''));
        $descricao = trim((string) ($_POST['descricao'] ?? ''));
        $ano = trim((string) ($_POST['ano'] ?? ''));
        $categoriaId = trim((string) ($_POST['categoria_id'] ?? ''));
        $capaUrl = trim((string) ($_POST['capa_url'] ?? ''));

        // #region agent log
        debugLog('initial', 'H5', 'controllers/JogoController.php:111', 'game form submitted', [
            'id' => $id,
            'titleLength' => strlen($titulo),
            'descriptionLength' => strlen($descricao),
            'yearRaw' => $ano,
            'hasCategory' => $categoriaId !== '',
            'hasCoverUrl' => $capaUrl !== '',
            'isEdit' => $id > 0,
        ]);
        // #endregion

        if ($titulo === '') {
            setFlash('danger', 'O título do jogo é obrigatório.');
            redirectTo('admin_jogos_form', $id > 0 ? ['id' => $id] : []);
        }

        if ($ano !== '' && (!ctype_digit($ano) || (int) $ano < 1970 || (int) $ano > 2100)) {
            setFlash('danger', 'Informe um ano válido entre 1970 e 2100.');
            redirectTo('admin_jogos_form', $id > 0 ? ['id' => $id] : []);
        }

        $dados = [
            'titulo' => $titulo,
            'descricao' => $descricao,
            'ano' => $ano !== '' ? (int) $ano : null,
            'categoria_id' => $categoriaId !== '' ? (int) $categoriaId : null,
            'capa_url' => $capaUrl !== '' ? $capaUrl : null,
        ];

        try {
            if ($id > 0) {
                $this->jogoModel->atualizar($id, $dados);
                setFlash('success', 'Jogo atualizado com sucesso.');
            } else {
                $this->jogoModel->inserir($dados);
                setFlash('success', 'Jogo cadastrado com sucesso.');
            }
        } catch (PDOException $exception) {
            // #region agent log
            debugLog('initial', 'H5', 'controllers/JogoController.php:149', 'game save failed', [
                'id' => $id,
                'isEdit' => $id > 0,
                'errorType' => get_class($exception),
                'errorCode' => (string) $exception->getCode(),
                'errorMessage' => $exception->getMessage(),
            ]);
            // #endregion

            setFlash('danger', 'Não foi possível salvar o jogo.');
            redirectTo('admin_jogos_form', $id > 0 ? ['id' => $id] : []);
        }

        redirectTo('admin_jogos');
    }

    public function excluirAdminJogo(int $id): void
    {
        validarCsrf();

        if ($id <= 0) {
            setFlash('warning', 'Jogo inválido para exclusão.');
            redirectTo('admin_jogos');
        }

        $this->jogoModel->deletar($id);
        setFlash('success', 'Jogo excluído com sucesso.');
        redirectTo('admin_jogos');
    }
}
