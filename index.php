<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <pre>
        <?php
       
        require './Provider/ZkLib.php';
        $zklib = new ZkLib("10.101.10.211");
        print_r($zklib->unlock());
        
        ?>
        </pre>
    </body>
</html>
