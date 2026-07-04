# Workflow Báo giá, Hợp đồng và Thanh toán

## 1. Mục tiêu

Tài liệu này định nghĩa luồng nghiệp vụ thống nhất giữa Kinh doanh, bộ phận thực hiện,
Kế toán và Ban giám đốc:

```text
Khách hàng → Báo giá → Theo dõi báo giá → Hợp đồng
            → Thực hiện dịch vụ → Chứng từ → Thanh toán → Hoàn tất
```

Mục tiêu thiết kế:

- Mỗi dữ liệu nghiệp vụ chỉ có một nguồn sự thật.
- Kinh doanh và Kế toán cùng xem một hợp đồng nhưng có quyền thao tác khác nhau.
- Danh mục hữu hạn được quản lý bằng PHP backed enum, không hardcode trong Livewire/Blade.
- Dữ liệu thay đổi theo từng hợp đồng, như số đợt thanh toán, phải được lưu thành bản ghi động.
- Mọi chuyển trạng thái quan trọng đều có người thực hiện, thời gian và lịch sử thay đổi.

## 2. Nguyên tắc enum và dữ liệu động

### Dùng enum

Các giá trị có tập hợp hữu hạn:

- `ContractType`
- `ServiceType`
- `QuotationStatus`
- `ContractStatus`
- `PaymentMethod`
- `PaymentConditionType`
- `PaymentScheduleStatus`
- `PaymentHandoverStatus`
- `DocumentType`
- `DocumentStatus`

Giá trị lưu database là mã tiếng Anh ổn định. Nhãn tiếng Việt, màu sắc và danh sách
lựa chọn được lấy từ enum qua `label()`, `color()` và `options()`.

Database dùng cột `string`, không dùng MySQL `ENUM`, để có thể mở rộng danh mục bằng
code và migration dữ liệu mà không phải thay đổi kiểu cột.

### Không dùng enum

Các thông tin thay đổi theo từng hợp đồng:

- Số lượng đợt thanh toán.
- Tên đợt thanh toán.
- Tỷ lệ và số tiền của từng đợt.
- Ngày đến hạn.
- Điều kiện thanh toán tùy chỉnh.
- Người/bộ phận đang phụ trách.
- Giao dịch tiền thực tế.
- Ghi chú và lịch sử trao đổi.

Những thông tin này được lưu thành các bản ghi trong bảng nghiệp vụ.

## 3. Loại hợp đồng và dịch vụ

### `ContractType`

| Mã | Nhãn |
|---|---|
| `training` | Đào tạo và bồi dưỡng chuyên môn |
| `consulting` | Tư vấn |
| `project` | Dự án |
| `research_technology_transfer` | Nghiên cứu khoa học & chuyển giao công nghệ |
| `scientific_event_communication` | Hội thảo, hội nghị & truyền thông khoa học |

### `ServiceType`

Mỗi `ServiceType` phải trả về đúng một `ContractType` thông qua
`contractType()`. Một báo giá/hợp đồng có thể có nhiều dịch vụ, nhưng các dịch vụ
trong cùng một hợp đồng phải thuộc cùng loại hợp đồng.

#### Đào tạo và bồi dưỡng chuyên môn

- Hệ thống quản lý ISO và cải tiến doanh nghiệp.
- An toàn, sức khỏe và môi trường (HSE).
- Kiểm kê khí nhà kính và quản lý carbon.
- ESG và phát triển bền vững.
- Chuỗi cung ứng bền vững và kinh tế tuần hoàn.
- Trách nhiệm xã hội và đánh giá chuỗi cung ứng.
- Công trình xanh và sản phẩm bền vững.

#### Tư vấn

- Tư vấn ESG và phát triển bền vững.
- Kiểm kê khí nhà kính và kế hoạch giảm phát thải khí nhà kính.
- Tư vấn SBTi và lộ trình Net Zero.
- Kiểm toán năng lượng và giải pháp tiết kiệm năng lượng.
- Tư vấn và lập báo cáo CBAM.
- EPR cho doanh nghiệp.
- Kinh tế xanh và kinh tế tuần hoàn.
- Phòng vệ thương mại và chống phá rừng (EUDR).
- Xây dựng bản đồ tiếng ồn (Noise Map).

