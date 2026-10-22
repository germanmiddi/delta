# Deploy de Delta (producción)

- **URL:** https://delta.onmedia.dev
- **Servidor:** SRV1-PRD, `ssh root@217.21.78.137`
- **Código:** `/var/www/html/delta` (rama `main`; la app Laravel está en `src/`)
- **PHP:** contenedor Docker `delta_app_prod` (nginx → fastcgi `127.0.0.1:9002`). No tocar el contenedor ni la imagen para un deploy de código.
- Referencia general de servidores: `~/www/nexus-desk-kit/docs/OPERACION.md` (sección "Delta").

El deploy es **manual**: pushear a GitHub no actualiza la web.

## Pasos

1. **Commit y push desde la Mac** a `main`.

2. **Revisar que el servidor no tenga cambios sin commitear** (alguna vez se editó código directo ahí):
   ```bash
   ssh root@217.21.78.137 'cd /var/www/html/delta && git status --short && git diff'
   ```
   Si hay cambios en `resources/js`, el `app.js` de producción puede incluirlos: pasarlos al repo antes de compilar, o se pierden.
   Ignorar `src/.env.bak-*` y `src/public/.well-known/`.

3. **Bajar el código en el servidor:**
   ```bash
   ssh root@217.21.78.137 'cd /var/www/html/delta && git pull --ff-only origin main'
   ```
   El servidor puede hacer fetch pero **no push** (remote https sin credenciales). Si hay que commitear algo desde ahí: `git bundle` en el servidor y push desde la Mac.

4. **Compilar el frontend en la Mac** (el servidor no tiene node):
   ```bash
   cd src && npm run prod
   ```
   `public/js/app.js`, `public/css/app.css` y `public/mix-manifest.json` están en `.gitignore`: no viajan con git.

5. **Backup y subida de los assets compilados:**
   ```bash
   ssh root@217.21.78.137 'cd /var/www/html/delta/src/public && tar czf /root/delta-assets-$(date +%Y%m%d%H%M).tgz js/app.js css/app.css mix-manifest.json'
   cd src
   scp public/js/app.js         root@217.21.78.137:/var/www/html/delta/src/public/js/app.js
   scp public/css/app.css       root@217.21.78.137:/var/www/html/delta/src/public/css/app.css
   scp public/mix-manifest.json root@217.21.78.137:/var/www/html/delta/src/public/mix-manifest.json
   ssh root@217.21.78.137 'cd /var/www/html/delta/src/public && chown www-data:www-data js/app.js css/app.css mix-manifest.json'
   ```
   Para volver atrás: `tar xzf /root/delta-assets-<fecha>.tgz -C /var/www/html/delta/src/public`.

6. **Solo si hubo cambios de PHP/composer/migraciones** (no aplica a cambios de frontend):
   ```bash
   ssh root@217.21.78.137 'docker exec delta_app_prod php artisan migrate --force'
   ssh root@217.21.78.137 'docker exec delta_app_prod php artisan optimize:clear'
   ```

7. **Verificar** en https://delta.onmedia.dev con recarga forzada (Cmd+Shift+R): Mix no versiona los archivos y el navegador puede seguir con el `app.js` viejo.
