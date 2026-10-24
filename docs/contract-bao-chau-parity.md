# Đồng bộ quản lý hợp đồng theo nội bộ Bảo Châu

Phạm vi: toàn bộ module quản lý hợp đồng. Nguồn đối chiếu: code hiện tại tại
`C:/laragon/www/laravel/app/Livewire/Admin/Contracts`, các concerns dùng chung,
models, routes và views tương ứng. Giữ năm loại hợp đồng/dịch vụ Greeco đang dùng.

## Tiêu chí nghiệm thu

- [x] Danh sách: tìm kiếm, phân loại, lọc ngày ký/ngày trình, nhân viên, phòng ban,
  nhà thầu phụ, tỉnh, trạng thái/tái ký/chứng từ; sắp xếp, phân trang, xuất dữ liệu.
- [x] Tạo trực tiếp, sửa, nhân bản có kiểm tra quyền; tạo từ báo giá tiếp tục hoạt động.
- [x] Biểu mẫu: khách hàng/chọn hoặc nhập mới, nhà thầu phụ, phụ trách, phòng ban,
  loại/dịch vụ, số hợp đồng, ngày ký/thời gian thực hiện/ngày trình, ghi chú,
  nguồn thông tin, tỉnh, nơi nộp, liên kết Drive, số báo cáo, trạng thái chứng từ/tái ký.
- [x] Tài chính: giá trị, hoa hồng, doanh số, tỷ lệ thanh toán, chi nhà thầu phụ;
  phân biệt quyền kinh doanh/kế toán và bảo toàn luồng thanh toán Greeco.
- [x] Giao việc nhiều người/đơn vị ngoài, hạn hoàn thành, thông báo, lịch sử trao đổi.
- [x] Tiến độ: sáu bước tuần tự, quyền theo phân công, lịch sử người/thời gian/ghi chú,
  file theo bước, bổ sung file vào bước cũ không đổi tiến độ.
- [x] Hồ sơ: tải lên/tải xuống/xóa có quyền, giữ tên gốc, cách ly file giữa hợp đồng,
  kiểm tra giới hạn tệp tạm và tệp cuối cùng.
- [x] Thông báo: người giao/nhận, phụ trách và lãnh đạo; kế toán khi `finished`;
  không gửi tiến độ cho IT; không gửi lại khi chỉ bổ sung file.
- [x] Quyền: middleware, action và query; danh sách/chi tiết/xuất/tải file không
  mở rộng phạm vi người dùng; quyền riêng tiếp tục có hiệu lực.
- [x] Kiểm chứng: Feature/Livewire, định dạng, migration, trình duyệt desktop/mobile,
  giữ các chức năng báo giá, chứng từ và dòng tiền đang có.

## Audit ban đầu

- Greeco mới có danh sách và sửa thông tin cơ bản; tạo hợp đồng từ báo giá.
- Nhà thầu phụ và liên kết hợp đồng đã có trong working tree.
- Workflow chưa kiểm tra thứ tự bước tại server, `workflow_status` chưa fillable,
  chưa hiện lại ghi chú đã lưu, chưa có upload bổ sung ở bước hoàn thành.
- Workflow đang gửi IT và chưa gửi kế toán khi hoàn tất.
- Giới hạn upload tạm 20MB trong khi workflow hiển thị 200MB.
- Giao việc chưa có transaction và kiểm tra quyền hợp đồng trong action.
- MySQL local từng từ chối kết nối; cần xác minh lại khi nghiệm thu runtime.

Chỉ đánh dấu đạt khi bằng chứng hiện tại bao phủ cả mục.

## Chỉ đạo trong phiên làm việc

- Người dùng xác nhận phạm vi toàn bộ module hợp đồng.
- Người dùng cho phép tăng giới hạn upload tạm từ 20MB lên 200MB; giữ các giới hạn riêng ở từng component.
- Người dùng chọn "Chưa thêm chức năng xóa": không triển khai xóa một hoặc nhiều hợp đồng.

## Kết quả nghiệm thu local ngày 24/10/2026

### Kiểm thử tự động

- Lần chạy cuối, tiến trình PHP mới sau khi thêm trạng thái trình lãnh đạo ký:
  `ContractUiTest|ContractManagementParityTest|ContractFormParityTest|ContractPaymentWorkflowTest`:
  **28 passed, 138 assertions**. Bao gồm trạng thái lãnh đạo, quyền duyệt riêng,
  sửa nhanh số báo cáo/trạng thái, chống vòng lặp tái ký và phạm vi hợp đồng gốc.
