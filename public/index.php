<?php
require_once("../classes/user.php");

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

/* Connect to an ODBC database using an alias */
$dsn = 'ijump';
$user = 'root';
$password = '';

try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}


require __DIR__ . '/../vendor/autoload.php';

session_start();

$usermodel = new UserModel();
$sessionModel = new SesssionModel();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);
$app->get('/user', array($usermodel, 'getUser'));
$app->post('/user', array($usermodel, 'createUser'));

$app->get('/session', array($sessionmodel, 'getSession'));
$app->post('/session', array($sessionmodel, 'createSession'));
        
class UserModel
{
    private $dbconn;
            
    public function __construct(PDO $connection)
    {
        $this->dbConn = $connection;
    }
    
    public function getUser()
    {
        
        $statement = $this->dbconn->prepare("SELECT * FROM user 
                                        WHERE id = :user_id");
        
        $statement->execute(array('user_id' => $userId));
        $row = $statement->fetch();
        echo json_encode($row);
    }
    
   public function createUser() {
       $name = $_POST['name'];
       $password = $_POST['password'];
       $phonenumber = $_POST['phonenumber'];
       $weight = $_POST['weight'];
       
       
        $this->dbconn->query("Insert into user (phonenumber, name, password, weight)"
                . "VALUES ('" . $phonenumber . "', '" . $name . "', '" . $password . "', '" . $weight . "')");
    } 
}

class SessionModel 
{
    private $dbconn;

    public function __construct(PDO $connection)
    {
        $this->dbConn = $connection;
    }

   public function getSession()
    {
        
        $statement = $this->dbconn->prepare("SELECT * FROM session 
                                        INNER JOIN user ON session.user_id = user.id AND user.id = :user_id
                                        WHERE session starttime >= :starttime
                                        AND session.endtime <= :endtime");
        
        $statement->execute(array('user_id' => $userId));
        $row = $statement->fetch();
        echo json_encode($row);
    }
    
   public function createSession() {
       $userid = $_POST['userid'];
       $jumpcount = $_POST['jumpcount'];
       $burnedcalories = $_POST['burnedcalories'];
       $starttime = $_POST['starttime'];
       $endtime = $_POST['endtime'];
       
        $this->dbconn->query("Insert into session (userid, jumpcount, burnedcalories, starttime, endtime)"
                . "VALUES ('" . $userid . "', '" . $jumpcount . "', '" . $burnedcalories . "', '" . $starttime . "', '" . $endtime . "')");
    } 
}


 
        
        


