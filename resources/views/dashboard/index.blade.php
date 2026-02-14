<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <title>Dashboard Leads - Wafa Indonesia</title>
  <style>
  html, body {
    font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, 
                 Segoe UI, Roboto, Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }
</style>

  @vite('resources/js/dashboard.js')
</head>
<body>

  <div id="app"></div>

  <script>
    window.__AUTH__ = {
      user: @json(auth()->user())
    };
  </script>

</body>
</html>
