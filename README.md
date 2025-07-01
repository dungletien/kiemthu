
# BÁO CÁO KIỂM THỬ HỆ THỐNG QUẢN LÝ THƯ VIỆN

## 👥 Thành viên nhóm
- Lê Tiến Dũng – 22011169  
- Nguyễn Đức Bảo – 22010697 
- Trần Gia Huy – 21012876  
- Nguyễn Duy Hưng – 22010199

---

## I. GIỚI THIỆU DỰ ÁN

Hệ thống Quản lý Thư viện được phát triển nhằm mục đích hỗ trợ quản lý sách, người dùng, và quá trình mượn – trả sách trong môi trường học đường. Dự án này áp dụng các kỹ thuật kiểm thử phần mềm để đánh giá mức độ ổn định, bảo mật và hiệu năng của hệ thống.

---

## II. MÔI TRƯỜNG VÀ CÔNG CỤ KIỂM THỬ

- Thiết bị: Laptop (Windows 10)
- Trình duyệt: Google Chrome, Microsoft Edge
- Ngôn ngữ sử dụng: PHP, MySQL, HTML/CSS

**Công cụ kiểm thử:**
- Selenium IDE – kiểm thử giao diện người dùng
- Apache JMeter – kiểm thử hiệu năng, tải hệ thống
- PHPUnit – kiểm thử đơn vị cho các hàm PHP

---

## III. PHẠM VI KIỂM THỬ

### 1. Các chức năng đã kiểm thử:
- Đăng ký (Sign Up)
- Đăng nhập (Sign In)
- Đổi mật khẩu (Change Password)
- Thêm sách (Add Book)
- Mượn sách (Issue Book)

### 2. Các chức năng chưa kiểm thử:
- Yêu cầu mượn sách (Book Requests)
- Yêu cầu sách mới (Request New Books)
- Đăng xuất (Logout)

---

## IV. KẾT QUẢ KIỂM THỬ

### 1. Thống kê test case

| Chức năng         | Pass | Fail |
|-------------------|------|------|
| Đăng nhập         | 8    | 0    |
| Đăng ký           | 5    | 4    |
| Đổi mật khẩu      | 6    | 1    |
| Thêm sách         | 3    | 2    |
| Mượn sách         | 2    | 1    |
| **Tổng cộng**     | **24** | **8** |

### 2. Tổng số liệu
- Tổng test case: 32
- Test case Pass: 24
- Test case Fail: 8

---

## V. PHÂN TÍCH LỖI

| Chức năng         | Lỗi mô tả                        | Mức độ     |
|-------------------|----------------------------------|------------|
| Đăng ký           | Xử lý Unicode                    | Thấp       |
|                   | Giới hạn độ dài                  | Thấp       |
|                   | Trùng mã sinh viên               | Trung bình |
|                   | SQL Injection                    | **Nghiêm trọng** |
| Đổi mật khẩu      | Mật khẩu yếu                     | Trung bình |
| Mượn sách         | XSS (Cross-Site Scripting)       | **Nghiêm trọng** |
| Thêm sách         | XSS, trùng lặp dữ liệu           | Nghiêm trọng / Trung bình |

---

## VI. ĐÁNH GIÁ CHUNG

- **Tính năng chính**: Hoạt động ổn định với dữ liệu hợp lệ.
- **Bảo mật**: Phát hiện lỗ hổng XSS và SQL Injection → cần khắc phục ngay.
- **Hiệu năng**: Trang tải nhanh (< 2 giây), kiểm thử tải ổn định bằng JMeter.
- **Tương thích trình duyệt**: Hệ thống hoạt động tốt trên Chrome và Edge.

---

## VII. TÀI LIỆU ĐÍNH KÈM

- Báo cáo kiểm thử trình chiếu: https://docs.google.com/presentation/d/1e8fHLvFxyEPnv6REFbcu4eThKXLHHUwIV3f36xt39eU/edit?usp=sharing
- Test case: https://docs.google.com/spreadsheets/d/19t5WjPvwUHGD1uTDwgDFbFfAkyIWqND7kdv0Q-IzhDk/edit?usp=sharing
- Link github dự án kiểm thử: https://github.com/ashishvegan/Library-Management-System