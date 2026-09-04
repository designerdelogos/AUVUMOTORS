# Auto Store

Sistema PHP/MySQL para vitrine de veículos com painel administrativo, cadastro de carros, galeria de até 5 fotos por veículo, categorias, banners e registro de interesses.

## Acesso administrativo temporário

Se ainda não existir administrador no banco, o `install.php` ou o `seed.php` cria um acesso temporário:

- E-mail: `admin@autostore.com`
- Senha: `AutoStore@2026`

Depois do primeiro acesso, altere a senha em `Painel > Configurações`.

## Recuperação de senha

A recuperação de senha fica em:

`admin/recover.php`

Ela funciona com:

- e-mail do administrador;
- chave de recuperação;
- nova senha.

A chave de recuperação fica no arquivo:

`includes/config.php`

Linha importante:

```php
define('ADMIN_RECOVERY_KEY', 'AUTO-RECUPERA-2026');
```

Se esquecer a chave, acesse o arquivo `includes/config.php`, troque o valor de `ADMIN_RECOVERY_KEY` por uma nova chave e use essa nova chave em `admin/recover.php`.

Antes de publicar o site em definitivo, troque a senha temporária e a chave de recuperação por valores fortes e exclusivos.
