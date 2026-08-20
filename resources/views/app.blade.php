<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'ميرال — متجر الحلي والهدايا الفاخرة')</title>
  <meta name="description" content="متجر ميرال — حلي وهدايا فاخرة: سلاسل، ساعات، بوكس هدايا وأكثر. جودة استثنائية مرتبطة بمنصة سلة.">
  <meta name="robots" content="index, follow">

  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="icon" type="image/x-icon" href="/favicon.ico">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">

  <meta property="og:type" content="website">
  <meta property="og:site_name" content="ميرال">
  <meta property="og:title" content="ميرال — متجر الحلي والهدايا الفاخرة">
  <meta property="og:description" content="حلي وهدايا فاخرة: سلاسل، ساعات، بوكس هدايا وأكثر. جودة استثنائية مرتبطة بمنصة سلة.">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta name="twitter:card" content="summary_large_image">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" media="print" onload="this.media='all'">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @inertiaHead
</head>
<body class="flex flex-col min-h-full bg-paper text-graphite">
  @inertia
</body>
</html>