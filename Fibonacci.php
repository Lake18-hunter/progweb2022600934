<html>
<head><h2><center>FIBONACCI</center></h2><h3><center>Flores Jasso Miguel Angel</center></h3>
</head>
<body>
<table border align="center">
<?php
    $numerof = [0, 1];
    
    for ($i = 2; $i < 19; $i++) {
        $numerof[$i] = $numerof[$i - 1] + $numerof[$i - 2];
    }
    foreach ($numerof as $num) {
        echo $num."<br>";
    }
    ?>
    </table>
</body>
</html>