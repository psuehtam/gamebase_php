<?php

declare(strict_types=1);

class CategoriaController
{
    private Categoria $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new Categoria();
    }

    public function adminCategorias(): void
    {
        render('admin/categorias_lista.php', [
            'pageTitle' => 'Gerenciar categorias',
            'categorias' => $this->categoriaModel->listarTodas(),
        ]);
    }

    public function adminCategoriaForm(int $id = 0): void
    {
        $categoria = $id > 0 ? $this->categoriaModel->buscarPorId($id) : null;

        if ($id > 0 && $categoria === null) {
            setFlash('warning', 'Categoria não encontrada.');
            redirectTo('admin_categorias');
        }

        render('admin/categorias_form.php', [
            'pageTitle' => $id > 0 ? 'Editar categoria' : 'Nova categoria',
            'categoria' => $categoria,
        ]);
    }

    public function salvarAdminCategoria(): void
    {
        validarCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $nome = trim((string) ($_POST['nome'] ?? ''));

        // #region agent log
        debugLog('initial', 'H5', 'controllers/CategoriaController.php:45', 'category form submitted', [
            'id' => $id,
            'nameLength' => strlen($nome),
            'isEdit' => $id > 0,
        ]);
        // #endregion

        if ($nome === '') {
            setFlash('danger', 'Informe o nome da categoria.');
            redirectTo('admin_categorias_form', $id > 0 ? ['id' => $id] : []);
        }

        try {
            if ($id > 0) {
                $this->categoriaModel->atualizar($id, $nome);
                setFlash('success', 'Categoria atualizada com sucesso.');
            } else {
                $this->categoriaModel->inserir($nome);
                setFlash('success', 'Categoria criada com sucesso.');
            }
        } catch (PDOException $exception) {
            // #region agent log
            debugLog('initial', 'H5', 'controllers/CategoriaController.php:67', 'category save failed', [
                'id' => $id,
                'isEdit' => $id > 0,
                'errorType' => get_class($exception),
                'errorCode' => (string) $exception->getCode(),
                'errorMessage' => $exception->getMessage(),
            ]);
            // #endregion

            setFlash('danger', 'Não foi possível salvar a categoria.');
            redirectTo('admin_categorias_form', $id > 0 ? ['id' => $id] : []);
        }

        redirectTo('admin_categorias');
    }

    public function excluirAdminCategoria(int $id): void
    {
        validarCsrf();

        if ($id <= 0) {
            setFlash('warning', 'Categoria inválida para exclusão.');
            redirectTo('admin_categorias');
        }

        $this->categoriaModel->deletar($id);
        setFlash('success', 'Categoria excluída com sucesso.');
        redirectTo('admin_categorias');
    }
}
