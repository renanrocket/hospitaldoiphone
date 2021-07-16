<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <?php
    include_once('inc/functions.inc.php');
  ?>
  <title><?php echo NOME_EMPRESA ?> | Entrar</title>
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
  <script src="js/mascara.js"></script>



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

      function formulario($msg = null, $e = null){
        
        echo "<p class='login-box-msg'>Escritório Virtual</p>";
        echo "<p class='login-box-msg' style='color:red;'>$msg</p>";
        
        if($e){
          echo '<form action="index.php" method="post">';
          echo "<input type='hidden' name='r' value='".$e."'>";
        }else{
          echo '<form action="index.php" method="post">';  
        }
        
        ?>
        <!-- /.login-logo -->

        
          <div class="form-group has-feedback">
            <input type="email" name="email" class="form-control" placeholder="Email">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          
          <div class="form-group has-feedback">
            <input type="password" name="password" class="form-control" placeholder="Senha">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>

          <div class="form-group has-feedback">
            <select class="form-control" name="filial">
              <?php

                $conn = TConnection::open(DB);

                $criterio = new TCriteria();
                $criterio->add(new TFilter('status', '=', 1));
                $criterio->add(new TFilter('contabilizar', '<>', 0));

                $sql = new TSqlSelect();
                $sql->setEntity('filiais');
                $sql->addColumn('id');
                $sql->addColumn('nome as filial');
                $sql->setCriteria($criterio);

                $result = $conn->query($sql->getInstruction());

                for($i=0; $i<$result->rowCount(); $i++){
                  
                  extract($result->fetch(PDO::FETCH_ASSOC));

                  if(isset($_COOKIE['filial'])){

                    if($_COOKIE['filial']==$id){
                      echo '<option value="'.$id.'" selected="yes">'.$filial.'</option>';
                    }else{
                      echo '<option value="'.$id.'">'.$filial.'</option>';
                    }

                  }else{

                    echo '<option value="'.$id.'">'.$filial.'</option>';
                  
                  }

                }
              ?>
            </select>
            <span class="glyphicon glyphicon-home form-control-feedback"></span>
          </div>
         
          <div class="row">
            <div class="col-xs-8">
              <div class="checkbox icheck">
                <label style="padding: 0px !important;">
                  <input type="checkbox" name="lembrar" style="padding: 0px !important;"> Lembrar-me
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Entrar</button>
            </div>
            <!-- /.col -->
          </div>
        </form>

        
        <a href="forgot.php">Esqueci minha senha</a><br>
        
        <!-- /.login-box-body -->
        <?php
        
      }
      


      extract($_POST);
      extract($_GET);
      $qtd_min = 5;

      $conn = TConnection::open(DB);

      

        if(isset($_COOKIE['email']) && isset($_COOKIE['password']) && isset($_COOKIE['filial'])){
          $email = $_COOKIE['email'];
          $password = $_COOKIE['password'];
          $filial = $_COOKIE['filial'];
        }


        if(!isset($email) && !isset($password) && !isset($filial)){
          if(isset($e)){
            formulario('Sua sessão expirou.<br>Você precisa logar novamente.', $e);
          }else{
            formulario();  
          }
        }else{

          $criterio = new TCriteria();
          $criterio->add(new TFilter('email', '=', $email));
          if(isset($_COOKIE['password'])){
            $criterio->add(new TFilter('senha', '=', $password));
          }else{
            $criterio->add(new TFilter('senha', '=', md5($password)));
          }

          $sql = new TSqlSelect();
          $sql->setEntity('usuario');
          $sql->addColumn('nome');
          $sql->addColumn('email');
          $sql->addColumn('id');
          $sql->setCriteria($criterio);
          $result = $conn->query($sql->getInstruction());


          if($result->rowCount()){
            


            $criterio = new TCriteria();
            $criterio->add(new TFilter('email', '=', $email));
            if(isset($_COOKIE['password'])){
              $criterio->add(new TFilter('senha', '=', $password));
            }else{
              $criterio->add(new TFilter('senha', '=', md5($password)));
            }
            $criterio->add(new TFilter('status', '=', 1));
            $sql = new TSqlSelect();
            $sql->setEntity('usuario');
            $sql->addColumn('nome');
            $sql->addColumn('email');
            $sql->addColumn('id');
            $sql->setCriteria($criterio);
            $result = $conn->query($sql->getInstruction());

            if($result->rowCount()){

              if(isset($lembrar)){
                setcookie('email', $email);
                if(isset($_COOKIE['password'])){
                  setcookie('password', $password);
                }else{
                  setcookie('password', md5($password));
                }
                setcookie('filial', $filial);

              }else{
                setcookie('email', $email, time()+60*$qtd_min);
                if(isset($_COOKIE['password'])){
                  setcookie('password', $password, time()+60*$qtd_min);
                }else{
                  setcookie('password', md5($password), time()+60*$qtd_min);
                }
                setcookie('filial', $filial, time()+60*$qtd_min);
              }
              

              extract($result->fetch(PDO::FETCH_ASSOC));
              echo "<p class='login-box-msg'>Bem vindo $nome !</p>";
              if(isset($r)){
                $r = base64_decode($r);
                redirecionar($r, 1);
              }else{
                redirecionar('index-usuario.php', 1);
              }

            }else{
              formulario('Usuário deletado do sistema :(');  
            }


          }else{
            formulario('Login e/ou senha invelido(s).');
          }
          
        }


      

      

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
