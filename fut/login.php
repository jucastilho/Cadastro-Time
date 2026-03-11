<?php
session_start();

if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_correto = 'admin';
    $senha_correta   = '1234';

    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = trim($_POST['senha'] ?? '');

    if ($usuario === $usuario_correto && $senha === $senha_correta) {
        $_SESSION['logado']  = true;
        $_SESSION['usuario'] = $usuario;
        header('Location: index.php');
        exit;
    } else {
        $erro = 'Usuário ou senha incorretos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SCCP Sistema</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --preto: #0a0a0a;
            --preto-medio: #111;
            --cinza: #1a1a1a;
            --cinza-claro: #252525;
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
            display: flex;
            align-items: center;
            justify-content: center;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse 60% 50% at 50% 0%, rgba(255,255,255,0.04) 0%, transparent 60%);
            pointer-events: none;
        }
        .login-wrap {
            width: 100%;
            max-width: 400px;
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .escudo {
            width: 72px;
            height: 72px;
            background: var(--branco-puro);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-family: 'Bebas Neue', cursive;
            font-size: 1.4rem;
            color: var(--preto);
            letter-spacing: 1px;
            box-shadow: 0 0 30px rgba(255,255,255,0.08);
        }
        .login-logo h1 {
            font-family: 'Bebas Neue', cursive;
            font-size: 1rem;
            letter-spacing: 6px;
            color: var(--texto);
            font-weight: 400;
            margin-bottom: 0.2rem;
        }
        .login-logo h2 {
            font-family: 'Bebas Neue', cursive;
            font-size: 2.8rem;
            letter-spacing: 5px;
            color: var(--branco-puro);
            line-height: 1;
        }
        .login-logo p {
            color: var(--texto);
            font-size: 0.72rem;
            margin-top: 0.6rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .divider {
            width: 30px;
            height: 2px;
            background: var(--branco-puro);
            margin: 0.8rem auto 0;
            opacity: 0.3;
        }
        .login-card {
            background: var(--cinza);
            border: 1px solid var(--cinza-claro);
            border-top: 3px solid var(--branco-puro);
            border-radius: 10px;
            padding: 2rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            margin-bottom: 1.1rem;
        }
        label {
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--texto);
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }
        input {
            background: var(--preto-medio);
            border: 1px solid #2a2a2a;
            border-radius: 6px;
            color: var(--branco);
            padding: 0.7rem 1rem;
            font-size: 0.93rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
            width: 100%;
        }
        input:focus {
            outline: none;
            border-color: var(--branco-puro);
            box-shadow: 0 0 0 3px rgba(255,255,255,0.06);
        }
        input::placeholder { color: #333; }
        .btn-login {
            width: 100%;
            padding: 0.8rem;
            background: var(--branco-puro);
            color: var(--preto);
            border: none;
            border-radius: 6px;
            font-size: 0.88rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: all 0.2s;
            margin-top: 0.5rem;
        }
        .btn-login:hover {
            background: #ddd;
            box-shadow: 0 4px 20px rgba(255,255,255,0.12);
            transform: translateY(-1px);
        }
        .btn-login:active { transform: translateY(0); }
        .alerta-erro {
            background: rgba(204,51,51,0.1);
            border: 1px solid var(--erro);
            color: #ff7777;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            font-size: 0.84rem;
            font-weight: 500;
            margin-bottom: 1.2rem;
        }
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #2a2a2a;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-logo">
        <div class="escudo">SCCP</div>
        <h1>Sport Club</h1>
        <h2>CORINTHIANS</h2>
        <p>Sistema de Gestão</p>
        <div class="divider"></div>
    </div>
    <div class="login-card">
        <?php if ($erro): ?>
            <div class="alerta-erro">&#9888; <?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="usuario">Usuário</label>
                <input type="text" id="usuario" name="usuario"
                    value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>"
                    placeholder="Digite seu usuário" required autofocus>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha"
                    placeholder="Digite sua senha" required>
            </div>
            <button type="submit" class="btn-login">Entrar</button>
        </form>
    </div>
    <div class="login-footer">Sport Club Corinthians Paulista &mdash; Sistema Interno</div>
</div>
</body>
</html>