<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Dr. Vicente | Recuperar senha</title>
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
      <a href="http://drvicente.rocketcomanda.com.br"><b>Dr Vicente</b> Cardiologista</a>
    </div>
    <div class="login-box-body">
      <?php

      function formulario($msg = null, $e = true, $email = false, $id = false){
        echo "<p class='login-box-msg'>Escritório Virtual</p>";
        echo "<p class='login-box-msg' style='color:red;'>$msg</p>";        
        ?>
        <!-- /.login-logo -->

        <form action="forgot.php" method="post">
          <div class="form-group has-feedback">

            <?php
              if($e):
            ?>
            <input type="email" name="email" class="form-control" placeholder="Email">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            <?php
              else:
                if($email)
                  echo "<input type='hidden' name='email' value='$email'>";
                if($id)
                  echo "<input type='hidden' name='id' value='$id'>";
                
            ?>
            <input type="text" name="senha" class="form-control" placeholder="Senha">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            <?php 
              endif;
            ?>
          </div>
          
          <div class="row">
            <!-- /.col -->
            <div class="col-xs-5">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Resetar senha</button>
            </div>
            <!-- /.col -->
          </div>
        </form>

        <a href="index">Entrar</a><br>
        <a href="#" class="text-center">Quero me registrar</a>


        <!-- /.login-box-body -->
        <?php
      }
      include('inc/functions.inc.php');


      
      $i = null;
      extract($_POST);
      extract($_GET);


      if(!isset($email) && !isset($i) && !isset($id)){

          formulario();  
        
      }elseif(isset($email) && !isset($i) && !isset($id)){      

        $conn = TConnection::open(DB);


        $criterio = new TCriteria();
        $criterio->add(new TFilter('email', '=', $email));
        

        $sql = new TSqlSelect();
        $sql->setEntity('usuario');
        $sql->addColumn('nome');
        $sql->addColumn('email');
        $sql->addColumn('id');
        $sql->setCriteria($criterio);
        $result = $conn->query($sql->getInstruction());


        if($result->rowCount()){

              extract($result->fetch(PDO::FETCH_ASSOC));

              // Inicia a classe PHPMailer
              $mail = new PHPMailer();

              // Define os dados do servidor e tipo de conexão
              // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
              $mail->IsSMTP(); // Define que a mensagem será SMTP
              $mail->Host = HOSTMAIL; // Endereço do servidor SMTP
              $mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
              $mail->Username = EMAIL; // Usuário do servidor SMTP
              $mail->Password = SENHA; // Senha do servidor SMTP

              // Define o remetente
              // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
              $mail->From = EMAIL; // Seu e-mail
              $mail->FromName = FROM; // Seu nome

              // Define os destinatário(s)
              // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
              $mail->AddAddress($email, $nome);
              $mail->AddAddress(EMAIL, FROM);
              //$mail->AddCC('ciclano@site.net', 'Ciclano'); // Copia
              //$mail->AddBCC('falecom@rocketsolution.com.br', 'Atendimento Rocket Solution');

              // Define os dados técnicos da Mensagem
              // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
              $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
              $mail->CharSet = 'utf-8'; // Charset da mensagem (opcional)

              // Define a mensagem (Texto e Assunto)
              // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
              $mail->Subject  = "Dr. Vicente - Resetar Senha"; // Assunto da mensagem
              $mail->Body = "<h1>Olá $nome</h1>";
              $mail->Body .= "Segue link para você recuperar sua senha.<br>";
              $link = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'?email='.base64_encode($email).'&i='.md5($id);
              $mail->Body .= "<a href='$link'>$link</a>";
              $mail->Body .= "<br><br><br>";
              $mail->Body .= "<a href='http://www.rocketsolution.com.br/'><img src='".IMGASS."'></a>";

              //$mail->AltBody = "Este é o corpo da mensagem de teste, em Texto Plano! \r\n :)";
              // Define os anexos (opcional)
              // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
              //$mail->AddAttachment("c:/temp/documento.pdf", "novo_nome.pdf");  // Insere um anexo

              // Envia o e-mail
              $enviado = $mail->Send();

              // Limpa os destinatários e os anexos
              $mail->ClearAllRecipients();
              $mail->ClearAttachments();
              if($enviado){
                  echo "<p class='login-box-msg'>Um link foi enviado para o seu e-mail.<br>Por favor acesse-o para resetar sua senha.<br><b style='color:red;'>Por favor verifique sua caixa de span ou lixeira, caso não visualize o e-mail.</b></p>";
              }else{
                  echo "Não foi possível enviar o e-mail.";
                  echo "<b style='color:red;'>Envie esse erro para o desenvolvedor do software para correção.<br>falecom@rocketsolution.com.br</b>";
                  echo "<b>Informações do erro:</b> " . $mail->ErrorInfo;

              }

          }else{
            
            formulario('O e-mail que você digitou é invalido.');      

          }
          

  
        }elseif(isset($email) && isset($i) && !isset($id)){
          

          $conn = TConnection::open(DB);


          $criterio = new TCriteria();
          $criterio->add(new TFilter('email', '=', base64_decode($email)));
          

          $sql = new TSqlSelect();
          $sql->setEntity('usuario');
          $sql->addColumn('nome');
          $sql->addColumn('email');
          $sql->addColumn('id');
          $sql->setCriteria($criterio);
          $result = $conn->query($sql->getInstruction());
  
          if($result->rowCount()){

            extract($result->fetch(PDO::FETCH_ASSOC));

            formulario($nome.', preencha com sua nova senha.', false, $email, $id);

          }else{

            formulario('O e-mail que você digitou é invalido.');

          }


        }else{

          $conn = TConnection::open(DB);


          $criterio = new TCriteria();
          $criterio->add(new TFilter('email', '=', $email));
          $criterio->add(new TFilter('id', '=', $id));
          

          $sql = new TSqlSelect();
          $sql->setEntity('usuario');
          $sql->addColumn('nome');
          $sql->setCriteria($criterio);
          $result = $conn->query($sql->getInstruction());
  
          if($result->rowCount()){

            extract($result->fetch(PDO::FETCH_ASSOC));

            $sql = new TSqlUpdate();
            $sql->setEntity('usuario');
            $sql->setRowData('senha', md5($senha));
            $sql->setCriteria($criterio);
            $result = $conn->query($sql->getInstruction());
            if($result->rowCount()){
              echo "<p class='login-box-msg'>$nome, sua senha foi alterada, agora você pode fazer longin!</p>";
              redirecionar('index.php', 1);
            }else{
              echo "<b style='color:red;'>Envie esse erro para o desenvolvedor do software para correção.<br>falecom@rocketsolution.com.br</b>";
            }

          }else{
            echo "<b style='color:red;'>Envie esse erro para o desenvolvedor do software para correção.<br>falecom@rocketsolution.com.br</b>";
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
