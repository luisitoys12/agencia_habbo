<?php
/**
 * register.php — Registro publico de usuarios.
 */
if (session_status() === PHP_SESSION_NONE) session_start();

// Si ya tiene sesion, ir al panel
if (isset($_SESSION['rol_id'])) {
    header('Location: /index.php');
    exit();
}

require_once(__DIR__ . '/../private/procesos/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario    = htmlspecialchars(trim($_POST['registro_usuario'] ?? ''));
    $password   = trim($_POST['registro_password'] ?? '');
    $ip_usuario = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

    if (!empty($usuario) && !empty($password)) {

        // Verificar si el nick ya existe (busca en ambas columnas por compatibilidad)
        $stmt = $conn->prepare(
            'SELECT id FROM registro_usuario
             WHERE nombre_usuario = ? OR usuario_registro = ? LIMIT 1'
        );
        $stmt->bind_param('ss', $usuario, $usuario);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->close();
            echo "<script>alert('El usuario ya existe.'); window.location.href='/register.php';</script>";
            exit();
        }
        $stmt->close();

        // Limite de 2 registros por IP
        $stmt_ip = $conn->prepare('SELECT COUNT(*) FROM registro_usuario WHERE ip_registro = ?');
        $stmt_ip->bind_param('s', $ip_usuario);
        $stmt_ip->execute();
        $stmt_ip->bind_result($total_ip);
        $stmt_ip->fetch();
        $stmt_ip->close();

        if ($total_ip >= 2) {
            echo "<script>alert('Maximo 2 registros por IP. Contacta soporte.'); window.location.href='/register.php';</script>";
            exit();
        }

        $hash  = password_hash($password, PASSWORD_DEFAULT);
        $rol   = 1;
        $rango = 1;
        $fecha = date('Y-m-d H:i:s');

        // Escribe nombre_usuario Y usuario_registro con el mismo valor
        // para garantizar compatibilidad con todo el codigo existente
        $stmt_ins = $conn->prepare(
            'INSERT INTO registro_usuario
             (nombre_usuario, usuario_registro, password_registro,
              rol_id, Rango_asignado, fecha_registro, ip_registro)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt_ins->bind_param('sssiiiss',
            $usuario, $usuario, $hash, $rol, $rango, $fecha, $ip_usuario
        );

        if ($stmt_ins->execute()) {
            $nuevo_id = $conn->insert_id;
            // Crear fila de creditos en dinero_digital
            $stmt_dd = $conn->prepare(
                'INSERT IGNORE INTO dinero_digital (id_usuario, creditos) VALUES (?, 0)'
            );
            $stmt_dd->bind_param('i', $nuevo_id);
            $stmt_dd->execute();
            $stmt_dd->close();

            echo "<script>alert('Registro exitoso. Ahora puedes iniciar sesion.'); window.location.href='/login.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error en el registro. Intentalo de nuevo.'); window.location.href='/register.php';</script>";
            exit();
        }

    } else {
        echo "<script>alert('Completa todos los campos.'); window.location.href='/register.php';</script>";
        exit();
    }
}
?>
<?php require_once(__DIR__ . '/../private/plantillas/headerlogin.php'); ?>
<body class="bg-theme bg-theme1 d-flex flex-column min-vh-100">

  <div class="scroll-top position-fixed" style="top:20px;right:20px;z-index:1000">
    <a class="btn btn-outline-primary" href="/login.php"
       style="height:50px;display:flex;align-items:center">Regresar</a>
  </div>

  <div class="container d-flex justify-content-center align-items-center flex-grow-1">
    <div class="card card-authentication1 mx-auto my-4">
      <div class="card-body">
        <div class="card-content p-2">

          <div class="text-center">
            <img src="/private/assets/images/logo-icon.png" alt="Logo" width="200" height="200">
          </div>

          <div class="card-title text-uppercase text-center py-3">Registrar usuario</div>

          <form method="POST" action="/register.php">
            <div class="form-group">
              <label>Nombre de Habbo</label>
              <input type="text" name="registro_usuario"
                     class="form-control input-shadow"
                     placeholder="Tu nick de Habbo" required>
            </div><br>
            <div class="form-group">
              <label>Contrasena</label>
              <input type="password" name="registro_password"
                     class="form-control input-shadow"
                     placeholder="Nueva contrasena" required>
            </div><br>
            <div class="form-group">
              <div class="icheck-material-white">
                <input type="checkbox" id="terms" required />
                <label for="terms">Acepto los </label>
                <span data-bs-toggle="modal" data-bs-target="#termsModal"
                      style="cursor:pointer;color:blue;text-decoration:underline"
                      >Terminos y Condiciones</span>
                <?php
                  $modal = __DIR__ . '/../private/modal/terminos_condiciones.php';
                  if (file_exists($modal)) require_once($modal);
                ?>
              </div>
            </div><br>
            <div class="text-center">
              <button type="submit"
                      class="btn btn-light btn-block waves-effect waves-light"
                      >Registrar</button>
            </div>
            <div class="card-footer text-center py-3">
              <p class="text-warning mb-0">
                &iquest;Ya tienes cuenta?
                <a href="/login.php">Inicia sesion</a>
              </p>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>

<?php require_once(__DIR__ . '/../private/plantillas/footer.php'); ?>
</body>
