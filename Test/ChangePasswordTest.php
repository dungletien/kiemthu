<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class ChangePasswordTest extends TestCase
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
        $password = sha1("oldpass123");
        mysqli_query($this->conn, "INSERT INTO students(sid,name,password) VALUES('$sid','$name','$password')");
    }
    
    protected function tearDown(): void
    {
        // Xóa dữ liệu test
        mysqli_query($this->conn, "TRUNCATE TABLE students");
        mysqli_close($this->conn);
    }
    
    public function testChangePasswordSuccess()
    {
        $sid = "CS001";
        $oldPass = sha1("oldpass123");
        $newPass = sha1("newpass123");
        
        // Kiểm tra mật khẩu cũ
        $check = mysqli_query($this->conn, "SELECT * FROM students WHERE sid='$sid' AND password='$oldPass'");
        $this->assertEquals(1, mysqli_num_rows($check));
        
        // Đổi mật khẩu
        $sql = "UPDATE students SET password='$newPass' WHERE sid='$sid'";
        $result = mysqli_query($this->conn, $sql);
        
        $this->assertTrue($result);
        
        // Kiểm tra mật khẩu mới
        $check = mysqli_query($this->conn, "SELECT * FROM students WHERE sid='$sid' AND password='$newPass'");
        $this->assertEquals(1, mysqli_num_rows($check));
    }
    
    public function testChangePasswordWrongOld()
    {
        $sid = "CS001";
        $wrongOldPass = sha1("wrongpass");
        $newPass = sha1("newpass123");
        
        // Kiểm tra mật khẩu cũ sai
        $check = mysqli_query($this->conn, "SELECT * FROM students WHERE sid='$sid' AND password='$wrongOldPass'");
        $this->assertEquals(0, mysqli_num_rows($check));
    }
    
    public function testChangePasswordEmptyFields()
    {
        $sid = "CS001";
        $oldPass = "";
        $newPass = "";
        
        // Kiểm tra mật khẩu trống
        $check = mysqli_query($this->conn, "SELECT * FROM students WHERE sid='$sid' AND password='$oldPass'");
        $this->assertEquals(0, mysqli_num_rows($check));
    }
    
    public function testChangePasswordSQLInjection()
    {
        $sid = "CS001";
        $oldPass = sha1("oldpass123");
        $newPass = "'; DROP TABLE students; --";
        
        // Đổi mật khẩu với SQL injection
        $sql = "UPDATE students SET password='$newPass' WHERE sid='$sid'";
        $result = mysqli_query($this->conn, $sql);
        
        // Kiểm tra bảng students vẫn tồn tại
        $check = mysqli_query($this->conn, "SHOW TABLES LIKE 'students'");
        $this->assertEquals(1, mysqli_num_rows($check));
    }
} 