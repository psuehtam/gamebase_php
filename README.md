# GameBase — Descubra. Avalie. Jogue.

Sistema de catálogo e avaliação de jogos desenvolvido em PHP para o Trabalho A1.

## Tema
Catálogo de jogos inspirado no estilo IMDB, com autenticação, avaliações públicas e área administrativa.

## Integrantes
- Gabriel Henrique Alves Raatz
- Lucas Ito Marques dos Santos
- Matheus Pupia
- Rafael Moritz Sumbach

## Credenciais de teste
| Tipo | E-mail | Senha |
|------|--------|-------|
| Admin | admin@gamebase.com | password |
| Usuário | user@gamebase.com | password |

## Recursos do sistema
- Cadastro e login de usuários com `password_hash()` e `password_verify()`.
- Catálogo público com busca por nome e filtro por categoria.
- Página de detalhes com nota média e lista de avaliações.
- Perfil do usuário com edição e exclusão das próprias avaliações.
- Área administrativa para gerenciar jogos e categorias.
- Proteção CSRF em todos os formulários POST sensíveis.
- Cookies de lembrar-me e último acesso.

## Como rodar
1. Importe `database/gamebase.sql` no phpMyAdmin ou cliente MySQL/MariaDB.
2. Edite `config/database.php` com as credenciais do seu banco, se necessário.
3. Coloque a pasta `gamebase_php/` dentro do `htdocs` (XAMPP) ou `www` (WAMP).
4. Acesse `http://localhost/gamebase_php/`.

## Estrutura
```text
gamebase_php/
├── assets/
├── config/
├── controllers/
├── database/
├── models/
├── views/
└── index.php
```
