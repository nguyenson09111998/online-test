<?php

namespace App\Controllers;
// use Ramsey\Uuid\Uuid;

class ExamController extends Controller{
    private $tableName = 'feedback_question';
    public function index($request,$response){

        if(isset($_SESSION['success'])){
            return $this->view->render($response,'mng_exam.phtml');            
        }else{
            return $response->withRedirect($this->router->pathFor('sign_in'));
        }
    }

    public function display_exam($request,$response){
        $sql = "SELECT * FROM exam JOIN subjects ON exam.SUBID = subjects.SUBID";
        $result = $this->database->query($sql)->fetchAll();
        echo json_encode($result);
    }
    public function add_exam($request,$response){
        $dataQuestion = $request->getParam('dataSave');
        $id = $request->getParam('id');
        $name = $request->getParam('name');
        $time = $request->getParam('time');
        $subject = $request->getParam('subject');
        $data = count($dataQuestion);
        if(empty($id) || empty($name) || empty($name) || empty($time) || empty($subject)){
            echo json_encode('null');
        }else{
            $result = $this->database->select('exam','*',['IDEXAM' => $id]);
            if(!empty($result)){
                echo json_encode('alreadyid');
            }else{
                $this->database->insert('exam',[
                    'IDEXAM'    => $id,
                    'EXAMTEXT'  => $name,
                    'SUBID'     => $subject,
                    'EXTIME'    => $time,
                    'EXNUM'     => $data,
                ]);
                for($i = 0;$i < $data; $i++){
                    if(!empty($dataQuestion[$i])){
                        $this->database->insert('detail_exam',[
                            'IDEXAM'    => $id,
                            'ID_QUE'    => $dataQuestion[$i]
                        ]);
                    }
                }
                echo json_encode('success');
            }
        }
    } 
    private function GetSubjectId($id){
        $result = $this->database->select('subjects','*',['SUBID' => $id]);
        return $result;
    }

    public function GetExamSubjectId($request,$response){
        $id = $request->getParam('id');
        if(!empty($id)){
            $sql = "SELECT * FROM exam JOIN subjects ON exam.SUBID = subjects.SUBID WHERE subjects.SUBID ='$id'";
            $result = $this->database->query($sql)->fetchAll();
            $data = [];
            $title = $this->GetSubjectId($id);
            if(!empty($result)){
                $data[] =[
                    'title' => $title[0],
                    'exams' => $result
                ];
                echo json_encode($data);exit;
            }else{
                $data[] =[
                    'title' => $title[0],
                    'exams' => ''
                ];
                echo json_encode($data);exit;
            }
            
        }else{
            $message['error'] = 'Không tìm thấy dữ liệu!';
            echo json_encode($message);
        }
    }

    public function DetailExam($request,$response,$args){
        $id = $args['id'];
        if(!empty($id)){
            $sql = "SELECT * FROM exam JOIN subjects ON exam.SUBID = subjects.SUBID WHERE exam.IDEXAM ='$id'";
            $result = $this->database->query($sql)->fetchAll();
            echo json_encode($result);
        }else{
            echo json_encode('null');
        }
    }
    public function GetExam($request,$response,$args){
        $id     = $request->getParam('id');
        $idux   = $request->getParam('idux');
        $result = $this->RequestExamRandomId($id,$idux);
        $questions = [];
        for($i = 0;$i < count($result);$i++){
            $answer = $this->RequestQuestionId($result[$i]['ID_QUE']);
            $answerRandom = $this->RandomAnserId($answer);
            $questions[] = [
                'ID_QUE'       => $result[$i]['ID_QUE'],
                'QUE_TEXT'     => $result[$i]['QUE_TEXT'],
                'Answer'       => $answerRandom
            ];
        }
        echo json_encode($questions);
    }
    private function RequestExamRandomId($id,$idux){
        if($id){
            $sql = "SELECT * FROM `random_exam` INNER JOIN question ON question.ID_QUE = random_exam.ID_QUE WHERE ID_UX='$idux' AND IDEXAM='$id'";
            $result = $this->database->query($sql)->fetchAll();
            return $result;
        }
        return [];
    }
    private function RandomAnserId($input){
        if(!empty($input)){
            $arr = [];
            $numbers = range(0,count($input)-1);
            shuffle($numbers);
            for($i = 0;$i < count($input);$i++){
                $arr[] = $input[$numbers[$i]];
            }
            return $arr;
        }else{
            return [];
        }
    }

