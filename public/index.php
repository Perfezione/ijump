<?php        
class UserModel extends Model
{
    public function getUser(Slim\Http\Request $request)
    {
        $userId = $request->getParam('id');
        $statement = $this->dbconn->prepare("SELECT * FROM user 
                                        WHERE id = :user_id");
        
        $statement->execute(array('user_id' => $userId));
        $row = $statement->fetch();
        
        if($row) {
            echo json_encode($row);
        } else {
            echo "{}";
        }
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

abstract class Model
{
   
    protected $dbconn;

    public function __construct(PDO $connection)
    {
        $this->dbconn = $connection;
    }

}

class SessionModel extends Model
{
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


if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

/* Connect to an ODBC database using an alias */
$dsn = 'mysql:dbname=ijump;host=127.0.0.1';
$user = 'root';
$password = 'root';

try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}


require __DIR__ . '/../vendor/autoload.php';

session_start();

$usermodel = new UserModel($dbh);
$sessionModel = new SessionModel($dbh);

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);
$app->get('/user', array($usermodel, 'getUser'));
$app->post('/user', array($usermodel, 'createUser'));

$app->get('/session', array($sessionModel, 'getSession'));
$app->post('/session', array($sessionModel, 'createSession'));
 
        
$app->run();


