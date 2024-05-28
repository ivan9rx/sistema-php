<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            /* Alterado de center para flex-start */
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
    <?php
    // Inicia a sessão
    session_start();

    // Verifica se o usuário está logado
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: index.php");
        exit;
    }

    // Conecta ao banco de dados
    $conn = new mysqli("localhost", "root", "", "sistema");

    // Verifica a conexão
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Consulta o banco de dados para verificar o nível de acesso do usuário
    $sql = "SELECT nivel_acesso FROM perfil WHERE id = (SELECT perfil_id FROM usuario WHERE username = '" . $_SESSION["username"] . "')";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nivel_acesso = $row["nivel_acesso"];
    } else {
        echo "Erro ao obter o nível de acesso do usuário";
        exit;
    }

    $conn->close();
    ?>
    <div class="form-container">
        <?php
        $conn = new mysqli("localhost", "root", "", "sistema");
        $sql = "SELECT nivel_acesso FROM perfil WHERE id = (SELECT perfil_id FROM usuario WHERE username = '" . $_SESSION["username"] . "')";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nivel_acesso = $row["nivel_acesso"];
        } else {
            echo "Erro ao obter o nível de acesso do usuário";
            exit;
        }

        // Se o usuário for um administrador, ele pode fazer o CRUD de tarefas
        if ($nivel_acesso == "Administrador") {

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nome"]) && isset($_POST["descricao"])) {
                $nome = $_POST["nome"];
                $descricao = $_POST["descricao"];
                $sql = "INSERT INTO tarefas (nome, descricao) VALUES ('$nome', '$descricao')";
                if ($conn->query($sql) === TRUE) {
                    echo "Nova tarefa criada com sucesso";
                } else {
                    echo "Erro: " . $sql . "<br>" . $conn->error;
                }
                header("Location: " . $_SERVER["PHP_SELF"]);
                exit;
            }



            // Atualização de tarefas
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"]) && isset($_POST["nome"]) && isset($_POST["descricao"])) {
                $id = $_POST["id"];
                $nome = $_POST["nome"];
                $descricao = $_POST["descricao"];
                $sql = "UPDATE tarefas SET nome='$nome', descricao='$descricao' WHERE id=$id";
                if ($conn->query($sql) === TRUE) {
                    echo "Tarefa atualizada com sucesso";
                } else {
                    echo "Erro ao atualizar a tarefa: " . $conn->error;
                }
            }

            // Deleção de tarefas
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
                $id = $_POST["id"];
                $sql = "DELETE FROM tarefas WHERE id=$id";
                if ($conn->query($sql) === TRUE) {
                    echo "Tarefa deletada com sucesso";
                } else {
                    echo "Erro ao deletar a tarefa: " . $conn->error;
                }
            }



            echo '
            
            <h1>Formulário de Tarefas</h1>
            <form action="" method="post">
                <h2>Criar Tarefa</h2>
                <label for="nome">Nome:</label><br>
                <input type="text" id="nome" name="nome"><br>
                <label for="descricao">Descrição:</label><br>
                <input type="text" id="descricao" name="descricao"><br>
                <input type="submit" value="Criar">
            </form>
        
         
            <form action="" method="post">
                <h2>Atualizar Tarefa</h2>
                <label for="id">ID:</label><br>
                <input type="text" id="id" name="id"><br>
                <label for="nome">Nome:</label><br>
                <input type="text" id="nome" name="nome"><br>
                <label for="descricao">Descrição:</label><br>
                <input type="text" id="descricao" name="descricao"><br>
                <input type="submit" value="Atualizar">
            </form>
        
            
            <form action="" method="post">
                <h2>Deletar Tarefa</h2>
                <label for="id">ID:</label><br>
                <input type="text" id="id" name="id"><br>
                <input type="submit" value="Deletar">
            </form>
            <a href="logout.php">Logout</a>
            ';
            // Leitura de tarefas
            $sql = "SELECT id, nome, descricao FROM tarefas";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "id: " . $row["id"] . " - Nome: " . $row["nome"] . " - Descrição: " . $row["descricao"] . "<br>";
                }
            } else {
                echo "Nenhuma tarefa encontrada";
            }

        } else if ($nivel_acesso == "Cliente") {
            // Se o usuário for um colaborador, ele pode apenas visualizar as tarefas
            $sql = "SELECT nome, descricao FROM tarefas";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "Nome: " . $row["nome"] . " - Descrição: " . $row["descricao"] . "<br> ";

                }
            } else {
                echo "Nenhuma tarefa encontrada <a href='logout.php'>Logout</a>";

            }
        } else {
            echo "<p>Você não tem permissão para acessar esta página.</p> <br> <a href='logout.php'>Logout</a>";
        }

        $conn->close();
        ?>
    </div>
</body>

</html>