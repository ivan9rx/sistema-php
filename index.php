<?php
// Inicia a sessão
session_start();

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Conecta ao banco de dados
    $conn = new mysqli("localhost", "root", "", "sistema");

    // Verifica a conexão
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Consulta o banco de dados para verificar o usuário e a senha
    $sql = "SELECT id FROM usuario WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    // Verifica se o usuário e a senha estão corretos
    if ($result->num_rows > 0) {
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;
        header("location: dashboard.php");
    } else {
        $error = "Usuário ou senha incorretos";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
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

        .form-container input {
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
            <label for="username">Usuário:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Login">
        </form>
        <a href="registro.php">Registrar</a>
        <?php
        if (!empty($error)) {
            echo "<p>" . $error . "</p>";
        }
        ?>
    </div>
</body>

</html>