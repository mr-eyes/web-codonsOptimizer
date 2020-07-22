had<?php
require_once "functions.php";
$errorMsg = '<div class="alert alert-danger">
  <strong>Alert!</strong> Something went wrong.
</div>';
$data = array();
if(!empty($_POST)){
    if(!empty($_POST['view'])){
        if($_POST['view'] != 'ok'){
           echo $errorMsg;
        }else{
// Ok Here
            $data = unserialize($_POST['msg']);
        }
    }else{
        echo $errorMsg;
    }
}else{
    echo $errorMsg;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <script src="d3.min.js"></script>
    <script src="d3pie.min.js"></script>
    <title>Codons Optimization Tools</title>

    <!-- Bootstrap Core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>


    <!-- Custom CSS -->
    <style>
        body {
            padding-top: 70px;
            /* Required padding for .navbar-fixed-top. Remove if using .navbar-static-top. Change if height of navigation changes. */
        }
    </style>
</head>

<body style="background-color: #f9f9f9">

<!-- Page Content -->
<div class="container" >

    <div class="bg-1 text-center" >
        <img src="assets/imgs/dna_0.png"  alt="cop" width="100px" height="100px">
        <h1 >Codons Optimizer</h1>
        <h3>Results</h3>
    </div>
    <!-- /.row -->

</div>
<!-- /.container -->
<hr>



<div class="container" >
    <h2>Sequences before and after optimization</h2>
    <div class="panel-group">


        <div class="panel panel-primary">
            <div class="panel-heading">Protein Sequence</div>
            <div class="panel-body" style="font-family: monospace">
                <!-- Sequence Body -->
                <?php
                fastaPrint(substr($data['fastaHeader'],12),$data['aminos']);
                ?>


            </div>
        </div>

        <div class="panel panel-primary">
            <div class="panel-heading">Original Sequence [FASTA]</div>
            <div class="panel-body" style="font-family: monospace">
                <!-- Sequence Body -->
                <?php
                fastaPrint(substr($data['fastaHeader'],12),$data['orseq']);
                ?>


            </div>
        </div>
<hr>
        <div class="panel panel-success">
            <div class="panel-heading">Optimized Sequence [FASTA]</div>
            <div class="panel-body" style="font-family: monospace">
                <!-- Sequence Body -->
                <?php
                fastaPrint(substr($data['fastaHeader'],12),$data['seq']);
                ?>
            </div>
        </div>

    </div>
    <hr>
<!--    <a href="results.csv" download><button type="button" class="btn btn-primary">Download Results</button></a>-->

</div>


<hr>



<?php

$changes = changeTable($data['orseq'],$data['seq'])[1];
?>




<hr>


<div class="container">
        <h2>Results in details</h2>
        <div class="panel-group">

            <div class="panel panel-primary">
                <div class="panel-heading"><a data-toggle="collapse" data-target="#accordion" style="color: white" href="#accordion"><h4>Changes Table</h4>(Click to expand)</a></div>
                <div class="panel-body" style="font-family: monospace">



    <h2>Optimized Codons</h2>
    <p></p>
    <table class="table table-hover table-sm table-bordered">
        <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="text-center">pos</th>
            <th class="text-center">Old</th>
            <th class="text-center">New</th>
            <th class="text-center">diff</th>
            <th class="text-center">amino</th>
            <th class="text-center">Fractions</th>
        </tr>
        </thead>
        <tbody id="accordion" class="panel-collapse collapse">

    <?php
    #$csv_file = new SplFileObject('results.csv', 'w+');
    #$csvHead = array('#','pos','old','new','amino','Fractions');
    #$pathToGenerate = 'results.csv';  // your path and file name
    $header=null;
    #$createFile = fopen($pathToGenerate,"w+");


    for($i=0;$i<sizeof($changes);$i++){
        $row = $changes[$i];
        $old = $row["old"];
        $new = $row["new"];
        $fractions = $row["fractions"];
        $amino = $row["amino"];
        $pos = $row["pos"];
        $pos = $pos + 1;
        $count = $i+1;
        $fr = "";
        $maxFraction = $fractions[$new];
        foreach ($fractions as $key => $value) {
            if($value == $maxFraction){
                $csvFr = " - $key : $value";
                $fr .= " - <span style='color: #ac2925;'><b><u>$key : $value</u></b></span> ";
            }else{
                $fr .= " - $key : $value";
                $csvFr = " - $key : $value";
            }
        }
        #$fr .= " - ";
        #$csvFr .= " - ";
        #$fr = substr($fr,2,-2);
        #$csvFr = substr($csvFr,2,-2);
        $diff = (($row["fractions"][$new])-($row["fractions"][$old]));

        echo <<<EOT

        <tr>
            <td class="text-center" style='color:blue'>{$count}</td>
            <td class="text-center">{$pos}</td>
            <td class="text-center">{$old}</td>
            <td class="text-center" style='color: #ac2925;'>{$new}</td>
            <td class="text-center">{$diff}</td>
            <td class="text-center">{$amino}</td>
            <td>{$fr}</td>
        </tr>
EOT;
?>

        <div id="cgPieChart"></div>
<!--        <div id="cgPieChartNew"></div>-->


<?php
$cgCountOld = countCG($data["orseq"]);
$cgCountNew = countCG($data["seq"]);
    }
?>
    <script>
        var a1 = <?php echo $cgCountOld['a']; ?>;
        var c1 = <?php echo $cgCountOld['c']; ?>;
        var g1 = <?php echo $cgCountOld['g']; ?>;
        var t1 = <?php echo $cgCountOld['t']; ?>;
        var a2 = <?php echo $cgCountNew['a']; ?>;
        var c2 = <?php echo $cgCountNew['c']; ?>;
        var g2 = <?php echo $cgCountNew['g']; ?>;
        var t2 = <?php echo $cgCountNew['t']; ?>;
    </script>

        </tbody>
    </table>
</div>
            </div>
        </div>
    <?php
    echo '
    <div class="row">
    <div id="pieOld" class="border col-xs-6"></div>
    <div id="pieNew" class="border col-xs-6"></div>
    </div>
    ';

    $cgCountOld = countCG($data['orseq']);
    $cgCountNew = countCG($data['seq']);

    generatePieChartChart("pieOld",$cgCountOld, "CG - Content | Old Sequence");
    generatePieChartChart("pieNew",$cgCountNew, "CG - Content | New Sequence");

    echo "<br/><br/><hr>";

    ?>
    <!--    $data['orseq'],$data['seq']-->




<!-- jQuery Version 1.11.1 -->
<script src="assets/js/jquery.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="assets/js/bootstrap.min.js"></script>
<hr>
<footer class="container-fluid bg-3 text-center">
<img src="assets/imgs/nu.png" alt="nu" width="100px" height="100px"><br/>
Developed by <a href="https://www.github.com/mr-eyes">Mohamed Abualainin</a> <br/>
</footer>
<br/>
<br/>
</body>
</html>

<script>
    $('#changesTable').collapse({
        toggle: false
    })
</script>
