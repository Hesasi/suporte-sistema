<?php
require_once 'config.php';

$action = $_GET['action'] ?? '';

function redirect($url, $type = 'success', $message = '') {
    $url_parts = explode('?', $url);
    $base_url = $url_parts[0];
    header("Location: $base_url?$type=" . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action == 'add_software') {
        $nome = $conn->real_escape_string($_POST['nome']);
        $descricao = $conn->real_escape_string($_POST['descricao']);
        $sql = "INSERT INTO softwares (nome, descricao) VALUES ('$nome', '$descricao')";
        if ($conn->query($sql)) redirect('index.php', 'success', 'Software adicionado com sucesso!');
        else redirect('index.php', 'error', 'Erro ao adicionar software: ' . $conn->error);
    }

    elseif ($action == 'edit_software') {
        $id = intval($_POST['id']);
        $nome = $conn->real_escape_string($_POST['nome']);
        $descricao = $conn->real_escape_string($_POST['descricao']);
        $sql = "UPDATE softwares SET nome='$nome', descricao='$descricao' WHERE id=$id";
        if ($conn->query($sql)) redirect('index.php', 'success', 'Software atualizado com sucesso!');
        else redirect('index.php', 'error', 'Erro ao atualizar software: ' . $conn->error);
    }

    elseif ($action == 'add_contact') {
        $nome = $conn->real_escape_string($_POST['nome']);
        $telefone = $conn->real_escape_string($_POST['telefone']);
        $email = $conn->real_escape_string($_POST['email'] ?? '');
        $cargo = $conn->real_escape_string($_POST['cargo']);
        $sql = "INSERT INTO contatos (nome, telefone, email, cargo) VALUES ('$nome', '$telefone', '$email', '$cargo')";
        if ($conn->query($sql)) redirect('index.php', 'success', 'Contato adicionado com sucesso!');
        else redirect('index.php', 'error', 'Erro ao adicionar contato: ' . $conn->error);
    }

    elseif ($action == 'add_problem') {
        $software_id = intval($_POST['software_id']);
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $solucao = isset($_POST['solucao']) ? $conn->real_escape_string($_POST['solucao']) : '';
        $sql = "INSERT INTO problemas (software_id, titulo, solucao) VALUES ($software_id, '$titulo', " . ($solucao === '' ? "NULL" : "'$solucao'") . ")";
        if ($conn->query($sql)) {
            header("Location: system-detail.php?id=$software_id&success=" . urlencode('Problema adicionado com sucesso!'));
            exit;
        } else {
            header("Location: system-detail.php?id=$software_id&error=" . urlencode('Erro ao adicionar problema: ' . $conn->error));
            exit;
        }
    }

    elseif ($action == 'edit_problem') {
        $id = intval($_POST['id']);
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $solucao = isset($_POST['solucao']) ? $conn->real_escape_string($_POST['solucao']) : '';
        $sql_find = "SELECT software_id FROM problemas WHERE id=$id";
        $result = $conn->query($sql_find);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $software_id = $row['software_id'];
            $sql = "UPDATE problemas SET titulo='$titulo', solucao=" . ($solucao === '' ? "NULL" : "'$solucao'") . " WHERE id=$id";
            if ($conn->query($sql)) {
                header("Location: system-detail.php?id=$software_id&success=" . urlencode('Problema atualizado com sucesso!'));
                exit;
            } else {
                header("Location: system-detail.php?id=$software_id&error=" . urlencode('Erro ao atualizar problema: ' . $conn->error));
                exit;
            }
        } else {
            redirect('index.php', 'error', 'Problema não encontrado!');
        }
    }
}
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action == 'delete_software') {
        $id = intval($_GET['id']);
        $conn->query("DELETE FROM problemas WHERE software_id=$id");
        $sql = "DELETE FROM softwares WHERE id=$id";
        if ($conn->query($sql)) redirect('index.php', 'success', 'Software excluído com sucesso!');
        else redirect('index.php', 'error', 'Erro ao excluir software: ' . $conn->error);
    }

    elseif ($action == 'delete_problem') {
        $id = intval($_GET['id']);
        $sql_find = "SELECT software_id FROM problemas WHERE id=$id";
        $result = $conn->query($sql_find);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $software_id = $row['software_id'];
            $sql = "DELETE FROM problemas WHERE id=$id";
            if ($conn->query($sql)) {
                header("Location: system-detail.php?id=$software_id&success=" . urlencode('Problema excluído com sucesso!'));
                exit;
            } else {
                header("Location: system-detail.php?id=$software_id&error=" . urlencode('Erro ao excluir problema: ' . $conn->error));
                exit;
            }
        } else {
            redirect('index.php', 'error', 'Problema não encontrado!');
        }
    }

    elseif ($action == 'delete_contact') {
        $id = intval($_GET['id']);
        $sql = "DELETE FROM contatos WHERE id=$id";
        if ($conn->query($sql)) redirect('index.php', 'success', 'Contato excluído com sucesso!');
        else redirect('index.php', 'error', 'Erro ao excluir contato: ' . $conn->error);
    }
}

$conn->close();
?>
