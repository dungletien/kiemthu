<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

class IssueBookTest extends TestCase
{
    private $conn;
    
    protected function setUp(): void
    {
        // Kết nối database test
        $this->conn = mysqli_connect("localhost", "root", "", "library_test");
        
        // Tạo bảng books test
        $sql = "CREATE TABLE IF NOT EXISTS books (
            id INT(255) NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            author VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        )";
        mysqli_query($this->conn, $sql);
        
        // Tạo bảng issue test
        $sql = "CREATE TABLE IF NOT EXISTS issue (
            id INT(255) NOT NULL AUTO_INCREMENT,
            sid VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            author VARCHAR(255) NOT NULL,
            date VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        )";
        mysqli_query($this->conn, $sql);
        
        // Thêm sách test
        $name = "Lập trình Python";
        $author = "John Smith";
        mysqli_query($this->conn, "INSERT INTO books(name,author) VALUES('$name','$author')");
    }
    
    protected function tearDown(): void
    {
        // Xóa dữ liệu test
        mysqli_query($this->conn, "TRUNCATE TABLE books");
        mysqli_query($this->conn, "TRUNCATE TABLE issue");
        mysqli_close($this->conn);
    }
    
    public function testIssueBookSuccess()
    {
        $sid = "CS001";
        $bookId = "1";
        $date = date('d/m/Y');
        
        // Lấy thông tin sách
        $bookQuery = mysqli_query($this->conn, "SELECT * FROM books WHERE id='$bookId'");
        $book = mysqli_fetch_array($bookQuery);
        
        // Mượn sách
        $sql = "INSERT INTO issue(sid,name,author,date) 
                VALUES('$sid','{$book['name']}','{$book['author']}','$date')";
        $result = mysqli_query($this->conn, $sql);
        
        $this->assertTrue($result);
        
        // Kiểm tra sách đã được mượn
        $check = mysqli_query($this->conn, "SELECT * FROM issue WHERE sid='$sid'");
        $issue = mysqli_fetch_array($check);
        
        $this->assertEquals($book['name'], $issue['name']);
        $this->assertEquals($book['author'], $issue['author']);
        $this->assertEquals($date, $issue['date']);
    }
    
    public function testIssueNonExistentBook()
    {
        $sid = "CS001";
        $bookId = "999"; // ID không tồn tại
        $date = date('d/m/Y');
        
        // Lấy thông tin sách
        $bookQuery = mysqli_query($this->conn, "SELECT * FROM books WHERE id='$bookId'");
        $book = mysqli_fetch_array($bookQuery);
        
        // Test mượn sách không tồn tại
        $sql = "INSERT INTO issue(sid,name,author,date) 
                VALUES('$sid','{$book['name']}','{$book['author']}','$date')";
        $result = mysqli_query($this->conn, $sql);
        
        $this->assertFalse($result);
    }
    
    public function testIssueBookEmptyFields()
    {
        $sid = "";
        $bookId = "";
        $date = date('d/m/Y');
        
        // Test mượn sách với dữ liệu trống
        $sql = "INSERT INTO issue(sid,name,author,date) VALUES('$sid','','','$date')";
        $result = mysqli_query($this->conn, $sql);
        
        $this->assertFalse($result);
    }
    
    public function testIssueBookSQLInjection()
    {
        $sid = "CS001";
        $bookId = "1";
        $date = "'; DROP TABLE issue; --";
        
        // Lấy thông tin sách
        $bookQuery = mysqli_query($this->conn, "SELECT * FROM books WHERE id='$bookId'");
        $book = mysqli_fetch_array($bookQuery);
        
        // Test SQL injection
        $sql = "INSERT INTO issue(sid,name,author,date) 
                VALUES('$sid','{$book['name']}','{$book['author']}','$date')";
        $result = mysqli_query($this->conn, $sql);
        
        // Kiểm tra bảng issue vẫn tồn tại
        $check = mysqli_query($this->conn, "SHOW TABLES LIKE 'issue'");
        $this->assertEquals(1, mysqli_num_rows($check));
    }
} 