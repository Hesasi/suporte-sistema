<?php
require_once 'config.php';

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suporte Quality</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <div class="logo">
            🔧 <span>Suporte Técnico</span>
        </div>
        <div class="nav-links">
            <a href="#systems">Sistemas</a>
            <a href="#contact">Contatos</a>
        </div>
    </nav>

    <?php if ($success): ?>
    <div class="alert alert-success">
        ✅ <?php echo htmlspecialchars($success); ?>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-error">
        ❌ <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    
    <section class="hero">
        <div class="hero-content">
            <h1>Equipe de Suporte Técnico</h1>
            <p>Central de conhecimento e soluções para problemas técnicos. Encontre respostas rápidas e contatos da equipe.</p>
            <a href="#systems" class="btn">Ver Sistemas</a>
        </div>
    </section>
    
    <section class="systems" id="systems">
        <h2 class="section-title">Nossos Sistemas</h2>
        <div class="systems-grid" id="systems-container">
            <?php
            $sql = "SELECT * FROM softwares ORDER BY nome ASC";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '
                    <div class="system-card">
                        <h3>📊 <span class="system-text">'.htmlspecialchars($row["nome"]).'</span></h3>
                        <p>'.htmlspecialchars($row["descricao"]).'</p>
                        <div class="software-actions">
                            <button class="btn-small" onclick="openSystem('.$row["id"].')">Abrir</button>
                            <button class="btn-edit" onclick="openEditSoftwareModal('.$row["id"].', \''.htmlspecialchars($row["nome"]).'\', \''.htmlspecialchars($row["descricao"]).'\')">Editar</button>
                        </div>
                    </div>';
                }
            } else {
                echo '<p class="no-software">Nenhum software cadastrado. Adicione o primeiro!</p>';
            }
            ?>
        </div>
        
        <div class="add-software-section">
            <button class="btn btn-outline" onclick="openAddSoftwareModal()">
                ➕ Adicionar Novo Software
            </button>
        </div>
    </section>
    
    <section class="contact" id="contact">
        <h2 class="section-title">Contatos da Equipe</h2>
        <div class="contact-grid" id="contacts-container">
            <?php
            $sql = "SELECT * FROM contatos ORDER BY nome ASC";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '
                    <div class="contact-card">
                        <h3>'.htmlspecialchars($row["nome"]).'</h3>
                        <p>📞 '.htmlspecialchars($row["telefone"]).'</p>
                        '.($row["email"] ? '<p>✉️ '.htmlspecialchars($row["email"]).'</p>' : '').'
                        <p>'.htmlspecialchars($row["cargo"]).'</p>
                        <div class="contact-actions">
                            <button class="btn-remove" onclick="deleteContact('.$row["id"].')">Remover</button>
                        </div>
                    </div>';
                }
            }
            ?>
        </div>
        
        <div class="add-contact">
            <h3>➕ Adicionar Novo Contato</h3>
            <form method="POST" action="actions.php?action=add_contact">
                <div class="add-contact-form">
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input type="text" name="nome" placeholder="Nome do técnico" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="text" name="telefone" placeholder="(83) 99090-9090" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" placeholder="tecnico@empresa.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="cargo">Cargo</label>
                        <input type="text" name="cargo" placeholder="Cargo/Função" required>
                    </div>
                    
                    <div class="form-group full-width">
                        <button type="submit" class="submit-btn">Adicionar Contato</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
    
    <footer>
        <p>© 2025 Equipe de Suporte Técnico</p>
    </footer>

    <div id="add-software-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>➕ Adicionar Novo Software</h2>
                <button class="close-modal" onclick="closeModal('add-software-modal')">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="actions.php?action=add_software">
                    <div class="form-group">
                        <label for="software-name">Nome do Software *</label>
                        <input type="text" name="nome" placeholder="Ex: Sistema Financeiro" required>
                    </div>
                    <div class="form-group">
                        <label for="software-description">Descrição *</label>
                        <textarea name="descricao" placeholder="Descreva o software..." required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Adicionar Software</button>
                </form>
            </div>
        </div>
    </div>

    <div id="edit-software-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>✏️ Editar Software</h2>
                <button class="close-modal" onclick="closeModal('edit-software-modal')">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="actions.php?action=edit_software">
                    <input type="hidden" name="id" id="edit-software-id">
                    <div class="form-group">
                        <label for="edit-software-name">Nome do Software *</label>
                        <input type="text" name="nome" id="edit-software-name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-software-description">Descrição *</label>
                        <textarea name="descricao" id="edit-software-description" required></textarea>
                    </div>
                    <div class="modal-actions">
                        <button type="submit" class="submit-btn">Salvar Alterações</button>
                        <button type="button" class="btn-delete" onclick="deleteSoftware()">Excluir Software</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
<?php
$conn->close();
?>