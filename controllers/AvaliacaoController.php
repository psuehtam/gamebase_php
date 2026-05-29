<?php

declare(strict_types=1);

class AvaliacaoController
{
    private Avaliacao $avaliacaoModel;
    private Jogo $jogoModel;

    public function __construct()
    {
        $this->avaliacaoModel = new Avaliacao();
        $this->jogoModel = new Jogo();
    }

    public function form(int $jogoId): void
    {
        if ($jogoId <= 0) {
            setFlash('warning', 'Jogo inválido para avaliação.');
            redirectTo('catalogo');
        }

        $jogo = $this->jogoModel->buscarPorId($jogoId);

        if ($jogo === null) {
            setFlash('warning', 'Jogo não encontrado.');
            redirectTo('catalogo');
        }

        $avaliacao = $this->avaliacaoModel->buscarDoUsuario((int) $_SESSION['usuario_id'], $jogoId);

        render('avaliacoes/form.php', [
            'pageTitle' => 'Avaliar jogo',
            'jogo' => $jogo,
            'avaliacao' => $avaliacao,
        ]);
    }

    public function salvar(int $jogoId): void
    {
        validarCsrf();

        if ($jogoId <= 0 || $this->jogoModel->buscarPorId($jogoId) === null) {
            setFlash('warning', 'Jogo inválido para avaliação.');
            redirectTo('catalogo');
        }

        $nota = (int) ($_POST['nota'] ?? 0);
        $comentario = trim((string) ($_POST['comentario'] ?? ''));

        if ($nota < 1 || $nota > 10) {
            setFlash('danger', 'A nota deve estar entre 1 e 10.');
            redirectTo('avaliar', ['jogo_id' => $jogoId]);
        }

        $this->avaliacaoModel->inserirOuAtualizar([
            'usuario_id' => (int) $_SESSION['usuario_id'],
            'jogo_id' => $jogoId,
            'nota' => $nota,
            'comentario' => $comentario,
        ]);

        setFlash('success', 'Sua avaliação foi salva com sucesso.');
        redirectTo('detalhes', ['id' => $jogoId]);
    }

    public function excluirPeloPerfil(): void
    {
        validarCsrf();

        $avaliacaoId = (int) ($_POST['avaliacao_id'] ?? 0);

        if ($avaliacaoId <= 0) {
            setFlash('warning', 'Avaliação inválida.');
            redirectTo('perfil');
        }

        $this->avaliacaoModel->deletar($avaliacaoId, (int) $_SESSION['usuario_id']);
        setFlash('success', 'Avaliação excluída com sucesso.');
        redirectTo('perfil');
    }
}
