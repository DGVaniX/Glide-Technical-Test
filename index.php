<?php
    include "includes/autoloader.inc.php";

    $calValue = new Script();
    $theData = $calValue->getData();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="libs/js/jquery.js"></script>
        <script src="libs/js/Chart.min.js"></script>
        <title>Natioanl Grid Calorific Values by George Dinu</title>
    </head>
    <body>
        <div id="container" class="container">
            <div class="title">National Grid Calorific Values</div>

            <?php
                if(isset($_GET['action'])){
                    if($_GET['action'] == "edit"){
                        if(isset($_GET['id'])){
                            echo '<div id="editRecord" class="editRecord">';
                            echo '<h2 style="font-size:2.0vh">Edit Record #'.$_GET['id'].'</h2>';
                            echo '<form action="index.php" method="post">';
                            echo '<input type="date" name="date" value="YYYY-MM-DD" min="2018-01-01" max='.date("Y-m-d").'>';
                            echo '<input type="text" name="area" placeholder="Area">';
                            echo '<input type="text" name="value" placeholder="Calorific Value">';
                            echo '<input type="hidden" name="id" value='.$_GET["id"].'>';
                            echo '<input type="submit" name="saveEdit" value="Save Changes">';
                            echo '</form>';
                            echo '</div>';                    
                        }
                    }
                }else if(isset($_POST['showGraph'])){
                    if(!isset($_POST['theData']) || empty($_POST['theData'])){
                        echo '<div id="infoTextMain" class="infoTextMain error">';
                        echo '<i class="fa-solid fa-circle-exclamation"></i> There is no data to be displayed into a graph.';
                        echo '<a id="infoClose" class="infoClose" title="Close Info Box"><i class="fa-solid fa-xmark"></i></a>';
                        echo '</div>';
                    }else{
                        $data = json_decode($_POST['theData'], true);

                        $areas = array();
                        $averages = array();
                        foreach($data as $row){
                            if(strlen($row['area']) == 2){
                                if(!in_array($row['area'], $areas)){
                                    $areas[] = $row['area'];
                                    $average = 0;
                                    $count = 0;
                                    foreach($data as $row2){
                                        if($row2['area'] == $row['area']){
                                            $average += $row2['value'];
                                            $count++;
                                        }
                                    }
                                    $average = $average / $count;
                                    $averages[] = round($average, 4);
                                }
                            }
                        }
                    ?>
                        <div style="width:100%;height:40%;text-align:center">
                            <p style="align:center;"><canvas id="chartjs_bar"></canvas></p>
                        </div>
                        <script type="text/javascript">
                            var ctx = document.getElementById("chartjs_bar").getContext('2d');
                            var myChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels:<?php echo json_encode($areas); ?>,
                                    datasets: [{
                                        backgroundColor: [
                                            "#5969aa",
                                            "#ff407b",
                                            "#25d5f2",
                                            "#ffc750",
                                            "#a21942",
                                            "#fd9644",
                                            "#1dd1a1",
                                            "#fa983a",
                                            "#e0eaea",
                                            "#9e9e9e",
                                            "#c45850",
                                            "#ffc750",
                                            "#a21942",
                                        ],
                                        data:<?php echo json_encode($averages); ?>,
                                    }]
                                },
                                options: {
                                    legend: {
                                        display: false,
                                        position: 'bottom',
                            
                                        labels: {
                                            fontColor: '#71748d',
                                            fontFamily: 'Circular Std Book',
                                            fontSize: 14,
                                        }
                                    },
                                }
                            });
                        </script>
                    <?php
                    }
                }
            ?>
            <?php if(!isset($_POST['showGraph'])): ?>
                <div class="dataList">
                    <?php
                        if(isset($_POST['saveEdit'])){
                            if((isset($_POST['id']) && !empty($_POST['id'])) && (isset($_POST['date']) && !empty($_POST['date'])) && (isset($_POST['area']) && !empty($_POST['area'])) && (isset($_POST['value']) && !empty($_POST['value']))){                     
                                $calValue->updateData($_POST['id'], $_POST['date'], $_POST['area'], $_POST['value']);

                                echo '<div id="infoTextMain" class="infoTextMain success">';
                                echo '<i class="fa-solid fa-circle-exclamation"></i> Record #'.$_POST['id'].' was successfully updated.';
                                echo '<a id="infoClose" class="infoClose" title="Close Info Box"><i class="fa-solid fa-xmark"></i></a>';
                                echo '</div>';
                            }else{
                                echo '<div id="infoTextMain" class="infoTextMain error">';
                                echo '<i class="fa-solid fa-circle-exclamation"></i> Please fill in all fields.';
                                echo '<a id="infoClose" class="infoClose" title="Close Info Box"><i class="fa-solid fa-xmark"></i></a>';
                                echo '</div>';
                            }
                            $theData = $calValue->getData();
                        }else if(isset($_POST['showACVBtn'])){
                            if(isset($_POST['fromDate']) && !empty($_POST['fromDate']) && isset($_POST['toDate']) && !empty($_POST['toDate'])){
                                $result = $calValue->getAverageValueByDateRange($_POST['fromDate'], $_POST['toDate']);
                                if($result){
                                    echo '<div id="infoTextMain" class="infoTextMain success">';
                                    echo '<i class="fa-solid fa-circle-exclamation"></i> Average Calorific Value from '.$_POST['fromDate'].' to '.$_POST['toDate'].' is '.round($result, 4).'.';
                                    echo '<a id="infoClose" class="infoClose" title="Close Info Box"><i class="fa-solid fa-xmark"></i></a>';
                                    echo '</div>';
                                }
                            }else{
                                echo '<div id="infoTextMain" class="infoTextMain error">';
                                echo '<i class="fa-solid fa-circle-exclamation"></i> Please select a FROM date and a TO date.';
                                echo '<a id="infoClose" class="infoClose" title="Close Info Box"><i class="fa-solid fa-xmark"></i></a>';
                                echo '</div>';
                            }
                        }else if(isset($_POST['showDataBtn'])){
                            if(isset($_POST['fromDate']) && !empty($_POST['fromDate']) && isset($_POST['toDate']) && !empty($_POST['toDate'])){
                                $theData = $calValue->getValueByDateRange($_POST['fromDate'], $_POST['toDate']);
                                if($theData){
                                    echo '<div id="infoTextMain" class="infoTextMain success">';
                                    echo '<i class="fa-solid fa-circle-exclamation"></i> #'.count($theData).' records from '.$_POST['fromDate'].' to '.$_POST['toDate'].' have been loaded.';
                                    echo '<a id="infoClose" class="infoClose" title="Close Info Box"><i class="fa-solid fa-xmark"></i></a>';
                                    echo '</div>';
                                }
                            }
                        }else if(isset($_GET['action'])){
                            if($_GET['action'] == "fetchData") {
                                $result = $calValue->fetchData();
                                if($result) {
                                    echo '<div id="infoTextMain" class="infoTextMain success">';
                                    echo '<i class="fa-solid fa-circle-exclamation"></i> '.$calValue->totalRecords().' records have been successfully fetched and loaded.';
                                    echo '<a id="infoClose" class="infoClose" title="Close Info Box"><i class="fa-solid fa-xmark"></i></a>';
                                    echo '</div>';
                                    $theData = $calValue->getData();
                                }
                            }else if($_GET['action'] == "reloadData") {
                                $theData = $calValue->getData();
                                echo '<div id="infoTextMain" class="infoTextMain success">';
                                echo '<i class="fa-solid fa-circle-exclamation"></i> The records have been successfully reloaded.';
                                echo '<a id="infoClose" class="infoClose" title="Close Info Box"><i class="fa-solid fa-xmark"></i></a>';
                                echo '</div>';
                            }else if($_GET['action'] == "delete") {
                                if(isset($_GET['id'])){
                                    $calValue->removeData($_GET['id']);
                                    echo '<div id="infoTextMain" class="infoTextMain success">';
                                    echo '<i class="fa-solid fa-circle-exclamation"></i> Record #'.$_GET['id'].' was successfully deleted.';
                                    echo '<a id="infoClose" class="infoClose" title="Close Info Box"><i class="fa-solid fa-xmark"></i></a>';
                                    echo '</div>';

                                    $theData = $calValue->getData();
                                }                        
                            }
                        }

                        if(isset($theData)) {
                            if(!empty($theData)){
                    ?>
                    <form action="index.php" method="post">
                        From: <input type="date" name="fromDate" value="YYYY-MM-DD" min="2018-01-01" max="<?= date('Y-m-d'); ?>"> 
                        To: <input type="date" name="toDate" value="<?= date('Y-m-d'); ?>" min="2018-01-01" max="<?= date('Y-m-d'); ?>">

                        <input type="submit" name="showDataBtn" title="Load data coresponding to those dates" value="Show Data">
                        <input type="submit" name="showACVBtn" title="Find the average calorific value" value="Average Calorific Value">
                    </form>
                    <?php 
                                echo "<div id='dataTable' class='dataTable'>";
                                echo "<table>";
                                echo "<tr>";
                                echo "<th>ID</th>";
                                echo "<th>Area</th>";
                                echo "<th>Calorific Value</th>";
                                echo "<th>Applicable Date</th>";
                                echo "<th></th>";
                                echo "</tr>";
                                foreach($theData as $data){
                                    echo "<tr>";
                                    echo "<td>".$data['id']."</td>";
                                    echo "<td>".$data['area']."</td>";
                                    echo "<td>".$data['value']."</td>";
                                    echo "<td>".$data['applicableDate']."</td>";
                                    echo "<td><a href='./index.php?action=edit&id=".$data['id']."' class='editBtn' title='Edit Record'><i class='fa-solid fa-edit'></i></a>";
                                    echo "<a href='./index.php?action=delete&id=".$data['id']."' class='deleteBtn' title='Delete Record'><i class='fa-solid fa-trash-alt'></i></a></td>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</table>";
                                echo "</div>";
                            }else{
                                echo "<div style='background-color: rgb(200, 50, 50); display: block !important;' id='infoTextMain' class='infoTextMain'>";
                                echo "<i class='fa-solid fa-circle-exclamation'></i> There is no data in the database! Please press 'Fetch New Data' to populate the database.";
                                echo "</div>";
                            }
                        }
                    ?>
                </div>
            <?php endif; ?>

            <div class="actions">
                <div class="actionBtns">
                    <a class="btnStyle" title="Delete and Fetch Data" href="./index.php?action=fetchData"><i class="fa-solid fa-cloud-arrow-down"></i> Fetch New Data</a>
                    <?php if(isset($theData) && !empty($theData)): ?>
                        <a class="btnStyle" title="Reload from Database" href="./index.php?action=reloadData"><i class="fa-solid fa-arrows-rotate"></i> Reload</a>
                        <?php if(isset($_POST['showGraph'])):?>
                            <a class="btnStyle" title="View data Table" href="./index.php"><i class="fa-solid fa-backward"></i> View Data Table</a>
                        <?php else: ?>
                            <form action="index.php" method="post"><br>
                                <input type="hidden" name="theData" value='<?php echo json_encode($theData, true); ?>'>
                                <input type="submit" name="showGraph" title="Show a graph of the displayed data" value="View Graph">
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </body>

    <script>
        $(document).ready(function(){
            $("#infoClose").click(function(){
                $("#infoTextMain").hide();
            });
        });
    </script>
</html>