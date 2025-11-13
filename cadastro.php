<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro - Rastreia Bicho 🐾</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
body {
  background-color: #9fccebff;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
}
.card {
  width: 100%;
  max-width: 500px;
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
    <h3 class="fw-bold text-dark">Crie sua conta 🐾</h3>
    <p class="text-muted">Preencha os dados abaixo</p>
  </div>

  <form action="salvar_usuario.php" method="POST">
    <div class="mb-3">
      <label class="form-label">Nome completo</label>
      <input type="text" class="form-control" name="nome" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" class="form-control" name="email" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Senha</label>
      <input type="password" class="form-control" name="senha" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Confirmar senha</label>
      <input type="password" class="form-control" name="confirmar_senha" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Telefone</label>
      <input type="text" class="form-control" name="telefone" placeholder="(xx) xxxxx-xxxx">
    </div>
    <div class="mb-3">
      <label class="form-label">Endereço</label>
      <input type="text" class="form-control" name="endereco">
    </div>
    <div class="mb-3">
      <label class="form-label">Data de nascimento</label>
      <input type="date" class="form-control" name="data_nascimento" required>
    </div>

    <button type="submit" class="btn btn-success w-100">Cadastrar</button>
  </form>

  <div class="text-center mt-3">
    <p>Já tem uma conta? <a href="login.php" class="text-success">Fazer login</a></p>
  </div>
</div>

</body>
</html>
