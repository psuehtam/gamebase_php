<?php

declare(strict_types=1);

class Jogo
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function listarTodos(?string $busca = null, ?int $categoriaId = null): array
    {
        $sql = '
            SELECT
                j.*,
                c.nome AS categoria_nome,
                COALESCE(ROUND(AVG(a.nota), 1), 0) AS media_nota,
                COUNT(a.id) AS total_avaliacoes
            FROM jogos j
            LEFT JOIN categorias c ON c.id = j.categoria_id
            LEFT JOIN avaliacoes a ON a.jogo_id = j.id
            WHERE 1 = 1
        ';

        $params = [];

        if ($categoriaId !== null) {
            $sql .= ' AND j.categoria_id = :categoria_id';
            $params[':categoria_id'] = [$categoriaId, PDO::PARAM_INT];
        }

        if ($busca !== null && $busca !== '') {
            $sql .= ' AND j.titulo LIKE :busca';
            $params[':busca'] = ['%' . $busca . '%', PDO::PARAM_STR];
        }

        $sql .= '
            GROUP BY j.id, c.nome
            ORDER BY j.titulo ASC
        ';

        $stmt = $this->conn->prepare($sql);

        foreach ($params as $placeholder => [$valor, $tipo]) {
            $stmt->bindValue($placeholder, $valor, $tipo);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function listarDestaques(int $limite = 6): array
    {
        $stmt = $this->conn->prepare(
            '
            SELECT
                j.*,
                c.nome AS categoria_nome,
                COALESCE(ROUND(AVG(a.nota), 1), 0) AS media_nota,
                COUNT(a.id) AS total_avaliacoes
            FROM jogos j
            LEFT JOIN categorias c ON c.id = j.categoria_id
            LEFT JOIN avaliacoes a ON a.jogo_id = j.id
            GROUP BY j.id, c.nome
            ORDER BY media_nota DESC, total_avaliacoes DESC, j.titulo ASC
            LIMIT :limite
            '
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare(
            '
            SELECT
                j.*,
                c.nome AS categoria_nome
            FROM jogos j
            LEFT JOIN categorias c ON c.id = j.categoria_id
            WHERE j.id = :id
            LIMIT 1
            '
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $jogo = $stmt->fetch();

        return $jogo !== false ? $jogo : null;
    }

    public function inserir(array $dados): bool
    {
        $stmt = $this->conn->prepare(
            '
            INSERT INTO jogos (titulo, descricao, ano, categoria_id, capa_url)
            VALUES (:titulo, :descricao, :ano, :categoria_id, :capa_url)
            '
        );

        $stmt->bindValue(':titulo', $dados['titulo']);
        $stmt->bindValue(':descricao', $dados['descricao']);
        $stmt->bindValue(':ano', $dados['ano'], $dados['ano'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':categoria_id', $dados['categoria_id'], $dados['categoria_id'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':capa_url', $dados['capa_url'], $dados['capa_url'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function atualizar(int $id, array $dados): bool
    {
        $stmt = $this->conn->prepare(
            '
            UPDATE jogos
            SET titulo = :titulo,
                descricao = :descricao,
                ano = :ano,
                categoria_id = :categoria_id,
                capa_url = :capa_url
            WHERE id = :id
            '
        );

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':titulo', $dados['titulo']);
        $stmt->bindValue(':descricao', $dados['descricao']);
        $stmt->bindValue(':ano', $dados['ano'], $dados['ano'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':categoria_id', $dados['categoria_id'], $dados['categoria_id'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':capa_url', $dados['capa_url'], $dados['capa_url'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function deletar(int $id): bool
    {
        $stmt = $this->conn->prepare('DELETE FROM jogos WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function listarPorCategoria(int $catId): array
    {
        return $this->listarTodos(null, $catId);
    }

    public function mediaNota(int $jogoId): float
    {
        $stmt = $this->conn->prepare(
            'SELECT COALESCE(ROUND(AVG(nota), 1), 0) AS media_nota FROM avaliacoes WHERE jogo_id = :jogo_id'
        );
        $stmt->bindValue(':jogo_id', $jogoId, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch();

        return (float) ($resultado['media_nota'] ?? 0);
    }

    public function contarTodos(): int
    {
        $stmt = $this->conn->query('SELECT COUNT(*) AS total FROM jogos');
        $resultado = $stmt->fetch();

        return (int) ($resultado['total'] ?? 0);
    }
}