    public function GetAnswerQuestionId($request,$response){
        $sql = "SELECT answer.ID_QUE,answer.ID_ANS,answer.ANS_TEXT,answer.CORRECT FROM question JOIN answer ON answer.ID_QUE = question.ID_QUE ";
        $result = $this->database->query($sql)->fetchAll();
        echo json_encode($result);
    }
    public function RequestAnswerQuestionId($request,$response){
        $id = $request->getParam('id');
        $sql = "SELECT answer.ID_QUE,answer.ID_ANS,answer.ANS_TEXT,answer.CORRECT FROM question JOIN answer ON answer.ID_QUE = question.ID_QUE WHERE question.ID_QUE = '$id'";
        $result = $this->database->query($sql)->fetchAll();
        echo json_encode($result);
    }
    public function GetUserExamId($request,$response){
        $idExam = $request->getParam('idExam');
        $idUser = $request->getParam('idUser');
        if(!empty($idExam) && !empty($idUser)){
            $checkConfirm = $this->database->select('user_exam','ID_UX',[
                'IDUSER' => $idUser,
                'IDEXAM'    => $idExam,
                "ORDER" => ["ID_UX" => "DESC"],
                "LIMIT" => 1
            ]);
            echo json_encode($checkConfirm[0]);
        }else{
            $message['error'] ='Không lấy thành công!';
            echo json_encode($message);
        }
    }
    public function GetExamMinuteId($request,$response){
        $id = $request->getParam('id');
        if(!empty($id)){
            $result = $this->database->select('exam','EXTIME',['IDEXAM' => $id]);
            if(!empty($result)){
                echo json_encode($result[0]);
            }else{
                $message['error'] = 'Trống!';
                echo json_encode($message);
            }
        }
    }
    public function GetUserExam($request,$response){
        $idExam = $request->getParam('idExam');
        $idUser = $request->getParam('idUser');
        $timeNow = $request->getParam('timeNow');
        $dateNow = $request->getParam('dateNow');
        if(!empty($idExam) && !empty($idUser)){
            
            $checkConfirm = $this->database->select('user_exam','*',[
                'IDUSER' => $idUser,
                'IDEXAM'    => $idExam,
                "ORDER" => ["ID_UX" => "DESC"],
                "LIMIT" => 1
            ]);
            $arr = $checkConfirm[0];
            if(empty($checkConfirm) || $arr['CONFIRM'] == "true"){
                $this->database->insert('user_exam',[
                    'IDUSER'    => $idUser,
                    'IDEXAM'    => $idExam,
                    'TIMESTART' => $timeNow,
                    'DATEEXAM'  => $dateNow
                ]);
                $message['success'] ='Đã thêm thành công!';
                echo json_encode($message);
            }else if($arr['CONFIRM'] == null){
                $this->database->update('user_exam',[
                    'TIMESTART' => $timeNow,
                    'DATEEXAM'  => $dateNow],[
                        'ID_UX' => $arr['ID_UX'],
                        'IDUSER'    => $idUser
                    ]
                );
                $message['success'] ='Đã cập nhật thành công!';
                echo json_encode($message);
            }else{
                echo json_encode('Chưa xử lý được dữ liệu');
            }
            
        }else{
            $message['error'] ='Không thành công!';
            echo json_encode($message);
        }
        
    }

