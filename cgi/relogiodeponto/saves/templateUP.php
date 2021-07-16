<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>
		Rel&oacute;gio de Ponto
	</title>
	<meta charset="UTF-8">

	<style type="text/css">
		@import url(../elements.css);

		.area{
			margin: 10px auto;
			box-shadow: 0 10px 100px #ccc;
			padding: 20px;
			box-sizing: border-box;
			max-width: 500px;
		}

		.area video{
			width: 100%;
			height: auto;
			background-color: whitesmoke;
		}

		.area textarea{
			width: 100%;
			margin-top: 10px;
			height: 80px;
			box-sizing: border-box;
		}

		.area button{
			-webkit-appearance: none;
			width: 100%;
			box-sizing: border-box;
			padding: 10px;
			text-align: center;
			background-color: #068c84;
			color: white;
			text-transform: uppercase;
			border: 1px solid white;
			box-shadow: 0 1px 5px #666;
		}

		.area button:focus{
			outline: none;
			background-color: #0989b0;
		}

		.area img{
			max-width: 100%;
			height: auto;
		}

		.area .caminho-imagem{
			padding: 5px 10px;
			border-radius: 3px;
			background-color: #068c84;
			text-align: center;
		}

		.area .caminho-imagem a{
			color: white;
			text-decoration: none;
		}

		.area .caminho-imagem a:hover{
			color: yellow;
		}

		.imagemTirada{
			display:none;
		}

	</style>
	<script src="../../../js/vendor/jquery-2.2.4.min.js"></script>
	<script type="text/javascript">
		
		function showtime(){
			setTimeout("showtime();",1000);
			callerdate.setTime(callerdate.getTime()+1000);
			var hh = String(callerdate.getHours());
			var mm = String(callerdate.getMinutes());
			var ss = String(callerdate.getSeconds());
			document.clock.face.value =
			((hh < 10) ? " " : "") + hh +
			((mm < 10) ? ":0" : ":") + mm +
			((ss < 10) ? ":0" : ":") + ss;
			
		}
		
		callerdate=new Date(<?php date_default_timezone_set('America/Sao_Paulo'); echo date("Y,m,d,H,i,s");?>);
		

		function loadCamera(){
			//Captura elemento de vídeo
			var video = document.querySelector("#webCamera");
				//As opções abaixo são necessárias para o funcionamento correto no iOS
				video.setAttribute('autoplay', '');
			    video.setAttribute('muted', '');
			    video.setAttribute('playsinline', '');
			    //--
			
			//Verifica se o navegador pode capturar mídia
			if (navigator.mediaDevices.getUserMedia) {
				navigator.mediaDevices.getUserMedia({audio: false, video: {facingMode: 'user'}})
				.then( function(stream) {
					//Definir o elemento vídeo a carregar o capturado pela webcam
					video.srcObject = stream;

				})
				.catch(function(error) {
					alert("Oooopps... Falhou :'(");
				});
			}
		}

		$(function(){
			loadCamera();
		});

		function takeSnapShot(){
			//Captura elemento de vídeo
			var video = document.querySelector("#webCamera");
			
			//Criando um canvas que vai guardar a imagem temporariamente
			var canvas = document.createElement('canvas');
			canvas.width = video.videoWidth;
			canvas.height = video.videoHeight;
			var ctx = canvas.getContext('2d');
			
			//Desenhando e convertendo as dimensões
			ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
			
			//Criando o JPG
			var dataURI = canvas.toDataURL('image/jpeg'); //O resultado é um BASE64 de uma imagem.
			document.querySelector("#base_img").value = dataURI;
			
			sendSnapShot(dataURI); //Gerar Imagem e Salvar Caminho no Banco

			$('.imagemNaoTirada').attr('style', 'display:none;');
			$('.imagemTirada').attr('style', 'display:inline-block;');

			setTimeout(function(){
	        	window.location.replace('../index.php');
	        }, 5000);


		}

		function sendSnapShot(base64){
			var request = new XMLHttpRequest();
			request.open('POST', 'save.php', true);
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
			request.onload = function() {
			        console.log(request);
			        if (request.status >= 200 && request.status < 400) {
			            //Colocar o caminho da imagem no SRC
			            var data = JSON.parse(request.responseText);

			            //verificar se houve erro
			            if(data.error){
			                alert(data.error);
			                return false;
			            }

			            //Mostrar informações
			            document.querySelector("#imagemConvertida").setAttribute("src", data.img);
			            /*
			            document.querySelector("#caminhoImagem a").setAttribute("href", data.img);
			            document.querySelector("#caminhoImagem a").innerHTML = data.img.split("/")[1];
			            */
			            document.querySelector("#caminhoImagem a").setAttribute("href", '../index.php');
			            var j = 5;
			            setInterval(function(){
			            	j--;
				        	document.querySelector("#caminhoImagem a").innerHTML = 'Redirecionando em ' + j;
				        }, 1000);

			        } else {
			            alert( "Erro ao salvar. Tipo:" + request.status );
			        }
			    };

			    request.onerror = function() {
			        alert("Erro ao salvar. Back-End inacessível.");
			    }

			    request.send("base_img="+base64); // Enviar dados
		}
	</script>
	<link rel="shortcut icon" href="imagem/icon.png">
</head>
	<body onload='showtime();'>
		<center>
			
