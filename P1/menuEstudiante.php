<html>
    <head>
        <title>Menú estudiante</title>
        <style type="text/css">
            *{
                font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
                position: relative;
            }

            p, h2{
                margin: 0;
                padding: 0;
                height: fit-content;
            }

            h2{
                background-color: lightgray;
                padding: 5px;
                padding-left: 10px;
                border-radius: 5px;
            }

            #head{
                height: fit-content;
                display: flex;
                justify-content: space-between;
            }

            #head h3{
                margin: 0;
            }

            #title{
                text-align: center;
                border: 2px solid;
                background-color: #97CAEF;
                font-size: larger;
            }

            body{
                padding: 20px 40px;
                max-width: 800px;
                margin: 0px auto;
            }

            .menu{
                border: 2px solid;
                margin-top: 20px;
                margin-bottom: 10px;
            }

            .menu form{
                margin: 0px;
            }

            .menu button{
                width: 100%;
                height: 3em;
            }

            #cerrar-sesion{
                height: fit-content;
                text-decoration: underline;
                font-size: small;
                cursor: pointer;
                color: blue;
                margin: auto 1px;
            }

            table{
                width: calc(100% - 20px);
                text-align: center;
                border: 2px solid;
                border-collapse: collapse;
                margin: 0 auto;
                margin-top: 10px;
            }

            th, tr, td{
                border: 2px solid black;
            }
        </style>
    </head>
    <body>
        <?php
            //Conexion a base de datos
            session_start();
            $conexion = mysqli_connect("127.0.0.1", "root", "", "bduca");
            $id_usuario = $_SESSION['id_usuario'];
            //Obtener nombre y apellidos del usuario
            $consulta = mysqli_query($conexion, "SELECT * FROM usuario WHERE id_usuario = '".$id_usuario."'");
            $consulta = mysqli_fetch_array($consulta);

            $nombre = $consulta['nombre'];
            $apellido = $consulta['apellido'];
            echo '<div id="head">';
            echo '<h3>Bienvenido, '.$nombre .' '. $apellido;
            echo '</h3>';
            echo '<a href="index.php" id="cerrar-sesion">Cerrar sesión</a>';
            echo '</div>';
        ?>
        <div class="menu">
            <h2 id="title">MENÚ ESTUDIANTE</h2>
            <form method="POST">
                <button name="boton" value="examen" type="submit">Realizar examen</button>
                <button name="boton" value="calificacion" type="submit">Consultar calificaciones</button>
                <button name="boton" value="revision" type="submit">Revisiones</button>
            </form>
        </div>
        <br>
        <?php
            if(isset($_POST['boton'])){
                if($_POST["boton"] == 'examen'){
                    $fechaActual = date('Y-m-d');
                    $consulta = mysqli_query($conexion, "SELECT * FROM examen e WHERE fecha >= CURRENT_DATE() AND (id_examen IN(SELECT id_examen FROM preguntaexamen WHERE respuesta IS NULL) OR NOT EXISTS (SELECT id_examen FROM preguntaexamen WHERE ".'e.id_examen'." = id_examen))");
                    $rows = mysqli_num_rows($consulta);
                    echo '<h2>Examenes</h2>';
                    echo "<table style>";
                    echo "<tr>";
                        echo "<th>Examen";
                        echo "<th>Fecha";
                        echo "<th>Disponibilidad";
                    for($i = 0; $i<$rows; $i++){
                        $examen = mysqli_fetch_array($consulta);
                        $queryNomTema = mysqli_query($conexion, "SELECT nombre_tema FROM tema WHERE id_tema IN (SELECT id_tema FROM examen WHERE id_examen = '".$examen['id_examen']."')");
                        $nomTema = mysqli_fetch_array($queryNomTema);
                        echo "<tr>";
                            echo "<td>".$nomTema['nombre_tema'];
                            if($examen['fecha'] == $fechaActual){
                                echo '<form action="examen.php" method="POST">';
                                echo '<td style="background-color:#34B23333; color:#34B233; width: 20%;">'.$examen['fecha'];
                                echo '<td style="width:20%;"><button name="boton" value='.$examen['id_examen'].' type="submit" style="width:100%; border:0px; text-decoration:underline; color:blue; cursor:pointer">Realizar</button>';
                                echo '</form>';
                            }
                            else{
                                echo "<td>".$examen['fecha'];
                                echo '<td style="width:20%;"><button style="width:100%; border:0px; cursor:default; color: gray;">No disponible</button>';
                            }
                    }      
                    echo "</table>";
                }
                elseif($_POST["boton"] == 'calificacion'){
                    $consulta = mysqli_query($conexion, "SELECT * FROM examen WHERE id_alumno = '".$id_usuario."'AND calificacion IS NOT NULL");
                    $rows = mysqli_num_rows($consulta);
                    echo '<h2>Calificaciones</h2>';
                    echo "<table>";
                        echo "<tr>";
                            echo '<th style="width:80%">Examen</th>';
                            echo "<th>Calificacion</th>";
                        echo "</tr>";
                    for($i=0; $i<$rows; $i++){
                        $res = mysqli_fetch_array($consulta);
                        //Conseguir nombre examen
                        $queryNomExam = mysqli_query($conexion, "SELECT nombre_tema FROM tema WHERE id_tema IN (SELECT id_tema FROM examen WHERE id_examen = '".$res['id_examen']."')");
                        $nomExam = mysqli_fetch_array($queryNomExam);
                        //Construccion tabla calificaciones
                        echo "<tr>";
                            echo "<td>" .$nomExam['nombre_tema'];
                            if($res['calificacion'] >= 5){
                                echo '<td style="color:#34B233; font-weight:bold; background-color: #34B23333;">' .$res['calificacion'];
                            }
                            else{
                                echo '<td style="color:#FF0000; font-weight:bold; background-color: #FF000055;">' .$res['calificacion'];
                            }
                        echo "</tr>";
                    }
                    echo "</table>";
                }
                elseif($_POST["boton"] == 'revision'){
                    echo '<h2>Calificaciones</h2>';
                    $consulta = mysqli_query($conexion, "SELECT * FROM examen WHERE id_alumno = '".$id_usuario."'AND calificacion IS NOT NULL");
                    $rows = mysqli_num_rows($consulta);
                    echo "<table>";
                        echo "<tr>";
                            echo '<th style="width:80%">Examen</th>';
                            echo "<th>Revision</th>";
                        echo "</tr>";
                    for($i=0; $i<$rows; $i++){
                        $res = mysqli_fetch_array($consulta);
                        //Conseguir nombre examen
                        $queryNomExam = mysqli_query($conexion, "SELECT nombre_tema FROM tema WHERE id_tema IN (SELECT id_tema FROM examen WHERE id_examen = '".$res['id_examen']."')");
                        $nomExam = mysqli_fetch_array($queryNomExam);
                        //Construccion tabla calificaciones
                        echo "<tr>";
                            echo "<td>" .$nomExam['nombre_tema'];
                            echo '<form action="revision.php" method="POST">';
                            echo '<td style="width:20%;"><button name="revision" value='.$res['id_examen'].' type="submit" style="width:100%; border:0px; text-decoration:underline; color:blue; cursor:pointer">Revisar</button>';
                            echo '</form>';
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            }
            mysqli_close($conexion);
        ?>
    </body>
</html>