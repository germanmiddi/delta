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

4. **Compilar el frontend en el servidor** (Node 18 vía nvm, en `src/`), con backup de lo compilado antes:
   ```bash
   ssh root@217.21.78.137 'export NVM_DIR="$HOME/.nvm"; . "$NVM_DIR/nvm.sh"; cd /var/www/html/delta/src && tar czf /root/delta-assets-$(date +%Y%m%d%H%M).tgz public/js/app.js public/css/app.css public/mix-manifest.json && npm ci && npm run prod && chown www-data:www-data public/js/app.js public/css/app.css public/mix-manifest.json'
   ```
   `npm ci` instala las versiones exactas de `package-lock.json` (versionado desde el 23/10/2026). Sin el lock el servidor
   tenía Tailwind 3.1.4 y el CSS salía sin las clases de 3.4 que usa `Components/Select.vue` (`size-5`, `pe-10`, `end-0`).
   `public/js/app.js`, `public/css/app.css` y `public/mix-manifest.json` están en `.gitignore`: se generan en el servidor.
   Para volver atrás: `tar xzf /root/delta-assets-<fecha>.tgz -C /var/www/html/delta/src`.

5. *(Ya no se compila en la Mac ni se suben los archivos compilados.)*

6. **Solo si hubo cambios de PHP/composer/migraciones** (no aplica a cambios de frontend):
   ```bash
   ssh root@217.21.78.137 'docker exec delta_app_prod php artisan migrate --force'
   ssh root@217.21.78.137 'docker exec delta_app_prod php artisan optimize:clear'
   ```

7. **Verificar** en https://delta.onmedia.dev con recarga forzada (Cmd+Shift+R): Mix no versiona los archivos y el navegador puede seguir con el `app.js` viejo.