- Các kiểm thử tiến độ, hồ sơ, báo giá chuyển hợp đồng, nhà thầu phụ, quyền riêng,
  vai trò và quyền kinh doanh đã đạt ở lượt hồi quy trước. Lượt 68 test có 67 đạt;
  lỗi còn lại do tiến trình đã nạp enum cũ trong khi source được cập nhật. Test đó
  (`ContractUiTest`) đã chạy lại đạt trong lượt 28 test nêu trên.
- Bộ kiểm thử mới cho form, quản lý và tiến độ trước các bổ sung cuối:
  **21 passed, 175 assertions**; bao phủ năm loại hợp đồng, thứ tự sáu bước,
  bổ sung tệp không đổi lịch sử, thu hồi phân công, giới hạn tệp, quyền tải/xóa
  tệp, ẩn và chống sửa dữ liệu tài chính của tư vấn, nhân bản và bộ lọc/xuất.
- Pint `--test` các PHP thuộc phạm vi: đạt. `git diff --check`: đạt.
- Toàn bộ suite: **182 passed, 3 failed, 999 assertions** tại thời điểm chạy.
  Cả ba lỗi đã được tái hiện trên bản HEAD gốc cách ly, không chứa thay đổi này:
  `BusinessReportTest::test_target_registration_form_prefills_existing_month_target`,
  `DutyScheduleModuleTest::test_user_can_save_and_auto_calculate_late_and_early_minutes`,
  `SampleCourseDataSeederTest::test_sample_data_contains_courses_and_students_with_multiple_enrollments`.
  Không thay đổi các chức năng ngoài phạm vi để xử lý ba lỗi cũ này.

### Trình duyệt với dữ liệu thử cách ly

- Server local cổng 8011 dùng SQLite riêng trong `storage/app`; dữ liệu QA không
  được chèn vào MySQL nghiệp vụ.
- Tạo trực tiếp, sửa đầy đủ, lọc tỉnh, sửa nhanh số báo cáo/trạng thái, trình ký
  lãnh đạo, lưu lại ghi chú sau reload và bổ sung tệp vào bước hoàn tất: đạt.
- Tệp thực 51MB tải qua Livewire và tải xuống qua route có quyền thành công.
  SHA-256 nguồn và bản tải xuống trùng nhau:
  `0B4D7E0C08EFC8C4032DCD0789B7F9A688BAF893FF3A624F7B9616A07430CA5C`.
  Sau bổ sung tệp, tiến độ vẫn 1/6 và ghi chú cũ còn nguyên.
- Kiểm tra màn hình desktop 1440px và mobile 390px; form, workflow, tệp hiển thị
  được, không tràn ngang trang trên mobile. Viewport thử đã được trả về mặc định.
- Ảnh: `storage/app/contract-parity-desktop.png`,
  `storage/app/contract-parity-mobile.png`.
- Giới hạn PHP local `upload_max_filesize` và `post_max_size` là 2G; Livewire
  temporary upload đã tăng lên 204800KB theo quyết định người dùng. Không khẳng
  định đã tải tệp thực 200MB; biên giới hạn 200MB được kiểm tra tự động.

### Database MySQL local

- Đã khởi động engine MySQL Laragon hiện có, bind `127.0.0.1`, và áp dụng đủ
  năm migration `2026_10_24_000001` đến `000005` vào `noibogreeco`.
- Kiểm tra schema, các quyền `handlers.*`, `contract.view-all`, `contract.export`,
  `contract.assign` và truy vấn Eloquent có quan hệ mới: đạt.
- Số bản ghi trước/sau migration giữ nguyên: 3 báo giá, 0 hợp đồng và 0 bản ghi
  trong các bảng con hợp đồng. Không chạy lại PermissionSeeder trên MySQL.
- Route tạo/sửa/chi tiết/tải tệp hợp đồng và hợp đồng theo nhà thầu phụ đã đăng ký.
- Chưa triển khai hoặc kiểm tra production. Bằng chứng trình duyệt dùng dữ liệu
  thử cách ly; bằng chứng MySQL là migration, schema, quyền và truy vấn local.
