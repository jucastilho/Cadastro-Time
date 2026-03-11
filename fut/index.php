<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corinthians — Sistema de Gestão</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --preto: #0a0a0a;
            --cinza: #1a1a1a;
            --cinza-claro: #252525;
            --branco: #f0f0f0;
            --branco-puro: #ffffff;
            --texto: #777;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--preto); color: var(--branco); min-height: 100vh; }

        header {
            background: var(--branco-puro);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 65px;
            border-bottom: 3px solid #000;
            box-shadow: 0 2px 15px rgba(0,0,0,0.5);
        }
        .logo {
            font-family: 'Bebas Neue', cursive;
            font-size: 1.5rem;
            letter-spacing: 3px;
            color: var(--preto);
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .logo-badge {
            background: var(--preto);
            color: #fff;
            font-size: 0.62rem;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 2px 7px;
            border-radius: 3px;
        }
        .btn-sair {
            color: #777;
            text-decoration: none;
            font-size: 0.73rem;
            font-weight: 600;
            padding: 0.32rem 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .btn-sair:hover { border-color: #cc3333; color: #cc3333; }

        .container { max-width: 1000px; margin: 0 auto; padding: 2rem 1.5rem; }

        .hero { text-align: center; padding: 3.5rem 1rem 3rem; }
        .hero h1 {
            font-family: 'Bebas Neue', cursive;
            font-size: clamp(3rem, 8vw, 5.5rem);
            letter-spacing: 6px;
            line-height: 1;
            margin-bottom: 1rem;
            color: var(--branco-puro);
        }
        .hero h1 span { color: var(--texto); }
        .hero p { color: var(--texto); font-size: 1rem; font-weight: 300; margin-bottom: 3rem; }

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 1rem; }

        .card {
            background: var(--cinza);
            border: 1px solid var(--cinza-claro);
            border-radius: 8px;
            padding: 1.6rem 1.3rem;
            text-decoration: none;
            color: var(--branco);
            transition: all 0.2s;
            display: block;
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 3px;
            height: 100%;
            background: var(--branco-puro);
            transform: scaleY(0);
            transition: transform 0.2s;
        }
        .card:hover { border-color: #333; transform: translateY(-3px); box-shadow: 0 6px 25px rgba(0,0,0,0.5); }
        .card:hover::before { transform: scaleY(1); }
        .card .icone { font-size: 1.8rem; margin-bottom: 0.8rem; display: block; }
        .card h3 { font-size: 0.92rem; font-weight: 600; margin-bottom: 0.35rem; color: var(--branco-puro); }
        .card p { font-size: 0.77rem; color: var(--texto); line-height: 1.5; }

        footer {
            text-align: center;
            padding: 2rem;
            color: #2a2a2a;
            font-size: 0.72rem;
            border-top: 1px solid #161616;
            margin-top: 3rem;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">CORINTHIANS <span class="logo-badge">SCCP</span></div>
    <a href="logout.php" class="btn-sair">&#x2192; Sair</a>
</header>

<div class="container">
    <div class="hero">
        <h1>SPORT CLUB <span><br>CORINTHIANS PAULISTA</br></span></h1>
        <p>Sistema interno de gestão do clube</p>
    </div>
    <div class="grid">
        <a href="jogadores.php" class="card">
            <span class="icone">🏃</span>
            <h3>Jogadores</h3>
            <p>Cadastre atletas, posições, gols e histórico completo</p>
        </a>
        <a href="tecnicos.php" class="card">
            <span class="icone">🧑‍💼</span>
            <h3>Técnicos</h3>
            <p>Registre treinadores e comissão técnica</p>
        </a>
        <a href="uniformes.php" class="card">
            <span class="icone">👕</span>
            <h3>Uniformes</h3>
            <p>Gerencie camisas, calções e meiões</p>
        </a>
        <a href="competicoes.php" class="card">
            <span class="icone">🏆</span>
            <h3>Competições</h3>
            <p>Campeonatos nacionais e internacionais</p>
        </a>
        <a href="posicoes.php" class="card">
            <span class="icone">📍</span>
            <h3>Posições</h3>
            <p>Goleiro, atacante, zagueiro, meia...</p>
        </a>
        <a href="localizacao.php" class="card">
            <span class="icone">🌍</span>
            <h3>Localização</h3>
            <p>Países, estados e cidades</p>
        </a>
        <a href="estadios.php" class="card">
            <span class="icone">🏟️</span>
            <h3>Estádios</h3>
            <p>Arenas e estádios com capacidade e localização</p>
        </a>
        <a href="times.php" class="card">
            <span class="icone">🛡️</span>
            <h3>Times</h3>
            <p>Clubes com nome, sigla, escudo e cidade</p>
        </a>
        <a href="arbitros.php" class="card">
            <span class="icone">🟨</span>
            <h3>Árbitros</h3>
            <p>Cadastre árbitros e assistentes</p>
        </a>
        <a href="patrocinadores.php" class="card">
            <span class="icone">💼</span>
            <h3>Patrocinadores</h3>
            <p>Empresas e marcas patrocinadoras do clube</p>
        </a>
    </div>
</div>

<footer>Sport Club Corinthians Paulista &mdash; Sistema Interno</footer>

</body>
</html>