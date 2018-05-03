<!DOCTYPE html>
 
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
       
        require './Provider/ZkLib.php';
        $zklib = new ZkLib("10.101.10.211");
        print_r($zklib->unlock());
        
        ?>
    </body>
</html>
