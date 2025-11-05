# Configuração Base de Dados

## Produção: MySQL (conforme enunciado)

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gest_app
DB_USERNAME=root
DB_PASSWORD=
```

## Desenvolvimento Local: SQLite (alternativa)

```
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=gest_app
# DB_USERNAME=root
# DB_PASSWORD=
```

## Notas

-   **Enunciado especifica MySQL** - configuração principal
-   SQLite pode ser usado para desenvolvimento local se MySQL não disponível
-   Ambas as configurações são funcionais com Laravel