    public function GetQuestionUser($request, $response){
        $idExam     = $request->getParam('idExam');
        $idUser     = $request->getParam('idUser');
        $timeNow    = $request->getParam('timeNow');
        $QueID  = $request->getParam('questions');
        $number     = count($QueID);
        $sqlAns = "SELECT question.ID_QUE,answer.ID_ANS FROM question INNER JOIN detail_exam ON detail_exam.ID_QUE = question.ID_QUE INNER JOIN answer ON answer.ID_QUE = question.ID_QUE WHERE detail_exam.IDEXAM = '$idExam' AND answer.CORRECT = 'true'";
        $resultAns = $this->database->query($sqlAns)->fetchAll();
        $m = count($resultAns);
        if(!empty($idExam) && !empty($idUser)){
            $checkConfirm = $this->database->select('user_exam','*',[
                'IDUSER'    => $idUser,
                'IDEXAM'    => $idExam,
                "ORDER"     => ["ID_UX" => "DESC"],
                "LIMIT"     => 1
            ]);
            $arr = $checkConfirm[0];
            if($arr['CONFIRM'] == null){
                $dem = 0;
                for($i = 0;$i < $m; $i++){
                    for($j = 0;$j < $number;$j++){
                        if($QueID[$j]['idQue'] == $resultAns[$i]['ID_QUE'] && $QueID[$j]['idAns'] == $resultAns[$i]['ID_ANS']){
                            $dem += 1;
                        }
                    }
                }  
                $this->database->update('user_exam',[
                    'TIMEEND'   => $timeNow,
                    'CONFIRM'   => 'true',
                    'SCORE'     => $dem
                ],[
                    'ID_UX' => $arr['ID_UX']
                ]);   
                        
                for($i = 0; $i < $number; $i++){
                    if(!empty($QueID[$i])){
                        $this->database->insert('detail_user_exam',[
                            'ID_UX'     => $arr['ID_UX'],
                            'ID_ANS'    => $QueID[$i]['idAns'],
                            'ID_QUE'    => $QueID[$i]['idQue']
                        ]);
                    }
                }
                $message['success'] = 'insert successfully!';
                echo json_encode($message);
            }else{
                $message['error'] = 'Có lỗi sảy ra,không thêm được dữ liệu!';
                echo json_encode($message);
            }
        }else{
            $message['error'] = 'Chưa lấy được dữ liệu!';
            echo json_encode($message);
        }
    }

    private function RequestQuestionId($id){
        if($id){
            $sql = "SELECT * FROM question JOIN answer ON answer.ID_QUE = question.ID_QUE WHERE question.ID_QUE = '$id'";
            $result = $this->database->query($sql)->fetchAll();
            return $result;
        }
        return [];
    }
    private function RequestExamId($id){
        if($id){
            $sql = "SELECT * FROM detail_exam INNER JOIN exam ON exam.IDEXAM = detail_exam.IDEXAM INNER JOIN question ON detail_exam.ID_QUE = question.ID_QUE WHERE exam.IDEXAM = '$id'";
            $result = $this->database->query($sql)->fetchAll();
            return $result;
        }
        return [];
    }
    private function RequestUserAnswer($idux,$idQuestion){
        if(!empty($idux) && !empty($idQuestion)){
            // $sql = "SELECT * FROM `user_exam` JOIN detail_user_exam ON user_exam.ID_UX = detail_user_exam.ID_UX WHERE user_exam.ID_UX = '$idux' AND user_exam.IDEXAM = '$idExam' AND detail_user_exam.ID_QUE = '$idQuestion'";
            $sql = "SELECT * FROM `user_exam` JOIN detail_user_exam ON user_exam.ID_UX = detail_user_exam.ID_UX WHERE user_exam.ID_UX = '$idux' AND detail_user_exam.ID_QUE = '$idQuestion'";            
            $result = $this->database->query($sql)->fetchAll();
            if(!empty($result)){
                return $result;    
            }
            return [];
        }
        return [];
    }

    public function RequestExam($request,$response){
        $id = $request->getParam('id');
        $idux = $request->getParam('idux');
        $result = $this->RequestExamRandom($id,$idux);
        if(!empty($id) && !empty($idux) && !empty($result)){
            $questions = [];
            for($i = 0;$i < count($result);$i++){
                $answer = $this->RequestQuestionId($result[$i]['ID_QUE']);
                $UserAnswer = $this->RequestUserAnswer($idux,$result[$i]['ID_QUE']);
                $ID_ANS = [];
                if(!empty($UserAnswer)){
                    $ID_ANS = $UserAnswer[0]['ID_ANS'];
                }
                $questions[] = [
                    'ID_QUE'       => $result[$i]['ID_QUE'],
                    'QUE_TEXT'     => $result[$i]['QUE_TEXT'],
                    'Answer'       => $answer,
                    'UserAnswer'   => $ID_ANS
                ];
            }
            echo json_encode($questions);
        }else{
            $message['error'] = 'Chưa lấy được dữ liệu!';
            echo json_encode($message);
        }
    }
    private function RequestExamRandom($id,$idux){
        if($id){
            $sql = "SELECT * FROM random_exam INNER JOIN question ON question.ID_QUE = random_exam.ID_QUE WHERE random_exam.ID_UX = '$idux' AND random_exam.IDEXAM = '$id'";
            $result = $this->database->query($sql)->fetchAll();
            return $result;
        }
        return [];
    }