#### Dự án

- Hệ thống năng lượng mặt trời.
- Tín chỉ carbon.
- Tín chỉ nhựa.
- Biochar.
- Thu gom, phân loại và tái chế chất thải.
- Giải pháp tiết kiệm năng lượng.
- Tiêu chí cảng xanh.

#### Nghiên cứu khoa học & chuyển giao công nghệ

- Nghiên cứu khoa học và phát triển công nghệ.
- Nghiên cứu biến đổi khí hậu và giải pháp thích ứng.
- Chuyển giao công nghệ và giải pháp xanh.

#### Hội thảo, hội nghị & truyền thông khoa học

Trước khi có danh mục chi tiết hơn, sử dụng một `ServiceType` cùng tên với loại hợp
đồng. Sau này có thể thêm dịch vụ con mà không thay đổi cấu trúc database.

## 4. Vai trò và trách nhiệm

| Vai trò | Trách nhiệm chính |
|---|---|
| Kinh doanh | Khách hàng, báo giá, theo dõi cơ hội, lịch thanh toán dự kiến, nhắc khách hàng |
| Trưởng bộ phận/Giám đốc | Phê duyệt hợp đồng theo chính sách và xem báo cáo tổng hợp |
| Bộ phận thực hiện | Cập nhật mốc công việc, kết quả, hồ sơ bàn giao và nghiệm thu |
| Kế toán | Kiểm tra lịch thanh toán, chứng từ, hóa đơn, ghi nhận tiền về và đối soát |
| Quản trị hệ thống | Phân quyền và xử lý cấu hình kỹ thuật, không thay thế phê duyệt nghiệp vụ |

Kinh doanh không được tự xác nhận tiền đã thu. Kế toán không tự ý thay đổi nội dung
thương mại của hợp đồng đã ký.

## 5. Workflow báo giá

### Trạng thái

```text
Draft
  → Sent
  → FollowingUp
  → Won
  → Lost

Draft/Sent/FollowingUp → Cancelled
Sent/FollowingUp → Expired
```

| Trạng thái | Ý nghĩa | Bộ phận hành động |
|---|---|---|
| `draft` | Đang soạn | Kinh doanh |
| `sent` | Đã gửi khách hàng | Kinh doanh |
| `following_up` | Đang chăm sóc/theo dõi | Kinh doanh |
| `won` | Khách hàng đồng ý, chuẩn bị hợp đồng | Kinh doanh |
| `lost` | Không thành công | Kinh doanh |
| `expired` | Hết hiệu lực | Hệ thống/Kinh doanh |
| `cancelled` | Dừng báo giá | Người có quyền |

### Quy tắc

- Báo giá phải có khách hàng, loại hợp đồng, ít nhất một dịch vụ và người phụ trách.
- Kinh doanh được tự tạo và gửi báo giá, không cần phê duyệt nội bộ.
- Chỉ báo giá đủ dữ liệu bắt buộc mới được chuyển từ `draft` thành `sent`.
- Khi sửa giá trị hoặc điều khoản của báo giá đã gửi, hệ thống phải giữ phiên bản cũ
  và tạo phiên bản mới để có thể đối chiếu với nội dung khách hàng đã nhận.
- Khi chuyển `lost`, bắt buộc nhập lý do.
- Chỉ báo giá `won` mới được chuyển thành hợp đồng.
- Một báo giá mặc định tạo một hợp đồng.
- Việc chuyển đổi chạy trong database transaction và không được tạo trùng hợp đồng.
- Mọi lần liên hệ được lưu trong `quotation_follow_ups`, không ghi đè lịch sử.

## 6. Chuyển báo giá thành hợp đồng

Khi người có quyền chọn **Tạo hợp đồng**:

