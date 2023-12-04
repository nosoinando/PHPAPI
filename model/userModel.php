<?php
require_once ("ConDB.php");
class UserModel{

    static public function createUser($data){
        $cantMail = self::getMail($data['use_mail']);
        if($cantMail==0){
            $query="INSERT INTO users(use_id,use_mail,use_pss,use_dateCreate,us_identifier,us_key,us_status) 
            VALUES (NULL,:use_mail,:use_pss,:use_dateCreate,:us_identifier,:us_key,:us_status)";
                       
            $status=0;// 0->inactivo , 1-> activo
            $date = date('Y-m-d');
            $stament = Connection::connecction()->prepare($query);
            $stament-> bindParam(":use_mail",$data["use_mail"],PDO::PARAM_STR);
            $stament-> bindParam(":use_pss",$data["use_pss"],PDO::PARAM_STR);
            $stament-> bindParam(":use_dateCreate",$date,PDO::PARAM_STR);
            $stament-> bindParam(":us_identifier",$data["us_identifier"],PDO::PARAM_STR);
            $stament-> bindParam(":us_key",$data["us_key"],PDO::PARAM_STR);
            $stament-> bindParam(":us_status",$status,PDO::PARAM_INT);
            $message= $stament->execute() ? "ok" : Connection::connecction()->errorInfo();
            $stament-> closeCursor();
            $stament = null;
            $query = "";
        }else{
            $message = "Usuario ya esta registrado";
        }
        return $message;

    }
    static private function getMail($mail){
        $query="";
        $query= "SELECT use_mail FROM users WHERE use_mail = '$mail' ";
        $stament = Connection::connecction()->prepare($query);
        $stament->execute(); 
        $result=$stament->rowCount();
        
        return $result; 
    }

    static function getUsers($id){  //Funcion que trae todos lo usuario
        $query="";
        $id = is_numeric($id) ? $id : 0;
        $query = "SELECT use_id, use_mail, use_dateCreate FROM users";
        $query.= ($id > 0) ? " WHERE users.use_id = '$id' AND  " : "";
        $query.= ($id > 0) ? " us_status = '1' " : " WHERE us_status= '1' ";
        $stament = Connection::connecction()->prepare($query);
        $stament->execute(); 
        $result=$stament->fetchAll(PDO::FETCH_ASSOC); //Mandar todos los registros asociados a la ejecucion
        return $result; 
    }

    //Login
    static public function login($data){
        $query="";
        $user = $data['use_mail'];
        $pss = md5($data['use_pss']);
        if (!empty($user) && !empty($pss)){
            $query = "SELECT us_key, us_identifier, use_id FROM users WHERE use_mail = '$user' and use_pss= '$pss' and us_status = '1' ";
            $stament = Connection::connecction()->prepare($query);
            $stament->execute(); 
            $result=$stament->fetchAll(PDO::FETCH_ASSOC);
            return $result; 
        }else{
            $mensaje = array(
                "CODE" => "001",
                "MENSAJE" => ("Error en Crendenciales")
            );
            return $mensaje;
        }
    }

    static public function enableUser($id){
        $query = "";
        $query = "UPDATE users SET us_status = '1' WHERE use_id = '$id'";
        $stament = Connection::connecction()->prepare($query);
        $message = $stament->execute() ? "ok" : Connection::connecction()->errorInfo();
        $stament-> closeCursor();
        $stament = null;
        $query = "";
        return $message;
    }

    static public function updateUser($id, $data){
        $query = "";
        $mail = $data["use_mail"];
        $cantMail = self::getMail($mail);
        if($cantMail == 0){
            $query = "UPDATE users SET use_mail = '$mail' WHERE users.use_id = '$id'";
            $stament = Connection::connecction()->prepare($query);
            $message = $stament->execute() ? "ok" : Connection::connecction()->errorInfo();
            $stament-> bindParam(":use_mail",$data["use_mail"],PDO::PARAM_STR);
            $stament-> closeCursor();
            $stament = null;
            $query = "";
        } else {
            $message = "Este correo ya esta registrado";
        }
        return $message;
    }

    static public function deleteUser($id){
        $query = "";
        $query = "UPDATE users SET us_status = '0' WHERE use_id = '$id';";
        $statement = Connection::connecction()->prepare($query);
        $message= $statement->execute() ? "ok" : Connection::connecction()->errorInfo();
        $statement-> closeCursor();
        $statement = null;
        $query = "";
        return $message;
    }

    static public function getUserAuth(){
        $query="";
        $query = "SELECT us_identifier, us_key FROM users WHERE us_status = '1' ";
        $stament = Connection::connecction()->prepare($query);
        $stament->execute(); 
        $result=$stament->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}

?>