# Deploy Android — Pindoor

App ID: `cl.pindoor.app` · versionCode actual: `7` · versionName: `1.0.0`  
minSdk: 33 (Android 13) · targetSdk: 36

Las credenciales del keystore están en `.env` (`ANDROID_KEYSTORE_FILE`, `ANDROID_KEYSTORE_PASSWORD`, `ANDROID_KEY_ALIAS`, `ANDROID_KEY_PASSWORD`). NativePHP las lee automáticamente.

---

## 1. Compilar

> **Google Play exige AAB** desde agosto 2021 — no acepta APK para nuevas versiones.

### AAB (Google Play)

```bash
cd /var/www/html/pindoor
npm run build
php artisan native:package android --build-type=bundle
```

Salida:
```
nativephp/android/app/build/outputs/bundle/release/app-release.aab
```

### APK (instalación directa / sideload)

```bash
cd /var/www/html/pindoor
php artisan native:package android
```

Salida:
```
nativephp/android/app/build/outputs/apk/release/app-release.apk
```

---

## 2. Subir una nueva versión

Usar `native:release` para incrementar la versión en `.env` (`NATIVEPHP_APP_VERSION` y `NATIVEPHP_APP_VERSION_CODE`):

```bash
php artisan native:release patch   # 1.0.0 → 1.0.1
php artisan native:release minor   # 1.0.0 → 1.1.0
php artisan native:release major   # 1.0.0 → 2.0.0
```

> El `versionCode` se auto-incrementa con cada llamada a `native:release`. No editar `build.gradle.kts` manualmente.

---

## 3. Distribución

### Opción A — Instalación directa (sideload)

Copiar el APK al teléfono y abrir desde el explorador de archivos.  
El dispositivo debe tener habilitado **"Instalar desde fuentes desconocidas"**:  
`Ajustes → Aplicaciones → Instalar apps desconocidas`

### Opción B — Google Play Console

1. Ir a [play.google.com/console](https://play.google.com/console)
2. Seleccionar la app **Pindoor** (`cl.pindoor.app`)
3. `Producción → Crear nueva versión`
4. Subir el archivo `app-release.aab`
5. Completar novedades de la versión y enviar a revisión

> Google Play tarda entre 1 h y 3 días en aprobar una actualización.

### Opción C — Subida directa a Play Store desde CLI

Requiere configurar un Google Service Account:
```bash
php artisan native:package android \
  --build-type=bundle \
  --upload-to-play-store \
  --play-store-track=internal
```

Tracks disponibles: `internal`, `alpha`, `beta`, `production`.

---

## 4. Compilado manual con Gradle (fallback)

Si `native:package` falla, compilar directamente con Gradle:

```bash
cd /var/www/html/pindoor/nativephp/android
JAVA_HOME=/usr/lib/jvm/java-17-openjdk-amd64 ./gradlew clean assembleRelease
```

> Usar siempre `clean` antes de `assembleRelease`.

---

## 5. Verificar el APK firmado

```bash
/usr/lib/jvm/java-17-openjdk-amd64/bin/keytool -printcert \
  -jarfile nativephp/android/app/build/outputs/apk/release/app-release.apk
```

---

## 6. Checklist antes de cada release

- [ ] `native:release patch/minor/major` ejecutado (actualiza `.env`)
- [ ] Assets compilados: `npm run build`
- [ ] AAB generado con `native:package android --build-type=bundle`
- [ ] AAB probado en dispositivo físico antes de subir (o APK con `native:package android`)
- [ ] Novedades de versión preparadas para Play Store
