<?php

namespace App\Controllers;

class SubjectController extends Controller{

    public function display_sub($req,$res){
        $result  = $this->database->select('subjects','*');
        echo json_encode($result);
    }

    public function getSubject($req,$res){
        $params = $req->getParams();
        $CountPerPage = 12;
        $page = isset($params['page'])?$params['page']:1;
        $search = isset($params['search'])?$params['search']:'';
        $count = $this->database->count('subjects',[
            'SUBTEXT[~]' => $search
        ]);
        $result  = $this->database->select('subjects','*',[
            'SUBTEXT[~]' => $search,
            "LIMIT" => [($page - 1)*$CountPerPage, $CountPerPage]
        ]);
        $rsData['page'] = (int)$page;
        $rsData['pageSize'] = $count;
        $rsData['CountPerPage'] = $CountPerPage;
        $rsData['data'] = $result;
        echo json_encode($rsData);
    }
    public function create_subject($req,$res){
        $id = $req->getParam('sub_id');
        $name = $req->getParam('sub_name');

        if(empty($id) || empty($name) ){
            echo 'null';
        }else{
            $result = $this->database->select('subjects','*',['SUBID'=>$id]);
            if(!empty($result)){
                echo json_encode('already');
            }else{
                $result = $this->database->insert('subjects',['SUBID'=>$id ,'SUBTEXT'=>$name]);
                echo json_encode('success');
            }
        }
    }
    public function edit_subject($req,$res){
        $id = $req->getParam('sub_id');
        $name = $req->getParam('sub_name');

        if(empty($id) || empty($name) ){
            echo 'null';
        }else{
            $result = $this->database->update('sub',['SUBNAME'   => $name],['IDSUB'     => $id]);
            echo 'success';
        }
    }
    public function del_subject($req,$res){
        $id = $req->getParam('id');
        if(empty($id)){
            echo 'null';
        }else{
            $SubQuestion = $this->database->select('question','*',['SUBID'=> $id]);
            for($i = 0;$i < count($SubQuestion);$i++){
                $this->database->delete('answer',['ID_QUE'=> $SubQuestion[$i]['ID_QUE']]);
            }
            $this->database->delete('question',['SUBID'=> $id]);
            $this->database->delete('subjects',['SUBID'=> $id]);
            $message['success'] = 'Đã xóa thành công';
            echo json_encode($message);
        }
    }
}