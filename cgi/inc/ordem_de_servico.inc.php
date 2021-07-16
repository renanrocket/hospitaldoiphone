<?php


include_once 'functions.inc.php';

extract($_POST);
extract($_GET);

$conn = TConnection::open(DB);

$id = base64_decode($id);

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>Salvar assinatura para OS <?php echo $id; ?></title>
		<link type="text/css" rel="stylesheet" href="../plugins/Drawing-Signature-App-jQuery-Canvas/resources/css/bcPaint.css"/>
		<link type="text/css" rel="stylesheet" href="../plugins/Drawing-Signature-App-jQuery-Canvas/resources/css/bcPaint.mobile.css"/>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="../plugins/Drawing-Signature-App-jQuery-Canvas/resources/js/bcPaint.js"></script>
		<style type="text/css">
			*{
			  margin: 0;
			  padding: 0;
			  list-style: none;
			}

			body{
			  font-family: "proxima-nova-soft", "Proxima Nova Soft", Helvetica, Arial, sans-serif;
			  background-color: #f9f9f9;
			}

			#container{
			  width: 100%;
			  height: 100%;
			  max-width: 960px;
			  margin: auto;
			  background-color: #ffffff;
			  position: relative;
			}

			#container h5{
			  font-size: 12px;
			  font-weight: 400;
			  text-transform: uppercase;
			  margin-top: 40px;
			  margin-bottom: 10px;
			}

			#container h4{
			  font-size: 12px;
			  font-weight: 300;
			}

			#container h3{
			  font-size: 16px;
			  font-weight: 400;
			  margin: 10px 0;
			}

			#container > #header{
			  background-color: #313131;
			  color: #e8e8e8;
			  text-align: center;
			  width: 100%;
			  padding: 15px 0;
			}

			#container > #middle{
			  height: 310px;
			  padding: 40px;
			}

			#container > #middle > #features{
			  float: left;
			  margin-top: -20px;
			}

			#container > #middle > #features > ul > li{
			  font-size: 12px;
			  font-weight: 300;
			  margin-bottom: 7px;
			  margin-left: 5px;
			}

			#container > #middle > #features > .code{
			  font-size: 10px;
			  font-weight: 300;
			  color: #5a5a5a;
			  border: 1px dashed #ddd;
			  padding: 7px 30px 7px 10px;
			}

			#container > #middle > #features > a{
			  color: #000;
			  font-size: 10px;
			}

			@media (max-width: 1024px){
				body{
					top: 0;
					right: 0;
					bottom: 0;
					left: 0;
					position: fixed;
				}

				#container{
					background-color: #f7f7f7;
				}

				#container h4{
					font-size: 18px;
				}

				#container h3{
					font-size: 32px;
					margin: 15px 0;
				}

				#bcPaint{
					width: 100% !important;
					height: 100% !important;
					margin: 0 !important;
					background-color: #ffffff;
				}
			}

            #bcPaint-header{
                display:none;
            }
            
		</style>
	</head>
	<body>
		<div id="container">
			<div id="header">
                <h3><?php echo registro($id, 'ordem_de_servico', 'nome'); ?></h3>
                <h4>Assine no espaço em branco e depois clique em salvar</h4>
			</div>
            <input type="hidden" name="idOS" id="idOS" value="<?php echo base64_encode($id); ?>">
			<div class="jquery-script-ads" style="margin:30px auto" align="center"><script type="text/javascript">
                        <!--
                        google_ad_client = "ca-pub-2783044520727903";
                        /* jQuery_demo */
                        google_ad_slot = "2780937993";
                        google_ad_width = 728;
                        google_ad_height = 90;
                        //-->
            </script>
            <!--<script type="text/javascript" src="https://pagead2.googlesyndication.com/pagead/show_ads.js"></script>-->
            </div>
			<center>
                <div id="bcPaint"></div>
            </center>
		</div>
        
		<script type="text/javascript">
			$('#bcPaint').bcPaint();          
            
            $('body').on('click', '#bcPaint-export', function(){
                var idOS = document.getElementById('idOS');
                var paintCanvas = document.getElementById('bcPaintCanvas');
                var imgData = paintCanvas.toDataURL('image/png');
                //var windowOpen = window.open('about:blank', 'Image');
                //windowOpen.document.write('<img src="' + imgData + '" alt="Exported Image"/>');
                //idOS.innerHTML('<img src="' + imgData + '" alt="Exported Image"/>')


				let variaveis = {
					op : 'salvarAssinatura', 
					id : idOS.value, 
					img : imgData
				};

				fetch('ajax_ordem_de_servico.inc.php', { 
						method: 'post',
						body: JSON.stringify(variaveis)
				})
				.then(data => {
					if(data.status==200){
						window.opener.location.reload(true);
        				window.close();
					}else{
						alert('Não foi possível registrar sua assinatura, por favor tente novamente.');
					}
					
				});
                
            });
            
		</script>
    
        <script type="text/javascript">
			
            
		</script>

		<script type="text/javascript">

            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-36251023-1']);
            _gaq.push(['_setDomainName', 'jqueryscript.net']);
            _gaq.push(['_trackPageview']);

            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();

        </script>
</html>
