<?php
/**
 * login.php — Pagina de login publica.
 */
if (session_status() === PHP_SESSION_NONE) session_start();

// Si ya esta autenticado, ir al panel
if (isset($_SESSION['rol_id'])) {
    header('Location: /index.php');
    exit();
}

require_once(__DIR__ . '/../private/procesos/db.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario !== '' && $password !== '') {
        // Busca por nombre_usuario (columna nueva) O usuario_registro (columna legacy)
        // para ser compatible con cuentas creadas antes y despues del fix
        $stmt = $conn->prepare(
            'SELECT id, nombre_usuario, usuario_registro, password_registro, rol_id, Rango_asignado
             FROM registro_usuario
             WHERE nombre_usuario = ? OR usuario_registro = ?
             LIMIT 1'
        );
        $stmt->bind_param('ss', $usuario, $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password_registro'])) {
                session_regenerate_id(true);
                // Usa nombre_usuario si existe, sino usuario_registro
                $nick = $row['nombre_usuario'] ?: $row['usuario_registro'];
                $_SESSION['id']             = $row['id'];
                $_SESSION['usuario']        = $nick;
                $_SESSION['rol_id']         = $row['rol_id'];
                $_SESSION['Rango_asignado'] = $row['Rango_asignado'];
                header('Location: /index.php');
                exit();
            }
        }
        $error = 'Usuario o contrasena incorrectos.';
        $stmt->close();
    } else {
        $error = 'Completa todos los campos.';
    }
}
?>
<?php require_once(__DIR__ . '/../private/plantillas/headerlogin.php'); ?>
<body class="bg-theme bg-theme1 d-flex flex-column min-vh-100">

<div class="container d-flex justify-content-center align-items-center flex-grow-1">
  <div class="card card-authentication1 mx-auto my-4">
    <div class="card-body">
      <div class="card-content p-2">

        <div class="text-center">
          <img src="/private/assets/images/logo-icon.png" alt="Logo" width="150" height="150">
        </div>

        <div class="card-title text-uppercase text-center py-3">Iniciar Sesion</div>

        <?php if ($error): ?>
          <div class="alert alert-danger text-center py-2">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="/login.php">
          <div class="form-group">
            <label for="usuario">Nombre de Habbo</label>
            <input type="text" id="usuario" name="usuario"
                   class="form-control input-shadow"
                   placeholder="Tu nick de Habbo"
                   value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>"
                   required autofocus>
          </div><br>
          <div class="form-group">
            <label for="password">Contrasena</label>
            <input type="password" id="password" name="password"
                   class="form-control input-shadow"
                   placeholder="Contrasena"
                   required>
          </div><br>
          <div class="text-center">
            <button type="submit"
                    class="btn btn-light btn-block waves-effect waves-light"
                    >Entrar</button>
          </div>
          <div class="card-footer text-center py-3">
            <p class="text-warning mb-0">
              &iquest;No tienes cuenta?
              <a href="/register.php">Registrate</a>
            </p>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<?php require_once(__DIR__ . '/../private/plantillas/footer.php'); ?>
</body>
