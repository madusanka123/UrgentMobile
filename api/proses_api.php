<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header("Content-type: application/json; charset=utf-8");
header("Access-Control-Allow-Headers: content-type ");

include "confiq.php";

$postjson = json_decode(file_get_contents('php://input'), true);

//process of register

if ($postjson['aksi'] == "proses_register") {

    $cekemail = mysqli_fetch_array(mysqli_query($mysqli, "SELECT email_address FROM tb_user,tb_data WHERE email_address='$postjson[email_address]'"));
   // $cekemail = mysqli_fetch_array(mysqli_query($mysqli, "SELECT email_address FROM tb_user WHERE email_address='$postjson[email_address]'"));

    if ($cekemail['email_address'] == $postjson['email_address']) {
        $result = json_encode(array('success' => false, 'msg' => 'Email is alredy'));
    } else {
        $password = md5($postjson['password']);
        $insert = mysqli_query($mysqli, "INSERT INTO tb_user SET
            your_name      = '$postjson[your_name]',
            email_address  = '$postjson[email_address]',
            password       = '$password'   
        ");
        $insert = mysqli_query($mysqli, "INSERT INTO tb_data SET
            email_address  = '$postjson[email_address]'
        ");


        if ($insert) {
            $result = json_encode(array('success' => true, 'msg' => 'Register successfuly'));
        } else {
            $result = json_encode(array('success' => false, 'msg' => 'Register error'));
        }
    }

    echo $result;

    //process of login

} elseif ($postjson['aksi'] == "proses_login") {
    $password = md5($postjson['password']);
    $logindata = mysqli_fetch_array(mysqli_query($mysqli, "SELECT * FROM tb_user WHERE email_address='$postjson[email_address]' AND password='$password'"));


    $data = array(
        'id_user'        => $logindata['id_user'],
        'your_name'      => $logindata['your_name'],
        'email_address'  => $logindata['email_address']
    );

    if ($logindata) {
        $result = json_encode(array('success' => true, 'result' => $data));
    } else {
        $result = json_encode(array('success' => false));
    }

    echo $result;

    //select user and load
    
} elseif ($postjson['aksi'] == "load_users") {
    $data = array();

    $query = mysqli_query($mysqli, "SELECT * FROM tb_data WHERE email_address='$postjson[email_address]'");

    while ($rows = mysqli_fetch_array($query)) {

        $data[] = array(
            'id_user'       => $rows['id_user'],
            'Name'          => $rows['Name'],
            'Telephone_No'  => $rows['Telephone_No']
        );
    }

    if ($query) {
        $result = json_encode(array('success' => true, 'result' => $data));
    } else {
        $result = json_encode(array('success' => false));
    }

    echo $result;
} elseif ($postjson['aksi'] == "del_users") {
    $query = mysqli_query($mysqli, "DELETE FROM tb_data WHERE id_user='$postjson[id]'");

    if ($query) {
        $result = json_encode(array('success' => true));
    } else {
        $result = json_encode(array('success' => false));
    }

    echo $result;
} elseif ($postjson['aksi'] == "proses_crud") {

    if ($postjson['action'] == "Create") {

        $insert = mysqli_query($mysqli, "INSERT INTO tb_data SET
                Name          = '$postjson[Name]',
                Telephone_No  = '$postjson[Telephone_No]'   
            ");

        if ($insert) {
            $result = json_encode(array('success' => true, 'msg' => 'Successfuly'));
        } else {
            $result = json_encode(array('success' => false, 'msg' => 'Proses error'));
        }

    } else {
        $updt = mysqli_query($mysqli, " UPDATE `tb_data` SET `Name`='$postjson[Name]',`Telephone_No`='$postjson[Telephone_No]' WHERE `id_data`='$postjson[id]' ");

        if ($updt) {
            $result = json_encode(array('success' => true, 'msg' => 'Successfuly'));
        } else {
            $result = json_encode(array('success' => false, 'msg' => 'Proses error'));
        }
    }

    echo $result;
} elseif ($postjson['aksi'] == "load_single_data") {
    $query = mysqli_query($mysqli, "SELECT * FROM tb_user WHERE id_user='$postjson[id]'");

    while ($rows = mysqli_fetch_array($query)) {

        $data = array(
            'your_name'      => $rows['your_name'],
            'email_address'  => $rows['email_address']
        );
    }

    if ($query) {
        $result = json_encode(array('success' => true, 'result' => $data));
    } else {
        $result = json_encode(array('success' => false));
    }

    echo $result;
}
