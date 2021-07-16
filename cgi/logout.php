<?php
include 'inc/functions.inc.php';

if(isset($_COOKIE['password'])){

  $conn = TConnection::open(DB);

  $email = $_COOKIE['email'];
  $password = $_COOKIE['password'];

  $criterio = new TCriteria();
  $criterio->add(new TFilter('email', '=', $email));
  $criterio->add(new TFilter('senha', '=', $password));

  $sql = new TSqlSelect();
  $sql->setEntity('usuario');
  $sql->addColumn('nome');
  $sql->addColumn('email');
  $sql->addColumn('id');
  $sql->setCriteria($criterio);
  $result = $conn->query($sql->getInstruction());
  extract($result->fetch(PDO::FETCH_ASSOC));

  setcookie('email', '');
  setcookie('password', '');
  
}elseif(isset($_COOKIE['cpf'])){

  $conn = TConnection::open(DB);

  $email = $_COOKIE['email'];
  $cpf = $_COOKIE['cpf'];

  $criterio = new TCriteria();
  $criterio->add(new TFilter('email', '=', $email));
  $criterio->add(new TFilter('cpf', '=', $cpf));

  $sql = new TSqlSelect();
  $sql->setEntity('cliente');
  $sql->addColumn('nome');
  $sql->setCriteria($criterio);
  $result = $conn->query($sql->getInstruction());
  extract($result->fetch(PDO::FETCH_ASSOC));

  setcookie('email', '');
  setcookie('cpf', '');

}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo NOME_EMPRESA; ?> | Sair</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="plugins/AdminLTE-master/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="plugins/AdminLTE-master/dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/AdminLTE-master/plugins/iCheck/square/blue.css">



  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page">

  <div class="login-box">
    <div class="login-logo">
       <a href="<?php echo END_SITE ?>"><b><?php echo NOME_EMPRESA_BLACK ?></b> <?php echo NOME_EMPRESA_LIGHT ?></a>
    </div>
    <div class="login-box-body">
      
      <?php
      if(!isset($nome))
        $nome = null;
      echo "<p class='login-box-msg'>Até logo $nome !</p>";
      redirecionar('index.php', 1);

      ?>

    </div>
  </div>
  <!-- /.login-box -->

  <!-- jQuery 2.2.0 -->
  <script src="plugins/AdminLTE-master/plugins/jQuery/jQuery-2.2.0.min.js"></script>
  <!-- Bootstrap 3.3.5 -->
  <script src="plugins/AdminLTE-master/bootstrap/js/bootstrap.min.js"></script>
  <!-- iCheck -->
  <script src="plugins/AdminLTE-master/plugins/iCheck/icheck.min.js"></script>
  <script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
  </script>
</body>
</html>
