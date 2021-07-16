<?php


include "inc/functions.inc.php";

is_logged_usuario();

class Line_Counter
{
    private $filepath;
    private $files = array();
    public $files_columns = array();
	public $files_lines = array();
	public $files_number = 0;
	public $files_php_number = 0;

    public function __construct($filepath)
    {
        $this->filepath = $filepath;
    }

    public function countLines($extensions = array('php', 'css', 'js'))
    {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->filepath));
        foreach ($it as $file)
        {
           // if ($file->isDir() || $file->isDot())
           if ($file->isDir() )
            {
                continue;
            }
            $parts = explode('.', $file->getFilename());
            $extension = end($parts);
            if (in_array($extension, $extensions))
            {
                $files[$file->getPathname()] = count(file($file->getPathname()));
                $this->files_php_number++;
				
				$path = explode('delphos', $file->getPathname());
				$count = count($path);
				$count--;
				//$this->files_lines[] = array($path[$count], count(file($file->getPathname())), "ln");
                $this->files_lines[] = $path[$count];
                $this->files_columns[] = count(file($file->getPathname()));
            }
            $this->files_number++;
        }
        return $files;
    }

    public function showLines()
    {
        //echo '<pre>';
        $this->countLines();
        //echo '</pre>';
    }

    public function totalLines()
    {
        return array_sum($this->countLines());
    }

}
$path = explode("/", $_SERVER["SCRIPT_FILENAME"]);
$count = count($path);
for($i=0, $path2="";$i<$count; $i++){
	$i!=0?$path2.="/":false;
	if($i+1!=$count){
		$path2 .= $path[$i];
	}
	
}
// Get all files with line count for each into an array
$loc = new Line_Counter($path2);
$loc->showLines();
$total_linha = $loc->totalLines();
//Arquivos de terceiros que não deve ser somados no calculo do orçamento do programa
$linhas_arq_terceiros = 148798;
$total_linha = $total_linha - $linhas_arq_terceiros;
//preco taxado previamente com cliente
$orcamento = 0.05;



$total_arq = $loc->files_number;

$msg = 'Registro do dia '.date('d/m/Y H:i:s')."\n";
$msg .= 'Total arquivos '.$total_arq."\n";
$msg .= 'Total linhas de codigo: '.$total_linha."\n";
//$msg .= 'Orçamento está avaliado em : '.($total_linha * $orcamento)."\n";

//ABRE O ARQUIVO TXT
$ponteiro = fopen ("registro-atualizacoes.txt","a+");
//LÊ O ARQUIVO ATE	CHEGAR AO FIM
$cod = null;
while (!feof ($ponteiro)) {
    //LÊ UMA LINHA DO ARQUIVO
    $cod .= fgets($ponteiro,4096);
}//FECHA WHILE


$var = explode('Total arquivos ', $cod);
$var = end($var);
$var = explode('Total linhas de codigo: ', $var);

if(!(str_replace("\n", '', $var[0]) == $total_arq && str_replace("\n", '', $var[1]) == $total_linha)){
    $cod.= "--------------------------------------------------\n".$msg;
}
$ponteiro = fopen ("registro-atualizacoes.txt","w+");
$escreve = fwrite($ponteiro, $cod);


$html = new template;      
$cod = $html->set_box('Registro de quantidade de arquivos e linhas de codigo', nl2br($cod));
$html->set_html($cod);
$html->get_html('Info', 'Aqui você visualiza as informações básicas do sistema.');
?>