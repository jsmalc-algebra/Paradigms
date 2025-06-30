<?php /** @noinspection PhpComposerExtensionStubsInspection */

use PHPUnit\Framework\TestCase;
class IntegrationTesting extends TestCase
{
    private $host;
    private $username;
    private $password;
    private $database;
    private $connection;

    protected function setUp(): void{
        if (!file_exists(__DIR__."/../database.ini")) {
            throw new Exception('Database configuration file not found');
        }

        $config = parse_ini_file(__DIR__."/../database.ini", true);

        if (!isset($config['database'])) {
            throw new Exception('Invalid database configuration');
        }

        $this->host = $config['database']['host'] ?? 'localhost';
        $this->username = $config['database']['username'] ?? '';
        $this->password = $config['database']['password'] ?? '';
        $this->database = $config['database']['test_database'] ?? '';
        $this->connection = mysqli_connect($this->host, $this->username, $this->password, $this->database);
    }

    protected function tearDown(): void
    {
        mysqli_close($this->connection);
    }

    public function testInsertAndSelect() : void {
        $username = "test_user";
        $password = "test_pass";
        $email = "test_email";
        $pfp = 0;
        $weekly = 20;
        $plan_id = 2;
        $user_login_type = 1;

        $Q="INSERT INTO users (username, password, email,profile_picture,plan_id,user_login_type)
        VALUES('$username','$password','$email',$pfp,$plan_id,$user_login_type)";

        $result = mysqli_query($this->connection, $Q);
        $this->assertTrue($result);

        $Q="SELECT * FROM users WHERE email = 'test_email'";
        $result = mysqli_query($this->connection, $Q);
        $user = $result->fetch_assoc();

        $this->assertNotNull($user);
        $this->assertEquals($email, $user["email"]);
        $this->assertEquals($username, $user["username"]);
    }

    public function testUpdate() : void {
        $username = "altered_name";
        $pfp = 1;
        $plan_id = 2;
        $user_id = 50;

        $sql = "UPDATE users SET username = '$username',profile_picture = '$pfp',plan_id = '$plan_id' WHERE id=$user_id;";
        $result = mysqli_query($this->connection, $sql);
        $this->assertTrue($result);

        $Q="SELECT * FROM users WHERE id = '$user_id'";
        $result = mysqli_query($this->connection, $Q);
        $user = $result->fetch_assoc();

        $this->assertNotNull($user);
        $this->assertEquals($username, $user["username"]);
    }




}