1. Hệ thống kiểm tra báo giá đang ở trạng thái `won`.
2. Người dùng được hiển thị một form modal để kiểm tra và điều chỉnh thông tin trước khi tạo, bao gồm:
   - Tên hợp đồng (mặc định khởi tạo theo khách hàng).
   - Số hợp đồng.
   - Các thông tin tài chính thương mại có khả năng chỉnh sửa trực tiếp: **Giá trị gốc (VND)**, **Hoa hồng KH (VND)**, **Thuế hoa hồng (VND)**, và **Giá trị hợp đồng (VND)**.
   - Phương thức thanh toán mặc định.
   - Ngày ký, Ngày bắt đầu, Ngày kết thúc.
   - Lịch thanh toán nhiều đợt động (tổng số tiền các đợt phải bằng giá trị hợp đồng).
   - Các file đính kèm hồ sơ chứng từ ban đầu (lưu thành tài liệu dự thảo đi kèm).
3. Hệ thống sao chép khách hàng, dịch vụ, người phụ trách và các giá trị tài chính thương mại đã được tinh chỉnh.
4. Hệ thống tạo `contract` ở trạng thái `draft` cùng các cột tài chính tương ứng (`original_amount`, `customer_commission`, `commission_tax`, `value`).
5. Hệ thống lưu lịch thanh toán, tài liệu và liên kết `contracts.quotation_id`.
6. Sự kiện chuyển đổi được ghi vào activity log.

Không sao chép file bằng đường dẫn rời rạc. File cần dùng chung phải được liên kết qua
quan hệ tài liệu hoặc tạo bản sao có nguồn gốc rõ ràng.

## 7. Workflow hợp đồng

```text
Draft
  → InternalReview
  → WaitingCustomerSignature
  → Active
  → Completed
  → Liquidated

Active → Suspended → Active
Draft/InternalReview/WaitingCustomerSignature/Active/Suspended → Cancelled
```

| Trạng thái | Điều kiện chuyển vào |
|---|---|
| `draft` | Hợp đồng vừa tạo |
| `internal_review` | Đủ thông tin bắt buộc và lịch thanh toán hợp lệ |
| `waiting_customer_signature` | Đã duyệt nội bộ |
| `active` | Có ngày ký và hợp đồng đã được xác nhận |
| `suspended` | Có lý do tạm dừng |
| `completed` | Hoàn thành phạm vi công việc |
| `liquidated` | Hoàn tất nghĩa vụ và hồ sơ thanh lý |
| `cancelled` | Có lý do hủy và người có quyền xác nhận |

Hoàn thành công việc không đồng nghĩa đã thu đủ tiền. Hai thông tin phải hiển thị độc
lập: trạng thái thực hiện hợp đồng và trạng thái tài chính.

## 8. Lịch thanh toán nhiều đợt

### Mô hình dữ liệu

`contract_payment_schedules` lưu nghĩa vụ phải thu:

- `contract_id`
- `installment_number`
- `name`
- `percentage`
- `amount`
- `condition_type`
- `custom_condition`
- `expected_trigger_date` (tùy chọn, chỉ phục vụ dự báo)
- `triggered_at` (ngày điều kiện thực sự được đáp ứng)
- `payment_term_days` (số ngày được phép thanh toán sau khi điều kiện xảy ra)
- `payment_term_unit` (`calendar_days` hoặc `business_days`)
- `due_date` (được tính sau khi có `triggered_at`, hoặc nhập trực tiếp nếu có ngày cố định)
- `status`
- `handover_status`
- `responsible_department_id`
- `responsible_user_id`
- `next_action`
- `next_action_due_at`
- `confirmed_at`
- `confirmed_by`
- `notes`

`contract_payments` lưu tiền thực tế:

- `contract_id`
- `paid_at`
- `amount`
- `payment_method`
- `reference_number`
- `proof_file_path`
- `recorded_by`
- `notes`

`contract_payment_allocations` phân bổ tiền thực tế vào các đợt:

