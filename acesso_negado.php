<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #0B5394;
        }
        p {
            color: #333;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #0B5394;
            color: #fff;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Acesso Negado</h1>
        <p>Você não tem permissão para acessar este modulo.</p>
        <button onclick="window.location.href='../index.php'">Voltar</button>
    </div>

    <script>
        function voltarPagina() {
            window.history.back();
        }
    </script>
</body>
</html>
