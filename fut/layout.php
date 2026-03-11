<?php
$titulo = $titulo_pagina ?? 'SCCP Sistema';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> — Corinthians</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --preto: #0a0a0a;
            --preto-medio: #111;
            --cinza: #1a1a1a;
            --cinza-claro: #252525;
            --cinza-borda: #2a2a2a;
            --branco: #f0f0f0;
            --branco-puro: #ffffff;
            --texto: #777;
            --erro: #cc3333;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--preto);
            color: var(--branco);
            min-height: 100vh;
        }
        header {
            background: var(--branco-puro);
            border-bottom: 3px solid #000;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 65px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 15px rgba(0,0,0,0.5);
        }
        .logo {
            font-family: 'Bebas Neue', cursive;
            font-size: 1.5rem;
            letter-spacing: 3px;
            color: var(--preto);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .logo-badge {
            background: var(--preto);
            color: var(--branco-puro);
            font-size: 0.62rem;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 2px 7px;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        nav { display: flex; gap: 0.2rem; flex-wrap: wrap; }
        nav a {
            color: #555;
            text-decoration: none;
            padding: 0.35rem 0.75rem;
            border-radius: 4px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            transition: all 0.2s;
        }
        nav a:hover { color: var(--preto); background: #f0f0f0; }
        nav a.ativo { background: var(--preto); color: var(--branco-puro); }

        .btn-sair {
            color: #999;
            text-decoration: none;
            font-size: 0.73rem;
            font-weight: 600;
            padding: 0.32rem 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: all 0.2s;
            white-space: nowrap;
            letter-spacing: 0.5px;
            color: #777;
        }
        .btn-sair:hover { border-color: var(--erro); color: var(--erro); }

        .container { max-width: 960px; margin: 0 auto; padding: 2rem 1.5rem; }

        .page-title {
            font-family: 'Bebas Neue', cursive;
            font-size: 2.4rem;
            letter-spacing: 4px;
            margin-bottom: 0.2rem;
            color: var(--branco-puro);
        }
        .page-title span { color: var(--texto); }
        .page-sub { color: var(--texto); font-size: 0.85rem; margin-bottom: 2rem; }

        .card {
            background: var(--cinza);
            border: 1px solid var(--cinza-claro);
            border-radius: 8px;
            padding: 1.8rem;
            margin-bottom: 1.5rem;
        }
        .card h2 {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--branco-puro);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 1.4rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid var(--cinza-claro);
        }

        .form-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; }
        .form-group { display: flex; flex-direction: column; gap: 0.35rem; }
        .form-group.full { grid-column: 1 / -1; }

        label {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--texto);
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        input, select, textarea {
            background: var(--preto-medio);
            border: 1px solid var(--cinza-borda);
            border-radius: 5px;
            color: var(--branco);
            padding: 0.6rem 0.85rem;
            font-size: 0.88rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s;
            width: 100%;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--branco-puro);
        }
        textarea { resize: vertical; min-height: 90px; }
        select option { background: var(--preto-medio); }
        input::placeholder { color: #333; }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-size: 0.88rem;
            color: var(--texto);
            margin-top: 0.3rem;
        }
        .form-check input[type=checkbox] { width: auto; cursor: pointer; accent-color: var(--branco-puro); }

        .form-actions { margin-top: 1.4rem; display: flex; gap: 0.8rem; align-items: center; flex-wrap: wrap; }

        .btn {
            padding: 0.6rem 1.6rem;
            border: none;
            border-radius: 5px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            letter-spacing: 0.5px;
        }
        .btn-primary { background: var(--branco-puro); color: var(--preto); }
        .btn-primary:hover { background: #e0e0e0; }
        .btn-secondary { background: #2a2a2a; color: var(--branco); border: 1px solid #333; }
        .btn-secondary:hover { background: #333; }
        .btn-danger { background: transparent; color: var(--erro); padding: 0.3rem 0.7rem; font-size: 0.75rem; border: 1px solid #3a1a1a; }
        .btn-danger:hover { background: rgba(204,51,51,0.08); border-color: var(--erro); }
        .btn-edit { background: #222; color: var(--texto); padding: 0.3rem 0.7rem; font-size: 0.75rem; border: 1px solid #333; }
        .btn-edit:hover { background: #2a2a2a; color: var(--branco); }

        .tabela-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.84rem; }
        thead th {
            background: #141414;
            color: var(--branco-puro);
            text-align: left;
            padding: 0.7rem 0.9rem;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: 700;
            border-bottom: 2px solid var(--branco-puro);
        }
        tbody tr { border-bottom: 1px solid #1e1e1e; transition: background 0.15s; }
        tbody tr:hover { background: rgba(255,255,255,0.02); }
        tbody td { padding: 0.65rem 0.9rem; color: var(--texto); vertical-align: middle; }
        tbody td strong { color: var(--branco); font-weight: 500; }
        .empty-row td { text-align: center; color: #333; padding: 2.5rem; font-style: italic; }

        .tag {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 3px;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .tag-verde   { background: rgba(255,255,255,0.08); color: var(--branco); }
        .tag-amarelo { background: rgba(255,255,255,0.05); color: #aaa; }
        .tag-cinza   { background: rgba(255,255,255,0.04); color: #555; }

        .alerta {
            padding: 0.85rem 1.1rem;
            border-radius: 6px;
            margin-bottom: 1.4rem;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .alerta-sucesso { background: rgba(255,255,255,0.05); border: 1px solid #444; color: var(--branco); }
        .alerta-erro    { background: rgba(204,51,51,0.1); border: 1px solid var(--erro); color: #ff7777; }

        .abas { display: flex; gap: 0.4rem; margin-bottom: 1.8rem; flex-wrap: wrap; }
        .aba {
            text-decoration: none;
            padding: 0.4rem 1rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--texto);
            background: var(--cinza);
            border: 1px solid var(--cinza-claro);
            transition: all 0.2s;
        }
        .aba:hover { color: var(--branco); border-color: #444; }
        .aba.ativo { background: var(--branco-puro); color: var(--preto); border-color: var(--branco-puro); }

        footer {
            text-align: center;
            padding: 2rem;
            color: #2a2a2a;
            font-size: 0.72rem;
            border-top: 1px solid #161616;
            margin-top: 3rem;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
<header>
    <a href="/fut/index.php" class="logo">
        CORINTHIANS
        <span class="logo-badge">SCCP</span>
    </a>
    <div class="header-right">
        <nav>
            <a href="/fut/index.php">Início</a>
            <a href="jogadores.php"   <?= basename($_SERVER['PHP_SELF']) == 'jogadores.php'   ? 'class="ativo"' : '' ?>>Jogadores</a>
            <a href="tecnicos.php"    <?= basename($_SERVER['PHP_SELF']) == 'tecnicos.php'    ? 'class="ativo"' : '' ?>>Técnicos</a>
            <a href="uniformes.php"   <?= basename($_SERVER['PHP_SELF']) == 'uniformes.php'   ? 'class="ativo"' : '' ?>>Uniformes</a>
            <a href="competicoes.php" <?= basename($_SERVER['PHP_SELF']) == 'competicoes.php' ? 'class="ativo"' : '' ?>>Competições</a>
            <a href="posicoes.php"    <?= basename($_SERVER['PHP_SELF']) == 'posicoes.php'    ? 'class="ativo"' : '' ?>>Posições</a>
            <a href="localizacao.php"    <?= basename($_SERVER['PHP_SELF']) == 'localizacao.php'    ? 'class="ativo"' : '' ?>>Localização</a>
            <a href="estadios.php"      <?= basename($_SERVER['PHP_SELF']) == 'estadios.php'      ? 'class="ativo"' : '' ?>>Estádios</a>
            <a href="times.php"         <?= basename($_SERVER['PHP_SELF']) == 'times.php'         ? 'class="ativo"' : '' ?>>Times</a>
            <a href="arbitros.php"      <?= basename($_SERVER['PHP_SELF']) == 'arbitros.php'      ? 'class="ativo"' : '' ?>>Árbitros</a>
            <a href="patrocinadores.php" <?= basename($_SERVER['PHP_SELF']) == 'patrocinadores.php' ? 'class="ativo"' : '' ?>>Patrocinadores</a>
        </nav>
        <a href="/fut/logout.php" class="btn-sair">&#x2192; Sair</a>
    </div>
</header>