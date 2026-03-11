<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

$pdo = conectar();
$mensagem = '';
$tipo_msg = '';

if (isset($_GET['deletar'])) {
    $pdo->prepare("DELETE FROM uniformes WHERE id = ?")->execute([(int)$_GET['deletar']]);
    $mensagem = 'Uniforme removido com sucesso!';
    $tipo_msg = 'sucesso';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int)($_POST['id'] ?? 0);
    $tipo = sanitizar($_POST['tipo'] ?? '');
    $nome = sanitizar($_POST['nome'] ?? '');

    if ($id > 0) {
        $pdo->prepare("UPDATE uniformes SET tipo=?, nome=? WHERE id=?")->execute([$tipo, $nome, $id]);
        $mensagem = 'Uniforme atualizado com sucesso!';
    } else {
        $pdo->prepare("INSERT INTO uniformes (tipo, nome) VALUES (?,?)")->execute([$tipo, $nome]);
        $mensagem = 'Uniforme cadastrado com sucesso!';
    }
    $tipo_msg = 'sucesso';
}

$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM uniformes WHERE id = ?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}

$uniformes = $pdo->query("SELECT * FROM uniformes ORDER BY tipo, nome")->fetchAll();
$titulo_pagina = 'Uniformes';
include __DIR__ . '/layout.php';
?>
<div class="container">
    <div class="page-title">UNIFOR<span>MES</span></div>
    <p class="page-sub">Gerencie camisas, calções e meiões</p>

    <?php if ($mensagem): ?>
        <div class="alerta alerta-<?= $tipo_msg ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2><?= $editando ? 'Editar Uniforme' : 'Novo Uniforme' ?></h2>
        <form method="POST">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= $editando['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Tipo *</label>
                    <select name="tipo" required>
                        <option value="">-- Selecione --</option>
                        <option value="camisa" <?= ($editando['tipo'] ?? '') === 'camisa' ? 'selected' : '' ?>>Camisa</option>
                        <option value="calção" <?= ($editando['tipo'] ?? '') === 'calção' ? 'selected' : '' ?>>Calção</option>
                        <option value="meião" <?= ($editando['tipo'] ?? '') === 'meião'  ? 'selected' : '' ?>>Meião</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nome / Descrição *</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($editando['nome'] ?? '') ?>" required placeholder="Ex: Camisa Titular 2024">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $editando ? 'Atualizar' : 'Cadastrar' ?></button>
                <?php if ($editando): ?>
                    <a href="uniformes.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Uniformes Cadastrados (<?= count($uniformes) ?>)</h2>
        <div class="tabela-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Tipo</th><th>Nome</th><th>Ações</th></tr>
                </thead>
                <tbody>
                <?php if (empty($uniformes)): ?>
                    <tr class="empty-row"><td colspan="4">Nenhum uniforme cadastrado.</td></tr>
                <?php else: ?>
                    <?php
                    $labels = ['camisa' => 'tag-verde', 'calção' => 'tag-amarelo', 'meião' => 'tag-cinza'];
                    foreach ($uniformes as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><span class="tag <?= $labels[$u['tipo']] ?? 'tag-cinza' ?>"><?= htmlspecialchars($u['tipo']) ?></span></td>
                        <td><strong><?= htmlspecialchars($u['nome'] ?? '—') ?></strong></td>
                        <td style="white-space:nowrap">
                            <a href="?editar=<?= $u['id'] ?>" class="btn btn-edit">Editar</a>
                            <a href="?deletar=<?= $u['id'] ?>" class="btn btn-danger" onclick="return confirm('Remover este uniforme?')">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<footer>Sistema de Futebol &mdash; PHP PDO &amp; MySQL</footer>
</body></html>
