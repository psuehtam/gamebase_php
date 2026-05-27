<?php

declare(strict_types=1);

class Avaliacao
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function inserirOuAtualizar(array $dados): bool
    {
        $stmt = $this->conn->prepare(
            '
            INSERT INTO avaliacoes (usuario_id, jogo_id, nota, comentario)
            VALUES (:usuario_id, :jogo_id, :nota, :comentario)
            ON DUPLICATE KEY UPDATE
                nota = VALUES(nota),
                comentario = VALUES(comentario),
                criado_em = CURRENT_TIMESTAMP
            '
        );

        $stmt->bindValue(':usuario_id', $dados['usuario_id'], PDO::PARAM_INT);
        $stmt->bindValue(':jogo_id', $dados['jogo_id'], PDO::PARAM_INT);
        $stmt->bindValue(':nota', $dados['nota'], PDO::PARAM_INT);
        $stmt->bindValue(':comentario', $dados['comentario']);

        return $stmt->execute();
    }

    public function listarPorJogo(int $jogoId): array
    {
        $stmt = $this->conn->prepare(
            '
            SELECT
                a.*,
                u.nome AS usuario_nome
            FROM avaliacoes a
            INNER JOIN usuarios u ON u.id = a.usuario_id
            WHERE a.jogo_id = :jogo_id
            ORDER BY a.criado_em DESC
            '
        );
        $stmt->bindValue(':jogo_id', $jogoId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function buscarDoUsuario(int $usuarioId, int $jogoId): ?array
    {
        $stmt = $this->conn->prepare(
            '
            SELECT * FROM avaliacoes
            WHERE usuario_id = :usuario_id AND jogo_id = :jogo_id
            LIMIT 1
            '
        );
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':jogo_id', $jogoId, PDO::PARAM_INT);
        $stmt->execute();

        $avaliacao = $stmt->fetch();

        return $avaliacao !== false ? $avaliacao : null;
    }

    public function listarPorUsuario(int $usuarioId): array
    {
        $stmt = $this->conn->prepare(
            '
            SELECT
                a.*,
                j.titulo AS jogo_titulo
            FROM avaliacoes a
            INNER JOIN jogos j ON j.id = a.jogo_id
            WHERE a.usuario_id = :usuario_id
            ORDER BY a.criado_em DESC
            '
        );
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function deletar(int $id, ?int $usuarioId = null): bool
    {
        $sql = 'DELETE FROM avaliacoes WHERE id = :id';
        if ($usuarioId !== null) {
            $sql .= ' AND usuario_id = :usuario_id';
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        if ($usuarioId !== null) {
            $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        }

        return $stmt->execute();
    }

    public function contarTodos(): int
    {
        $stmt = $this->conn->query('SELECT COUNT(*) AS total FROM avaliacoes');
        $resultado = $stmt->fetch();

        return (int) ($resultado['total'] ?? 0);
    }
}
