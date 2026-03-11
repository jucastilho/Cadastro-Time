<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

$pdo = conectar();
$mensagem = '';
$tipo_msg = '';

if (isset($_GET['deletar'])) {
    $pdo->prepare("DELETE FROM patrocinadores WHERE id = ?")->execute([(int)$_GET['deletar']]);
    $mensagem = 'Patrocinador removido com sucesso!';
    $tipo_msg = 'sucesso';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int)($_POST['id'] ?? 0);
    $nome = sanitizar($_POST['nome'] ?? '');

    if ($id > 0) {
        $pdo->prepare("UPDATE patrocinadores SET nome=? WHERE id=?")->execute([$nome, $id]);
        $mensagem = 'Patrocinador atualizado com sucesso!';
    } else {
        $pdo->prepare("INSERT INTO patrocinadores (nome) VALUES (?)")->execute([$nome]);
        $mensagem = 'Patrocinador cadastrado com sucesso!';
    }
    $tipo_msg = 'sucesso';
}

$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM patrocinadores WHERE id = ?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}

$patrocinadores = $pdo->query("SELECT * FROM patrocinadores ORDER BY nome")->fetchAll();
$titulo_pagina = 'Patrocinadores';
include __DIR__ . '/layout.php';
?>
<div class="container">
    <div class="page-title">PATROCI<span>NADORES</span></div>
    <p class="page-sub">Empresas e marcas patrocinadoras do clube</p>

    <?php if ($mensagem): ?>
        <div class="alerta alerta-<?= $tipo_msg ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2><?= $editando ? 'Editar Patrocinador' : 'Novo Patrocinador' ?></h2>
        <form method="POST">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= $editando['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($editando['nome'] ?? '') ?>" required placeholder="Ex: Nike, Banco do Brasil...">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $editando ? 'Atualizar' : 'Cadastrar' ?></button>
                <?php if ($editando): ?>
                    <a href="patrocinadores.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Patrocinadores Cadastrados (<?= count($patrocinadores) ?>)</h2>
        <div class="tabela-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Nome</th><th>Ações</th></tr>
                </thead>
                <tbody>
                <?php if (empty($patrocinadores)): ?>
                    <tr class="empty-row"><td colspan="3">Nenhum patrocinador cadastrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($patrocinadores as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><strong><?= htmlspecialchars($p['nome']) ?></strong></td>
                        <td style="white-space:nowrap">
                            <a href="?editar=<?= $p['id'] ?>" class="btn btn-edit">Editar</a>
                            <a href="?deletar=<?= $p['id'] ?>" class="btn btn-danger" onclick="return confirm('Remover este patrocinador?')">Excluir</a>
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