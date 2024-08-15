<?php
// Inicia a sessão
session_start();

// Verifica se o usuário já está logado, se sim, redireciona para a página do dashboard
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}

// Verifica se o formulário de registro foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = $_POST["reg_username"];
    $password = $_POST["reg_password"];
    $nivel_acesso = intval($_POST["nivel_acesso"]); // Converte o valor para um número inteiro

    // Conecta ao banco de dados
    $conn = new mysqli("localhost", "root", "", "sistemaphp");

    // Verifica a conexão
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insere o novo usuário no banco de dados
    $sql = "INSERT INTO usuario (username, password, perfil_id) VALUES ('$username', '$password', '$nivel_acesso')";
    if ($conn->query($sql) === TRUE) {
        echo "Usuário registrado com sucesso!";
        header("location: login.php");
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registro</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
        }

        .form-container label {
            margin-bottom: 5px;
        }

        .form-container input,
        .form-container select {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="reg_username">Usuário:</label>
            <input type="text" id="reg_username" name="reg_username" required>
            <label for="reg_password">Senha:</label>
            <input type="password" id="reg_password" name="reg_password" required>
            <label for="nivel_acesso">Nível de Acesso:</label>
            <select id="nivel_acesso" name="nivel_acesso" required>
                <option value=1>Administrador</option>
                <option value=2>Cliente</option>
            </select>
            <input type="submit" name="register" value="Registrar">
        </form>
        <a href="login.php">Fazer login</a>
    </div>
</body>

</html>