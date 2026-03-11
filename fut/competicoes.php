<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

$pdo = conectar();
$mensagem = '';
$tipo_msg = '';

if (isset($_GET['deletar'])) {
    $pdo->prepare("DELETE FROM competicoes WHERE id = ?")->execute([(int)$_GET['deletar']]);
    $mensagem = 'Competição removida com sucesso!';
    $tipo_msg = 'sucesso';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id            = (int)($_POST['id'] ?? 0);
    $nome          = sanitizar($_POST['nome'] ?? '');
    $internacional = isset($_POST['internacional']) ? 1 : 0;

    if ($id > 0) {
        $pdo->prepare("UPDATE competicoes SET nome=?, internacional=? WHERE id=?")->execute([$nome, $internacional, $id]);
        $mensagem = 'Competição atualizada com sucesso!';
    } else {
        $pdo->prepare("INSERT INTO competicoes (nome, internacional) VALUES (?,?)")->execute([$nome, $internacional]);
        $mensagem = 'Competição cadastrada com sucesso!';
    }
    $tipo_msg = 'sucesso';
}

$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM competicoes WHERE id = ?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}

$competicoes = $pdo->query("SELECT * FROM competicoes ORDER BY nome")->fetchAll();
$titulo_pagina = 'Competições';
include __DIR__ . '/layout.php';
?>
<div class="container">
    <div class="page-title">COMPETI<span>ÇÕES</span></div>
    <p class="page-sub">Campeonatos nacionais e internacionais</p>

    <?php if ($mensagem): ?>
        <div class="alerta alerta-<?= $tipo_msg ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2><?= $editando ? 'Editar Competição' : 'Nova Competição' ?></h2>
        <form method="POST">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= $editando['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nome da Competição *</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($editando['nome'] ?? '') ?>" required placeholder="Ex: Campeonato Brasileiro">
                </div>
                <div class="form-group" style="justify-content: flex-end;">
                    <label class="form-check">
                        <input type="checkbox" name="internacional" value="1" <?= ($editando['internacional'] ?? 0) ? 'checked' : '' ?>>
                        Competição Internacional
                    </label>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $editando ? 'Atualizar' : 'Cadastrar' ?></button>
                <?php if ($editando): ?>
                    <a href="competicoes.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Competições Cadastradas (<?= count($competicoes) ?>)</h2>
        <div class="tabela-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Nome</th><th>Tipo</th><th>Ações</th></tr>
                </thead>
                <tbody>
                <?php if (empty($competicoes)): ?>
                    <tr class="empty-row"><td colspan="4">Nenhuma competição cadastrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($competicoes as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><strong><?= htmlspecialchars($c['nome']) ?></strong></td>
                        <td><?= $c['internacional'] ? '<span class="tag tag-verde">Internacional</span>' : '<span class="tag tag-cinza">Nacional</span>' ?></td>
                        <td style="white-space:nowrap">
                            <a href="?editar=<?= $c['id'] ?>" class="btn btn-edit">Editar</a>
                            <a href="?deletar=<?= $c['id'] ?>" class="btn btn-danger" onclick="return confirm('Remover esta competição?')">Excluir</a>
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
