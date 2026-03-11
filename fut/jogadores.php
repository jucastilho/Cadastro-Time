<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

$pdo = conectar();
$mensagem = '';
$tipo_msg = '';

// DELETAR
if (isset($_GET['deletar'])) {
    $pdo->prepare("DELETE FROM jogadores WHERE id = ?")->execute([(int)$_GET['deletar']]);
    $mensagem = 'Jogador removido com sucesso!';
    $tipo_msg = 'sucesso';
}

// SALVAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id            = (int)($_POST['id'] ?? 0);
    $posicao_id    = (int)($_POST['posicao_id'] ?? 0) ?: null;
    $gols_sofridos = $_POST['gols_sofridos'] !== '' ? (int)$_POST['gols_sofridos'] : null;
    $nome          = sanitizar($_POST['nome'] ?? '');
    $nome_real     = sanitizar($_POST['nome_real'] ?? '');
    $descricao     = trim($_POST['descricao'] ?? '');
    $titulos       = trim($_POST['titulos'] ?? '');
    $data_nasc     = $_POST['data_nascimento'] ?: null;
    $data_falec    = $_POST['data_falecimento'] ?: null;
    $time          = isset($_POST['time']) ? 1 : 0;

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE jogadores SET posicao_id=?, gols_sofridos=?, nome=?, nome_real=?, descricao=?, titulos=?, data_nascimento=?, data_falecimento=?, time=? WHERE id=?");
        $stmt->execute([$posicao_id, $gols_sofridos, $nome, $nome_real, $descricao, $titulos, $data_nasc, $data_falec, $time, $id]);
        $mensagem = 'Jogador atualizado com sucesso!';
    } else {
        $stmt = $pdo->prepare("INSERT INTO jogadores (posicao_id, gols_sofridos, nome, nome_real, descricao, titulos, data_nascimento, data_falecimento, time) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$posicao_id, $gols_sofridos, $nome, $nome_real, $descricao, $titulos, $data_nasc, $data_falec, $time]);
        $mensagem = 'Jogador cadastrado com sucesso!';
    }
    $tipo_msg = 'sucesso';
}

// EDITAR
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM jogadores WHERE id = ?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}

$posicoes  = $pdo->query("SELECT * FROM posicoes ORDER BY nome")->fetchAll();
$jogadores = $pdo->query("SELECT j.*, p.nome AS posicao FROM jogadores j LEFT JOIN posicoes p ON j.posicao_id = p.id ORDER BY j.nome")->fetchAll();

$titulo_pagina = 'Jogadores';
include __DIR__ . '/layout.php';
?>
<div class="container">
    <div class="page-title">JOGA<span>DORES</span></div>
    <p class="page-sub">Cadastre e gerencie os atletas do clube</p>

    <?php if ($mensagem): ?>
        <div class="alerta alerta-<?= $tipo_msg ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2><?= $editando ? 'Editar Jogador' : 'Novo Jogador' ?></h2>
        <form method="POST">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= $editando['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nome (apelido) *</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($editando['nome'] ?? '') ?>" required placeholder="Ex: Pelé">
                </div>
                <div class="form-group">
                    <label>Nome Real</label>
                    <input type="text" name="nome_real" value="<?= htmlspecialchars($editando['nome_real'] ?? '') ?>" placeholder="Ex: Edson Arantes do Nascimento">
                </div>
                <div class="form-group">
                    <label>Posição</label>
                    <select name="posicao_id">
                        <option value="">-- Selecione --</option>
                        <?php foreach ($posicoes as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ($editando['posicao_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Gols Sofridos</label>
                    <input type="number" name="gols_sofridos" value="<?= $editando['gols_sofridos'] ?? '' ?>" min="0" placeholder="0">
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
                        Faz parte do time atual
                    </label>
                </div>
                <div class="form-group full">
                    <label>Descrição</label>
                    <textarea name="descricao" placeholder="Histórico e informações do jogador..."><?= htmlspecialchars($editando['descricao'] ?? '') ?></textarea>
                </div>
                <div class="form-group full">
                    <label>Títulos</label>
                    <textarea name="titulos" placeholder="Liste os títulos conquistados..."><?= htmlspecialchars($editando['titulos'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $editando ? 'Atualizar' : 'Cadastrar' ?></button>
                <?php if ($editando): ?>
                    <a href="jogadores.php" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Jogadores Cadastrados (<?= count($jogadores) ?>)</h2>
        <div class="tabela-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Nome Real</th>
                        <th>Posição</th>
                        <th>Gols Sofridos</th>
                        <th>Nascimento</th>
                        <th>Time Atual</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($jogadores)): ?>
                    <tr class="empty-row"><td colspan="8">Nenhum jogador cadastrado ainda.</td></tr>
                <?php else: ?>
                    <?php foreach ($jogadores as $j): ?>
                    <tr>
                        <td><?= $j['id'] ?></td>
                        <td><strong><?= htmlspecialchars($j['nome']) ?></strong></td>
                        <td><?= htmlspecialchars($j['nome_real'] ?? '—') ?></td>
                        <td><?= $j['posicao'] ? '<span class="tag tag-verde">'.htmlspecialchars($j['posicao']).'</span>' : '—' ?></td>
                        <td><?= $j['gols_sofridos'] ?? '—' ?></td>
                        <td><?= $j['data_nascimento'] ? date('d/m/Y', strtotime($j['data_nascimento'])) : '—' ?></td>
                        <td><?= $j['time'] ? '<span class="tag tag-verde">Sim</span>' : '<span class="tag tag-cinza">Não</span>' ?></td>
                        <td style="white-space:nowrap">
                            <a href="?editar=<?= $j['id'] ?>" class="btn btn-edit">Editar</a>
                            <a href="?deletar=<?= $j['id'] ?>" class="btn btn-danger" onclick="return confirm('Remover este jogador?')">Excluir</a>
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
