<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistema Escolar' ?></title>
    <style>
        body {
            font-family: 'Sora', Arial, sans-serif;
            line-height: 1.6;
            color: #1A2238;
            margin: 0;
            padding: 0;
            background-color: #F5F7FC;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 20px auto;
            background-color: #FFFFFF;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(27,43,75,.10);
        }
        .email-header {
            background: linear-gradient(135deg, #1B2B4B 0%, #243761 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .email-header img {
            max-width: 150px;
            max-height: 60px;
            margin-bottom: 15px;
        }
        .email-header h1 {
            color: #FFFFFF;
            margin: 10px 0 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 30px;
        }
        .email-footer {
            background-color: #F5F7FC;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #E2E8F4;
        }
        .email-footer p {
            color: #6B7A99;
            font-size: 14px;
            margin: 5px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #3B7FE8;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #2C6FD4;
        }
        .info-box {
            background-color: #F5F7FC;
            border-left: 4px solid #3B7FE8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background-color: #1B2B4B;
            color: #FFFFFF;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #E2E8F4;
        }
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                margin: 10px;
            }
            .email-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <?php if (!empty($school_logo)): ?>
                <img src="<?= $school_logo ?>" alt="<?= $school_name ?>">
            <?php endif; ?>
            <h1><?= $title ?? $school_name ?></h1>
        </div>
        
        <div class="email-body">
            <?= $this->renderSection('content') ?>
        </div>
        
        <div class="email-footer">
            <p>&copy; <?= date('Y') ?> <?= $school_name ?>. Todos os direitos reservados.</p>
            <p>Este é um email automático, por favor não responda.</p>
        </div>
    </div>
</body>
</html>