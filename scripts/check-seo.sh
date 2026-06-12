#!/bin/bash

VIEWS="/var/www/html/pindoor/resources/views"
ROUTES="/var/www/html/pindoor/routes/web.php"
ISSUES=0

echo ""
echo "━━━ SEO Check — Pindoor ━━━━━━━━━━━━━━━━━━━━━━━━━"

# 1. Vistas públicas sin canónico
MISSING=$(find "$VIEWS" -name "*.blade.php" \
  | grep -vE "/(layouts|partials|admin|cliente|artista|auth|emails|components|livewire)/" \
  | grep -vE "(sitemap|offline|navigation|app\.blade|guest\.blade|atractivos-grid)" \
  | xargs grep -rL "@section('canonical'" 2>/dev/null \
  | sed "s|$VIEWS/||" | sort)

if [ -n "$MISSING" ]; then
  echo ""; echo "⚠  Sin canónico:"
  echo "$MISSING" | while read -r f; do echo "   · $f"; done
  ISSUES=$((ISSUES+1))
else
  echo "✓  Canónicos: OK"
fi

# 2. Redirects en rutas sin 301
R302=$(grep -n "redirect()" "$ROUTES" | grep -v "301\|#")
if [ -n "$R302" ]; then
  echo ""; echo "⚠  Redirects sin 301 en web.php:"
  echo "$R302" | while read -r l; do echo "   · $l"; done
  ISSUES=$((ISSUES+1))
else
  echo "✓  Redirects 301: OK"
fi

# 3. Vistas con filtros URL sin control de robots
FILTER_VIEWS=$(grep -rl "request()->anyFilled\|request()->filled\|request()->has(" "$VIEWS" \
  --include="*.blade.php" \
  | grep -vE "/(admin|cliente|artista|auth|emails|components|livewire)/" \
  | sed "s|$VIEWS/||" | sort)

MISSING_ROBOTS=""
for v in $FILTER_VIEWS; do
  if ! grep -q "@section('robots'" "$VIEWS/$v" 2>/dev/null; then
    MISSING_ROBOTS="$MISSING_ROBOTS\n   · $v"
  fi
done

if [ -n "$MISSING_ROBOTS" ]; then
  echo ""; echo "⚠  Con filtros pero sin noindex:"
  echo -e "$MISSING_ROBOTS"
  ISSUES=$((ISSUES+1))
else
  echo "✓  Noindex en filtros: OK"
fi

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
if [ "$ISSUES" -eq 0 ]; then
  echo "  Todo OK"
else
  echo "  $ISSUES problema(s) encontrado(s)"
fi
echo ""
