<?php

declare(strict_types=1);

class Categoria
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function listarTodas(): array
    {
        $stmt = $this->conn->query('SELECT * FROM categorias ORDER BY nome ASC');

        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare('SELECT * FROM categorias WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $categoria = $stmt->fetch();

        return $categoria !== false ? $categoria : null;
    }

    public function inserir(string $nome): bool
    {
        $stmt = $this->conn->prepare('INSERT INTO categorias (nome) VALUES (:nome)');
        $stmt->bindValue(':nome', $nome);

        return $stmt->execute();
    }

    public function atualizar(int $id, string $nome): bool
    {
        $stmt = $this->conn->prepare('UPDATE categorias SET nome = :nome WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nome', $nome);

        return $stmt->execute();
    }

    public function deletar(int $id): bool
    {
        $stmt = $this->conn->prepare('DELETE FROM categorias WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
