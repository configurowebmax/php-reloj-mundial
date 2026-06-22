<?php
/**
 * Reloj Mundial - Muestra la hora actual en múltiples zonas horarias
 */
header('Content-Type: text/html; charset=utf-8');

$zonasDefault = [
    'America/Bogota'       => ['🇨🇴', 'Bogotá, Colombia'],
    'America/Mexico_City'  => ['🇲🇽', 'Ciudad de México'],
    'America/New_York'     => ['🇺🇸', 'Nueva York, EE.UU.'],
    'America/Los_Angeles'  => ['🇺🇸', 'Los Ángeles, EE.UU.'],
    'America/Buenos_Aires' => ['🇦🇷', 'Buenos Aires, Argentina'],
    'America/Sao_Paulo'    => ['🇧🇷', 'São Paulo, Brasil'],
    'Europe/Madrid'        => ['🇪🇸', 'Madrid, España'],
    'Europe/London'        => ['🇬🇧', 'Londres, Reino Unido'],
    'Europe/Berlin'        => ['🇩🇪', 'Berlín, Alemania'],
    'Asia/Tokyo'           => ['🇯🇵', 'Tokio, Japón'],
    'Asia/Shanghai'        => ['🇨🇳', 'Shanghái, China'],
    'Asia/Dubai'           => ['🇦🇪', 'Dubái, EAU'],
    'Australia/Sydney'     => ['🇦🇺', 'Sídney, Australia'],
    'Pacific/Auckland'     => ['🇳🇿', 'Auckland, Nueva Zelanda'],
];

// Zona de búsqueda personalizada
$busqueda = '';
$zonaCustom = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $busqueda = trim($_POST['zona'] ?? '');
    if ($busqueda !== '') {
        $todas = DateTimeZone::listIdentifiers();
        foreach ($todas as $tz) {
            if (stripos($tz, str_replace(' ', '_', $busqueda)) !== false) {
                $dt = new DateTime('now', new DateTimeZone($tz));
                $zonaCustom = [
                    'zona' => $tz,
                    'hora' => $dt->format('H:i:s'),
                    'fecha' => $dt->format('l, d M Y'),
                    'offset' => $dt->format('P'),
                ];
                break;
            }
        }
    }
}

// Generar datos para todas las zonas
$relojes = [];
foreach ($zonasDefault as $tz => $info) {
    $dt = new DateTime('now', new DateTimeZone($tz));
    $relojes[] = [
        'bandera' => $info[0],
        'ciudad'  => $info[1],
        'zona'    => $tz,
        'hora'    => $dt->format('H:i'),
        'seg'     => $dt->format(':s'),
        'fecha'   => $dt->format('D, d M'),
        'offset'  => 'UTC' . $dt->format('P'),
        'ampm'    => $dt->format('A'),
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reloj Mundial Online | ConfiguroWeb</title>
<meta name="description" content="Consulta la hora actual en las principales ciudades del mundo. Reloj mundial online gratis con 14 zonas horarias.">
<meta name="keywords" content="reloj mundial, hora actual, zonas horarias, hora en bogota, hora en madrid, UTC">
<meta property="og:type" content="website">
<meta property="og:title" content="Reloj Mundial Online">
<meta property="og:description" content="Consulta la hora actual en las principales ciudades del mundo online gratis.">
<link rel="canonical" href="https://demoscweb.com/github/php-reloj-mundial/">
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebApplication","name":"Reloj Mundial","applicationCategory":"UtilitiesApplication","operatingSystem":"Any","offers":{"@type":"Offer","price":"0","priceCurrency":"USD"},"author":{"@type":"Person","name":"ConfiguroWeb","url":"https://configuroweb.com"}}
</script>
<link rel="stylesheet" href="assets/style.css">
<style>
.grid-relojes{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:.8rem;margin-top:1.5rem}
.tarjeta-reloj{background:var(--surface);padding:1rem;border-radius:var(--radius);border:1px solid var(--border);transition:border-color .2s}
.tarjeta-reloj:hover{border-color:var(--primary)}
.tarjeta-reloj .ciudad{font-size:.85rem;color:var(--muted)}
.tarjeta-reloj .hora{font-size:1.8rem;font-weight:700;font-family:'Cascadia Code',Consolas,monospace;color:var(--text)}
.tarjeta-reloj .hora .seg{font-size:1rem;color:var(--muted)}
.tarjeta-reloj .meta{font-size:.75rem;color:var(--muted);margin-top:.2rem}
main{max-width:700px}
</style>
</head>
<body>
<header>
  <h1>🌍 Reloj Mundial</h1>
  <p class="subtitle">Hora actual en las principales ciudades del mundo</p>
</header>
<main>
  <form method="POST">
    <label for="zona">Buscar zona horaria</label>
    <input type="text" name="zona" id="zona" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Ej. Lima, Toronto, Seoul, Paris...">
    <button type="submit" class="btn-primary">🔍 Buscar zona</button>
  </form>

  <?php if ($busqueda !== '' && $zonaCustom): ?>
  <div class="resultado" style="margin-top:1.5rem">
    <span class="etiqueta">Resultado de búsqueda</span>
    <div class="valor"><?php echo htmlspecialchars($zonaCustom['hora']); ?></div>
    <p style="margin-top:.3rem;opacity:.8"><?php echo htmlspecialchars($zonaCustom['zona']); ?> · UTC<?php echo htmlspecialchars($zonaCustom['offset']); ?></p>
    <p style="font-size:.85rem;opacity:.7"><?php echo htmlspecialchars($zonaCustom['fecha']); ?></p>
  </div>
  <?php elseif ($busqueda !== '' && !$zonaCustom): ?>
  <div style="margin-top:1.5rem;padding:1rem;background:#7f1d1d;border-radius:var(--radius);color:#fca5a5">
    ⚠️ No se encontró la zona horaria "<?php echo htmlspecialchars($busqueda); ?>". Intenta con el nombre de la ciudad en inglés.
  </div>
  <?php endif; ?>

  <div class="grid-relojes">
    <?php foreach ($relojes as $r): ?>
    <div class="tarjeta-reloj">
      <div class="ciudad"><?php echo $r['bandera']; ?> <?php echo htmlspecialchars($r['ciudad']); ?></div>
      <div class="hora"><?php echo $r['hora']; ?><span class="seg"><?php echo $r['seg']; ?></span> <span style="font-size:.8rem;color:var(--muted)"><?php echo $r['ampm']; ?></span></div>
      <div class="meta"><?php echo htmlspecialchars($r['fecha']); ?> · <?php echo $r['offset']; ?></div>
    </div>
    <?php endforeach; ?>
  </div>

  <section class="info" style="margin-top:2rem">
    <h2>¿Cómo funciona?</h2>
    <p>Este reloj mundial muestra la <strong>hora del servidor</strong> en el momento de cargar la página. Las horas se calculan con las zonas horarias oficiales de la base de datos IANA.</p>
    <p><strong>Tip:</strong> Usa la búsqueda para encontrar cualquier ciudad del mundo (en inglés). Hay más de 400 zonas horarias disponibles.</p>
  </section>
</main>
<footer>
  <p>Desarrollado por <a href="https://configuroweb.com" target="_blank">ConfiguroWeb</a> ·
     <a href="https://appscweb.com/citas/" target="_blank">Sistema de Citas</a> ·
     <a href="https://appscweb.com/negocios/" target="_blank">Gestión de Negocios</a></p>
  <p>&copy; <?php echo date('Y'); ?> ConfiguroWeb</p>
</footer>
<script src="assets/script.js"></script>
</body>
</html>
