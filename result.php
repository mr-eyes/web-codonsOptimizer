<?php
require_once "functions.php";
$cub = "";
$seq = "";
$proceed = array('cub' => 0,'seq'=> 0,'file'=>0);
$data = array();
$print = false;
$status = "";
$seq = "";
$error = "";
if(!empty($_POST)){
    #echo "Not Empty Post </br>";
    if(!empty($_POST['cub'])){
        #echo "Not Empty cub </br>";

        $cub = $_POST['cub'];
        $proceed['cub'] = 1;
    }else{
        $proceed['cub'] = 0;
    }

        if(!empty($_POST['seq'])){
            $seq = $_POST['seq'];
            $proceed['seq'] = 1;
        }else{
            $proceed['seq'] = 0;
        }

}


if($_FILES){
    if($_FILES['file']['name'] != "") {

        $fileName = $_FILES['file']['tmp_name'];
        $file = fopen($fileName,"r") or exit("Unable to open file!");
        while(!feof($file)) {
            $seq .= fgets($file);
        }
        fclose($file);
        $proceed['file'] = 1;
    }else{
        $proceed['file'] = 0;
        //echo "Please Choose a file by click on 'Browse' or 'Choose File' button.";
    }
}else{
    $proceed['file'] = 0;
}

if($proceed['cub'] && ($proceed['seq'] || $proceed['file'])){
    $payload = http_build_query(array('cub' => $cub, 'seq' => $seq));
    $data = postData($payload,'https://www.mr-eyes.com/cgi-bin/cop.py');
    $status = $data["status"];
}else{
    if(!$proceed['cub']) echo "No Codon Usage Entered";
    if(!$proceed['seq'] && !$proceed['file']) echo "</br>No Fasta Sequence Entered";
}

if($status === 1){
    #echo "status Good";
    $fastaHeader = $data['fastaHeader']; $opseq = $data['seq']; $cub = $data['cub']; $aminos = $data['aminos']; $orseq = $data['orseq'];

    // ---------------------------------------------------

    $viewData = http_build_query(
        array('view' => 'ok', 'msg' => serialize($data))
    );


    $ch = curl_init();
    $curlConfig = array(
        CURLOPT_URL            => 'https://www.mr-eyes.com/nu/cop/view.php',
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS     => $viewData,
        CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded')
    );
    curl_setopt_array($ch, $curlConfig);
    $curl_result = curl_exec($ch);
    curl_close($ch);
    echo $curl_result;



    //----------------------------------------------------



//    $print = true;
//    print_r($cub);
//    echo "<hr>";
//    echo $fastaHeader;
//    echo "<hr>";
//    echo $opseq;
//    echo "<hr>";
//    echo $aminos;
//    echo "<hr>";
//    echo $orseq;
}else{
    echo "</br>Error parsing Data";
}











