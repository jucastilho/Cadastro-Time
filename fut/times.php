<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

$pdo = conectar();
$mensagem = '';
$tipo_msg = '';

if (isset($_GET['deletar'])) {
    $pdo->prepare("DELETE FROM times WHERE id = ?")->execute([(int)$_GET['deletar']]);
    $mensagem = 'Time removido com sucesso!';
    $tipo_msg = 'sucesso';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = (int)($_POST['id'] ?? 0);
    $nome         = sanitizar($_POST['nome'] ?? '');
    $nome_completo = sanitizar($_POST['nome_completo'] ?? '');
    $sigla        = sanitizar($_POST['sigla'] ?? '');
    $escudo       = sanitizar($_POST['escudo'] ?? '');
    $local_id     = (int)($_POST['local_id'] ?? 0) ?: null;

    if ($id > 0) {
        $pdo->prepare("UPDATE times SET nome=?, nome_completo=?, sigla=?, escudo=?, local_id=? WHERE id=?")
            ->execute([$nome, $nome_completo, $sigla, $escudo, $local_id, $id]);
        $mensagem = 'Time atualizado com sucesso!';
    } else {
        $pdo->prepare("INSERT INTO times (nome, nome_completo, sigla, escudo, local_id) VALUES (?,?,?,?,?)")
            ->execute([$nome, $nome_completo, $sigla, $escudo, $local_id]);
        $mensagem = 'Time cadastrado com sucesso!';
    }
    $tipo_msg = 'sucesso';
}

$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM times WHERE id = ?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}

$cidades = $pdo->query("SELECT c.id, c.nome, e.nome_estado FROM cidades c LEFT JOIN estado e ON c.estado_id = e.estado_id ORDER BY c.nome")->fetchAll();
$times   = $pdo->query("SELECT t.*, c.nome AS cidade FROM times t LEFT JOIN cidades c ON t.local_id = c.id ORDER BY t.nome")->fetchAll();

$titulo_pagina = 'Times';
include __DIR__ . '/layout.php';
?>
<div class="container">
    <div class="page-title">TI<span>MES</span></div>
    <p class="page-sub">Clubes e equipes cadastrados no sistema</p>

    <?php if ($mensagem): ?>
        <div class="alerta alerta-<?= $tipo_msg ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2><?= $editando ? 'Editar Time' : 'Novo Time' ?></h2>
        <form method="POST">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= $editando['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($editando['nome'] ?? '') ?>" required placeholder="Ex: Corinthians">
                </div>
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome_completo" value="<?= htmlspecialchars($editando['nome_completo'] ?? '') ?>" placeholder="Ex: Sport Club Corinthians Paulista">
                </div>
                <div class="form-group">
                    <label>Sigla</label>
                    <input type="text" name="sigla" value="<?= htmlspecialchars($editando['sigla'] ?? '') ?>" maxlength="3" placeholder="Ex: COR">
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
                <div class="form-group full">
                    <label>URL do Escudo</label>
                    <input type="text" name="escudo" value="<?= htmlspecialchars($editando['escudo'] ?? '') ?>" placeholder="https://exemplo.com/escudo.png">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $editando ? 'Atualizar' : 'Cadastrar' ?></button>
                <?php if ($editando): ?>
                    <a href="times.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Times Cadastrados (<?= count($times) ?>)</h2>
        <div class="tabela-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Escudo</th><th>Nome</th><th>Nome Completo</th><th>Sigla</th><th>Cidade</th><th>Ações</th></tr>
                </thead>
                <tbody>
                <?php if (empty($times)): ?>
                    <tr class="empty-row"><td colspan="7">Nenhum time cadastrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($times as $t): ?>
                    <tr>
                        <td><?= $t['id'] ?></td>
                        <td>
                            <?php if ($t['escudo']): ?>
                                <img src="<?= htmlspecialchars($t['escudo']) ?>" alt="escudo" style="height:28px;width:28px;object-fit:contain;vertical-align:middle;">
                            <?php else: ?>
                                <span style="color:#333;font-size:1.2rem;">🛡️</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($t['nome']) ?></strong></td>
                        <td><?= $t['nome_completo'] ? htmlspecialchars($t['nome_completo']) : '—' ?></td>
                        <td><?= $t['sigla'] ? '<span class="tag tag-verde">'.htmlspecialchars($t['sigla']).'</span>' : '—' ?></td>
                        <td><?= $t['cidade'] ? '<span class="tag tag-cinza">'.htmlspecialchars($t['cidade']).'</span>' : '—' ?></td>
                        <td style="white-space:nowrap">
                            <a href="?editar=<?= $t['id'] ?>" class="btn btn-edit">Editar</a>
                            <a href="?deletar=<?= $t['id'] ?>" class="btn btn-danger" onclick="return confirm('Remover este time?')">Excluir</a>
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