- `payment_id`
- `payment_schedule_id`
- `allocated_amount`

Thiết kế phân bổ cho phép:

- Một đợt được khách hàng thanh toán bằng nhiều giao dịch.
- Một giao dịch được phân bổ cho nhiều đợt.
- Khoản ứng trước được ghi nhận trước và để ở trạng thái chưa phân bổ.
- Kế toán phân bổ lại khoản chưa xác định khi có đủ chứng từ.

### Điều kiện thanh toán

`PaymentConditionType`:

- `after_contract_signed`
- `after_milestone_completed`
- `after_acceptance`
- `after_invoice_issued`
- `fixed_date`
- `custom`

Khi chọn `custom`, bắt buộc nhập `custom_condition`.

### Trạng thái tài chính

`PaymentScheduleStatus`:

- `waiting_condition`
- `pending`
- `partially_paid`
- `paid`
- `overdue`
- `cancelled`

Trạng thái được hệ thống tính:

- `waiting_condition`: điều kiện thanh toán chưa xảy ra và chưa có ngày đến hạn.
- `paid`: tổng giao dịch lớn hơn hoặc bằng số phải thu.
- `overdue`: có ngày đến hạn, đã qua hạn và chưa thu đủ; trạng thái này được ưu tiên
  cả khi khách hàng đã thanh toán một phần.
- `partially_paid`: đã thu lớn hơn 0, chưa đủ và chưa quá hạn.
- `pending`: điều kiện đã xảy ra hoặc đã có ngày đến hạn, nhưng chưa quá hạn và chưa thu đủ.

Người dùng không được tùy ý chọn các trạng thái tính toán này.

### Khi chưa biết ngày thanh toán

Ngày dự kiến và ngày đến hạn đều có thể để trống. Hệ thống quản lý theo điều kiện:

- “Thanh toán trong 05 ngày làm việc sau khi ký hợp đồng”: khi hợp đồng được ghi nhận
  đã ký, hệ thống đặt `triggered_at` và tính `due_date` sau 05 ngày làm việc.
- “Thanh toán trong 07 ngày sau nghiệm thu”: khi biên bản nghiệm thu được xác nhận,
  hệ thống tính ngày đến hạn sau 07 ngày lịch.
- “Thanh toán sau khi bàn giao báo cáo”: trước khi bàn giao, đợt ở trạng thái
  `waiting_condition` và hiển thị “Chờ bàn giao báo cáo”.
- Nếu hợp đồng không quy định số ngày thanh toán, `due_date` tiếp tục để trống. Kế
  toán có thể bổ sung ngày dự kiến để theo dõi, nhưng hệ thống không được tự đánh dấu
  quá hạn khi chưa có ngày đến hạn chính thức.

`expected_trigger_date` chỉ dùng để lập dự báo dòng tiền. Thay đổi ngày dự kiến không
làm thay đổi nghĩa vụ pháp lý và không kích hoạt trạng thái quá hạn.

### Quy tắc giá trị

- Tổng tỷ lệ của các đợt phải bằng 100%, ngoại trừ hợp đồng được cấu hình theo số tiền.
- Tổng số tiền các đợt phải bằng giá trị thanh toán của hợp đồng.
- Tiền sử dụng số nguyên theo đơn vị VND, không dùng floating point.
- Chênh lệch làm tròn được cộng vào đợt cuối.
- Không được xóa đợt đã có giao dịch; chỉ được hủy với lý do.
- Sau khi Kế toán xác nhận lịch, thay đổi giá trị phải tạo điều chỉnh và lưu lịch sử.
- Tổng số tiền giao dịch của một đợt không được vượt số phải thu nếu chưa có quyền
  ghi nhận thanh toán vượt/ứng trước.
- Tổng tiền phân bổ không được lớn hơn giá trị của giao dịch.
- Phần tiền chưa phân bổ phải được hiển thị riêng là tiền ứng trước/chờ đối soát,
  không được tự tính là một đợt đã thanh toán.

