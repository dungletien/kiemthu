<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class SignInTest extends TestCase
{
    private $conn;
    
    protected function setUp(): void
    {
        // Kết nối database test
        $this->conn = mysqli_connect("localhost", "root", "", "library_test");
        
        // Tạo bảng students test
        $sql = "CREATE TABLE IF NOT EXISTS students (
            id INT(255) NOT NULL AUTO_INCREMENT,
            sid VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY sid (sid)
        )";
        mysqli_query($this->conn, $sql);
        
        // Thêm user test
        $sid = "CS001";
        $name = "Test User";
        $password = sha1("password123");
        mysqli_query($this->conn, "INSERT INTO students(sid,name,password) VALUES('$sid','$name','$password')");
    }
    
    protected function tearDown(): void
    {
        // Xóa dữ liệu test
        mysqli_query($this->conn, "TRUNCATE TABLE students");
        mysqli_close($this->conn);
    }
    
    public function testSignInSuccess()
    {
        $sid = "CS001";
        $pass = sha1("password123");
        
        // Kiểm tra đăng nhập thành công
        $sql = "SELECT * FROM students WHERE sid='$sid' AND password='$pass'";
        $result = mysqli_query($this->conn, $sql);
        
        $this->assertEquals(1, mysqli_num_rows($result));
    }
    
    public function testSignInWrongPassword()
    {
        $sid = "CS001";
        $wrongPass = sha1("wrongpass");
        
        // Kiểm tra đăng nhập với mật khẩu sai
        $sql = "SELECT * FROM students WHERE sid='$sid' AND password='$wrongPass'";
        $result = mysqli_query($this->conn, $sql);
        
        $this->assertEquals(0, mysqli_num_rows($result));
    }
    
    public function testSignInEmptyFields()
    {
        $sid = "";
        $pass = "";
        
        // Kiểm tra đăng nhập với dữ liệu trống
        $sql = "SELECT * FROM students WHERE sid='$sid' AND password='$pass'";
        $result = mysqli_query($this->conn, $sql);
        
        $this->assertEquals(0, mysqli_num_rows($result));
    }
    
    public function testSignInSQLInjection()
    {
        $sid = "' OR '1'='1";
        $pass = "anything";
        
        // Kiểm tra SQL injection
        $sql = "SELECT * FROM students WHERE sid='$sid' AND password='$pass'";
        $result = mysqli_query($this->conn, $sql);
        
        $this->assertEquals(0, mysqli_num_rows($result));
    }
} 