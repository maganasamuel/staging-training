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

$autoloadPath = realpath(__DIR__ . '/../package/vendor/autoload.php');

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
        $training_topic = [],
        $training_attendee,
        $training_date = '',
        $training_venue = '',
        $attendee_id = '',
        $trainer_signature = '',
        $topic_type = '',
        $topic_level = '',
        $host_name = '',
        $comp_name = ''
    ) {
        $date = date('Y-m-d');

        $query = 'INSERT INTO ta_test (
                id_user_tested,
                id_user_checked,
                id_set,
                venue
            )
            VALUES (
                ?,
                0,
                ?,
                ?
            )';
        $statement = $this->prepare($query);
        $statement->bind_param(
            'iis',
            $idUser,
            $idSet,
            $venue
        );
        $this->execute($statement);

        $query = 'INSERT INTO ta_training (
                    trainer_id,
                    training_attendee,
                    training_date,
                    trainer_signiture,
                    training_venue,
                    attendee_id,
                    training_type,
                    host_name,
                    comp_name
                )
                VALUES (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                )';

        $statement = $this->prepare($query);
        $statement->bind_param(
            'isssssiss',
            $trainer_id,
            $training_attendee,
            $training_date,
            $trainer_signature,
            $training_venue,
            $attendee_id,
            $topic_type,
            $host_name,
            $comp_name
        );

        $dataset = $this->execute($statement);

        $insert_id = $this->mysqli->insert_id;
        $training_id = $insert_id;

        for ($i = 0; $i < count($training_topic); $i++) {
            if (1 == $topic_type) {
                $topicLevel = '';
            } else {
                $topicLevel = $topic_level[$i];
            }
            $query = 'INSERT INTO ta_training_topic (
                        training_id,
                        topic_title,
                        topic_level
                    )
                    VALUES (
                        ?,
                        ?,
                        ?
                    )';
            $statement = $this->prepare($query);
            $statement->bind_param(
                'iss',
                $training_id,
                $training_topic[$i],
                $topicLevel
            );

            $dataset = $this->execute($statement);
        }

        return $insert_id;
    }

    public function getTraining($id, $idUserType)
    {
        //prepare/execute
        $query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

        $query = 'SELECT 
                    ta_training.trainer_id,
                    ta_user.id_user,
                    ta_training.training_id,
                    ta_training.training_topic,
                    ta_training.host_name,
                    ta_training.training_attendee,
                    CONCAT(ta_user.first_name," ",ta_user.last_name) as fullname,
                    ta_training.training_date
                FROM
                    ta_training
                    LEFT JOIN ta_user
                    ON ta_user.id_user = ta_training.trainer_id ';

        if (1 == $idUserType || 3 == $idUserType) {
            //do nothing
        } else {
            $query .= " WHERE ta_training.trainer_id = '$id' OR 
                    ta_training.training_attendee LIKE '%$id%'";
        }

        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function getTrainingTopic($id, $idUserType, $trainingID)
    {
        //prepare/execute
        $query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

        $query = 'SELECT  ta_training.training_id,
                    ta_training.training_topic,
                    ta_training.training_attendee,
                    CONCAT(ta_user.first_name,"     ",ta_user.last_name) as fullname,
                    ta_training.training_date,
                    ta_training_topic.topic_level,
                    ta_training_topic.topic_title
                FROM
                    ta_training
                    LEFT JOIN ta_user
                    ON ta_user.id_user = ta_training.trainer_id
                    LEFT JOIN ta_training_topic 
                    ON ta_training.training_id = ta_training_topic.training_id
                    where ta_training.training_id = "' . $trainingID . '"';

        if (1 == $idUserType || 3 == $idUserType) {
            //do nothing
        } else {
            $query .= " and ta_training.trainer_id = '$id' OR 
                    ta_training.training_attendee LIKE '%$id%' and ta_training.training_id = '$trainingID' ";
        }

        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function getAdviser()
    {
        $query = 'SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user) FROM ta_user WHERE id_user != "1" and email_address like "%eliteinsure.co.nz" GROUP BY email_address) AND a.status = "1"  ORDER BY a.first_name';

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

    public function addUserTraining($email_address, $first_name, $last_name, $password, $user_type, $ssf_number = '0', $adr_id, $sadr_id)
    {
        $query = "SELECT * FROM ta_user where email_address = '$email_address'";
        $statement = $this->prepare($query);
        $chckemail = $this->execute($statement);

        $query = "SELECT * FROM ta_user where ssf_number = '$ssf_number'";
        $statement = $this->prepare($query);
        $chckfsp = $this->execute($statement);

        while ($row = $chckemail->fetch_assoc()) {
            if ($row['email_address'] == $email_address) {
                return 'existed';
            }
        }
        while ($row = $chckfsp->fetch_assoc()) {
            if ($row['ssf_number'] == $ssf_number) {
                return 'fspexisted';
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
        $query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

        $query = 'SELECT 
                    ta_training.trainer_id,
                    ta_user.id_user,
                    ta_training.training_id,
                    ta_training.training_topic,
                    ta_training.training_attendee,
                    ta_training.host_name,
                    CONCAT(ta_user.first_name," ",ta_user.last_name) as fullname,
                    ta_training.training_date,
                    ta_training.training_type
                FROM
                    ta_training
                    LEFT JOIN ta_user
                    ON ta_user.id_user = ta_training.trainer_id WHERE FIND_IN_SET ("' . $id . '",ta_training.training_attendee) and ta_training.training_type = "2"';

        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function cpdTraining($id)
    {
        $query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

        $query = 'SELECT 
                    ta_training.trainer_id,
                    ta_user.id_user,
                    ta_training.training_id,
                    ta_training.training_topic,
                    ta_training.training_attendee,
                    CONCAT(ta_user.first_name," ",ta_user.last_name) as fullname,
                    ta_training.training_date,
                    ta_training.training_type
                FROM
                    ta_training
                    LEFT JOIN ta_user
                    ON ta_user.id_user = ta_training.trainer_id WHERE FIND_IN_SET ("' . $id . '",ta_training.training_attendee) and ta_training.training_type = "1"';

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

        /* $mailDomain = '@eliteinsure.co.nz';

        $query = 'SELECT * FROM ta_user WHERE id_user != 1 AND RIGHT(email_address, ' . strlen($mailDomain) . ') = "' . $mailDomain . '"'; */
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

    public function updateUserTraining($first_name = '', $last_name = '', $email_address = '', $password = '', $ssf_number = 0, $user_type = '', $id_user = '', $adr_id = 0, $sadr_id = 0)
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

    public function addCPD($cpd_name, $cpd_description)
    {
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

    public function getCPD()
    {
        $query = 'SELECT * FROM training_cpd';
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function getSpecificCpd($id)
    {
        $query = "SELECT * FROM training_cpd where id_cpd = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function deleteCPD($id)
    {
        $query = "DELETE FROM training_cpd WHERE id_cpd = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function getModularTraining(
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

    public function activeStatus($id, $status)
    {
        $query = "UPDATE ta_user SET status = '{$status}' where id_user = '{$id}'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
    }

    public function updateCPD($topic, $description, $id)
    {
        $query = "UPDATE training_cpd SET cpd_name = '{$topic}',cpd_description = '{$description}' where id_cpd = '{$id}'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
    }

    public function verfiyEmail($emailAddress)
    {
        $query = "SELECT link_status FROM ta_user WHERE email_address = '{$emailAddress}' ORDER BY date_registered DESC LIMIT 0,1";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
        $dataset = $dataset->fetch_assoc();

        $link_status = $dataset['link_status'];

        if (1 == $link_status) {
            $query = "UPDATE ta_user SET status = '1' , link_status = '0' where email_address = '{$emailAddress}'";
            $statement = $this->prepare($query);
            $dataset = $this->execute($statement);
        }
    }

    public function sendPassword($emailAddress)
    {
        $query = "SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user)
FROM ta_user WHERE email_address = '$emailAddress' GROUP BY email_address) ";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
        $pword = $dataset->fetch_assoc();

        return $pword['password'];
    }

    public function adviserTeam($id)
    {
        $query = "SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user) FROM ta_user WHERE sadr_id = '$id' and id_user_type = '2' GROUP BY email_address)  ";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function adminadrTeam($id)
    {
        $query = "SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user) FROM ta_user WHERE sadr_id = '$id' and id_user_type = '7' GROUP BY email_address)  ";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function adrTeam($id)
    {
        $query = "SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user) FROM ta_user WHERE adr_id = '$id' GROUP BY email_address)  ";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function getAdrMember($id)
    {
        $query = "SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user) FROM ta_user WHERE adr_id = '$id' GROUP BY email_address)  ";

        // $query = 'SELECT * FROM ta_user WHERE adr_id != ' . $id;
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function getSadrMember($id)
    {
        $query = "SELECT * FROM ta_user a WHERE a.id_user IN (SELECT MAX(id_user) FROM ta_user WHERE sadr_id = '$id'  GROUP BY email_address)  ";

        // $query = 'SELECT * FROM ta_user WHERE sadr_id != ' . $id;
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function getTrainingMaterials()
    {
        $query = 'SELECT * FROM ta_materials ORDER BY material_title DESC';
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function getMaterials()
    {
        $query = 'SELECT * FROM ta_materials';
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function addMaterial($topicTitle, $fileName, $path)
    {
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

    public function deleteMaterials($id)
    {
        $query = "DELETE FROM ta_materials WHERE id_material = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function getMaterial($id)
    {
        $query = "SELECT * FROM ta_materials where id_material = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function updateMaterial($topicTitle, $fileName, $id)
    {
        $query = "UPDATE ta_materials SET material_title = '{$topicTitle}', file_name = '{$fileName}' where id_material = '{$id}'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
    }

    public function getTrainingSpecific($id)
    {
        $query = "SELECT * FROM ta_training where training_id = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function updateTraining(
        $trainer_id = '',
        $training_topic = '',
        $training_attendee,
        $training_date = '',
        $training_venue = '',
        $attendee_id = '',
        $topic_type = '',
        $tId = '',
        $topic_id = '',
        $topic_level = '',
        $host_name = '',
        $comp_name = ''
    ) {
        $query = 'UPDATE ta_training SET training_attendee = ? ,
                    training_date = ? , training_venue = ? , host_name = ? , comp_name = ? where training_id = ?';
        $statement = $this->prepare($query);

        $statement->bind_param(
            'sssssi',
            $training_attendee,
            $training_date,
            $training_venue,
            $host_name,
            $comp_name,
            $tId
        );

        $dataset = $this->execute($statement);

        for ($i = 0; $i < count($training_topic); $i++) {
            if ('1' == $topic_type) {
                $query = "UPDATE ta_training_topic SET topic_title = ?,topic_level = '' where id = ?";
                $statement = $this->prepare($query);
                $statement->bind_param(
                    'si',
                    $training_topic[$i],
                    $topic_id[0]
                );
            } else {
                if (0 != $topic_id[$i]) {
                    $query = 'UPDATE ta_training_topic SET topic_title =? , topic_level = ? where id = ?';
                    $statement = $this->prepare($query);
                    $statement->bind_param(
                        'ssi',
                        $training_topic[$i],
                        $topic_level[$i],
                        $topic_id[$i]
                    );
                } else {
                    $query = 'INSERT INTO ta_training_topic (
                        topic_title,
                        topic_level,
                        training_id
                    )
                    VALUES (
                        ?,
                        ?,
                        ?
                    )';
                    $statement = $this->prepare($query);
                    $statement->bind_param(
                        'ssi',
                        $training_topic[$i],
                        $topic_level[$i],
                        $tId
                    );
                }
                $dataset = $this->execute($statement);
            }
        }
    }

    public function getTopic($id)
    {
        $query = "SELECT * FROM ta_training_topic where training_id = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function deletetopic($id)
    {
        $query = "DELETE FROM ta_training_topic where id = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }
    public function getFeedback($id){
        $query = "SELECT * FROM ta_feedback where training_id = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
        return $dataset;   
    }
    public function participants($id){
        $query = "SELECT count(id) as participants FROM ta_feedback where training_id = '$id'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
        
        $totalAttended = $dataset->fetch_assoc();

        return $totalAttended['participants'];  
    }
    public function addFeedback($idTrain,$first_question,$second_question,$third_question,$fourth_question,$fifth_question,$improvement){

        $query = "INSERT INTO ta_feedback (
                    training_id,
                    first_question,
                    second_question,
                    third_question,
                    fourth_question,
                    fifth_question,
                    improvement
                )
                VALUES (
                    '$idTrain',
                    '$first_question',
                    '$second_question',
                    '$third_question',
                    '$fourth_question',
                    '$fifth_question',
                    '$improvement'
                )";

        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
        $insert_id = $this->mysqli->insert_id;
    }
}
