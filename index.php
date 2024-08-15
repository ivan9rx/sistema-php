<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            /* Alterado de flex-start para center */
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

        #formEdit {
            display: none;
            /* Esconde o formulário */
        }
    </style>
</head>

<body>

    <script>
        function preencherFormularioEdicao(id, nome, descricao) {
            document.getElementById('formEdit').style.display = 'block';
            // Corrigindo os IDs aqui para corresponder aos do formulário
            document.getElementById('idEdit').value = id;
            document.getElementById('nomeEdit').value = nome;
            document.getElementById('descricaoEdit').value = descricao;
        }
    </script>

    <?php
    // Inicia a sessão
    session_start();

    // Verifica se o usuário está logado
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: login.php");
        exit;
    }

    // Conecta ao banco de dados
    $conn = new mysqli("localhost", "root", "", "sistemaphp");

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
        $conn = new mysqli("localhost", "root", "", "sistemaphp");
        $sql = "SELECT nivel_acesso FROM perfil WHERE id = (SELECT perfil_id FROM usuario WHERE username = '" . $_SESSION["username"] . "')";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nivel_acesso = $row["nivel_acesso"];
        } else {
            echo "Erro ao obter o nível de acesso do usuário";
            exit;
        }

        // Atualização de tarefas
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["idEdit"])) {
            $id = $_POST["idEdit"];
            $nome = isset($_POST["nomeEdit"]) && !empty($_POST["nomeEdit"]) ? $_POST["nomeEdit"] : null;
            $descricao = isset($_POST["descricaoEdit"]) && !empty($_POST["descricaoEdit"]) ? $_POST["descricaoEdit"] : null;

            // Busca a tarefa atual no banco de dados
            $sql = "SELECT nome, descricao FROM tarefas WHERE id=$id";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                // Se o nome ou a descrição não foram enviados no form, mantém o valor que estava
                $nome = $nome !== null ? $nome : $row["nome"];
                $descricao = $descricao !== null ? $descricao : $row["descricao"];
            }

            $sql = "UPDATE tarefas SET nome='$nome', descricao='$descricao' WHERE id=$id";
            if ($conn->query($sql) === TRUE) {
            } else {
                echo "Erro ao atualizar a tarefa: " . $conn->error;
            }
        }


        // Se o usuário for um administrador, ele pode fazer o CRUD de tarefas
        if ($nivel_acesso == "Administrador") {

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nome"]) && isset($_POST["descricao"])) {
                $nome = $_POST["nome"];
                $descricao = $_POST["descricao"];
                $sql = "INSERT INTO tarefas (nome, descricao) VALUES ('$nome', '$descricao')";
                if ($conn->query($sql) === TRUE) {
                } else {
                    echo "Erro: " . $sql . "<br>" . $conn->error;
                }
                header("Location: " . $_SERVER["PHP_SELF"]);
                exit;
            }

            // Deleção de tarefas
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
                $id = $_POST["id"];
                $sql = "DELETE FROM tarefas WHERE id=$id";
                if ($conn->query($sql) === TRUE) {
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
        
         
            <form id="formEdit" action="" method="post">
    <h2>Atualizar Tarefa</h2>
    <label for="idEdit">ID:</label><br>
    <input type="text" id="idEdit" name="idEdit"><br>
    <label for="nomeEdit">Nome:</label><br>
    <input type="text" id="nomeEdit" name="nomeEdit" value=""><br>
    <label for="descricaoEdit">Descrição:</label><br>
    <input type="text" id="descricaoEdit" name="descricaoEdit"><br>
    <input type="submit" value="Atualizar">
</form>
            
            ';
            // Leitura de tarefas
            $sql = "SELECT id, nome, descricao FROM tarefas";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<table>"; // Iniciar tabela
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>Id: " . $row["id"] . "</td>";
                    echo "<td>Tarefa: " . $row["nome"] . "</td>";
                    echo "<td>Descrição: " . $row["descricao"] . "</td>";
                    // Adiciona um botão de deletar com o ID da tarefa
                    echo "<td><form action='' method='post'>";
                    echo "<input type='hidden' name='id' value='" . $row["id"] . "'>";
                    echo "<input type='submit' value='Deletar'>";
                    echo "</form></td>";
                    echo "</tr>";
                    echo "<td><button onclick='preencherFormularioEdicao(\"" . $row["id"] . "\", \"" . $row["nome"] . "\", \"" . $row["descricao"] . "\")'>Editar</button></td>";
                }
                echo "</table>"; // Fechar tabela
            } else {
                echo "Nenhuma tarefa encontrada <br> ";
            }
            echo "<br> <a href='logout.php'>Logout</a>";
        } else if ($nivel_acesso == "Cliente") {
            // Se o usuário for um colaborador, ele pode apenas visualizar as tarefas
            $sql = "SELECT nome, descricao FROM tarefas";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "Nome: " . $row["nome"] . " - Descrição: " . $row["descricao"] . "<br>";
                }
            } else {
                echo "Nenhuma tarefa encontrada";
            }
            echo "<br> <a href='logout.php'>Logout</a>";
        } else {
            echo "<p>Você não tem permissão para acessar esta página.</p> <br> <a href='logout.php'>Logout</a>";
        }

        $conn->close();
        ?>
    </div>
</body>

</html>