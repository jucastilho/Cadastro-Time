<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

$pdo = conectar();
$mensagem = '';
$tipo_msg = '';

if (isset($_GET['deletar'])) {
    $pdo->prepare("DELETE FROM tecnicos WHERE id = ?")->execute([(int)$_GET['deletar']]);
    $mensagem = 'Técnico removido com sucesso!';
    $tipo_msg = 'sucesso';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = (int)($_POST['id'] ?? 0);
    $nome       = sanitizar($_POST['nome'] ?? '');
    $descricao  = trim($_POST['descricao'] ?? '');
    $data_nasc  = $_POST['data_nascimento'] ?: null;
    $data_falec = $_POST['data_falecimento'] ?: null;
    $time       = isset($_POST['time']) ? 1 : 0;

    if ($id > 0) {
        $pdo->prepare("UPDATE tecnicos SET nome=?, descricao=?, data_nascimento=?, data_falecimento=?, time=? WHERE id=?")
            ->execute([$nome, $descricao, $data_nasc, $data_falec, $time, $id]);
        $mensagem = 'Técnico atualizado com sucesso!';
    } else {
        $pdo->prepare("INSERT INTO tecnicos (nome, descricao, data_nascimento, data_falecimento, time) VALUES (?,?,?,?,?)")
            ->execute([$nome, $descricao, $data_nasc, $data_falec, $time]);
        $mensagem = 'Técnico cadastrado com sucesso!';
    }
    $tipo_msg = 'sucesso';
}

$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM tecnicos WHERE id = ?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}

$tecnicos = $pdo->query("SELECT * FROM tecnicos ORDER BY nome")->fetchAll();
$titulo_pagina = 'Técnicos';
include __DIR__ . '/layout.php';
?>
<div class="container">
    <div class="page-title">TÉC<span>NICOS</span></div>
    <p class="page-sub">Treinadores e comissão técnica do clube</p>

    <?php if ($mensagem): ?>
        <div class="alerta alerta-<?= $tipo_msg ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2><?= $editando ? 'Editar Técnico' : 'Novo Técnico' ?></h2>
        <form method="POST">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= $editando['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($editando['nome'] ?? '') ?>" required placeholder="Nome do técnico">
                </div>
                <div class="form-group">
                    <label>Data de Nascimento</label>
                    <input type="date" name="data_nascimento" value="<?= $editando['data_nascimento'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label>Data de Falecimento</label>
                    <input type="date" name="data_falecimento" value="<?= $editando['data_falecimento'] ?? '' ?>">
                </div>
                <div class="form-group" style="justify-content: flex-end;">
                    <label class="form-check">
                        <input type="checkbox" name="time" value="1" <?= ($editando['time'] ?? 0) ? 'checked' : '' ?>>
                        Técnico do time atual
                    </label>
                </div>
                <div class="form-group full">
                    <label>Descrição / Biografia</label>
                    <textarea name="descricao" placeholder="Histórico profissional do técnico..."><?= htmlspecialchars($editando['descricao'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $editando ? 'Atualizar' : 'Cadastrar' ?></button>
                <?php if ($editando): ?>
                    <a href="tecnicos.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Técnicos Cadastrados (<?= count($tecnicos) ?>)</h2>
        <div class="tabela-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Nome</th><th>Nascimento</th><th>Falecimento</th><th>Time Atual</th><th>Ações</th></tr>
                </thead>
                <tbody>
                <?php if (empty($tecnicos)): ?>
                    <tr class="empty-row"><td colspan="6">Nenhum técnico cadastrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($tecnicos as $t): ?>
                    <tr>
                        <td><?= $t['id'] ?></td>
                        <td><strong><?= htmlspecialchars($t['nome']) ?></strong></td>
                        <td><?= $t['data_nascimento'] ? date('d/m/Y', strtotime($t['data_nascimento'])) : '—' ?></td>
                        <td><?= $t['data_falecimento'] ? date('d/m/Y', strtotime($t['data_falecimento'])) : '—' ?></td>
                        <td><?= $t['time'] ? '<span class="tag tag-verde">Sim</span>' : '<span class="tag tag-cinza">Não</span>' ?></td>
                        <td style="white-space:nowrap">
                            <a href="?editar=<?= $t['id'] ?>" class="btn btn-edit">Editar</a>
                            <a href="?deletar=<?= $t['id'] ?>" class="btn btn-danger" onclick="return confirm('Remover este técnico?')">Excluir</a>
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
