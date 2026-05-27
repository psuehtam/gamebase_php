<?php

declare(strict_types=1);

class Usuario
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->conn->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $usuario = $stmt->fetch();

        return $usuario !== false ? $usuario : null;
    }

    public function inserir(array $dados): bool
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO usuarios (nome, email, senha, tipo) VALUES (:nome, :email, :senha, :tipo)'
        );

        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':email', $dados['email']);
        $stmt->bindValue(':senha', $dados['senha']);
        $stmt->bindValue(':tipo', $dados['tipo'] ?? 'usuario');

        return $stmt->execute();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch();

        return $usuario !== false ? $usuario : null;
    }

    public function buscarPorToken(string $token): ?array
    {
        $stmt = $this->conn->prepare('SELECT * FROM usuarios WHERE lembrar_token = :token LIMIT 1');
        $stmt->bindValue(':token', $token);
        $stmt->execute();

        $usuario = $stmt->fetch();

        return $usuario !== false ? $usuario : null;
    }

    public function atualizarToken(int $id, ?string $token): bool
    {
        $stmt = $this->conn->prepare('UPDATE usuarios SET lembrar_token = :token WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':token', $token, $token === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function contarTodos(): int
    {
        $stmt = $this->conn->query('SELECT COUNT(*) AS total FROM usuarios');
        $resultado = $stmt->fetch();

        return (int) ($resultado['total'] ?? 0);
    }
}
