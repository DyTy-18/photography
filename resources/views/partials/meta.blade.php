{{-- Descripción y vista previa al compartir (WhatsApp, Instagram, Facebook...). Variables: $title, $description, $image, $canonical. --}}
<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="es_BO">
<meta property="og:site_name" content="MarKual">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
@if ($image)
    <meta property="og:image" content="{{ $image }}">
    <meta name="twitter:card" content="summary_large_image">
@endif
