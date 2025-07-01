<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class SignUpTest extends TestCase
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
            branch VARCHAR(255) NOT NULL,
            sem VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            UNIQUE KEY sid (sid)
        )";
        mysqli_query($this->conn, $sql);
    }
    
    protected function tearDown(): void
    {
        // Xóa dữ liệu test
        mysqli_query($this->conn, "TRUNCATE TABLE students");
        mysqli_close($this->conn);
    }
    
    public function testSignUpSuccess()
    {
        $name = "Nguyen Van A";
        $email = "nguyenvana@example.com";
        $sem = "1";
        $branch = "Computer Engineering";
        $sid = "CS001";
        $pass = sha1("password123");
        
        // Đăng ký thành công
        $sql = "INSERT INTO students(sid,name,branch,sem,password,email) 
                VALUES('$sid','$name','$branch','$sem','$pass','$email')";
        $result = mysqli_query($this->conn, $sql);
        
        $this->assertTrue($result);
        
        // Kiểm tra user đã được tạo
        $check = mysqli_query($this->conn, "SELECT * FROM students WHERE sid='$sid'");
        $user = mysqli_fetch_array($check);
        
        $this->assertEquals($name, $user['name']);
        $this->assertEquals($email, $user['email']);
        $this->assertEquals($sem, $user['sem']);
        $this->assertEquals($branch, $user['branch']);
    }
    
    public function testSignUpDuplicateID()
    {
        // Thêm user lần đầu
        $name = "Nguyen Van A";
        $email = "nguyenvana@example.com";
        $sem = "1";
        $branch = "Computer Engineering";
        $sid = "CS001";
        $pass = sha1("password123");
        
        mysqli_query($this->conn, "INSERT INTO students(sid,name,branch,sem,password,email) 
                                  VALUES('$sid','$name','$branch','$sem','$pass','$email')");
        
        // Test đăng ký với ID trùng
        $sql = "INSERT INTO students(sid,name,branch,sem,password,email) 
                VALUES('$sid','$name','$branch','$sem','$pass','$email')";
        $result = mysqli_query($this->conn, $sql);
        
        $this->assertFalse($result);
    }
    
    public function testSignUpEmptyFields()
    {
        $name = "";
        $email = "";
        $sem = "";
        $branch = "";
        $sid = "";
        $pass = "";
        
        // Test đăng ký với dữ liệu trống
        $sql = "INSERT INTO students(sid,name,branch,sem,password,email) 
                VALUES('$sid','$name','$branch','$sem','$pass','$email')";
        $result = mysqli_query($this->conn, $sql);
        
        $this->assertFalse($result);
    }
    
    public function testSignUpInvalidEmail()
    {
        $name = "Nguyen Van A";
        $email = "invalid-email";
        $sem = "1";
        $branch = "Computer Engineering";
        $sid = "CS001";
        $pass = sha1("password123");
        
        // Test đăng ký với email không hợp lệ
        $sql = "INSERT INTO students(sid,name,branch,sem,password,email) 
                VALUES('$sid','$name','$branch','$sem','$pass','$email')";
        $result = mysqli_query($this->conn, $sql);
        
        $this->assertFalse($result);
    }
    
    public function testSignUpSQLInjection()
    {
        $name = "'; DROP TABLE students; --";
        $email = "test@example.com";
        $sem = "1";
        $branch = "Computer Engineering";
        $sid = "CS001";
        $pass = sha1("password123");
        
        // Test SQL injection
        $sql = "INSERT INTO students(sid,name,branch,sem,password,email) 
                VALUES('$sid','$name','$branch','$sem','$pass','$email')";
        $result = mysqli_query($this->conn, $sql);
        
        // Kiểm tra bảng students vẫn tồn tại
        $check = mysqli_query($this->conn, "SHOW TABLES LIKE 'students'");
        $this->assertEquals(1, mysqli_num_rows($check));
    }
} 