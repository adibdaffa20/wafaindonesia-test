<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <title>Form Pendaftaran</title>

  <style>
    /* Reset + Fullscreen base */
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }

    /* #app ikut tinggi layar supaya background fullscreen stabil */
    #app {
      min-height: 100%;
    }

    /* Hindari scroll horizontal, tapi jangan matikan scroll vertikal */
    body {
      font-family: Arial, sans-serif;
      overflow-x: hidden;
      background: #000; /* fallback jika image belum load */
    }
  </style>
</head>

<body>
  <div id="app"></div>

  @vite('resources/js/app.js')
</body>
</html>
