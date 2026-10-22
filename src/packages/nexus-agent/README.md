# onmedia/nexus-agent

Agente de **NEXUS** para apps Laravel: cada minuto, el scheduler de la app manda a NEXUS el estado de la app. Si el reporte deja de llegar, NEXUS avisa: se cayó la app, su scheduler o el paquete.

Soporta **Laravel 8 a 12** y **PHP 7.4 en adelante**.

## Instalación

El paquete vive en el repo **privado** `germanmiddi/nexus-agent-laravel` (se publica desde `agents/laravel` del repo de NEXUS con `bin/publicar.sh`). Desde la raíz de la app Laravel (donde está su `composer.json`):

```bash
composer config repositories.nexus-agent vcs ssh://git@github.com/germanmiddi/nexus-agent-laravel.git
composer require onmedia/nexus-agent:^0.2
```

La URL va con `ssh://` a propósito: con `git@github.com:…` Composer intenta bajar el zip por la API de GitHub, que en un repo privado y sin token da 404, y un `composer install --prefer-dist` corta ahí. Con `ssh://` clona por git.

**Acceso del servidor.** Cada servidor que instala la app necesita leer el repo. Se le da una deploy key de solo lectura del repo del paquete (no una clave de la cuenta), con un alias para no pisar la clave que ya usa para el repo de la app. Como el usuario que corre el deploy:

```bash
ssh-keygen -t ed25519 -N "" -C "$(whoami)@$(hostname) nexus-agent-laravel (solo lectura)" -f ~/.ssh/nexus_agent_ed25519
cat >> ~/.ssh/config <<'CFG'
Host github-nexus-agent
    HostName github.com
    User git
    IdentityFile ~/.ssh/nexus_agent_ed25519
    IdentitiesOnly yes
CFG
git config --global url."git@github-nexus-agent:germanmiddi/nexus-agent-laravel".insteadOf "ssh://git@github.com/germanmiddi/nexus-agent-laravel"
```

y la clave pública se agrega en el repo (`gh repo deploy-key add … -R germanmiddi/nexus-agent-laravel`, sin escritura). Prueba: `git ls-remote --tags ssh://git@github.com/germanmiddi/nexus-agent-laravel.git`.

Para actualizar: `composer update onmedia/nexus-agent`.

### Variantes

- **App en Docker que corre `composer install` al arrancar** (Delta, Joji): el contenedor no tiene acceso a GitHub y, si composer falla, no levanta php-fpm. Ahí el paquete va **copiado** en `src/packages/nexus-agent` con un repositorio `path` (`symlink: false`, `versions: {"onmedia/nexus-agent": "0.2.0"}`), y se instala adentro del contenedor (`docker exec … composer install`). Para actualizarlo hay que volver a copiarlo.
- **App sin scheduler cuyas tareas no conviene largar** (Motorplan, por ejemplo): en vez de activar `schedule:run`, un cron que corra solo `php artisan nexus:report` cada minuto. Se pierde el aviso de "scheduler caído", que en esa app no aplica.
- **Lock desfasado de `composer.json`** (BAT): `composer require` saca los paquetes que están solo en el lock aunque el código los use. Ahí se agrega la entrada del paquete al lock a mano, copiada de otra app, y el resto no se toca.

En el `.env` de la app, con el token que genera el panel de NEXUS (proyecto → "Paquete Laravel" → "Generar token"):

```dotenv
NEXUS_URL=https://nexusapi.onmedia.dev
NEXUS_TOKEN="..."
```

El token va **entre comillas**: lleva un `|`, y un script que carga el `.env` con `. .env` (algunos deploys lo hacen) lo interpreta como un pipe.

Si la app cachea la config: `php artisan config:cache`.

Probar:

```bash
php artisan nexus:report            # tiene que decir "Reporte enviado a NEXUS."
php artisan nexus:report --dry-run  # muestra el reporte sin mandarlo
```

No hay que tocar `Kernel.php` ni `routes/console.php`: el paquete se agenda solo en el scheduler (que tiene que estar corriendo, como siempre: `* * * * * php artisan schedule:run`).

Para apagarlo en un entorno: `NEXUS_ENABLED=false`.

## Qué manda

Cada minuto, un JSON con:

- **Ficha de la app:** nombre, entorno, si `APP_DEBUG` está encendido, URL, versiones de Laravel, PHP y del paquete.
- **Tráfico web** de los últimos 5 minutos: requests (y por minuto), cuántas dieron 4xx y 5xx, latencia p50/p95/máxima y cuántos usuarios logueados distintos hubo.
- **Últimos requests** (50, desde 0.3): hora, método, ruta, código, ms e IP. La ruta va sin query string y con los segmentos que parecen tokens tapados (`/reset-password/…`). Se cambia la cantidad con `NEXUS_RECENT_REQUESTS` (0 los apaga). La IP es la que ve Laravel: detrás de un proxy hace falta `TrustProxies` configurado o va a ser la del proxy.
- **Jobs fallidos** de las últimas 24 h (20, desde 0.3.2): hora, job, cola y clase de la excepción. El mensaje no va: puede traer datos del cliente.
- Los chequeos del propio NEXUS (User-Agent `NEXUS-Monitor`, desde 0.3.1) no se anotan: no cuentan en el tráfico ni aparecen en la lista.
- **Excepciones** de los últimos 5 minutos (web, colas y comandos): cantidad y las 5 clases más repetidas.
- **Cola:** conexión y driver, jobs pendientes por cola, fallidos en total y en la última hora.
- **Base de datos:** si responde y en cuántos ms.

Nada de datos de usuarios, mensajes de error ni secretos: de los usuarios va solo cuántos hubo (el id se guarda hasheado en el servidor de la app y no sale), de las excepciones solo la clase y de un error de base solo la clase.

Para el tráfico no hace falta middleware: el paquete escucha `RequestHandled` y `MessageLogged` y anota una línea por evento en `storage/framework/cache/nexus-agent` (un archivo por minuto, se borran solos a los 15). Se apaga con `NEXUS_REQUESTS=false`. Para medir otras colas además de la por defecto: publicar la config (`php artisan vendor:publish --tag=nexus-config`) y completar `queues`.

## Desarrollo

El código vive en el repo de NEXUS (`agents/laravel`); el repo del paquete es una copia que se publica desde ahí con `bin/publicar.sh vX.Y.Z` (rama `main` + tag).

```bash
bin/matriz.sh       # tests contra Laravel 8 (PHP 7.4) a 12 (PHP 8.4), en Docker
```
