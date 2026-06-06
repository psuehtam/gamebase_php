<?php

declare(strict_types=1);

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $host = getenv("GAMEBASE_DB_HOST") ?: "localhost";
        $dbname = getenv("GAMEBASE_DB_NAME") ?: "gamebase";
        $username = getenv("GAMEBASE_DB_USER") ?: "root";
        $password = getenv("GAMEBASE_DB_PASS");
        $password = $password !== false ? $password : "root";
        $charset = getenv("GAMEBASE_DB_CHARSET") ?: "utf8mb4";

        $dsn = sprintf(
            "mysql:host=%s;dbname=%s;charset=%s",
            $host,
            $dbname,
            $charset
        );
        try {
            self::$connection = self::createPdo($dsn, $username, $password);

            if (function_exists("debugLog")) {
                debugLog(
                    "initial",
                    "H1",
                    "config/database.php:34",
                    "database connection established",
                    [
                        "host" => $host,
                        "database" => $dbname,
                        "charset" => $charset,
                    ]
                );
            }
        } catch (Throwable $exception) {
            if (function_exists("debugLog")) {
                debugLog(
                    "initial",
                    "H1",
                    "config/database.php:42",
                    "database connection failed",
                    [
                        "host" => $host,
                        "database" => $dbname,
                        "errorType" => get_class($exception),
                        "errorCode" => (string) $exception->getCode(),
                        "errorMessage" => $exception->getMessage(),
                    ]
                );
            }

            if ((string) $exception->getCode() === "1049") {
                try {
                    if (function_exists("debugLog")) {
                        debugLog(
                            "post-fix",
                            "H1",
                            "config/database.php:50",
                            "database missing, starting bootstrap",
                            [
                                "host" => $host,
                                "database" => $dbname,
                                "schemaExists" => is_file(
                                    dirname(__DIR__) . "/database/gamebase.sql"
                                ),
                            ]
                        );
                    }

                    self::bootstrapDatabase(
                        $host,
                        $dbname,
                        $username,
                        $password,
                        $charset
                    );
                    self::$connection = self::createPdo(
                        $dsn,
                        $username,
                        $password
                    );

                    if (function_exists("debugLog")) {
                        debugLog(
                            "post-fix",
                            "H1",
                            "config/database.php:62",
                            "database bootstrap completed",
                            [
                                "host" => $host,
                                "database" => $dbname,
                            ]
                        );
                    }

                    return self::$connection;
                } catch (Throwable $bootstrapException) {
                    if (function_exists("debugLog")) {
                        debugLog(
                            "post-fix",
                            "H1",
                            "config/database.php:73",
                            "database bootstrap failed",
                            [
                                "host" => $host,
                                "database" => $dbname,
                                "errorType" => get_class($bootstrapException),
                                "errorCode" => (string) $bootstrapException->getCode(),
                                "errorMessage" => $bootstrapException->getMessage(),
                            ]
                        );
                    }

                    http_response_code(500);

                    die("<h1>Erro ao inicializar o banco</h1>" .
                        "<p>" .
                        htmlspecialchars(
                            "O banco gamebase nao existe e a inicializacao automatica falhou.",
                            ENT_QUOTES,
                            "UTF-8"
                        ) .
                        "</p>" .
                        "<p>" .
                        htmlspecialchars(
                            "Verifique se o MySQL esta ativo e se o arquivo database/gamebase.sql esta acessivel.",
                            ENT_QUOTES,
                            "UTF-8"
                        ) .
                        "</p>" .
                        "<p>" .
                        htmlspecialchars(
                            "Detalhe tecnico: " .
                                $bootstrapException->getMessage(),
                            ENT_QUOTES,
                            "UTF-8"
                        ) .
                        "</p>");
                }
            }

            http_response_code(500);

            $mensagem = "Nao foi possivel conectar ao banco de dados GameBase.";
            $orientacao =
                "Verifique host, nome do banco, usuario e senha em config/database.php ou nas variaveis de ambiente GAMEBASE_DB_*.";
            $credenciais = sprintf(
                "Configuracao atual: host=%s | banco=%s | usuario=%s",
                $host,
                $dbname,
                $username
            );
            $detalheTecnico = "Detalhe tecnico: " . $exception->getMessage();

            die("<h1>Erro de conexao com o banco</h1>" .
                "<p>" .
                htmlspecialchars($mensagem, ENT_QUOTES, "UTF-8") .
                "</p>" .
                "<p>" .
                htmlspecialchars($orientacao, ENT_QUOTES, "UTF-8") .
                "</p>" .
                "<p>" .
                htmlspecialchars($credenciais, ENT_QUOTES, "UTF-8") .
                "</p>" .
                "<p>" .
                htmlspecialchars($detalheTecnico, ENT_QUOTES, "UTF-8") .
                "</p>");
        }

        return self::$connection;
    }

    private static function createPdo(
        string $dsn,
        string $username,
        string $password
    ): PDO {
        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    private static function bootstrapDatabase(
        string $host,
        string $dbname,
        string $username,
        string $password,
        string $charset
    ): void {
        $schemaPath = dirname(__DIR__) . "/database/gamebase.sql";
        if (!is_file($schemaPath)) {
            throw new RuntimeException(
                "Arquivo database/gamebase.sql nao encontrado."
            );
        }

        $sql = file_get_contents($schemaPath);
        if ($sql === false) {
            throw new RuntimeException(
                "Nao foi possivel ler o arquivo database/gamebase.sql."
            );
        }

        $serverDsn = sprintf("mysql:host=%s;charset=%s", $host, $charset);
        $serverPdo = self::createPdo($serverDsn, $username, $password);
        self::executeSqlScript($serverPdo, $sql);
    }

    private static function executeSqlScript(PDO $pdo, string $sql): void
    {
        $buffer = "";
        $lines = preg_split("/\r\n|\n|\r/", $sql) ?: [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === "" || str_starts_with($trimmed, "--")) {
                continue;
            }

            $buffer .= $line . PHP_EOL;

            if (str_ends_with(rtrim($line), ";")) {
                $statement = trim($buffer);
                if ($statement !== "") {
                    $pdo->exec($statement);
                }
                $buffer = "";
            }
        }

        $remaining = trim($buffer);
        if ($remaining !== "") {
            $pdo->exec($remaining);
        }
    }
}
