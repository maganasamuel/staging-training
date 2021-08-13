<?php
/**
 * @name: Test.helper.php
 * @author: Gio
 * @desc:
 * Serves as the API of the test form and test checker page
 * This page handles all asynchronous javascript request from the above mentioned page
 * @returnType:
 * JSON
 */

$autoloadPath = __DIR__ . '../package/vendor/autoload.php';

if (! file_exists($autoloadPath)) {
    $autoloadPath = $_SERVER['DOCUMENT_ROOT'] . '/staging/staging-training/package/vendor/autoload.php';
    // $autoloadPath = $_SERVER['DOCUMENT_ROOT'] . '/package/vendor/autoload.php';
}

require_once $autoloadPath;

include_once('class/DB.class.php');

class TrainingController extends DB
{
    /**
     * @desc: Init class
     */
    public function __construct()
    {
        // init API
        parent::__construct();
    }

    public function addTraining(
        $trainer_id = '',
        $training_topic = '',
        $training_attendee,
        $training_date = '',
        $training_venue = '',
        $attendee_id = '',
        $trainer_signature = '',
        $topic_type = ''
    )
    {
        $date = date('Y-m-d');

        $query = "INSERT INTO ta_training (
                    trainer_id,
                    training_topic,
                    training_attendee,
                    training_date,
                    attendee_signiture,
                    trainer_signiture,
                    training_venue,
                    attendee_id,
                    training_type
                )
                VALUES (
                    '$trainer_id',
                    '$training_topic',
                    '$training_attendee',
                    '$training_date',
                    '',
                    '$trainer_signature',
                    '$training_venue',
                    '$attendee_id',
                    '$topic_type'
                )";

        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
        $insert_id = $this->mysqli->insert_id;

