<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login - Rastreia Bicho 🐾</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
body {
  background-color: #9fccebff;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}
.card {
  width: 100%;
  max-width: 400px;
  border: none;
  border-radius: 15px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}
.btn-success {
  background-color: #179e46ff;
  border: none;
}
.btn-success:hover {
  background-color: #12843b;
}
</style>
</head>
<body>

<div class="card p-4">
  <div class="text-center mb-3">
    <h3 class="fw-bold text-dark">Rastreia Bicho 🐾</h3>
    <p class="text-muted">Entre na sua conta</p>
  </div>

  <form action="verifica_login.php" method="POST">
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" class="form-control" name="email" placeholder="Digite seu email" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Senha</label>
      <input type="password" class="form-control" name="senha" placeholder="Digite sua senha" required>
    </div>
    <button type="submit" class="btn btn-success w-100">Entrar</button>
  </form>

  <div class="text-center mt-3">
    <p class="mb-1">Ainda não possui uma conta? <a href="cadastro.php" class="text-success">Fazer Cadastro</a></p>
    <p><a href="recuperar_senha.html" class="text-muted">Esqueceu a senha?</a></p>
  </div>
</div>

</body>
</html>
