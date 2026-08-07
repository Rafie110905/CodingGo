<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodingGo - Belajar Coding Lebih Mudah</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <link rel="stylesheet" href="src/css/index.css">
    <?php if(isset($dashboard_pages) && in_array($page ?? '', $dashboard_pages)): ?>
        <link rel="stylesheet" href="src/css/dashboard.css">
    <?php endif; ?>
    <?php if(isset($page) && in_array($page, ['login', 'register'])): ?>
        <link rel="stylesheet" href="src/css/auth.css">
    <?php endif; ?>
</head>
<body>
