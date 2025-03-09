
<html>
	<head>
<h1><center>EXPRESIONES</center></h1><h2><center>Lake18-Hunter<center></h2>
	</head>
	<body>
       
    <?php
 $x = 2;
 $y = 4;
 $resultado = (1 / $x) + (($x + $y) / 3) + (2 * ($x / $y));
 echo '<img src="imagen1.png" alt="1" style="width:200px; float:left; margin-right:40px;">';
 echo "a) Res: ". $resultado; 
 

?> <br style="clear:both;"><br>
<?php
 $x = 3;
 $resultado = ((0.5 * $x) + ((3 + $x) / 2) * (2 * $x * $x)) / ((2 + 3) * $x);
 echo '<img src="2.png" alt="2" style="width:200px; float:left; margin-right:40px;">';
 echo "b) Res: ". $resultado; 
?> <br style="clear:both;"><br>
<?php
 $x = 2;
 $resultado = sqrt (((2 * ($x)**2) + (3 **2)) / 5) + sqrt ($x **2) ;
  echo '<img src="3.png" alt="3" style="width:200px; float:left; margin-right:40px;">';
 echo "c) Res: ". intval($resultado); 
?> <br style="clear:both;"><br>
<?php
 $x = 2;
 $resultado = ((1 / 2 + 1 / 4 + 1 / 8) * pow($x, 1/3)) / (( sqrt ($x) / 2) + (3 * ($x **2)) / 4);
  echo '<img src="4.png" alt="4" style="width:200px; float:left; margin-right:40px;">';
 echo "d) Res: ". ($resultado); 
?> <br style="clear:both;"><br>
    <?php
 $x = 4;
 $resultado = ((($x**2 / 2) + (1 / sqrt($x))) / (3 + (1 / 2) * ($x**3)));
  echo '<img src="5.png" alt="5" style="width:300px; float:left; margin-right:40px;">';
 echo "e) Res: ". sqrt($resultado); 
?> <br style="clear:both;"><br
	</body>
</html>

