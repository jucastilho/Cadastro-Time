<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

$pdo = conectar();
$mensagem = '';
$tipo_msg = '';

if (isset($_GET['deletar'])) {
    $pdo->prepare("DELETE FROM arbitros WHERE id = ?")->execute([(int)$_GET['deletar']]);
    $mensagem = 'Árbitro removido com sucesso!';
    $tipo_msg = 'sucesso';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = (int)($_POST['id'] ?? 0);
    $nome         = sanitizar($_POST['nome'] ?? '');
    $nacionalidade = sanitizar($_POST['nacionalidade'] ?? '');
    $descricao    = trim($_POST['descricao'] ?? '');
    $foto         = sanitizar($_POST['foto'] ?? '');
    $data_nasc    = $_POST['data_nascimento'] ?: null;
    $data_falec   = $_POST['data_falecimento'] ?: null;

    if ($id > 0) {
        $pdo->prepare("UPDATE arbitros SET nome=?, nacionalidade=?, descricao=?, foto=?, data_nascimento=?, data_falecimento=? WHERE id=?")
            ->execute([$nome, $nacionalidade, $descricao, $foto, $data_nasc, $data_falec, $id]);
        $mensagem = 'Árbitro atualizado com sucesso!';
    } else {
        $pdo->prepare("INSERT INTO arbitros (nome, nacionalidade, descricao, foto, data_nascimento, data_falecimento) VALUES (?,?,?,?,?,?)")
            ->execute([$nome, $nacionalidade, $descricao, $foto, $data_nasc, $data_falec]);
        $mensagem = 'Árbitro cadastrado com sucesso!';
    }
    $tipo_msg = 'sucesso';
}

$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM arbitros WHERE id = ?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}

$arbitros = $pdo->query("SELECT * FROM arbitros ORDER BY nome")->fetchAll();
$titulo_pagina = 'Árbitros';
include __DIR__ . '/layout.php';
?>
<div class="container">
    <div class="page-title">ÁRBI<span>TROS</span></div>
    <p class="page-sub">Cadastre e gerencie os árbitros</p>

    <?php if ($mensagem): ?>
        <div class="alerta alerta-<?= $tipo_msg ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2><?= $editando ? 'Editar Árbitro' : 'Novo Árbitro' ?></h2>
        <form method="POST">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= $editando['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($editando['nome'] ?? '') ?>" required placeholder="Nome do árbitro">
                </div>
                <div class="form-group">
                    <label>Nacionalidade</label>
                    <input type="text" name="nacionalidade" value="<?= htmlspecialchars($editando['nacionalidade'] ?? '') ?>" placeholder="Ex: Brasileiro">
                </div>
                <div class="form-group">
                    <label>Data de Nascimento</label>
                    <input type="date" name="data_nascimento" value="<?= $editando['data_nascimento'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label>Data de Falecimento</label>
                    <input type="date" name="data_falecimento" value="<?= $editando['data_falecimento'] ?? '' ?>">
                </div>
                <div class="form-group full">
                    <label>URL da Foto</label>
                    <input type="text" name="foto" value="<?= htmlspecialchars($editando['foto'] ?? '') ?>" placeholder="https://exemplo.com/foto.jpg">
                </div>
                <div class="form-group full">
                    <label>Descrição / Biografia</label>
                    <textarea name="descricao" placeholder="Histórico e informações do árbitro..."><?= htmlspecialchars($editando['descricao'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $editando ? 'Atualizar' : 'Cadastrar' ?></button>
                <?php if ($editando): ?>
                    <a href="arbitros.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Árbitros Cadastrados (<?= count($arbitros) ?>)</h2>
        <div class="tabela-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Nome</th><th>Nacionalidade</th><th>Nascimento</th><th>Falecimento</th><th>Ações</th></tr>
                </thead>
                <tbody>
                <?php if (empty($arbitros)): ?>
                    <tr class="empty-row"><td colspan="6">Nenhum árbitro cadastrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($arbitros as $a): ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td><strong><?= htmlspecialchars($a['nome']) ?></strong></td>
                        <td><?= $a['nacionalidade'] ? '<span class="tag tag-cinza">'.htmlspecialchars($a['nacionalidade']).'</span>' : '—' ?></td>
                        <td><?= $a['data_nascimento'] ? date('d/m/Y', strtotime($a['data_nascimento'])) : '—' ?></td>
                        <td><?= $a['data_falecimento'] ? date('d/m/Y', strtotime($a['data_falecimento'])) : '—' ?></td>
                        <td style="white-space:nowrap">
                            <a href="?editar=<?= $a['id'] ?>" class="btn btn-edit">Editar</a>
                            <a href="?deletar=<?= $a['id'] ?>" class="btn btn-danger" onclick="return confirm('Remover este árbitro?')">Excluir</a>
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