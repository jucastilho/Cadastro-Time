<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

$pdo = conectar();
$mensagem = '';
$tipo_msg = '';

if (isset($_GET['deletar'])) {
    $pdo->prepare("DELETE FROM estadios WHERE id = ?")->execute([(int)$_GET['deletar']]);
    $mensagem = 'Estádio removido com sucesso!';
    $tipo_msg = 'sucesso';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = (int)($_POST['id'] ?? 0);
    $nome         = sanitizar($_POST['nome'] ?? '');
    $nome_popular = sanitizar($_POST['nome_popular'] ?? '');
    $lotacao      = $_POST['lotacao'] !== '' ? (int)$_POST['lotacao'] : null;
    $local_id     = (int)($_POST['local_id'] ?? 0) ?: null;

    if ($id > 0) {
        $pdo->prepare("UPDATE estadios SET nome=?, nome_popular=?, lotacao=?, local_id=? WHERE id=?")
            ->execute([$nome, $nome_popular, $lotacao, $local_id, $id]);
        $mensagem = 'Estádio atualizado com sucesso!';
    } else {
        $pdo->prepare("INSERT INTO estadios (nome, nome_popular, lotacao, local_id) VALUES (?,?,?,?)")
            ->execute([$nome, $nome_popular, $lotacao, $local_id]);
        $mensagem = 'Estádio cadastrado com sucesso!';
    }
    $tipo_msg = 'sucesso';
}

$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM estadios WHERE id = ?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}

$cidades  = $pdo->query("SELECT c.id, c.nome, e.nome_estado FROM cidades c LEFT JOIN estado e ON c.estado_id = e.estado_id ORDER BY c.nome")->fetchAll();
$estadios = $pdo->query("SELECT es.*, c.nome AS cidade FROM estadios es LEFT JOIN cidades c ON es.local_id = c.id ORDER BY es.nome")->fetchAll();

$titulo_pagina = 'Estádios';
include __DIR__ . '/layout.php';
?>
<div class="container">
    <div class="page-title">ESTÁ<span>DIOS</span></div>
    <p class="page-sub">Arenas e estádios cadastrados no sistema</p>

    <?php if ($mensagem): ?>
        <div class="alerta alerta-<?= $tipo_msg ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2><?= $editando ? 'Editar Estádio' : 'Novo Estádio' ?></h2>
        <form method="POST">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= $editando['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nome Oficial *</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($editando['nome'] ?? '') ?>" required placeholder="Ex: Neo Química Arena">
                </div>
                <div class="form-group">
                    <label>Nome Popular</label>
                    <input type="text" name="nome_popular" value="<?= htmlspecialchars($editando['nome_popular'] ?? '') ?>" placeholder="Ex: Itaquerão">
                </div>
                <div class="form-group">
                    <label>Lotação</label>
                    <input type="number" name="lotacao" value="<?= $editando['lotacao'] ?? '' ?>" min="0" placeholder="Ex: 49205">
                </div>
                <div class="form-group">
                    <label>Cidade</label>
                    <select name="local_id">
                        <option value="">-- Selecione --</option>
                        <?php foreach ($cidades as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($editando['local_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nome']) ?><?= $c['nome_estado'] ? ' — ' . htmlspecialchars($c['nome_estado']) : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $editando ? 'Atualizar' : 'Cadastrar' ?></button>
                <?php if ($editando): ?>
                    <a href="estadios.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Estádios Cadastrados (<?= count($estadios) ?>)</h2>
        <div class="tabela-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Nome</th><th>Nome Popular</th><th>Lotação</th><th>Cidade</th><th>Ações</th></tr>
                </thead>
                <tbody>
                <?php if (empty($estadios)): ?>
                    <tr class="empty-row"><td colspan="6">Nenhum estádio cadastrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($estadios as $es): ?>
                    <tr>
                        <td><?= $es['id'] ?></td>
                        <td><strong><?= htmlspecialchars($es['nome']) ?></strong></td>
                        <td><?= $es['nome_popular'] ? htmlspecialchars($es['nome_popular']) : '—' ?></td>
                        <td><?= $es['lotacao'] ? number_format($es['lotacao'], 0, ',', '.') : '—' ?></td>
                        <td><?= $es['cidade'] ? '<span class="tag tag-cinza">'.htmlspecialchars($es['cidade']).'</span>' : '—' ?></td>
                        <td style="white-space:nowrap">
                            <a href="?editar=<?= $es['id'] ?>" class="btn btn-edit">Editar</a>
                            <a href="?deletar=<?= $es['id'] ?>" class="btn btn-danger" onclick="return confirm('Remover este estádio?')">Excluir</a>
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