# Scripts de Diagnóstico e Manutenção

Esta pasta contém scripts auxiliares para diagnóstico e manutenção do sistema.

## Scripts PHP

### Permissões de Utilizadores

-   **check_user_permissions.php** - Verifica permissões de um utilizador específico

    ```bash
    php scripts/check_user_permissions.php [user_id]
    ```

-   **check_admin.php** - Verifica utilizadores com permissões de administrador
    ```bash
    php scripts/check_admin.php
    ```

### Permissões de Calendário

-   **check_calendar_permissions.php** - Lista todas as permissões de calendário e roles associados

    ```bash
    php scripts/check_calendar_permissions.php
    ```

-   **assign_calendar_permissions.php** - Atribui permissões de calendário a um role específico

    ```bash
    php scripts/assign_calendar_permissions.php [role_name]
    ```

-   **update_calendar_permissions.php** - Atualiza permissões de calendário para todos os roles relevantes
    ```bash
    php scripts/update_calendar_permissions.php
    ```

## Scripts Python

-   **update_permissions.py** - Script Python para atualização de permissões (legado)

## Notas

-   Todos os scripts PHP devem ser executados a partir da raiz do projeto
-   Certifique-se de que tem as dependências do Composer instaladas
-   Para scripts de produção, considere criar Artisan commands em vez de scripts standalone