## 9. Workflow phối hợp Kinh doanh và Kế toán

`PaymentHandoverStatus` phản ánh trách nhiệm vận hành, tách khỏi trạng thái tiền:

```text
BusinessPreparing
  → SubmittedToAccounting
  → AccountingReviewing
  → WaitingForCustomerPayment
  → Completed

AccountingReviewing → ReturnedToBusiness → SubmittedToAccounting
```

### Các bước

1. **Kinh doanh chuẩn bị**
   - Khai báo các đợt theo hợp đồng đã thống nhất.
   - Đính kèm hợp đồng và tài liệu liên quan.
   - Gửi đợt thanh toán sang Kế toán.

2. **Kế toán kiểm tra**
   - Kiểm tra số tiền, điều kiện, thời hạn và hồ sơ.
   - Chấp nhận hoặc trả lại với lý do cụ thể.
   - Sau khi chấp nhận, lịch thanh toán được khóa các trường tài chính chính.

3. **Đủ điều kiện thu**
   - Bộ phận thực hiện hoặc Kinh doanh xác nhận mốc công việc đã hoàn thành.
   - Hệ thống thông báo cho Kế toán chuẩn bị chứng từ/hóa đơn.

4. **Chờ khách hàng thanh toán**
   - Kế toán cập nhật chứng từ đã phát hành.
   - Kinh doanh thấy công việc tiếp theo là nhắc khách hàng.

5. **Ghi nhận tiền về**
   - Chỉ Kế toán hoặc người có quyền được tạo `contract_payments`.
   - Kế toán phân bổ giao dịch vào một hoặc nhiều đợt thanh toán.
   - Nếu chưa xác định được đợt, giao dịch vẫn được lưu dưới dạng chưa phân bổ.
   - Hệ thống tính lại đã thu, còn phải thu và trạng thái đợt.
   - Kinh doanh và người phụ trách nhận thông báo.

6. **Hoàn tất đợt**
   - Khi thu đủ và chứng từ đạt yêu cầu, đợt chuyển `completed`.
   - Hoàn tất đợt cuối không tự động thanh lý hợp đồng; vẫn phải kiểm tra nghĩa vụ hồ sơ.

## 10. Workflow chứng từ

`DocumentStatus`:

```text
Draft → Submitted → UnderReview → Approved → Archived
                         ↓
                  RevisionRequired
                         ↓
                     Submitted

UnderReview → Rejected
```

Mỗi chứng từ lưu:

- Loại chứng từ.
- Hợp đồng và đợt thanh toán liên quan, nếu có.
- File và phiên bản.
- Người gửi, người kiểm tra.
- Trạng thái.
- Phản hồi/yêu cầu bổ sung.
- Thời điểm gửi, duyệt và hết hiệu lực.

Không ghi đè file khi bổ sung. Mỗi lần nộp lại tạo phiên bản mới để giữ lịch sử.

## 11. Giao diện trực quan

### Trang chi tiết hợp đồng

Header hiển thị:

- Giá trị hợp đồng.
- Tổng phải thu.
- Đã thu.
- Còn phải thu.
- Số tiền quá hạn.
- Tiến độ thực hiện.

Các tab:

1. Tổng quan.
2. Dịch vụ.
3. Tiến độ thực hiện.
4. Thanh toán.
5. Chứng từ.
6. Lịch sử hoạt động.

Tab thanh toán hiển thị mỗi đợt theo dạng timeline hoặc bảng:

| Đợt | Điều kiện | Phải thu | Đã thu | Còn lại | Hạn | Chứng từ | Trạng thái | Phụ trách |
|---|---|---:|---:|---:|---|---|---|---|

Màu quy ước:

- Xám: chưa đến hạn.
- Xanh dương: đang chờ.
- Cam: thanh toán một phần/cần bổ sung.
- Đỏ: quá hạn/bị trả lại.
- Xanh lá: hoàn tất.

### Dashboard Kinh doanh

