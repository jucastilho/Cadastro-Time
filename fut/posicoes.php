<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

$pdo = conectar();
$mensagem = '';
$tipo_msg = '';

if (isset($_GET['deletar'])) {
    try {
        $pdo->prepare("DELETE FROM posicoes WHERE id = ?")->execute([(int)$_GET['deletar']]);
        $mensagem = 'Posição removida com sucesso!';
        $tipo_msg = 'sucesso';
    } catch (PDOException $e) {
        $mensagem = 'Não é possível remover: esta posição está vinculada a jogadores.';
        $tipo_msg = 'erro';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int)($_POST['id'] ?? 0);
    $nome = sanitizar($_POST['nome'] ?? '');

    if ($id > 0) {
        $pdo->prepare("UPDATE posicoes SET nome=? WHERE id=?")->execute([$nome, $id]);
        $mensagem = 'Posição atualizada com sucesso!';
    } else {
        $pdo->prepare("INSERT INTO posicoes (nome) VALUES (?)")->execute([$nome]);
        $mensagem = 'Posição cadastrada com sucesso!';
    }
    $tipo_msg = 'sucesso';
}

$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM posicoes WHERE id = ?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}

$posicoes = $pdo->query("SELECT p.*, COUNT(j.id) as total FROM posicoes p LEFT JOIN jogadores j ON j.posicao_id = p.id GROUP BY p.id ORDER BY p.nome")->fetchAll();
$titulo_pagina = 'Posições';
include __DIR__ . '/layout.php';
?>
<div class="container">
    <div class="page-title">POSI<span>ÇÕES</span></div>
    <p class="page-sub">Posições em campo dos jogadores</p>

    <?php if ($mensagem): ?>
        <div class="alerta alerta-<?= $tipo_msg ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2><?= $editando ? 'Editar Posição' : 'Nova Posição' ?></h2>
        <form method="POST">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= $editando['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nome da Posição *</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($editando['nome'] ?? '') ?>" required placeholder="Ex: Goleiro, Atacante, Meia...">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $editando ? 'Atualizar' : 'Cadastrar' ?></button>
                <?php if ($editando): ?>
                    <a href="posicoes.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Posições Cadastradas (<?= count($posicoes) ?>)</h2>
        <div class="tabela-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Nome</th><th>Jogadores</th><th>Ações</th></tr>
                </thead>
                <tbody>
                <?php if (empty($posicoes)): ?>
                    <tr class="empty-row"><td colspan="4">Nenhuma posição cadastrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($posicoes as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><strong><?= htmlspecialchars($p['nome']) ?></strong></td>
                        <td><span class="tag tag-verde"><?= $p['total'] ?></span></td>
                        <td style="white-space:nowrap">
                            <a href="?editar=<?= $p['id'] ?>" class="btn btn-edit">Editar</a>
                            <a href="?deletar=<?= $p['id'] ?>" class="btn btn-danger" onclick="return confirm('Remover esta posição?')">Excluir</a>
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
