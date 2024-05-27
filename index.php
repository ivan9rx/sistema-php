

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
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="username">Usuário:</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Senha:</label><br>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
    <?php
    if (!empty($error)) {
        echo "<p>" . $error . "</p>";
    }
    ?>
</body>
</html>