- Báo giá cần theo dõi hôm nay.
- Báo giá sắp hết hiệu lực.
- Hợp đồng chờ khách ký.
- Đợt thanh toán cần bàn giao Kế toán.
- Khách hàng cần nhắc thanh toán.
- Các yêu cầu Kế toán trả lại cần bổ sung.

### Dashboard Kế toán

- Lịch thanh toán chờ kiểm tra.
- Chứng từ/hóa đơn cần xử lý.
- Khoản phải thu hôm nay và trong 7/30 ngày.
- Thanh toán một phần.
- Khoản quá hạn.
- Giao dịch cần đối soát.

### Dashboard Giám đốc

- Giá trị báo giá, tỷ lệ chuyển đổi và giá trị hợp đồng.
- Đã thu, phải thu, quá hạn.
- Dòng tiền dự kiến theo tháng.
- Số hợp đồng theo loại và dịch vụ.
- Hiệu suất theo nhân viên/phòng ban.

## 12. Phân quyền tối thiểu

Các permission nên tách theo hành động:

- `customer.view`, `customer.manage`
- `quotation.view`, `quotation.create`, `quotation.update`
- `quotation.send`, `quotation.convert`
- `contract.view`, `contract.create`, `contract.update`
- `contract.approve`, `contract.activate`, `contract.complete`, `contract.cancel`
- `payment-schedule.view`, `payment-schedule.manage`, `payment-schedule.confirm`
- `payment.record`, `payment.adjust`
- `document.view`, `document.submit`, `document.review`
- `business-dashboard.view`, `accounting-dashboard.view`, `management-dashboard.view`

Policy quyết định quyền trên từng bản ghi. Route middleware chỉ là lớp bảo vệ đầu tiên.

## 13. Thông báo và cảnh báo

Hệ thống phát thông báo khi:

- Báo giá cần theo dõi hoặc sắp hết hiệu lực.
- Hợp đồng được gửi phê duyệt, duyệt hoặc trả lại.
- Hợp đồng chờ ký hoặc sắp hết hạn.
- Đợt thanh toán được bàn giao giữa hai bộ phận.
- Điều kiện thanh toán đã đạt.
- Chứng từ cần bổ sung hoặc đã duyệt.
- Khoản phải thu sắp đến hạn, đến hạn hoặc quá hạn.
- Kế toán ghi nhận thanh toán.

Thông báo phải liên kết trực tiếp đến đúng báo giá, hợp đồng, đợt thanh toán hoặc chứng từ.

## 14. Audit và an toàn dữ liệu

- Dùng database transaction cho chuyển báo giá thành hợp đồng, xác nhận lịch và ghi nhận tiền.
- Lưu activity log cho thay đổi trạng thái, giá trị, ngày hạn và người phụ trách.
- Dùng soft delete cho dữ liệu nghiệp vụ; không cho xóa giao dịch tài chính đã xác nhận.
- File lưu trong private storage và tải qua route có authorization.
- Kiểm tra optimistic locking hoặc `updated_at` khi nhiều bộ phận cùng chỉnh sửa.
- Các job tự động chỉ đánh dấu quá hạn và gửi thông báo; không tự tạo giao dịch tiền.

## 15. Thứ tự triển khai

1. Enum và state-transition rules.
2. Khách hàng, báo giá, dịch vụ và lịch sử theo dõi.
3. Chuyển báo giá thành hợp đồng.
4. Hợp đồng, dịch vụ và tiến độ thực hiện.
5. Lịch thanh toán động và giao dịch thực tế.
6. Workflow bàn giao Kinh doanh ↔ Kế toán.
7. Chứng từ có phiên bản và phản hồi.
8. Dashboard theo vai trò, thông báo và cảnh báo quá hạn.
9. Export, báo cáo quản trị và kiểm thử phân quyền.

Mỗi giai đoạn phải có feature test cho quyền, chuyển trạng thái hợp lệ, chuyển trạng
thái không hợp lệ và các bất biến về số tiền.
