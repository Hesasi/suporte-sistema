<?php
require_once 'config.php';

$software_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($software_id == 0) {
    header("Location: index.php");
    exit;
}
$stmt = $conn->prepare("SELECT * FROM softwares WHERE id = ?");
$stmt->bind_param("i", $software_id);
$stmt->execute();
$result_software = $stmt->get_result();

if (!$result_software || $result_software->num_rows == 0) {
    header("Location: index.php?error=Software não encontrado!");
    exit;
}

$software = $result_software->fetch_assoc();
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($software['nome']); ?> - Suporte Técnico</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <div class="logo">
            🔧 <span>Suporte Técnico</span>
        </div>
        <div class="nav-links">
            <a href="index.php">🏠 Voltar para Início</a>
            <a href="index.php#contact">Contatos</a>
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

    <section class="systems">
        <div class="hero-content">
            <h1>📊 <?php echo htmlspecialchars($software['nome']); ?></h1>
            <p><?php echo htmlspecialchars($software['descricao']); ?></p>
        </div>

        <div class="content-section">
            <h2 class="section-title">Problemas e Soluções</h2>
            
            <div class="add-problem-section">
                <button class="btn btn-outline" onclick="openAddProblemModal()">
                    ➕ Adicionar Novo Problema
                </button>
            </div>
            
            <div class="problems-grid" id="problems-container">
                <?php
                $stmt_problems = $conn->prepare("SELECT * FROM problemas WHERE software_id = ? ORDER BY titulo ASC");
                $stmt_problems->bind_param("i", $software_id);
                $stmt_problems->execute();
                $result_problems = $stmt_problems->get_result();
                
                if ($result_problems->num_rows > 0) {
                    while($problem = $result_problems->fetch_assoc()) {
                        echo '
                        <div class="problem-card">
                            <h3>'.htmlspecialchars($problem["titulo"]).'</h3>
                            <p>Clique para ver a solução</p>
                            <div class="problem-actions">
                                <button class="btn-small" onclick="showSolution('.htmlspecialchars($problem["id"]).', \''.htmlspecialchars($problem["titulo"]).'\', \''.htmlspecialchars($problem["solucao"]).'\')">Ver Solução</button>
                                <button class="btn-edit" onclick="openEditProblemModal('.htmlspecialchars($problem["id"]).', \''.htmlspecialchars($problem["titulo"]).'\', \''.htmlspecialchars($problem["solucao"]).'\')">Editar</button>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p class="no-problems">Nenhum problema cadastrado para este software.</p>';
                }
                ?>
            </div>

            <div class="solutions-container">
                <div id="solution-display" class="solution-content" style="display: none;">
                    <h4 id="solution-title">Título do Problema</h4>
                    <pre id="solution-text">Conteúdo da solução...</pre>
                    <div class="problem-actions">
                        <button class="btn-edit" id="edit-solution-btn">Editar Esta Solução</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <p>© 2025 Equipe de Suporte Técnico</p>
    </footer>

    <div id="add-problem-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>➕ Adicionar Novo Problema</h2>
                <button class="close-modal" onclick="closeModal('add-problem-modal')">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="actions.php?action=add_problem">
                    <input type="hidden" name="software_id" value="<?php echo $software_id; ?>">
                    <div class="form-group">
                        <label for="problem-title">Título do Problema *</label>
                        <input type="text" name="titulo" placeholder="Ex: Erro no Comunicador" required>
                    </div>
                    <div class="form-group">
                        <label for="problem-solution">Solução *</label>
                        <textarea name="solucao" rows="10" placeholder="Descreva a solução passo a passo..." required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Adicionar Problema</button>
                </form>
            </div>
        </div>
    </div>

    <div id="edit-problem-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>✏️ Editar Problema</h2>
                <button class="close-modal" onclick="closeModal('edit-problem-modal')">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="actions.php?action=edit_problem">
                    <input type="hidden" name="id" id="edit-problem-id">
                    <div class="form-group">
                        <label for="edit-problem-title">Título do Problema *</label>
                        <input type="text" name="titulo" id="edit-problem-title" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-problem-solution">Solução *</label>
                        <textarea name="solucao" id="edit-problem-solution" rows="10" required></textarea>
                    </div>
                    <div class="modal-actions">
                        <button type="submit" class="submit-btn">Salvar Alterações</button>
                        <button type="button" class="btn-delete" onclick="deleteProblem()">Excluir Problema</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentProblemId = null;
        const currentSoftwareId = <?php echo $software_id; ?>;

        function showSolution(problemId, title, solution) {
            currentProblemId = problemId;
            document.getElementById('solution-title').textContent = title;
            document.getElementById('solution-text').textContent = solution;
            document.getElementById('solution-display').style.display = 'block';
            
            document.getElementById('edit-solution-btn').onclick = function() {
                openEditProblemModal(problemId, title, solution);
            };
            
            document.getElementById('solution-display').scrollIntoView({ behavior: 'smooth' });
        }

        function openEditProblemModal(problemId, title, solution) {
            document.getElementById('edit-problem-id').value = problemId;
            document.getElementById('edit-problem-title').value = title;
            document.getElementById('edit-problem-solution').value = solution;
            document.getElementById('edit-problem-modal').style.display = 'block';
        }

        function deleteProblem() {
            const problemId = document.getElementById('edit-problem-id').value;
            if (confirm('Tem certeza que deseja excluir este problema?')) {
                window.location.href = 'actions.php?action=delete_problem&id=' + problemId;
            }
        }

        function openAddProblemModal() {
            document.getElementById('add-problem-modal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        document.addEventListener('click', function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    modal.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>
<?php
if (isset($stmt)) $stmt->close();
if (isset($stmt_problems)) $stmt_problems->close();
$conn->close();
?>