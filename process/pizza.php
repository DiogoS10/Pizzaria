<?php
    include_once("conn.php");

    $method = $_SERVER['REQUEST_METHOD'];

    //Resgate dos dados, montagem do pedido
    if ($method == 'GET') {

        $bordasQuery = $conn->query("SELECT * FROM bordas;");

        $bordas = $bordasQuery->fetchAll(); //coloca os dados num array para podermos utilizar

        $massasQuery = $conn->query("SELECT * FROM massas;");

        $massas = $massasQuery->fetchAll();

        $saboresQuery = $conn->query("SELECT * FROM sabores;");

        $sabores = $saboresQuery->fetchAll();


    //Criação do Pedido    
    } else if ($method == 'POST') {

        $data = $_POST;

        $borda = $data["borda"];
        $massa = $data["massa"];
        $sabores = $data["sabores"];

        //Validação de sabores maximos
        if(count($sabores)>3){

            $_SESSION["msg"] = "Selecione no máximo 3 sabores!";
            $_SESSION["status"] = "warning";

        } else {

            //guardar borda e massa na pizza
            $stmt = $conn->prepare("INSERT INTO pizzas (borda_id, massa_id) VALUES (:borda, :massa)");

            //filtrar inputs
            $stmt->bindParam(":borda", $borda, PDO::PARAM_INT);
            $stmt->bindParam(":massa", $massa, PDO::PARAM_INT);

            $stmt->execute();

            //recolher ultimo id da pizza
            $pizzaId = $conn->lastInsertId();

            $stmt= $conn->prepare("INSERT INTO pizza_sabor(pizza_id, sabor_id) VALUES (:pizza, :sabor)");

            //repetição ate terminar de guiardar todos os sabores

            foreach ($sabores as $sabor) {

                //filtrar
        $stmt->bindParam(":pizza", $pizzaId, PDO::PARAM_INT);
                $stmt->bindParam(":sabor", $sabor, PDO::PARAM_INT); 

                $stmt->execute();   
    }

    //criar pedido da pizza

    $stmt = $conn->prepare("INSERT INTO pedidos (pizza_id, status_id) VALUES (:pizza,:status)");

    //status -> sempre inicia com 1, que é em produção

    $statusId = 1;

    //filtrar inputs

    $stmt->bindParam(":pizza", $pizzaId);
    $stmt->bindParam(":status", $statusId,);

    $stmt->execute();
    
    //Exibir mensagem de sucesso
    $_SESSION["msg"]="Pedido Realizado com Sucesso";
    $_SESSION["status"] = "success";
    
}

    //Retorna para pagina inicial
    header("Location: ..");

}

?>