    public function GetResultRequest($request,$response,$args){
        $id = $args['id'];
        if(!empty($id)){
            $sql = "SELECT * FROM `user_exam` JOIN exam ON user_exam.IDEXAM = exam.IDEXAM WHERE user_exam.ID_UX =".$id." ";
            $result = $this->database->query($sql)->fetchAll();
            if(!empty($result)){
                echo json_encode($result);
            }else{
                $message['error'] = 'Dữ liệu không tồn tại!';
                echo json_encode($message);
            }
        }else{
            $message['error'] = 'Không tìm thấy dữ liệu!';
            echo json_encode($message);
        }
    }
    public function GetHistoryExamUser($request,$response){
        $id = $request->getParam('id');
        if(!empty($id)){
            $sql = "SELECT * FROM exam INNER JOIN user_exam ON user_exam.IDEXAM = exam.IDEXAM INNER JOIN subjects ON subjects.SUBID = exam.SUBID WHERE user_exam.IDUSER = '$id' ORDER BY user_exam.ID_UX DESC";
            $result = $this->database->query($sql)->fetchAll();
            if(!empty($result)){
                echo json_encode($result);
            }else{
                $message['error'] = 'Trống!';
                echo json_encode($message);
            }
        }else{
            $message['error'] = 'Không tìm thấy dữ liệu!';
            echo json_encode($message);
        }
    }
    private function uuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    public function SaveFeedBack($request,$response){
        
        $rsData = array(
            'status' => 'error',
            'message' => 'Xin lỗi! Dữ liệu chưa được cập nhật thành công!'
        );
        // $uuid1 = Uuid::uuid4();
        // $id = $uuid1->toString();
        // $date = new \DateTime();
        // echo json_encode($date);    
        // die;
        $params = $request->getParams();
        $id             = isset(	$params['id']) ? $params['id'] : '';
        $user_id        = isset(	$params['user_id']) ? $params['user_id'] : '';
        $content        = isset(	$params['valueFeedback']) ? $params['valueFeedback'] : '';
        $question_id    = isset(	$params['question_id']) ? $params['question_id'] : '';
        $time           = isset(	$params['time']) ? $params['time'] : '';
        $exam_id        = isset(	$params['exam_id']) ? $params['exam_id'] : '';

        if(empty($user_id)) {
			$rsData['message'] = 'Người dùng không được để trống!';
			echo json_encode($rsData);
			return;
		}
		if(empty($content)) {
			$rsData['message'] = 'Nội dung không được để trống!';
			echo json_encode($rsData);
			return;
        }
        if(empty($question_id)) {
			$rsData['message'] = 'không tìm thấy id câu hỏi!';
			echo json_encode($rsData);
			return;
        }
        if(empty($exam_id)) {
			$rsData['message'] = 'không tìm thấy id đề thi!';
			echo json_encode($rsData);
			return;
        }
        if(!$id){
            $id = $this->uuid();
            $date = new \DateTime();
            $datetime = $date->format('Y-m-d')." ".$time;
            
            $itemData = [
				'id'	=> $id,
                'user_id' => $user_id,
                'exam_id' => $exam_id,
				'content' => $content,
				'question_id' => $question_id,
				'status' => 1,
				'create_on' => $datetime
            ];
            $selectColumns = ['id'];
            $where = ['id' => $itemData['id']];
            $data = $this->database->select($this->tableName, $selectColumns, $where);
            if(!empty($data)) {
				$rsData['message'] = "Vui lòng nhập lại.chưa gửi được!";
				echo json_encode($rsData);exit;
            }
            $result = $this->database->insert($this->tableName, $itemData);
            if($result->rowCount()) {
				$rsData['status'] = 'success';
				$rsData['message'] = 'Đã gửi thành công!';
			} else {
				$rsData['message'] = 'Dữ liệu chưa được cập nhật vào cơ sở dữ liệu!';
			}
        }
        echo json_encode($rsData);
    }
}