# CITIC 2026 — Congreso Internacional de Tecnología e Innovación

Landing + formulario de inscripción + dashboard de administrador, construido con **HTML, CSS, Bootstrap y JS** en el frontend y **PHP + MySQL** en el backend — el mismo stack de la Sesión 4 del curso (Bloque 3).

## Qué hace

- `index.php` — landing pública del congreso con el formulario de inscripción. Los cupos disponibles (`CUPO_MAXIMO - inscritos`) se calculan en cada carga contra la base de datos.
- `procesar_inscripcion.php` — recibe el formulario, valida los datos y guarda la inscripción con una consulta preparada (protegida contra inyección SQL).
- `admin/login.php` — acceso del administrador. La contraseña nunca se guarda en texto plano: se compara con `password_verify()` contra un hash `password_hash()`.
- `admin/dashboard.php` — panel protegido por sesión, con tarjetas de resumen (total de inscritos, cupos disponibles, % de ocupación), un gráfico de inscritos por eje temático y la tabla completa con buscador.
- `admin/logout.php` — cierra la sesión.
- `sql/schema.sql` — crea las tablas `inscritos` y `admin_usuarios`, con un usuario admin y 3 inscritos de ejemplo.

## Credenciales de prueba (bórralas o cámbialas antes de usarlo con datos reales)

- Usuario: `admin`
- Contraseña: `Congreso2026!`

## Cómo probarlo en tu computadora (XAMPP / MAMP / Laragon)

1. Copia toda esta carpeta dentro de `htdocs` (o `www`, según tu instalador).
2. Crea una base de datos vacía (por ejemplo `congreso_tic`) desde phpMyAdmin.
3. Importa `sql/schema.sql` en esa base.
4. Abre `config.php` y reemplaza `DB_HOST`, `DB_NAME`, `DB_USER` y `DB_PASS` con los datos de tu MySQL local (normalmente `DB_HOST = localhost`, `DB_USER = root`, `DB_PASS` vacío).
5. Entra a `http://localhost/congreso-tic/` en tu navegador.

## Cómo subirlo a un hosting con cPanel (como en el Bloque 5 de la Sesión 4)

1. **Crea la base de datos:** cPanel → *Bases de datos MySQL* → crea la base y un usuario, y asígnale todos los privilegios sobre esa base. Anota los 3 datos: nombre de la base, usuario y contraseña (cPanel normalmente les agrega un prefijo, tipo `turodrigo_congreso_tic`).
2. **Importa la estructura:** cPanel → *phpMyAdmin* → selecciona tu base → pestaña *Importar* → sube `sql/schema.sql`.
3. **Sube el código:**
   - Opción rápida: *Administrador de archivos* → sube todo el contenido de esta carpeta a `public_html` (o a una subcarpeta si el congreso no va en el dominio raíz).
   - Opción con Git (la que vimos en el Bloque 5): sube este proyecto a un repositorio de GitHub y en cPanel usa *Git™ Version Control* para clonarlo directo desde ahí.
4. **Edita `config.php`** en el servidor con los 3 datos reales de la base de datos que creaste en el paso 1.
5. **Activa SSL** (Bloque 6): cPanel → *SSL/TLS Status* → Let's Encrypt, y fuerza HTTPS.
6. Abre tu dominio y prueba: inscríbete desde el landing, y entra a `/admin/login.php` con las credenciales de arriba para ver el dashboard.

## Cosas para ajustar antes de usarlo con el grupo

- Las fechas, el lugar y el nombre corto (`CITIC 2026`) en `index.php` son un ejemplo — cámbialos por los datos reales de tu congreso.
- `CUPO_MAXIMO` está en `config.php` — un solo lugar para ajustarlo.
- Los 3 inscritos de ejemplo en `schema.sql` son solo para que el dashboard no se vea vacío al probarlo; bórralos de la tabla `inscritos` cuando conectes inscripciones reales.
- Cambia la contraseña del admin: genera un nuevo hash con `php -r "echo password_hash('tu_nueva_clave', PASSWORD_BCRYPT);"` y actualiza la fila en `admin_usuarios`.
