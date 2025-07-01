<?php
require __DIR__ . '/../vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class AddBooksTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        // Kết nối đến cơ sở dữ liệu test
        $this->conn = mysqli_connect("localhost", "root", "", "library_t");

        // Đặt charset để tránh lỗi collation
        mysqli_set_charset($this->conn, "utf8mb4");

        // Tạo bảng books nếu chưa có, dùng charset phù hợp
        $sql = "CREATE TABLE IF NOT EXISTS books (
            id INT(255) NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            author VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY name (name)
        )";
        mysqli_query($this->conn, $sql);
    }

    protected function tearDown(): void
    {
        // Xóa dữ liệu test sau mỗi test
        mysqli_query($this->conn, "TRUNCATE TABLE books");
        mysqli_close($this->conn);
    }

    private function addBook($name, $author)
    {
        // Nếu trống thì không thêm
        if (empty($name) || empty($author)) {
            return false;
        }

        $stmt = mysqli_prepare($this->conn, "INSERT INTO books(name, author) VALUES (?, ?)");
        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "ss", $name, $author);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }

    public function testAddBookSuccess()
    {
        $name = "Lập trình Python";
        $author = "John Smith";

        $result = $this->addBook($name, $author);
        $this->assertTrue($result);

        $check = mysqli_query($this->conn, "SELECT * FROM books WHERE name='$name'");
        $book = mysqli_fetch_array($check);

        $this->assertEquals($name, $book['name']);
        $this->assertEquals($author, $book['author']);
    }

    public function testAddDuplicateBook()
    {
        $name = "Lập trình Python";
        $author = "John Smith";

        $this->addBook($name, $author);

        $result = $this->addBook($name, $author);
        $this->assertFalse($result);
    }

    public function testAddBookEmptyFields()
    {
        $result = $this->addBook("", "");
        $this->assertFalse($result);
    }

    public function testAddBookSQLInjection()
    {
        $name = "'; DROP TABLE books; --";
        $author = "Hacker";

        $result = $this->addBook($name, $author);
        $this->assertTrue($result);

        // Kiểm tra bảng vẫn còn
        $check = mysqli_query($this->conn, "SHOW TABLES LIKE 'books'");
        $this->assertEquals(1, mysqli_num_rows($check));
    }
}