        return $insert_id;
    }

    public function getTraining($id, $idUserType)
    {
        //prepare/execute
        $query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);



        $query = 'SELECT 
                    ta_user.id_user,
                    ta_training.training_id,
                    ta_training.training_topic,
                    ta_training.training_attendee,
                    CONCAT(ta_user.first_name,"     ",ta_user.last_name) as fullname,
                    ta_training.training_date
                FROM
                    ta_training
                    LEFT JOIN ta_user
                    ON ta_user.id_user = ta_training.trainer_id
                    ';

        if($idUserType == 1  || $idUserType == 3){
            //do nothing
        }else{
             $query .= "WHERE ta_training.trainer_id = '$id' OR 
                    ta_training.training_attendee LIKE '%$id%'";
        }

        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function getAdviser()
    {
        $query = 'SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user) FROM ta_user WHERE id_user != "1" and email_address like "%eliteinsure.co.nz" and status = "1" and id_user_type = "2" GROUP BY email_address) ';

        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }
    public function getADR()
    {
        $query = 'SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user) FROM ta_user WHERE id_user != "1" and email_address like "%eliteinsure.co.nz" and status = "1" and id_user_type = "7" GROUP BY email_address) ';

        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

     public function getSADR()
    {
        $query = 'SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user) FROM ta_user WHERE id_user != "1" and email_address like "%eliteinsure.co.nz" and status = "1" and id_user_type = "8" GROUP BY email_address) ';

        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function getTrainingDetail($id)
    {
        $query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

        $query = "SELECT *
        FROM ta_training
        WHERE training_id = $id";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function getAttendee($id)
    {
        $query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

        $query = "SELECT *
        FROM ta_user
        WHERE id_user = $id";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function addUserTraining($email_address,$first_name,$last_name,$password,$user_type,$ssf_number ='0',$adr_id,$sadr_id){

        $query = "SELECT * FROM ta_user where email_address = '$email_address'";
        $statement = $this->prepare($query);
        $chckemail = $this->execute($statement);

        $query = "SELECT * FROM ta_user where ssf_number = '$ssf_number'";
        $statement = $this->prepare($query);
        $chckfsp = $this->execute($statement);

        while ($row = $chckemail->fetch_assoc()) {
            if($row['email_address'] == $email_address){
                return "existed";
            }
        }
        while ($row = $chckfsp->fetch_assoc()) {
            if($row['ssf_number'] == $ssf_number){
                return "fspexisted";
            }
        }   


        $query = "INSERT INTO ta_user (
                    email_address,
                    first_name,
                    last_name,
                    password,
                    id_user_type,
                    ssf_number,
                    adr_id,
                    sadr_id
                )
                VALUES (
                    '$email_address',
                    '$first_name',
                    '$last_name',
                    '$password',
                    '$user_type',
                    '$ssf_number',
                    '$adr_id',
                    '$sadr_id'
                )";

        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
        $insert_id = $this->mysqli->insert_id;
            
}

    public function trainingLogin($email_address, $password)
    {
        $query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

        $query = "SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user)
FROM ta_user WHERE email_address = '$email_address' GROUP BY email_address)  ";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function adminLogin($email_address)
    {
        $query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

        $query = "SELECT *
        FROM ta_user
        WHERE email_address = '$email_address'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function conductedTraining($id)
    {
        $query = "SELECT *
        FROM ta_training
        WHERE trainer_id = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function gettotalContducted($id)
    {
        $query = "SELECT COUNT(trainer_id) AS totalConducted FROM ta_training WHERE trainer_id = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
        $totalContacted = $dataset->fetch_assoc();

        return $totalContacted['totalConducted'];
    }

    public function gettotalAttended($id)
    {
        $query = "SELECT COUNT(trainer_id) AS totalAttended FROM ta_training WHERE FIND_IN_SET ('$id',training_attendee)";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        $totalAttended = $dataset->fetch_assoc();

        return $totalAttended['totalAttended'];
    }

    public function attendedTraining($id)
    {
        $query = "SELECT * FROM ta_training WHERE FIND_IN_SET ('$id',training_attendee) and training_type = '2'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function cpdTraining($id)
    {
        $query = "SELECT * FROM ta_training WHERE FIND_IN_SET ('$id',training_attendee) and training_type = '1'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function deleteTraining($id)
    {
        $query = "DELETE FROM ta_training WHERE training_id = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function getUser()
    {
        $query = 'SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user) FROM ta_user WHERE id_user != "1" and email_address like "%eliteinsure.co.nz" GROUP BY email_address) ';
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function getSpecificUser($id)
    {
        $query = "SELECT * FROM ta_user where id_user = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function updateUserTraining($first_name='',$last_name='', $email_address = '', $password = '', $ssf_number = 0, $user_type = '', $id_user = '',$adr_id = 0,$sadr_id = 0)
    {
        $query = "UPDATE ta_user SET email_address = '$email_address' , first_name = '$first_name' , last_name = '$last_name' , password = '$password' , id_user_type = '$user_type' , ssf_number = '$ssf_number' , adr_id = '$adr_id' , sadr_id = '$sadr_id'
                WHERE id_user = '$id_user'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function deteUsertraining($id)
    {
        $query = "DELETE FROM ta_user WHERE id_user = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }
    public function addCPD($cpd_name,$cpd_description){
            $query = "INSERT INTO training_cpd (
                    cpd_name,
                    cpd_description)
                VALUES (
                    '$cpd_name',
                    '$cpd_description')";

        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
        $insert_id = $this->mysqli->insert_id;

        return $insert_id;
    }
    public function getCPD(){
        $query = "SELECT * FROM training_cpd";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
        return $dataset;
    }
    public function getSpecificCpd($id){
        $query = "SELECT * FROM training_cpd where id_cpd = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }
    public function deleteCPD($id){
        $query = "DELETE FROM training_cpd WHERE id_cpd = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }
    public function getModularTraining (
        $idProfile // specific ID to be displayed
    ) {
        //prepare/execute
        $query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

        $query = "SELECT 
            tsa.set_name,
            DATE_FORMAT(DATE(tta.date_took), '%d %b %Y') date_took,
            fn_get_test_score(tta.id_test) score,
            fn_get_test_max_score(tta.id_set) max_score,(
            SELECT
                COUNT(1)
            FROM 
                ta_test ttb LEFT JOIN (
                    SELECT
                        COUNT(*) answer_count,
                        ta_test_detail.id_test
                    FROM
                        ta_test_detail
                    GROUP BY
                        ta_test_detail.id_test
                ) tdb ON ttb.id_test = tdb.id_test LEFT JOIN 
                ta_set tsb ON ttb.id_set = tsb.id_set LEFT JOIN (
                    SELECT
                        COUNT(*) question_count,
                        ta_set_question.id_set
                    FROM
                        ta_set_question
                    GROUP BY
                        ta_set_question.id_set
                ) sqb ON tsb.id_set = sqb.id_set
           WHERE
                ttb.id_user_tested = tua.id_user AND
                ttb.id_set = tsa.id_set AND
                ttb.is_deleted = 0 AND
                tdb.answer_count = sqb.question_count
            ) attempts
        FROM 
            ta_test tta LEFT JOIN 
            ta_set tsa ON tta.id_set = tsa.id_set LEFT JOIN
            ta_user tua ON tta.id_user_tested = tua.id_user
        WHERE
            tta.id_test IN (
                SELECT
                    MAX(ta_test.id_test) id_test
                FROM
                    ta_test LEFT JOIN 
                    ta_user ta_user_took ON ta_test.id_user_tested = ta_user_took.id_user LEFT JOIN (
                        SELECT
                            COUNT(*) answer_count,
                            ta_test_detail.id_test
                        FROM
                            ta_test_detail
                        GROUP BY
                            ta_test_detail.id_test
                    ) test_detail ON ta_test.id_test = test_detail.id_test LEFT JOIN 
                    ta_set ON ta_test.id_set = ta_set.id_set LEFT JOIN (
                        SELECT
                            COUNT(*) question_count,
                            ta_set_question.id_set
                        FROM
                            ta_set_question
                        GROUP BY
                            ta_set_question.id_set
                    ) set_question ON ta_set.id_set = set_question.id_set
                WHERE 
                    ta_user_took.email_address = '{$idProfile}' AND 
                    ta_test.is_deleted = 0 AND 
                    test_detail.answer_count = set_question.question_count
                GROUP BY
                    ta_set.id_set
                ORDER BY
                    ta_test.id_set DESC,
                    ta_test.id_test DESC
        ) ORDER BY
            tsa.set_name";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }
    public function activeStatus($id,$status){
    	$query = "UPDATE ta_user SET status = '{$status}' where id_user = '{$id}'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
    }
    public function updateCPD($topic,$description,$id){
        $query = "UPDATE training_cpd SET cpd_name = '{$topic}',cpd_description = '{$description}' where id_cpd = '{$id}'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
    }

    public function verfiyEmail($emailAddress){
        $query = "SELECT link_status FROM ta_user WHERE email_address = '{$emailAddress}' ORDER BY date_registered DESC LIMIT 0,1";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
        $dataset = $dataset->fetch_assoc();

        $link_status = $dataset['link_status'];

        if($link_status == 1) {
            $query = "UPDATE ta_user SET status = '1' , link_status = '0' where email_address = '{$emailAddress}'";
            $statement = $this->prepare($query);
            $dataset = $this->execute($statement);
        }
    }
    public function sendPassword($emailAddress){
        $query = "SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user)
FROM ta_user WHERE email_address = '$emailAddress' GROUP BY email_address) ";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
        $pword = $dataset->fetch_assoc();

        return $pword['password'];

    }
    public function adviserTeam($id){
        $query = "SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user) FROM ta_user WHERE sadr_id = '$id' and id_user_type = '2' GROUP BY email_address)  ";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }
    public function adminadrTeam($id){
        $query = "SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user) FROM ta_user WHERE sadr_id = '$id' and id_user_type = '7' GROUP BY email_address)  ";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }
    public function adrTeam($id){
        $query = "SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user) FROM ta_user WHERE adr_id = '$id' GROUP BY email_address)  ";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }
    public function getAdrMember($id){
        $query = "SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user) FROM ta_user WHERE adr_id = '$id' GROUP BY email_address)  ";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }
    public function getSadrMember($id){
        $query = "SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user) FROM ta_user WHERE sadr_id = '$id'  GROUP BY email_address)  ";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }
    public function getTrainingMaterials(){
        $query = "SELECT * FROM ta_materials ORDER BY material_title DESC";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }
     public function getMaterials(){
        $query = "SELECT * FROM ta_materials";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;   
    }
    public function addMaterial($topicTitle,$fileName,$path){
         $query = "INSERT INTO ta_materials (
                    material_title,
                    file_name,
                    file_uploaded
                )
                VALUES (
                    '$topicTitle',
                    '$fileName',
                    '$path'
                )";

        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
        $insert_id = $this->mysqli->insert_id;

        return $insert_id;
    }
    public function deleteMaterials($id){
        $query = "DELETE FROM ta_materials WHERE id_material = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
        return $dataset;
    }
    public function getMaterial($id){
        $query = "SELECT * FROM ta_materials where id_material = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
        return $dataset;
    }
     public function updateMaterial($topicTitle,$fileName,$id){
        $query = "UPDATE ta_materials SET material_title = '{$topicTitle}', file_name = '{$fileName}' where id_material = '{$id}'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
    }
}