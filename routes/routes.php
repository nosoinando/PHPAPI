<?php
$rutasArray = explode("/",$_SERVER['REQUEST_URI']);
$inputs = array();
$inputs['raw_inputs'] = @file_get_contents('php://input');
$_POST = json_decode($inputs['raw_inputs'],true);

if (count(array_filter($rutasArray))<2) {
    $json = array(
        "ruta" => "not found"
    );
    echo json_encode($json,true);
    return;
}else{
    //Endpoint correctos
    $endPoint = (array_filter($rutasArray)[2]);
    $complement = (array_key_exists(3,$rutasArray)) ? ($rutasArray)[3] : 0;
    $add = (array_key_exists(4,$rutasArray)) ? ($rutasArray)[4] : "";
    if($add != ""){
        $complement .= "/" . $add; // Si $add no esta vacia se guarda el complement se adicion un "/" y se adiciona lo de la variable $add
    } 
    $method = $_SERVER['REQUEST_METHOD'];
    switch ($endPoint) {
        case 'users':
            if (isset($_POST)) {
                $user =  new UserController($method,$complement,$_POST);
            // } else if(isset($_REQUEST)) {
            } else {
                $user =  new UserController($method,$complement,0);
            }
            $user->index();
            return;
        case 'login':
            if (isset($_POST) && $method == 'POST') {
                $user = new loginController($method, $_POST);
                $user->index();
            }else{
                $json = array(
                    "ruta" => "not found",
                );
                echo json_encode($json,true);
                return;
            }
        default:
            $json = array(
                "ruta" => "not found",
            );
            echo json_encode($json,true);
            return;
    }
}
?>