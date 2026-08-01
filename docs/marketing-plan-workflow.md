# Quy Trình Nghiệp Vụ & Phân Quyền Kế Hoạch Marketing & Upload Hình Ảnh

## 1. Giới thiệu
Tài liệu này định nghĩa quy trình nghiệp vụ, quy tắc xử lý dữ liệu và phân quyền đối với tính năng **Kế hoạch Marketing & Truyền thông** (Marketing Plan), đặc biệt là cơ chế tải lên và quản lý hình ảnh (Media Attachments & Inline Rich Text Images).

---

## 2. Vai Trò & Phân Quyền (Roles & Permissions)

| Vai trò (Role) | Mô tả & Trách nhiệm | Quyền hạn (Permissions) |
|---|---|---|
| **Marketing** (`marketing`) | Nhân viên soạn thảo bài viết, lên kế hoạch truyền thông | `marketing-plan.view`, `marketing-plan.create`, `marketing-plan.update`, `marketing-plan.delete` |
| **Ban Quản lý / Giám đốc** (`director`, `super_admin`) | Người xem xét và duyệt bài viết trước khi xuất bản | `marketing-plan.view`, `marketing-plan.approve`, `marketing-plan.update`, `marketing-plan.delete` |

---

## 3. Quy Trình Upload Ảnh & Quản Lý Kế Hoạch

### 3.1 Luồng Tải Ảnh & Lưu Trữ
1. **Hình ảnh đính kèm (Media Attachments Gallery)**:
   - Được chọn từ nút Tải ảnh lên trong form tạo/sửa kế hoạch.
   - Hỗ trợ các định dạng: `JPG`, `JPEG`, `PNG`, `WEBP`, `GIF`.
   - Dung lượng tối đa: **10MB** mỗi ảnh.
   - Tệp được lưu tại đĩa `public`: `storage/app/public/marketing-plans/{marketing_plan_id}/{filename}`.
   - Thông tin tệp ghi vào bảng `marketing_plan_images` (`file_path`, `file_name`, `file_size`, `mime_type`, `sort_order`).

2. **Hình ảnh nội dung bài viết (Quill Rich Text Inline Image)**:
   - Được chèn trực tiếp thông qua nút `Image` trên thanh công cụ Quill Editor.
   - Hỗ trợ xem trực tiếp ảnh trong thân bài viết `content`.

### 3.2 Luồng Trạng Thái (Status Workflow)
- **Draft (Bản nháp)**: Bài viết do Marketing khởi tạo, có thể chỉnh sửa/xóa tệp ảnh tự do.
- **Pending Approval (Chờ duyệt)**: Marketing gửi bài lên Ban Quản lý. Bài viết chờ người có quyền `marketing-plan.approve` duyệt.
- **Approved (Đã duyệt)**: Đã được thông qua, hiển thị trên Lịch truyền thông xuất bản.
- **Rejected (Từ chối)**: Bài viết bị trả về kèm lý do từ chối. Nhân viên Marketing xem lý do, cập nhật lại nội dung/hình ảnh và gửi duyệt lại.

---

## 4. Xóa Dữ Liệu & Đảm Bảo Bộ Nhớ (Storage Safety)
- Khi nhân viên gỡ ảnh đính kèm khỏi form bài viết, tệp tương ứng trong `Storage::disk('public')` sẽ tự động bị xóa cùng với bản ghi trong DB.
- Khi một kế hoạch bị xóa (`delete`), tất cả tệp ảnh liên quan đến kế hoạch đó trong thư mục `storage/app/public/marketing-plans/{id}` sẽ bị xóa sạch khỏi đĩa cứng.
