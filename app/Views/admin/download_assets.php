<?php
// app/Views/admin/download_assets.php
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download de Assets</title>
    <style>
        body {
            background: #1B2B4B;
            color: #fff;
            font-family: 'Courier New', monospace;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #243761;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h1 {
            color: #3B7FE8;
            text-align: center;
            margin-top: 0;
        }
        pre {
            background: #1a2a45;
            padding: 20px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 13px;
            line-height: 1.5;
            color: #a0e0a0;
        }
        .button {
            display: inline-block;
            background: #3B7FE8;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .button:hover {
            background: #2C6FD4;
        }
        .button.danger {
            background: #E84646;
        }
        .button.danger:hover {
            background: #d13a3a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Download de Assets</h1>
        
        <div style="margin-bottom: 20px; text-align: center;">
            <a href="<?= site_url('admin/download-assets') ?>" class="button">▶️ Executar Download</a>
            <a href="<?= site_url('admin/dashboard') ?>" class="button danger">⬅️ Voltar ao Dashboard</a>
        </div>
        
        <pre><?= $output ?></pre>
    </div>
</body>
</html>