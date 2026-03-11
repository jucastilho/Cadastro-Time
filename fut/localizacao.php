<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

$pdo = conectar();
$mensagem = '';
$tipo_msg = '';
$aba = $_GET['aba'] ?? 'pais';

// ======================== PAÍSES ========================
if ($aba === 'pais') {
    if (isset($_GET['deletar'])) {
        try {
            $pdo->prepare("DELETE FROM pais WHERE pais_id = ?")->execute([(int)$_GET['deletar']]);
            $mensagem = 'País removido com sucesso!'; $tipo_msg = 'sucesso';
        } catch (PDOException $e) {
            $mensagem = 'Não é possível remover: existem estados vinculados a este país.'; $tipo_msg = 'erro';
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_pais'])) {
        $id   = (int)($_POST['id'] ?? 0);
        $nome = sanitizar($_POST['nome_pais'] ?? '');
        if ($id > 0) {
            $pdo->prepare("UPDATE pais SET nome_pais=? WHERE pais_id=?")->execute([$nome, $id]);
            $mensagem = 'País atualizado com sucesso!';
        } else {
            $pdo->prepare("INSERT INTO pais (nome_pais) VALUES (?)")->execute([$nome]);
            $mensagem = 'País cadastrado com sucesso!';
        }
        $tipo_msg = 'sucesso';
    }
}

// ======================== ESTADOS ========================
if ($aba === 'estado') {
    if (isset($_GET['deletar'])) {
        try {
            $pdo->prepare("DELETE FROM estado WHERE estado_id = ?")->execute([(int)$_GET['deletar']]);
            $mensagem = 'Estado removido com sucesso!'; $tipo_msg = 'sucesso';
        } catch (PDOException $e) {
            $mensagem = 'Não é possível remover: existem cidades vinculadas a este estado.'; $tipo_msg = 'erro';
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_estado'])) {
        $id      = (int)($_POST['id'] ?? 0);
        $nome    = sanitizar($_POST['nome_estado'] ?? '');
        $pais_id = (int)($_POST['pais_id'] ?? 0) ?: null;
        if ($id > 0) {
            $pdo->prepare("UPDATE estado SET nome_estado=?, pais_id=? WHERE estado_id=?")->execute([$nome, $pais_id, $id]);
            $mensagem = 'Estado atualizado com sucesso!';
        } else {
            $pdo->prepare("INSERT INTO estado (nome_estado, pais_id) VALUES (?,?)")->execute([$nome, $pais_id]);
            $mensagem = 'Estado cadastrado com sucesso!';
        }
        $tipo_msg = 'sucesso';
    }
}

// ======================== CIDADES ========================
if ($aba === 'cidade') {
    if (isset($_GET['deletar'])) {
        $pdo->prepare("DELETE FROM cidades WHERE id = ?")->execute([(int)$_GET['deletar']]);
        $mensagem = 'Cidade removida com sucesso!'; $tipo_msg = 'sucesso';
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_cidade'])) {
        $id        = (int)($_POST['id'] ?? 0);
        $nome      = sanitizar($_POST['nome_cidade'] ?? '');
        $estado_id = (int)($_POST['estado_id'] ?? 0) ?: null;
        if ($id > 0) {
            $pdo->prepare("UPDATE cidades SET nome=?, estado_id=? WHERE id=?")->execute([$nome, $estado_id, $id]);
            $mensagem = 'Cidade atualizada com sucesso!';
        } else {
            $pdo->prepare("INSERT INTO cidades (nome, estado_id) VALUES (?,?)")->execute([$nome, $estado_id]);
            $mensagem = 'Cidade cadastrada com sucesso!';
        }
        $tipo_msg = 'sucesso';
    }
}

// Listas
$paises  = $pdo->query("SELECT * FROM pais ORDER BY nome_pais")->fetchAll();
$estados = $pdo->query("SELECT e.*, p.nome_pais FROM estado e LEFT JOIN pais p ON e.pais_id = p.pais_id ORDER BY e.nome_estado")->fetchAll();
$cidades = $pdo->query("SELECT c.*, e.nome_estado FROM cidades c LEFT JOIN estado e ON c.estado_id = e.estado_id ORDER BY c.nome")->fetchAll();

// Editar
$edit_pais = $edit_estado = $edit_cidade = null;
if (isset($_GET['editar'])) {
    $eid = (int)$_GET['editar'];
    if ($aba === 'pais')   { $s = $pdo->prepare("SELECT * FROM pais WHERE pais_id=?");     $s->execute([$eid]); $edit_pais   = $s->fetch(); }
    if ($aba === 'estado') { $s = $pdo->prepare("SELECT * FROM estado WHERE estado_id=?"); $s->execute([$eid]); $edit_estado = $s->fetch(); }
    if ($aba === 'cidade') { $s = $pdo->prepare("SELECT * FROM cidades WHERE id=?");       $s->execute([$eid]); $edit_cidade = $s->fetch(); }
}

$titulo_pagina = 'Localização';
include __DIR__ . '/layout.php';
?>
<div class="container">
    <div class="page-title">LOCALI<span>ZAÇÃO</span></div>
    <p class="page-sub">Gerencie países, estados e cidades</p>

    <?php if ($mensagem): ?>
        <div class="alerta alerta-<?= $tipo_msg ?>"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <div class="abas">
        <a href="?aba=pais"   class="aba <?= $aba === 'pais'   ? 'ativo' : '' ?>">Países (<?= count($paises) ?>)</a>
        <a href="?aba=estado" class="aba <?= $aba === 'estado' ? 'ativo' : '' ?>">Estados (<?= count($estados) ?>)</a>
        <a href="?aba=cidade" class="aba <?= $aba === 'cidade' ? 'ativo' : '' ?>">Cidades (<?= count($cidades) ?>)</a>
    </div>

    <?php if ($aba === 'pais'): ?>
    <!-- PAÍSES -->
    <div class="card">
        <h2><?= $edit_pais ? 'Editar País' : 'Novo País' ?></h2>
        <form method="POST">
            <input type="hidden" name="salvar_pais" value="1">
            <?php if ($edit_pais): ?><input type="hidden" name="id" value="<?= $edit_pais['pais_id'] ?>"><?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nome do País *</label>
                    <input type="text" name="nome_pais" value="<?= htmlspecialchars($edit_pais['nome_pais'] ?? '') ?>" required placeholder="Ex: Brasil">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $edit_pais ? 'Atualizar' : 'Cadastrar' ?></button>
                <?php if ($edit_pais): ?><a href="?aba=pais" class="btn btn-secondary">Cancelar</a><?php endif; ?>
            </div>
        </form>
    </div>
    <div class="card">
        <h2>Países Cadastrados</h2>
        <div class="tabela-wrap"><table>
            <thead><tr><th>#</th><th>País</th><th>Ações</th></tr></thead>
            <tbody>
            <?php if (empty($paises)): ?>
                <tr class="empty-row"><td colspan="3">Nenhum país cadastrado.</td></tr>
            <?php else: foreach ($paises as $p): ?>
                <tr>
                    <td><?= $p['pais_id'] ?></td>
                    <td><strong><?= htmlspecialchars($p['nome_pais']) ?></strong></td>
                    <td style="white-space:nowrap">
                        <a href="?aba=pais&editar=<?= $p['pais_id'] ?>" class="btn btn-edit">Editar</a>
                        <a href="?aba=pais&deletar=<?= $p['pais_id'] ?>" class="btn btn-danger" onclick="return confirm('Remover este país?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>

    <?php elseif ($aba === 'estado'): ?>
    <!-- ESTADOS -->
    <div class="card">
        <h2><?= $edit_estado ? 'Editar Estado' : 'Novo Estado' ?></h2>
        <form method="POST">
            <input type="hidden" name="salvar_estado" value="1">
            <?php if ($edit_estado): ?><input type="hidden" name="id" value="<?= $edit_estado['estado_id'] ?>"><?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nome do Estado *</label>
                    <input type="text" name="nome_estado" value="<?= htmlspecialchars($edit_estado['nome_estado'] ?? '') ?>" required placeholder="Ex: São Paulo">
                </div>
                <div class="form-group">
                    <label>País</label>
                    <select name="pais_id">
                        <option value="">-- Selecione --</option>
                        <?php foreach ($paises as $p): ?>
                            <option value="<?= $p['pais_id'] ?>" <?= ($edit_estado['pais_id'] ?? '') == $p['pais_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nome_pais']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $edit_estado ? 'Atualizar' : 'Cadastrar' ?></button>
                <?php if ($edit_estado): ?><a href="?aba=estado" class="btn btn-secondary">Cancelar</a><?php endif; ?>
            </div>
        </form>
    </div>
    <div class="card">
        <h2>Estados Cadastrados</h2>
        <div class="tabela-wrap"><table>
            <thead><tr><th>#</th><th>Estado</th><th>País</th><th>Ações</th></tr></thead>
            <tbody>
            <?php if (empty($estados)): ?>
                <tr class="empty-row"><td colspan="4">Nenhum estado cadastrado.</td></tr>
            <?php else: foreach ($estados as $e): ?>
                <tr>
                    <td><?= $e['estado_id'] ?></td>
                    <td><strong><?= htmlspecialchars($e['nome_estado']) ?></strong></td>
                    <td><span class="tag tag-cinza"><?= htmlspecialchars($e['nome_pais'] ?? '—') ?></span></td>
                    <td style="white-space:nowrap">
                        <a href="?aba=estado&editar=<?= $e['estado_id'] ?>" class="btn btn-edit">Editar</a>
                        <a href="?aba=estado&deletar=<?= $e['estado_id'] ?>" class="btn btn-danger" onclick="return confirm('Remover este estado?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>

    <?php elseif ($aba === 'cidade'): ?>
    <!-- CIDADES -->
    <div class="card">
        <h2><?= $edit_cidade ? 'Editar Cidade' : 'Nova Cidade' ?></h2>
        <form method="POST">
            <input type="hidden" name="salvar_cidade" value="1">
            <?php if ($edit_cidade): ?><input type="hidden" name="id" value="<?= $edit_cidade['id'] ?>"><?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nome da Cidade *</label>
                    <input type="text" name="nome_cidade" value="<?= htmlspecialchars($edit_cidade['nome'] ?? '') ?>" required placeholder="Ex: São Paulo">
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado_id">
                        <option value="">-- Selecione --</option>
                        <?php foreach ($estados as $e): ?>
                            <option value="<?= $e['estado_id'] ?>" <?= ($edit_cidade['estado_id'] ?? '') == $e['estado_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['nome_estado']) ?><?= $e['nome_pais'] ? ' (' . htmlspecialchars($e['nome_pais']) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $edit_cidade ? 'Atualizar' : 'Cadastrar' ?></button>
                <?php if ($edit_cidade): ?><a href="?aba=cidade" class="btn btn-secondary">Cancelar</a><?php endif; ?>
            </div>
        </form>
    </div>
    <div class="card">
        <h2>Cidades Cadastradas</h2>
        <div class="tabela-wrap"><table>
            <thead><tr><th>#</th><th>Cidade</th><th>Estado</th><th>Ações</th></tr></thead>
            <tbody>
            <?php if (empty($cidades)): ?>
                <tr class="empty-row"><td colspan="4">Nenhuma cidade cadastrada.</td></tr>
            <?php else: foreach ($cidades as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><strong><?= htmlspecialchars($c['nome']) ?></strong></td>
                    <td><span class="tag tag-cinza"><?= htmlspecialchars($c['nome_estado'] ?? '—') ?></span></td>
                    <td style="white-space:nowrap">
                        <a href="?aba=cidade&editar=<?= $c['id'] ?>" class="btn btn-edit">Editar</a>
                        <a href="?aba=cidade&deletar=<?= $c['id'] ?>" class="btn btn-danger" onclick="return confirm('Remover esta cidade?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>
    <?php endif; ?>

</div>
<footer>Sistema de Futebol &mdash; PHP PDO &amp; MySQL</footer>
</body></html>
