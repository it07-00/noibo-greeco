<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

final class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
        [
            'code' => 'KHC-01',
            'name' => 'KIẾN TRÚC VÀ QUẢN TRỊ HỆ THỐNG CHẤT LƯỢNG CHO DOANH NGHIỆP THEO ISO 9001',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => 'Ban lãnh đạo, Ban ISO, QA/QC, HSE, cán bộ kỹ thuật, sản xuất và các cá nhân tham gia xây dựng, vận hành hệ thống ISO.',
            'objectives' => '✔ Hiểu và áp dụng đúng ISO 9001:2015.
✔ Chuẩn hóa quy trình và tài liệu.
✔ Nâng cao năng suất, chất lượng và hiệu quả quản lý.
✔ Sẵn sàng cho đánh giá và chứng nhận ISO.',
            'content_summary' => '– KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
– XÂY DỰNG HỆ THỐNG TÀI LIỆU
– TRIỂN KHAI ÁP DỤNG HỆ THỐNG
– ĐÀO TẠO ĐÁNH GIÁ NỘI BỘ
– ĐÁNH GIÁ NỘI BỘ VÀ HOÀN THIỆN HỆ THỐNG',
            'content_detail' => '– KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
• Thành lập Ban ISO
• Xây dựng kế hoạch triển khai
• Phân công vai trò và trách nhiệm
• Đào tạo nhận thức ISO 9001:2015
• Xây dựng chính sách chất lượng
• Xây dựng mục tiêu chất lượng
– XÂY DỰNG HỆ THỐNG TÀI LIỆU
• Xây dựng quy trình quản lý
• Xây dựng SOP (Standard Operating Procedure – Quy trình thao tác chuẩn)
• Xây dựng hướng dẫn công việc
• Xây dựng biểu mẫu quản lý
• Thiết lập hệ thống kiểm soát tài liệu và hồ sơ
• Thiết lập KPI chất lượng
– TRIỂN KHAI ÁP DỤNG HỆ THỐNG
• Hướng dẫn áp dụng quy trình
• Hướng dẫn sử dụng biểu mẫu
• Coaching áp dụng thực tế tại hiện trường
• Kiểm soát hồ sơ và tài liệu
• Theo dõi việc áp dụng hệ thống
• Hướng dẫn kiểm soát sản phẩm không phù hợp
– ĐÀO TẠO ĐÁNH GIÁ NỘI BỘ
• Đào tạo Internal Auditor ISO 9001:2015
• Kỹ năng đánh giá nội bộ
• Kỹ năng viết báo cáo đánh giá
• Root Cause Analysis
• CAPA Management
– ĐÁNH GIÁ NỘI BỘ VÀ HOÀN THIỆN HỆ THỐNG
• Thực hiện Internal Audit
• Theo dõi hành động khắc phục
• Họp xem xét lãnh đạo
• Hoàn thiện hồ sơ hệ thống
• Chuẩn bị đánh giá chứng nhận ISO 9001:2015',
        ],
        [
            'code' => 'KHC-02',
            'name' => 'QUẢN TRỊ CHIẾN LƯỢC MÔI TRƯỜNG DOANH NGHIỆP THEO ISO 14001:2026',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => 'Ban lãnh đạo, Ban ISO, HSE, QA/QC, Cán bộ môi trường, Kỹ thuật – Sản xuất, Các cá nhân tham gia xây dựng và vận hành Hệ thống Quản lý Môi trường',
            'objectives' => '✅ Hiểu đầy đủ các yêu cầu của ISO 14001:2026.
✅ Xây dựng và triển khai Hệ thống Quản lý Môi trường theo đúng tiêu chuẩn quốc tế.
✅ Nhận diện và kiểm soát các khía cạnh môi trường, rủi ro và cơ hội.
✅ Chuẩn hóa quy trình quản lý, đáp ứng yêu cầu pháp luật và các bên liên quan.
✅ Nâng cao hiệu quả hoạt động môi trường, giảm chi phí và hướng tới phát triển bền vững.
✅ Sẵn sàng cho đánh giá nội bộ và đánh giá chứng nhận ISO 14001:2026.',
            'content_summary' => '– KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
– XÂY DỰNG HỆ THỐNG TÀI LIỆU
– TRIỂN KHAI ÁP DỤNG HỆ THỐNG
– ĐÀO TẠO ĐÁNH GIÁ NỘI BỘ
– ĐÁNH GIÁ NỘI BỘ VÀ HOÀN THIỆN HỆ THỐNG',
            'content_detail' => 'KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
• Thành lập Ban ISO
• Xây dựng kế hoạch triển khai
• Phân công vai trò và trách nhiệm
• Đào tạo nhận thức ISO 14001:2026
• Xây dựng chính sách môi trường
• Xây dựng mục tiêu môi trường
– XÂY DỰNG HỆ THỐNG TÀI LIỆU
• Xây dựng quy trình quản lý môi trường
• Xây dựng SOP (Standard Operating Procedure – Quy trình thao tác chuẩn)
• Xây dựng hướng dẫn công việc
• Xây dựng biểu mẫu quản lý môi trường
• Thiết lập hệ thống kiểm soát tài liệu và hồ sơ
• Thiết lập KPI môi trường
• Xây dựng ma trận khía cạnh và tác động môi trường
• Xây dựng danh mục yêu cầu pháp luật môi trường
– TRIỂN KHAI ÁP DỤNG HỆ THỐNG
• Hướng dẫn áp dụng quy trình môi trường
• Hướng dẫn nhận diện khía cạnh môi trường
• Hướng dẫn kiểm soát chất thải
• Hướng dẫn quản lý hóa chất
• Hướng dẫn kiểm soát sự cố môi trường
• Coaching áp dụng thực tế tại hiện trường
• Kiểm soát hồ sơ và tài liệu
• Theo dõi việc áp dụng hệ thống
– ĐÀO TẠO ĐÁNH GIÁ NỘI BỘ
• Đào tạo Internal Auditor ISO 14001:2026
• Kỹ năng đánh giá nội bộ
• Kỹ năng viết báo cáo đánh giá
• Root Cause Analysis
• CAPA Management
• Đánh giá tuân thủ pháp luật môi trường
– ĐÁNH GIÁ NỘI BỘ VÀ HOÀN THIỆN HỆ THỐNG
• Thực hiện Internal Audit
• Theo dõi hành động khắc phục
• Họp xem xét lãnh đạo
• Hoàn thiện hồ sơ hệ thống
• Chuẩn bị đánh giá chứng nhận ISO 14001:2026',
        ],
        [
            'code' => 'KHC-03',
            'name' => 'QUẢN TRỊ CHIẾN LƯỢC AN TOÀN - SỨC KHỎE NGHỀ NGHIỆP VÀ KHỦNG HOẢNG DOANH NGHIỆP THEO ISO 45001',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => '• Ban lãnh đạo doanh nghiệp.
• Cán bộ HSE/EHS/An toàn lao động.
• Ban ISO và cán bộ quản lý hệ thống.
• Quản đốc, trưởng bộ phận, giám sát sản xuất.
• Doanh nghiệp đang xây dựng hoặc nâng cấp hệ thống ISO 45001',
            'objectives' => '✅ Khởi động dự án và xây dựng chính sách, mục tiêu an toàn.
✅ Thiết lập đầy đủ hệ thống tài liệu, SOP, biểu mẫu và KPI an toàn.
✅ Nhận diện mối nguy, đánh giá rủi ro và kiểm soát các công việc có nguy cơ cao.
✅ Hướng dẫn triển khai thực tế tại hiện trường và kiểm soát việc áp dụng hệ thống.
✅ Đào tạo đánh giá viên nội bộ (Internal Auditor), Root Cause Analysis và CAPA.
✅ Thực hành đánh giá nội bộ, họp xem xét lãnh đạo và chuẩn bị đánh giá chứng nhận ISO 45001:2018.',
            'content_summary' => '– KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
– XÂY DỰNG HỆ THỐNG TÀI LIỆU
– TRIỂN KHAI ÁP DỤNG HỆ THỐNG
– ĐÀO TẠO ĐÁNH GIÁ NỘI BỘ
– ĐÁNH GIÁ NỘI BỘ VÀ HOÀN THIỆN HỆ THỐNG',
            'content_detail' => '– KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
• Thành lập Ban ISO
• Xây dựng kế hoạch triển khai
• Phân công vai trò và trách nhiệm
• Đào tạo nhận thức ISO 45001:2018
• Xây dựng chính sách an toàn và sức khỏe nghề nghiệp
• Xây dựng mục tiêu an toàn
– XÂY DỰNG HỆ THỐNG TÀI LIỆU
• Xây dựng quy trình quản lý an toàn
• Xây dựng SOP (Standard Operating Procedure – Quy trình thao tác chuẩn)
• Xây dựng hướng dẫn công việc an toàn
• Xây dựng biểu mẫu quản lý an toàn
• Thiết lập hệ thống kiểm soát tài liệu và hồ sơ
• Thiết lập KPI an toàn
• Xây dựng ma trận nhận diện mối nguy và đánh giá rủi ro
• Xây dựng danh mục yêu cầu pháp luật an toàn lao động
• Xây dựng quy trình ứng phó tình huống khẩn cấp
– TRIỂN KHAI ÁP DỤNG HỆ THỐNG
• Hướng dẫn áp dụng quy trình an toàn
• Hướng dẫn nhận diện mối nguy và đánh giá rủi ro
• Hướng dẫn kiểm soát nhà thầu
• Hướng dẫn kiểm soát công việc nguy hiểm
• Hướng dẫn điều tra tai nạn và sự cố
• Hướng dẫn kiểm soát hóa chất và thiết bị
• Coaching áp dụng thực tế tại hiện trường
• Kiểm soát hồ sơ và tài liệu
• Theo dõi việc áp dụng hệ thống
– ĐÀO TẠO ĐÁNH GIÁ NỘI BỘ
• Đào tạo Internal Auditor ISO 45001:2018
• Kỹ năng đánh giá nội bộ
• Kỹ năng viết báo cáo đánh giá
• Root Cause Analysis
• CAPA Management
• Đánh giá tuân thủ pháp luật an toàn lao động
– ĐÁNH GIÁ NỘI BỘ VÀ HOÀN THIỆN HỆ THỐNG
• Thực hiện Internal Audit
• Theo dõi hành động khắc phục
• Họp xem xét lãnh đạo
• Hoàn thiện hồ sơ hệ thống
• Chuẩn bị đánh giá chứng nhận ISO 45001:2018',
        ],
        [
            'code' => 'KHC-04',
            'name' => 'QUẢN TRỊ CHIẾN LƯỢC NĂNG LƯỢNG DOANH NGHIỆP: TỐI ƯU CHI PHÍ VÀ CHUYỂN DỊCH NĂNG LƯỢNG XANH THEO ISO 50001',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => '• Ban lãnh đạo doanh nghiệp
• Energy Team
• Cán bộ quản lý năng lượng
• Bộ phận HSE, EHS, Môi trường
• Quản lý sản xuất, bảo trì, kỹ thuật
• Doanh nghiệp có nhu cầu tiết kiệm năng lượng, giảm phát thải và nâng cao hiệu quả vận hành',
            'objectives' => '✔ Tiết kiệm chi phí năng lượng và nâng cao hiệu quả sản xuất
✔ Cải thiện hiệu suất vận hành thiết bị và hệ thống năng lượng
✔ Hỗ trợ kiểm kê khí nhà kính và mục tiêu Net Zero
✔ Đáp ứng yêu cầu của khách hàng, đối tác và các tiêu chuẩn quốc tế
✔ Nâng cao năng lực cạnh tranh và hình ảnh doanh nghiệp phát triển bền vững',
            'content_summary' => '– KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
– ĐÁNH GIÁ NĂNG LƯỢNG VÀ XÁC ĐỊNH SEU
– XÂY DỰNG HỆ THỐNG QUẢN LÝ NĂNG LƯỢNG
– TRIỂN KHAI ÁP DỤNG VÀ ĐÀO TẠO
– ĐÁNH GIÁ NỘI BỘ VÀ HOÀN THIỆN HỆ THỐNG',
            'content_detail' => '– KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
• Thành lập Ban Energy Team
• Xây dựng kế hoạch triển khai
• Phân công vai trò và trách nhiệm
• Đào tạo nhận thức ISO 50001:2018
• Tổng quan về quản lý năng lượng
• Tổng quan về Energy Performance
• Tổng quan về GHG và Carbon Management
• Xây dựng chính sách năng lượng
• Xây dựng mục tiêu năng lượng
– ĐÁNH GIÁ NĂNG LƯỢNG VÀ XÁC ĐỊNH SEU
• Thu thập dữ liệu năng lượng
• Phân tích sử dụng năng lượng
• Xác định Significant Energy Use (SEU)
• Đánh giá hiệu suất thiết bị
• Đánh giá hệ thống điện
• Đánh giá hệ thống khí nén
• Đánh giá hệ thống hơi
• Đánh giá động cơ và thiết bị tiêu thụ năng lượng
• Xây dựng Energy Review
– XÂY DỰNG HỆ THỐNG QUẢN LÝ NĂNG LƯỢNG
• Xây dựng quy trình quản lý năng lượng
• Xây dựng SOP vận hành năng lượng
• Thiết lập Energy Baseline
• Thiết lập Energy Performance Indicator (EnPI)
• Thiết lập hệ thống giám sát năng lượng
• Xây dựng KPI năng lượng
• Xây dựng kế hoạch tiết kiệm năng lượng
• Thiết lập hệ thống quản lý dữ liệu năng lượng
– TRIỂN KHAI ÁP DỤNG VÀ ĐÀO TẠO
• Hướng dẫn áp dụng hệ thống ISO 50001
• Coaching hiện trường
• Đào tạo kiểm soát vận hành năng lượng
• Đào tạo quản lý dữ liệu năng lượng
• Đào tạo Internal Auditor ISO 50001
• Theo dõi hiệu quả áp dụng thực tế
– ĐÁNH GIÁ NỘI BỘ VÀ HOÀN THIỆN HỆ THỐNG
• Thực hiện Internal Audit
• Theo dõi hành động khắc phục
• Họp xem xét lãnh đạo
• Rà soát hệ thống năng lượng
• Hoàn thiện hồ sơ chứng nhận
• Chuẩn bị đánh giá chứng nhận ISO 50001:2018',
        ],
        [
            'code' => 'KHC-05',
            'name' => 'QUẢN TRỊ CHIẾN LƯỢC TÀI NGUYÊN NƯỚC DOANH NGHIỆP: TỐI ƯU CHI PHÍ VÀ KIẾN TRÚC TUẦN HOÀN DÒNG CHẢY THEO ISO 46001',
            'duration' => '2 ngày',
            'fee' => 2660000.0,
            'instructor' => 'HUY THANH',
            'audience' => '• Ban lãnh đạo doanh nghiệp
• Cán bộ HSE, EHS, Môi trường
• Bộ phận quản lý sản xuất, bảo trì, kỹ thuật
• Cán bộ quản lý năng lượng và tài nguyên
• Doanh nghiệp có nhu cầu tối ưu sử dụng nước và phát triển bền vững',
            'objectives' => '✅ Nhận diện và kiểm soát các khía cạnh môi trường, rủi ro và cơ hội.',
            'content_summary' => '– TỔNG QUAN VỀ ISO 46001:2019 VÀ HỆ THỐNG QUẢN LÝ HIỆU QUẢ SỬ DỤNG NƯỚC
– XÂY DỰNG HỆ THỐNG QUẢN LÝ THEO CẤU TRÚC ISO 46001
– HOẠCH ĐỊNH SỬ DỤNG NƯỚC VÀ THIẾT LẬP MỤC TIÊU
– KIỂM SOÁT VẬN HÀNH VÀ QUẢN LÝ HIỆU QUẢ SỬ DỤNG NƯỚC
– THỰC HÀNH TRIỂN KHAI VÀ ĐÁNH GIÁ HỆ THỐNG',
            'content_detail' => '– TỔNG QUAN VỀ ISO 46001:2019 VÀ HỆ THỐNG QUẢN LÝ HIỆU QUẢ SỬ DỤNG NƯỚC
- Tổng quan về tiêu chuẩn ISO 46001:2019.
- Bối cảnh sử dụng tài nguyên nước và xu hướng quản lý nước bền vững.
- Lợi ích của việc xây dựng Hệ thống Quản lý Hiệu quả Sử dụng Nước.
- Phạm vi áp dụng và đối tượng áp dụng của tiêu chuẩn.
- Mối liên hệ giữa ISO 46001 với các hệ thống quản lý khác (ISO 9001, ISO 14001, ISO 50001...).
- Cấu trúc cấp cao (High Level Structure - HLS) của tiêu chuẩn ISO.
– XÂY DỰNG HỆ THỐNG QUẢN LÝ THEO CẤU TRÚC ISO 46001
- Bối cảnh của tổ chức
Xác định bối cảnh bên trong và bên ngoài.
Phân tích các bên quan tâm.
Xác định phạm vi áp dụng hệ thống.
Thiết lập Hệ thống Quản lý Hiệu quả Sử dụng Nước.
- Vai trò lãnh đạo
Cam kết của lãnh đạo.
Chính sách sử dụng nước.
Phân công vai trò, trách nhiệm và quyền hạn.
Truyền thông nội bộ về quản lý nước.
- Hoạch định
Nhận diện rủi ro và cơ hội.
Thiết lập mục tiêu và chương trình quản lý nước.
Hoạch định các hành động nhằm cải thiện hiệu quả sử dụng nước.
- Hỗ trợ
Nguồn lực.
Năng lực nhân sự.
Nhận thức.
Truyền thông.
Kiểm soát thông tin dạng văn bản.
- Vận hành
Hoạch định và kiểm soát vận hành.
Kiểm soát các quá trình ảnh hưởng đến hiệu quả sử dụng nước.
Kiểm soát nhà thầu và nhà cung cấp.
Chuẩn bị và ứng phó tình huống khẩn cấp liên quan đến nguồn nước.
- Đánh giá kết quả hoạt động
Theo dõi và đo lường.
Phân tích dữ liệu sử dụng nước.
Đánh giá nội bộ.
Xem xét của lãnh đạo.
- Cải tiến: Nhận diện điểm không phù hợp, Hành động khắc phục, Cải tiến liên tục Hệ thống Quản lý Hiệu quả Sử dụng Nước.
– HOẠCH ĐỊNH SỬ DỤNG NƯỚC VÀ THIẾT LẬP MỤC TIÊU
- Phương pháp xây dựng kế hoạch sử dụng nước.
- Xác định yêu cầu pháp luật và các yêu cầu khác liên quan đến tài nguyên nước.
- Đánh giá hiện trạng sử dụng nước của doanh nghiệp.
- Xây dựng đường cơ sở sử dụng nước (Water Baseline).
- Nhận diện các hoạt động, thiết bị và khu vực tiêu thụ nước trọng yếu.
- Thiết lập chỉ số hiệu quả sử dụng nước (Water Performance Indicators - WPI).
- Phương pháp xác định cơ hội tiết kiệm nước.
- Thiết lập mục tiêu, chỉ tiêu và chương trình hành động cải thiện hiệu quả sử dụng nước.
- Xây dựng kế hoạch theo dõi và đánh giá kết quả thực hiện.
– KIỂM SOÁT VẬN HÀNH VÀ QUẢN LÝ HIỆU QUẢ SỬ DỤNG NƯỚC
- Thiết kế hệ thống cấp nước theo định hướng sử dụng hiệu quả.
- Quản lý mua sắm dịch vụ liên quan đến nguồn nước.
- Quản lý mua sắm thiết bị và công nghệ tiết kiệm nước.
- Kiểm soát vận hành hệ thống sử dụng nước.
- Quản lý bảo trì thiết bị sử dụng nước.
- Theo dõi, đo lường và kiểm tra hiệu suất sử dụng nước.
- Phân tích nguyên nhân thất thoát và lãng phí nước.
- Xây dựng các biện pháp cải tiến nhằm nâng cao hiệu quả sử dụng nước.
- Thực hành xây dựng quy trình kiểm soát vận hành phù hợp với doanh nghiệp.
– THỰC HÀNH TRIỂN KHAI VÀ ĐÁNH GIÁ HỆ THỐNG
- Hướng dẫn xây dựng hệ thống tài liệu theo ISO 46001.
- Thực hành xây dựng Chính sách và Mục tiêu sử dụng nước.
- Thực hành xây dựng Water Review và Water Baseline.
- Thực hành xây dựng Water Performance Indicators (WPI).
- Hướng dẫn lập kế hoạch đánh giá nội bộ.
- Chuẩn bị hồ sơ phục vụ đánh giá chứng nhận.
- Thảo luận các tình huống thực tế tại doanh nghiệp.
- Giải đáp các khó khăn trong quá trình triển khai.',
        ],
        [
            'code' => 'KHC-06',
            'name' => 'QUẢN TRỊ CHIẾN LƯỢC RỦI RO HSE THEO ISO 45001:2018 TÍCH HỢP CHUẨN MỰC ISO 14001:2026 VÀ TỐI ƯU VĂN HÓA AN TOÀN',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => '• Cán bộ HSE, EHS, SHE
• Quản lý sản xuất, quản đốc, tổ trưởng
• Ban ISO, cán bộ môi trường
• Doanh nghiệp đang triển khai hoặc duy trì ISO 45001, ISO 14001',
            'objectives' => '✅ Xây dựng hệ thống đánh giá rủi ro, thiết lập biện pháp kiểm soát phù hợp và nâng cao hiệu quả quản lý an toàn tại doanh nghiệp.',
            'content_summary' => '– NHẬN DIỆN MỐI NGUY VÀ ĐÁNH GIÁ RỦI RO
+ TỔNG QUAN VỀ ĐÁNH GIÁ RỦI RO
+ NHẬN DIỆN MỐI NGUY
+ PHƯƠNG PHÁP ĐÁNH GIÁ RỦI RO
+ THỰC HÀNH NHẬN DIỆN MỐI NGUY
– KIỂM SOÁT RỦI RO VÀ TRIỂN KHAI THỰC TẾ
+ BIỆN PHÁP KIỂM SOÁT RỦI RO
+ ĐÁNH GIÁ RỦI RO TRONG CÁC CÔNG VIỆC NGUY HIỂM
+ ĐIỀU TRA TAI NẠN VÀ ROOT CAUSE ANALYSIS
+ TRIỂN KHAI HỆ THỐNG ĐÁNH GIÁ RỦI RO TẠI DOANH NGHIỆP
+ THỰC HÀNH VÀ ĐÁNH GIÁ CUỐI KHÓA',
            'content_detail' => 'NHẬN DIỆN MỐI NGUY VÀ ĐÁNH GIÁ RỦI RO
– TỔNG QUAN VỀ ĐÁNH GIÁ RỦI RO
• Khái niệm Hazard và Risk
• Tư duy phòng ngừa trong công tác an toàn
• Risk-Based Thinking
• Tai nạn và nguyên nhân tai nạn
• Yêu cầu pháp luật liên quan
• Yêu cầu ISO 45001 và ISO 14001
• Vai trò của đánh giá rủi ro trong doanh nghiệp
– NHẬN DIỆN MỐI NGUY
• Phương pháp nhận diện mối nguy
• Nhận diện mối nguy theo công việc
• Nhận diện mối nguy theo thiết bị
• Nhận diện mối nguy theo khu vực
• Nhận diện hành vi không an toàn
• Nhận diện điều kiện không an toàn
• Nhận diện tác động môi trường
Các nhóm mối nguy chính
• Cơ học
• Điện
• Hóa chất
• Cháy nổ
• Tiếng ồn
• Nhiệt
• Ergonomic
• Làm việc trên cao
• Không gian hạn chế
• Thiết bị nâng
• Xe nâng và giao thông nội bộ
– PHƯƠNG PHÁP ĐÁNH GIÁ RỦI RO
• Xác định khả năng xảy ra
• Xác định mức độ hậu quả
• Xác định mức độ rủi ro
• Ma trận đánh giá rủi ro
• Phân loại mức độ rủi ro
• Xác định mức độ chấp nhận rủi ro
Các phương pháp áp dụng
• HIRA (Hazard Identification and Risk Assessment)
• JSA (Job Safety Analysis)
• What-if Analysis
• Checklist Method
• Bowtie Concept
– THỰC HÀNH NHẬN DIỆN MỐI NGUY
• Thực hành nhận diện mối nguy tại hiện trường
• Thực hành đánh giá rủi ro theo công việc
• Phân tích tình huống tai nạn thực tế
• Thảo luận nhóm và trình bày kết quả
KIỂM SOÁT RỦI RO VÀ TRIỂN KHAI THỰC TẾ
– BIỆN PHÁP KIỂM SOÁT RỦI RO
• Hierarchy of Controls
• Elimination
• Substitution
• Engineering Control
• Administrative Control
• PPE (Personal Protective Equipment – Phương tiện bảo vệ cá nhân)
Thiết lập biện pháp kiểm soát
• Kiểm soát kỹ thuật
• Kiểm soát hành chính
• SOP an toàn
• Permit To Work
• Lockout Tagout
• Biển báo và cảnh báo
• Kiểm tra và giám sát
– ĐÁNH GIÁ RỦI RO TRONG CÁC CÔNG VIỆC NGUY HIỂM
• Làm việc trên cao
• Hot Work
• Không gian hạn chế
• Điện
• Thiết bị nâng
• Hóa chất nguy hiểm
• Xe nâng
• Bảo trì máy móc
• Nhà thầu thi công
– ĐIỀU TRA TAI NẠN VÀ ROOT CAUSE ANALYSIS
• Quy trình điều tra tai nạn
• Thu thập bằng chứng
• Phân tích nguyên nhân trực tiếp
• Phân tích nguyên nhân gốc rễ
• 5 Why
• Fishbone Diagram
• Thiết lập CAPA
– TRIỂN KHAI HỆ THỐNG ĐÁNH GIÁ RỦI RO TẠI DOANH NGHIỆP
• Thiết lập Risk Assessment Procedure
• Thiết lập Risk Register
• Cập nhật đánh giá rủi ro
• Đánh giá rủi ro theo thay đổi
• Theo dõi hành động kiểm soát
• Tích hợp với ISO 45001 và ISO 14001
– THỰC HÀNH VÀ ĐÁNH GIÁ CUỐI KHÓA
• Thực hành đánh giá rủi ro theo nhóm
• Trình bày kết quả
• Coaching và góp ý
• Kiểm tra cuối khóa
• Tổng kết đào tạo',
        ],
        [
            'code' => 'KHC-07',
            'name' => 'HỆ THỐNG QUẢN LÝ HÓA CHẤT: THIẾT KẾ PHÒNG VỆ CHỦ ĐỘNG VÀ SỐ HÓA DỮ LIỆU SDS',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => '• Cán bộ HSE, EHS, SHE
• Cán bộ môi trường, an toàn lao động
• Quản lý sản xuất, quản đốc, tổ trưởng
• Nhân sự trực tiếp sử dụng hoặc quản lý hóa chất
• Doanh nghiệp đang triển khai ISO 14001 và ISO 45001',
            'objectives' => '✅ Chuẩn hóa quy trình quản lý, đáp ứng yêu cầu pháp luật và các bên liên quan.',
            'content_summary' => '– TỔNG QUAN VỀ QUẢN LÝ HÓA CHẤT VÀ NHẬN DIỆN RỦI RO
+ TỔNG QUAN VỀ QUẢN LÝ HÓA CHẤT
+ NHẬN DIỆN MỐI NGUY HÓA CHẤT
+ SDS VÀ GHS
+ ĐÁNH GIÁ RỦI RO HÓA CHẤT
– KIỂM SOÁT HÓA CHẤT VÀ ỨNG PHÓ SỰ CỐ
+ QUẢN LÝ LƯU TRỮ VÀ SỬ DỤNG HÓA CHẤT
+ KIỂM SOÁT PHƠI NHIỄM VÀ PPE
+ ỨNG PHÓ SỰ CỐ HÓA CHẤT
+ XÂY DỰNG HỆ THỐNG QUẢN LÝ HÓA CHẤT
+ TỔNG KẾT VÀ ĐÁNH GIÁ CUỐI KHÓA',
            'content_detail' => '– TỔNG QUAN VỀ QUẢN LÝ HÓA CHẤT VÀ NHẬN DIỆN RỦI RO
+ TỔNG QUAN VỀ QUẢN LÝ HÓA CHẤT
• Tổng quan về hóa chất trong doanh nghiệp
• Nguy cơ và tác động của hóa chất
• Các sự cố hóa chất điển hình
• Yêu cầu pháp luật liên quan
• Yêu cầu ISO 14001 và ISO 45001 liên quan đến hóa chất
• Vai trò và trách nhiệm trong quản lý hóa chất
+ NHẬN DIỆN MỐI NGUY HÓA CHẤT
• Phân loại hóa chất nguy hiểm
• Nhận diện mối nguy hóa chất
• Tính chất vật lý và hóa học nguy hiểm
• Hóa chất cháy nổ
• Hóa chất ăn mòn
• Hóa chất độc hại
• Hóa chất phản ứng mạnh
• Tác động sức khỏe nghề nghiệp
• Tác động môi trường
+ SDS VÀ GHS
• Cấu trúc SDS
• Cách đọc SDS
• Nhãn GHS
• Pictogram cảnh báo
• Signal Word
• Hazard Statement
• Precautionary Statement
• Yêu cầu ghi nhãn hóa chất
Nội dung thực hành
• Thực hành đọc SDS
• Nhận diện nhãn GHS
• Phân tích thông tin hóa chất thực tế
+ ĐÁNH GIÁ RỦI RO HÓA CHẤT
• Nhận diện rủi ro hóa chất
• Đánh giá mức độ rủi ro
• Exposure Assessment
• Risk Matrix
• Biện pháp kiểm soát hóa chất
• Hierarchy of Controls
Nội dung thực hành
• Thực hành đánh giá rủi ro hóa chất
• Thảo luận tình huống thực tế
• Coaching hiện trường
– KIỂM SOÁT HÓA CHẤT VÀ ỨNG PHÓ SỰ CỐ
+ QUẢN LÝ LƯU TRỮ VÀ SỬ DỤNG HÓA CHẤT
• Nguyên tắc lưu trữ hóa chất
• Phân khu hóa chất
• Tương thích hóa chất
• Secondary Containment
• Quản lý tồn kho hóa chất
• Kiểm soát cấp phát hóa chất
• Quản lý hóa chất hết hạn
• Kiểm soát nhà thầu hóa chất
+ KIỂM SOÁT PHƠI NHIỄM VÀ PPE
• Đường phơi nhiễm hóa chất
• Kiểm soát phơi nhiễm
• Thông gió và hút cục bộ
• PPE (Personal Protective Equipment – Phương tiện bảo vệ cá nhân)
• Eyewash và Safety Shower
• Monitoring hóa chất
• Kiểm tra sức khỏe nghề nghiệp
+ ỨNG PHÓ SỰ CỐ HÓA CHẤT
• Quy trình ứng phó sự cố hóa chất
• Spill Control
• Sự cố rò rỉ hóa chất
• Cháy nổ hóa chất
• Sơ cứu phơi nhiễm hóa chất
• Emergency Response
• Điều tra sự cố hóa chất
Nội dung thực hành
• Thực hành ứng phó sự cố hóa chất
• Thực hành xử lý spill kit
• Thảo luận tình huống thực tế
+ XÂY DỰNG HỆ THỐNG QUẢN LÝ HÓA CHẤT
• Chemical Management Procedure
• Chemical Register
• Chemical Approval Process
• Risk Assessment
• Inspection Checklist
• Chemical Audit
• Tích hợp với ISO 14001 và ISO 45001
+ TỔNG KẾT VÀ ĐÁNH GIÁ CUỐI KHÓA
• Ôn tập nội dung đào tạo
• Kiểm tra cuối khóa
• Đánh giá năng lực học viên
• Tổng kết chương trình đào tạo',
        ],
        [
            'code' => 'KHC-08',
            'name' => 'ĐIỀU TRA SỰ CỐ VÀ PHÂN TÍCH NGUYÊN NHÂN GỐC RỄ (RCA)',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => 'Ban lãnh đạo doanh nghiệp.
Quản lý nhà máy, quản đốc, trưởng ca sản xuất.
Cán bộ HSE/EHS, An toàn lao động.
Cán bộ ISO, QA/QC, QHSE.
Bộ phận bảo trì, kỹ thuật, vận hành.
Thành viên Ban điều tra sự cố.
Cán bộ quản lý chất lượng, môi trường.
Doanh nghiệp đang áp dụng hoặc chuẩn bị triển khai ISO 45001, ISO 9001, ISO 14001.
Các cá nhân quan tâm đến điều tra sự cố, quản lý rủi ro và cải tiến hệ thống.',
            'objectives' => '✔ Xác định đúng nguyên nhân gốc rễ của sự cố.
✔ Giảm nguy cơ tái diễn tai nạn và rủi ro.
✔ Nâng cao hiệu quả quản lý HSE, chất lượng và môi trường.
✔ Hỗ trợ doanh nghiệp đáp ứng yêu cầu của ISO 45001 và ISO 9001.',
            'content_summary' => '– TỔNG QUAN ĐIỀU TRA SỰ CỐ VÀ PHÂN TÍCH NGUYÊN NHÂN
+ TỔNG QUAN VỀ ĐIỀU TRA SỰ CỐ VÀ RCA
+ QUY TRÌNH ĐIỀU TRA SỰ CỐ
+ CÁC PHƯƠNG PHÁP PHÂN TÍCH NGUYÊN NHÂN GỐC RỄ
+ HUMAN FACTOR VÀ HÀNH VI TRONG SỰ CỐ
– THIẾT LẬP GIẢI PHÁP VÀ TRIỂN KHAI THỰC TẾ
+ THIẾT LẬP HÀNH ĐỘNG KHẮC PHỤC VÀ PHÒNG NGỪA
+ RCA TRONG CÁC HOẠT ĐỘNG DOANH NGHIỆP
+ XÂY DỰNG HỆ THỐNG ĐIỀU TRA SỰ CỐ TẠI DOANH NGHIỆP
+ WORKSHOP THỰC HÀNH RCA
+ TỔNG KẾT VÀ ĐÁNH GIÁ CUỐI KHÓA',
            'content_detail' => '– TỔNG QUAN ĐIỀU TRA SỰ CỐ VÀ PHÂN TÍCH NGUYÊN NHÂN
+ TỔNG QUAN VỀ ĐIỀU TRA SỰ CỐ VÀ RCA
• Khái niệm Incident, Accident và Near Miss
• Tầm quan trọng của điều tra sự cố
• Chi phí ẩn của tai nạn và sự cố
• Nguyên tắc điều tra sự cố
• Khái niệm Root Cause Analysis
• Direct Cause và Root Cause
• Tư duy phòng ngừa và cải tiến liên tục
+ QUY TRÌNH ĐIỀU TRA SỰ CỐ
• Quy trình điều tra sự cố
• Thu thập bằng chứng
• Thu thập thông tin hiện trường
• Phỏng vấn nhân chứng
• Chụp hình và lưu hồ sơ
• Xác định chuỗi sự kiện
• Phân tích điều kiện và hành vi
• Xây dựng Timeline sự cố
+ CÁC PHƯƠNG PHÁP PHÂN TÍCH NGUYÊN NHÂN GỐC RỄ
• 5 Why Analysis
• Fishbone Diagram
• Fault Tree Analysis
• Barrier Analysis
• Cause and Effect Analysis
• Human Factor Analysis
Nội dung thực hành
• Thực hành phân tích tình huống sự cố
• Xây dựng Fishbone Diagram
• Thực hành 5 Why
• Thảo luận nhóm và trình bày kết quả
+ HUMAN FACTOR VÀ HÀNH VI TRONG SỰ CỐ
• Human Error
• Unsafe Behavior
• Unsafe Condition
• Organizational Failure
• Leadership Failure
• Communication Failure
• Work Pressure
• Fatigue
• Safety Culture
– THIẾT LẬP GIẢI PHÁP VÀ TRIỂN KHAI THỰC TẾ
+ THIẾT LẬP HÀNH ĐỘNG KHẮC PHỤC VÀ PHÒNG NGỪA
• CAPA (Corrective and Preventive Action)
• Thiết lập hành động hiệu quả
• Kiểm soát tái diễn
• Theo dõi hiệu quả hành động
• Đánh giá tính bền vững của giải pháp
Nội dung thực hành
• Thiết lập CAPA cho tình huống thực tế
• Đánh giá hiệu lực hành động
• Thảo luận nhóm
+ RCA TRONG CÁC HOẠT ĐỘNG DOANH NGHIỆP
• RCA trong tai nạn lao động
• RCA trong lỗi chất lượng
• RCA trong sự cố môi trường
• RCA trong sự cố thiết bị
• RCA trong Near Miss
• RCA trong hoạt động sản xuất
+ XÂY DỰNG HỆ THỐNG ĐIỀU TRA SỰ CỐ TẠI DOANH NGHIỆP
• Xây dựng Incident Investigation Procedure
• Thiết lập Incident Reporting System
• Thiết lập RCA Team
• Quản lý hồ sơ điều tra
• Theo dõi xu hướng sự cố
• Tích hợp RCA với ISO 45001 và ISO 9001
+ WORKSHOP THỰC HÀNH RCA
• Phân tích Case Study thực tế
• Điều tra tình huống mô phỏng
• Xây dựng Timeline sự cố
• Thực hiện RCA theo nhóm
• Trình bày kết quả và coaching
+ TỔNG KẾT VÀ ĐÁNH GIÁ CUỐI KHÓA
• Ôn tập nội dung đào tạo
• Kiểm tra cuối khóa
• Đánh giá năng lực học viên
• Tổng kết chương trình đào tạo',
        ],
        [
            'code' => 'KHC-09',
            'name' => 'QUẢN TRỊ NĂNG LỰC HSE CHUYÊN SÂU: KIẾN TẠO VĂN HÓA AN TOÀN VÀ GIÁ TRỊ BỀN VỮNG',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => '• Cán bộ HSE, EHS, SHE.
• Quản lý sản xuất, quản đốc, giám sát.
• Thành viên Ban An toàn – Môi trường.
• Doanh nghiệp đang triển khai hoặc duy trì hệ thống HSE theo tiêu chuẩn quốc tế.',
            'objectives' => '✅ Nâng cao hiệu quả hoạt động môi trường, giảm chi phí và hướng tới phát triển bền vững.',
            'content_summary' => '- NỀN TẢNG QUẢN LÝ HSE VÀ ĐÁNH GIÁ RỦI RO
+ Tổng quan về HSE trong doanh nghiệp
+ Nhận diện mối nguy và đánh giá rủi ro
+ Kiểm tra hiện trường và Safety Patrol
- QUẢN LÝ AN TOÀN – SỨC KHỎE NGHỀ NGHIỆP
+ Quản lý máy móc thiết bị
+ Quản lý hóa chất
+ Quản lý sức khỏe nghề nghiệp
+ Điều tra tai nạn lao động
- QUẢN LÝ MÔI TRƯỜNG VÀ KỸ NĂNG HSE OFFICER
+ Quản lý môi trường
+ Ứng phó khẩn cấp
+ Kỹ năng chuyên nghiệp của HSE Officer',
            'content_detail' => '- NỀN TẢNG QUẢN LÝ HSE VÀ ĐÁNH GIÁ RỦI RO
+ Tổng quan về HSE trong doanh nghiệp
• Vai trò của HSE Officer. 
• Trách nhiệm pháp lý của doanh nghiệp. 
• Trách nhiệm của cán bộ HSE. 
• Hệ thống quản lý HSE hiện đại. 
• Tổng quan ISO 14001. 
• Tổng quan ISO 45001. 
• Tổng quan SMETA. 
• Tổng quan BSCI. 
• Các yêu cầu khách hàng thường gặp. 
+ Nhận diện mối nguy và đánh giá rủi ro
• Khái niệm mối nguy. 
• Khái niệm rủi ro. 
• Phân loại mối nguy. 
Bao gồm:
• Mối nguy cơ học. 
• Mối nguy điện. 
• Mối nguy hóa chất. 
• Mối nguy tiếng ồn. 
• Mối nguy bụi. 
• Mối nguy nhiệt. 
• Mối nguy công thái học. 
• Mối nguy giao thông nội bộ. 
Thực hành:
• Xây dựng bảng nhận diện mối nguy. 
• Xây dựng ma trận đánh giá rủi ro. 
• Thực hành đánh giá rủi ro thực tế. 
+ Kiểm tra hiện trường và Safety Patrol
• Phương pháp kiểm tra hiện trường. 
• Quan sát hành vi không an toàn. 
• Điều kiện không an toàn. 
• Kỹ năng giao tiếp với người lao động. 
• Kỹ năng ghi nhận bằng chứng. 
• Kỹ năng lập biên bản. 
Thực hành:
• Safety Walk. 
• Safety Observation. 
• Near Miss Reporting. 
- QUẢN LÝ AN TOÀN – SỨC KHỎE NGHỀ NGHIỆP
+ Quản lý máy móc thiết bị
• Kiểm soát thiết bị có yêu cầu nghiêm ngặt. 
• Kiểm định thiết bị. 
• Bảo trì phòng ngừa. 
• Khóa và gắn thẻ (LOTO). 
• Che chắn máy. 
• An toàn điện. 
+ Quản lý hóa chất
• Hệ thống GHS. 
• Nhãn hóa chất. 
• Phiếu SDS. 
• Đánh giá rủi ro hóa chất. 
• Kho hóa chất. 
• Ứng phó tràn đổ. 
• PPE đối với hóa chất. 
Thực hành:
• Kiểm tra nhãn hóa chất. 
• Đánh giá khu vực lưu trữ. 
+ Quản lý sức khỏe nghề nghiệp
• Bệnh nghề nghiệp. 
• Quan trắc môi trường lao động. 
• Khám sức khỏe định kỳ. 
• Khám bệnh nghề nghiệp. 
• Quản lý lao động nữ. 
• Quản lý lao động chưa thành niên. 
+ Điều tra tai nạn lao động
• Khái niệm tai nạn lao động. 
• Near Miss. 
• Incident. 
• Root Cause Analysis. 
Các công cụ:
• 5 Why. 
• Fishbone Diagram. 
• Corrective Action Plan. 
Thực hành:
• Phân tích tình huống thực tế. 
• Lập báo cáo điều tra. 
- QUẢN LÝ MÔI TRƯỜNG VÀ KỸ NĂNG HSE OFFICER
+ Quản lý môi trường
• Khía cạnh môi trường. 
• Tác động môi trường. 
• Chất thải nguy hại. 
• Chất thải thông thường. 
• Nước thải. 
• Khí thải. 
• Tiếng ồn. 
• Tiết kiệm năng lượng. 
Thực hành:
• Nhận diện khía cạnh môi trường. 
• Đánh giá mức độ tác động. 
+ Ứng phó khẩn cấp
• Cháy nổ. 
• Tràn đổ hóa chất. 
• Tai nạn lao động. 
• Mất điện. 
• Thiên tai. 
Thực hành:
• Xây dựng kịch bản ứng phó. 
• Tổ chức diễn tập. 
+ Kỹ năng chuyên nghiệp của HSE Officer
• Kỹ năng đào tạo nội bộ. 
• Kỹ năng thuyết trình. 
• Kỹ năng viết báo cáo. 
• Kỹ năng lập KPI. 
• Kỹ năng xây dựng chương trình HSE. 
• Kỹ năng làm việc với cơ quan chức năng. 
• Kỹ năng làm việc với khách hàng đánh giá.',
        ],
        [
            'code' => 'KHC-10',
            'name' => 'NÂNG TẦM NĂNG LỰC LÃNH ĐẠO HSE: TỪ GIÁM SÁT KỸ THUẬT ĐẾN ĐỐI TÁC CHIẾN LƯỢC DOANH NGHIỆP',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => '• Ban Tổng Giám đốc, Ban Lãnh đạo doanh nghiệp.
• Giám đốc/Phó Giám đốc nhà máy, Quản đốc phân xưởng.
• Trưởng/Phó phòng HSE, EHS, SHE.
• Quản lý sản xuất, bảo trì, kỹ thuật, vận hành.
• Cán bộ HSE, An toàn lao động và các cấp giám sát.
• Doanh nghiệp có nguy cơ rủi ro cao trong các lĩnh vực sản xuất, dầu khí, hóa chất, năng lượng, xây dựng và công nghiệp.',
            'objectives' => '• Hiểu và áp dụng các nguyên tắc quản trị an toàn theo chuẩn quốc tế.
• Nâng cao năng lực lãnh đạo và xây dựng văn hóa an toàn trong doanh nghiệp.
• Thiết lập hệ thống KPI, kế hoạch hành động 90 ngày và Roadmap cải tiến 12 tháng.
• Góp phần phòng ngừa sự cố nghiêm trọng, giảm thiểu rủi ro và nâng cao hiệu quả hoạt động.',
            'content_summary' => '- PROCESS SAFETY MANAGEMENT (PSM)
LÃNH ĐẠO VÀ QUẢN TRỊ RỦI RO THẢM HỌA
+ Bài học từ các thảm họa công nghiệp trên thế giới
+ Tổng quan Process Safety Management (PSM)
+ Vai trò lãnh đạo trong quản lý rủi ro thảm họa
- XÂY DỰNG VĂN HÓA AN TOÀN
+ Safety Culture – Văn hóa an toàn là gì?
+ Trách nhiệm giải trình về an toàn (Safety Accountability)
- TỪ CHIẾN LƯỢC ĐẾN HÀNH ĐỘNG
+ Các công cụ lãnh đạo an toàn hiện đại
+ Xây dựng chiến lược văn hóa an toàn
+ Cam kết lãnh đạo và kế hoạch hành động',
            'content_detail' => '- PROCESS SAFETY MANAGEMENT (PSM)
LÃNH ĐẠO VÀ QUẢN TRỊ RỦI RO THẢM HỌA
+ Bài học từ các thảm họa công nghiệp trên thế giới
Phân tích các vụ việc điển hình:
• Bhopal – Ấn Độ. 
• Piper Alpha – Vương quốc Anh. 
• Texas City – Hoa Kỳ. 
• Deepwater Horizon. 
• Beirut Explosion. 
• Các sự cố cháy nổ lớn tại Việt Nam. 
Nội dung trọng tâm
• Vì sao doanh nghiệp thất bại? 
• Vai trò của lãnh đạo trong các thảm họa. 
• Các dấu hiệu cảnh báo bị bỏ qua. 
• Chi phí thực sự của một tai nạn nghiêm trọng. 
+ Tổng quan Process Safety Management (PSM)
• Khái niệm PSM. 
• Sự khác biệt giữa: 
An toàn nghề nghiệp An toàn quá trình
Tai nạn cá nhân Sự cố thảm họa
Tác động cục bộ Tác động quy mô lớn
Mất ngày công Mất nhà máy
Chấn thương Tử vong hàng loạt
Các yếu tố cốt lõi của PSM
• Leadership Commitment. 
• Risk Management. 
• Asset Integrity. 
• Management of Change. 
• Emergency Preparedness. 
• Incident Investigation. 
• Process Safety Information. 
+ Vai trò lãnh đạo trong quản lý rủi ro thảm họa
• Risk Governance. 
• Risk Appetite. 
• Risk Ownership. 
• Leadership Visibility. 
• Safety Accountability. 
Workshop
Đánh giá mức độ trưởng thành của hệ thống PSM hiện tại.
- XÂY DỰNG VĂN HÓA AN TOÀN
+ Safety Culture – Văn hóa an toàn là gì?
• Các cấp độ trưởng thành văn hóa an toàn. 
• Mô hình Bradley Curve. 
• Mô hình Hudson Safety Culture. 
• Từ "Compliance" đến "Commitment". 
Đánh giá hiện trạng
Doanh nghiệp đang ở cấp độ nào?
• Reactive. 
• Dependent. 
• Independent. 
• Interdependent. 
+ Hành vi lãnh đạo ảnh hưởng đến văn hóa an toàn
• Những hành vi lãnh đạo tạo nên văn hóa an toàn. 
• Những hành vi lãnh đạo phá hủy văn hóa an toàn. 
• Quyền lực của người quản lý tuyến đầu. 
• Leadership Walkthrough. 
• Safety Gemba Walk. 
Workshop
Tự đánh giá phong cách lãnh đạo an toàn của từng học viên.
+ Trách nhiệm giải trình về an toàn (Safety Accountability)
• Khác biệt giữa Responsibility và Accountability. 
• Trách nhiệm của Tổng Giám đốc. 
• Trách nhiệm của Giám đốc nhà máy. 
• Trách nhiệm của Quản lý cấp trung. 
• Trách nhiệm của Giám sát. 
Xây dựng:
• Safety Accountability Matrix. 
• Safety KPI Cascade. 
- TỪ CHIẾN LƯỢC ĐẾN HÀNH ĐỘNG
+ Các công cụ lãnh đạo an toàn hiện đại
• Near Miss Management. 
• Safety Observation. 
• Life Saving Rules. 
• Safety Walk. 
• Management Safety Tour. 
• Leadership Field Visit. 
• High Risk Activity Review. 
Xây dựng hệ thống KPI chủ động
Bao gồm:
• Near Miss Reporting Rate. 
• Safety Walk Completion. 
• Action Closure Rate. 
• Training Completion Rate. 
• High Risk Audit Score. 
+ Xây dựng chiến lược văn hóa an toàn
• Safety Vision. 
• Safety Mission. 
• Safety Strategic Plan. 
• Communication Campaign. 
• Employee Engagement. 
Workshop
Xây dựng Roadmap Văn hóa An toàn 12 tháng.
+ Cam kết lãnh đạo và kế hoạch hành động
• Các rào cản hiện tại. 
• Các cơ hội cải tiến. 
• Các mục tiêu ưu tiên. 
Kết quả cuối khóa
Mỗi lãnh đạo xây dựng:
• Cam kết an toàn cá nhân. 
• Kế hoạch hành động 90 ngày. 
• Kế hoạch cải tiến 12 tháng. 
• Chương trình hiện diện lãnh đạo tại hiện trường.',
        ],
        [
            'code' => 'KHC-11',
            'name' => 'QUẢN TRỊ CHIẾN LƯỢC RỦI RO VÀ KHỦNG HOẢNG AN TOÀN: PHÒNG VỆ CHỦ ĐỘNG TRONG BỘ CHỈ SỐ ESG',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => 'Ban Lãnh đạo, Ban Giám đốc doanh nghiệp.
Quản lý, cán bộ HSE/EHS/SHE/HSSE.
Quản đốc, Trưởng ca, Giám sát sản xuất và hiện trường.
Cán bộ phụ trách an toàn, đánh giá rủi ro và quản lý HSE.
Doanh nghiệp và cá nhân mong muốn xây dựng văn hóa an toàn, nâng cao hiệu quả quản lý rủi ro.',
            'objectives' => '✅ Sẵn sàng cho đánh giá nội bộ và đánh giá chứng nhận ISO 14001:2026.',
            'content_summary' => '- XÂY DỰNG VĂN HÓA AN TOÀN VÀ HỆ THỐNG NEAR MISS
+ Vai trò lãnh đạo trong quản lý an toàn
+ Quản lý Near Miss
+ Điều tra Near Miss
- BEHAVIOR BASED SAFETY (BBS)
+ Tổng quan BBS
+ Thiết kế chương trình BBS
+ Kỹ năng Safety Coaching
- LIFE SAVING RULES (LSR) VÀ WSFI
+ Life Saving Rules
+ Workplace Safety Field Inspection (WSFI)
- JOB HAZARD ANALYSIS (JHA)
+ Phân tích công việc và nhận diện nguy cơ
+ Thực hành JHA
+ Permit To Work liên kết JHA
- ĐÁNH GIÁ RỦI RO (DGRR) VÀ XÂY DỰNG CHIẾN LƯỢC HSE
+ Đánh giá rủi ro nâng cao
+ Thiết lập KPI an toàn chủ động
+ Xây dựng Roadmap HSE 12 tháng',
            'content_detail' => '- XÂY DỰNG VĂN HÓA AN TOÀN VÀ HỆ THỐNG NEAR MISS
+ Vai trò lãnh đạo trong quản lý an toàn
• Từ quản lý an toàn sang lãnh đạo an toàn. 
• Mô hình Bradley Curve. 
• Safety Culture Maturity. 
• Các cấp độ trưởng thành văn hóa an toàn. 
• Vai trò của lãnh đạo tuyến đầu. 
+ Quản lý Near Miss
• Khái niệm Near Miss. 
• Mối liên hệ giữa Near Miss và tai nạn. 
• Mô hình Heinrich Pyramid. 
• Các loại Near Miss phổ biến. 
• Quy trình báo cáo Near Miss. 
Workshop
Xây dựng:
• Quy trình Near Miss. 
• Phiếu báo cáo Near Miss. 
• KPI Near Miss. 
• Chương trình thưởng khuyến khích báo cáo. 
+ Điều tra Near Miss
Công cụ áp dụng:
• 5 Why. 
• Fishbone Diagram. 
• Root Cause Analysis. 
• Corrective Action Plan. 
Thực hành
Điều tra 05 tình huống Near Miss thực tế.
- BEHAVIOR BASED SAFETY (BBS)
+ Tổng quan BBS
• Hành vi ảnh hưởng đến tai nạn như thế nào. 
• Mô hình ABC. 
• Human Factors. 
• Unsafe Act. 
• Unsafe Condition. 
+ Thiết kế chương trình BBS
• Xác định Critical Behaviors. 
• Xây dựng Checklist BBS. 
• Phân công Observer. 
• Phương pháp thu thập dữ liệu. 
Workshop
Thiết kế:
• BBS Observation Card. 
• BBS KPI. 
• Dashboard BBS. 
+ Kỹ năng Safety Coaching
• Quan sát hành vi. 
• Phản hồi tích cực. 
• Coaching tại hiện trường. 
• Giao tiếp với người lao động. 
Thực hành
Role Play BBS Coaching.
- LIFE SAVING RULES (LSR) VÀ WSFI
+ Life Saving Rules
Nội dung:
• Khái niệm Life Saving Rules. 
• Các hành vi có nguy cơ tử vong. 
• Các nguyên nhân gây tai nạn nghiêm trọng. 
Xây dựng bộ LSR
Bao gồm:
• Khóa và gắn thẻ. 
• Làm việc trên cao. 
• Không gian hạn chế. 
• Điện. 
• Xe nâng. 
• Hóa chất. 
• Thiết bị nâng. 
Workshop
Xây dựng:
• Bộ 10 – 12 Life Saving Rules cho nhà máy. 
+ Workplace Safety Field Inspection (WSFI)
Nội dung:
• Khác biệt giữa Audit và Inspection. 
• Phương pháp WSFI. 
• Kỹ thuật quan sát hiện trường. 
• Kỹ năng phát hiện nguy cơ. 
Workshop
Xây dựng:
• Checklist WSFI. 
• Lịch WSFI. 
• KPI WSFI. 
- JOB HAZARD ANALYSIS (JHA)
+ Phân tích công việc và nhận diện nguy cơ
Nội dung:
• Khái niệm JHA. 
• Khi nào cần JHA. 
• Các bước thực hiện JHA. 
+ Thực hành JHA
Thực hiện JHA cho:
• Xe nâng. 
• Làm việc trên cao. 
• Bảo trì máy. 
• Hàn cắt. 
• Hóa chất. 
• Không gian hạn chế. 
Workshop
Xây dựng:
• Biểu mẫu JHA. 
• Quy trình JHA. 
• Ma trận phê duyệt JHA. 
+ Permit To Work liên kết JHA
Nội dung:
• Giấy phép làm việc. 
• Kiểm soát công việc nguy hiểm. 
• Trách nhiệm các bên. 
- ĐÁNH GIÁ RỦI RO (DGRR) VÀ XÂY DỰNG CHIẾN LƯỢC HSE
+ Đánh giá rủi ro nâng cao
Nội dung:
• ISO 45001 Risk Based Thinking. 
• Ma trận rủi ro. 
• HIRA (Hazard Identification and Risk Assessment). 
• ALARP Principle. 
• Risk Register. 
+ Thiết lập KPI an toàn chủ động
KPI đề xuất:
• Near Miss Rate. 
• BBS Observation. 
• JHA Completion. 
• WSFI Closure Rate. 
• Action Closure Rate. 
• Safety Training Completion. 
+ Xây dựng Roadmap HSE 12 tháng
Xây dựng:
• Kế hoạch Near Miss. 
• Kế hoạch BBS. 
• Kế hoạch JHA. 
• Kế hoạch WSFI. 
• Kế hoạch đào tạo. 
• Kế hoạch cải tiến văn hóa an toàn.',
        ],
        [
            'code' => 'KHC-12',
            'name' => 'TRIỂN KHAI HỆ THỐNG QUẢN LÝ LAO ĐỘNG VÀ TRÁCH NHIỆM XÃ HỘI THEO YÊU CẦU CASCALE',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => 'Ban Lãnh đạo, Ban Giám đốc doanh nghiệp.
Quản lý và cán bộ Nhân sự (HR), HSE, EHS, CSR/ESG, Compliance.
Quản lý sản xuất, Quản đốc, Trưởng ca và Giám sát.
Thành viên Ban ISO, Ban Trách nhiệm xã hội và nhóm triển khai Cascale/Higg FSLM.
Doanh nghiệp sản xuất tham gia chuỗi cung ứng của các thương hiệu quốc tế hoặc có nhu cầu đáp ứng yêu cầu Cascale (Higg FSLM).',
            'objectives' => 'Hiểu và áp dụng các yêu cầu của Cascale (Higg FSLM).
Xây dựng hệ thống quản lý lao động và trách nhiệm xã hội theo thông lệ quốc tế.
Chuẩn hóa chính sách, quy trình và hồ sơ đáp ứng yêu cầu đánh giá.
Nâng cao năng lực tự đánh giá và sẵn sàng cho các cuộc đánh giá của khách hàng.
Tăng cường tuân thủ, giảm rủi ro và nâng cao năng lực cạnh tranh của doanh nghiệp.',
            'content_summary' => '- KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
- ĐÁNH GIÁ THỰC TRẠNG (GAP ASSESSMENT)
- XÂY DỰNG HỆ THỐNG HIGG FSLM
- TRIỂN KHAI VÀ ĐÀO TẠO CHUYÊN SÂU
- TỰ ĐÁNH GIÁ HIGG FSLM
- PRE-ASSESSMENT VÀ CHUẨN BỊ ĐÁNH GIÁ KHÁCH HÀNG',
            'content_detail' => '- KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
Nội dung thực hiện
• Tổng quan Cascale và Higg Index. 
• Tổng quan Higg FSLM. 
• Mục tiêu và lợi ích của Higg FSLM. 
• Cấu trúc đánh giá Higg FSLM. 
• Vai trò của các bộ phận. 
• Thành lập nhóm triển khai. 
• Xây dựng kế hoạch thực hiện. 
Đầu ra
• Kế hoạch triển khai. 
• Hồ sơ đào tạo nhận thức. 
• Ban triển khai Higg FSLM. 
- ĐÁNH GIÁ THỰC TRẠNG (GAP ASSESSMENT)
Nội dung thực hiện
Đánh giá hiện trạng hệ thống quản lý lao động và trách nhiệm xã hội:
Quản trị và hệ thống quản lý
• Chính sách. 
• Mục tiêu. 
• Trách nhiệm. 
• Truyền thông. 
• Đánh giá nội bộ. 
Tuyển dụng và lao động
• Tuyển dụng. 
• Hợp đồng lao động. 
• Hồ sơ lao động. 
• Lao động thời vụ. 
• Lao động chưa thành niên. 
Tiền lương và phúc lợi
• Lương tối thiểu. 
• Làm thêm giờ. 
• Phụ cấp. 
• Phúc lợi. 
• Bảo hiểm. 
Thời giờ làm việc
• Chấm công. 
• Tăng ca. 
• Nghỉ phép. 
• Ngày nghỉ. 
Quan hệ lao động
• Khiếu nại. 
• Đối thoại. 
• Công đoàn. 
• Đại diện người lao động. 
Sức khỏe và an toàn nghề nghiệp
• Đánh giá rủi ro. 
• PPE. 
• Hóa chất. 
• Máy móc thiết bị. 
• PCCC. 
• Ứng phó khẩn cấp. 
Đầu ra
• Báo cáo Gap Assessment. 
• Danh mục điểm chưa phù hợp. 
• Kế hoạch cải tiến. 
- XÂY DỰNG HỆ THỐNG HIGG FSLM
Nội dung thực hiện
Xây dựng và chuẩn hóa:
Chính sách
• Chính sách trách nhiệm xã hội. 
• Chính sách nhân quyền. 
• Chính sách lao động. 
• Chính sách chống phân biệt đối xử. 
• Chính sách chống quấy rối. 
• Chính sách an toàn sức khỏe nghề nghiệp. 
Quy trình
• Quy trình tuyển dụng. 
• Quy trình quản lý hồ sơ nhân sự. 
• Quy trình khiếu nại. 
• Quy trình đối thoại người lao động. 
• Quy trình đánh giá rủi ro. 
• Quy trình quản lý nhà thầu. 
• Quy trình quản lý nhà cung cấp. 
• Quy trình điều tra sự cố. 
• Quy trình hành động khắc phục. 
Đầu ra
• Bộ tài liệu Higg FSLM. 
• Bộ quy trình quản lý lao động. 
• Bộ biểu mẫu quản lý. 
- TRIỂN KHAI VÀ ĐÀO TẠO CHUYÊN SÂU
Nội dung thực hiện
Mô-đun 1
Quản lý lao động
• Tuyển dụng. 
• Hợp đồng lao động. 
• Hồ sơ nhân sự. 
• Lao động chưa thành niên. 
Mô-đun 2
Tiền lương và thời giờ làm việc
• Kiểm soát bảng công. 
• Kiểm soát bảng lương. 
• Quản lý tăng ca. 
• Phúc lợi. 
Mô-đun 3
Sức khỏe và an toàn nghề nghiệp
• Đánh giá rủi ro. 
• PPE. 
• Hóa chất. 
• Tai nạn lao động. 
• PCCC. 
Mô-đun 4
Quan hệ lao động
• Khiếu nại. 
• Đối thoại. 
• Truyền thông nội bộ. 
Đầu ra
• Hồ sơ đào tạo. 
• Hồ sơ triển khai. 
• Hồ sơ vận hành thực tế. 
- TỰ ĐÁNH GIÁ HIGG FSLM
Nội dung thực hiện
• Hướng dẫn thực hiện Self-Assessment. 
• Thu thập bằng chứng. 
• Đánh giá mức độ trưởng thành. 
• Chấm điểm theo từng phần. 
• Phân tích kết quả. 
• Xây dựng kế hoạch cải tiến. 
Đầu ra
• Báo cáo Self-Assessment. 
• Bảng điểm Higg FSLM. 
• Kế hoạch cải tiến. 
- PRE-ASSESSMENT VÀ CHUẨN BỊ ĐÁNH GIÁ KHÁCH HÀNG
Nội dung thực hiện
• Đánh giá thử toàn bộ hệ thống. 
• Phỏng vấn người lao động. 
• Kiểm tra hồ sơ. 
• Kiểm tra hiện trường. 
• Rà soát điểm chưa phù hợp. 
• Hướng dẫn khắc phục. 
• Chuẩn bị hồ sơ khách hàng. 
Đầu ra
• Báo cáo Pre-Assessment. 
• Danh mục hồ sơ sẵn sàng đánh giá. 
• Kế hoạch hành động cuối cùng.',
        ],
        [
            'code' => 'KHC-13',
            'name' => 'QUẢN LÝ HSE CHIẾN LƯỢC: PHÁT TRIỂN NĂNG LỰC LÃNH ĐẠO VÀ KHẢ NĂNG THÍCH ỨNG TỔ CHỨC',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => 'Ban lãnh đạo doanh nghiệp (Hội đồng quản trị, Ban Giám đốc, Tổng Giám đốc).
Giám đốc, Trưởng/Phó phòng HSE, EHS, SHE, QHSE.
Quản lý nhà máy, quản đốc phân xưởng, trưởng các bộ phận sản xuất.
Cán bộ HSE, cán bộ quản lý rủi ro, môi trường và sức khỏe nghề nghiệp.
Thành viên Ban ISO, Ban ESG, Ban Phát triển bền vững.
Doanh nghiệp đang xây dựng hoặc chuyển đổi hệ thống quản trị HSE theo định hướng quốc tế.
Các tổ chức mong muốn xây dựng văn hóa an toàn, nâng cao năng lực lãnh đạo và phát triển hệ thống HSE bền vững.',
            'objectives' => 'Xây dựng tầm nhìn, sứ mệnh, giá trị cốt lõi và chiến lược HSE phù hợp với định hướng phát triển doanh nghiệp.
Thiết lập lộ trình chuyển đổi từ HSE Compliance sang HSE Excellence, gắn với ESG và phát triển bền vững.
Xây dựng KPI, mục tiêu và Roadmap HSE 3 năm nhằm nâng cao hiệu quả quản trị và văn hóa an toàn.
Nâng cao năng lực lãnh đạo HSE, thúc đẩy sự cam kết của lãnh đạo và cải tiến liên tục trong doanh nghiệp.',
            'content_summary' => '- HSE TRONG CHIẾN LƯỢC PHÁT TRIỂN DOANH NGHIỆP
+ Từ HSE Compliance đến HSE Excellence
+ Đánh giá mức độ trưởng thành của hệ thống HSE
- XÂY DỰNG TẦM NHÌN HSE
- XÂY DỰNG SỨ MỆNH HSE
- XÂY DỰNG GIÁ TRỊ CỐT LÕI HSE
- XÂY DỰNG CHIẾN LƯỢC HSE
+ HSE Strategic Planning
+ Xác định các trụ cột chiến lược
- XÂY DỰNG KPI VÀ MỤC TIÊU HSE
- XÂY DỰNG ROADMAP HSE 3 NĂM
- CAM KẾT LÃNH ĐẠO',
            'content_detail' => '- HSE TRONG CHIẾN LƯỢC PHÁT TRIỂN DOANH NGHIỆP
+ Từ HSE Compliance đến HSE Excellence
Nội dung:
• Sự khác biệt giữa doanh nghiệp tuân thủ và doanh nghiệp xuất sắc về HSE. 
• Xu hướng quản trị HSE trên thế giới. 
• Vai trò của HSE trong ESG. 
• Vai trò của HSE trong phát triển bền vững. 
• Vai trò của HSE trong chuỗi cung ứng toàn cầu. 
• Chi phí hữu hình và vô hình của tai nạn lao động. 
Workshop
Đánh giá hiện trạng HSE của doanh nghiệp.
+ Đánh giá mức độ trưởng thành của hệ thống HSE
Mô hình đánh giá:
• Reactive. 
• Dependent. 
• Independent. 
• Interdependent. 
Workshop
Xác định doanh nghiệp đang ở cấp độ nào.
Xác định khoảng cách cần cải thiện.
- XÂY DỰNG TẦM NHÌN HSE
Nội dung:
• Khái niệm tầm nhìn. 
• Vai trò của tầm nhìn. 
• Đặc điểm của một tầm nhìn hiệu quả. 
• Tầm nhìn HSE của các tập đoàn hàng đầu thế giới. 
Ví dụ tham khảo
• Zero Harm. 
• Everyone Goes Home Safe Every Day. 
• Safety is Our Value. 
Workshop
Xây dựng dự thảo tầm nhìn HSE cho doanh nghiệp.
Đầu ra
Bản dự thảo Tầm nhìn HSE.
- XÂY DỰNG SỨ MỆNH HSE
Nội dung:
• Khái niệm sứ mệnh. 
• Vai trò của sứ mệnh. 
• Cách xây dựng sứ mệnh tạo động lực. 
Workshop
Xác định:
• Đối tượng được bảo vệ. 
• Giá trị doanh nghiệp tạo ra. 
• Cam kết đối với người lao động. 
• Cam kết đối với cộng đồng. 
Đầu ra
Bản dự thảo Sứ mệnh HSE.
- XÂY DỰNG GIÁ TRỊ CỐT LÕI HSE
Nội dung:
Các giá trị cốt lõi thường gặp:
• Safety First. 
• Respect for People. 
• Accountability. 
• Integrity. 
• Continuous Improvement. 
• Zero Tolerance for Unsafe Acts. 
Workshop
Xây dựng bộ giá trị cốt lõi HSE riêng của doanh nghiệp.
Đầu ra
Bộ giá trị cốt lõi HSE.
- XÂY DỰNG CHIẾN LƯỢC HSE
+ HSE Strategic Planning
• Khái niệm chiến lược HSE. 
• Chiến lược 3 năm. 
• Chiến lược 5 năm. 
• Mô hình Balanced Scorecard cho HSE. 
+ Xác định các trụ cột chiến lược
Workshop xây dựng:
Trụ cột 1
Lãnh đạo và văn hóa an toàn.
Trụ cột 2
Quản lý rủi ro.
Trụ cột 3
Năng lực và nhận thức.
Trụ cột 4
Hiệu suất môi trường.
Trụ cột 5
Sức khỏe nghề nghiệp.
Trụ cột 6
Số hóa và chuyển đổi HSE.
Đầu ra
Bản đồ chiến lược HSE.
- XÂY DỰNG KPI VÀ MỤC TIÊU HSE
Thiết lập mục tiêu HSE
Nội dung:
• KPI dẫn dắt (Leading Indicators). 
• KPI kết quả (Lagging Indicators). 
Ví dụ KPI
• Near Miss Reporting. 
• BBS Observation. 
• JHA Completion. 
• Safety Walk. 
• WSFI. 
• Closure Rate. 
• LTIFR. 
• TRIR. 
Workshop
Xây dựng KPI cho:
• Ban lãnh đạo. 
• Quản lý. 
• HSE. 
• Sản xuất. 
- XÂY DỰNG ROADMAP HSE 3 NĂM
HSE Transformation Roadmap
Nội dung:
Năm 1
Xây nền tảng.
Năm 2
Chuẩn hóa hệ thống.
Năm 3
Văn hóa an toàn.
Workshop
Xây dựng:
• Mục tiêu năm. 
• Kế hoạch năm. 
• Dự án ưu tiên. 
• Ngân sách HSE. 
Đầu ra
Roadmap HSE 3 năm.
- CAM KẾT LÃNH ĐẠO
Leadership Commitment Workshop
Ban lãnh đạo cùng xây dựng:
• Tuyên bố cam kết HSE. 
• Cam kết trách nhiệm giải trình. 
• Chương trình Leadership Safety Walk. 
• Chương trình Safety Culture Campaign.',
        ],
        [
            'code' => 'KHC-14',
            'name' => 'KIỂM KÊ KHÍ NHÀ KÍNH VÀ XÂY DỰNG CHIẾN LƯỢC GIẢM PHÁT THẢI THEO NGHỊ ĐỊNH 06/2022/NĐ-CP VÀ ISO 14064-1:2018',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => 'Ban lãnh đạo doanh nghiệp và các cấp quản lý.
Cán bộ phụ trách ESG, phát triển bền vững, môi trường, HSE/EHS.
Cán bộ quản lý năng lượng, kỹ thuật, sản xuất, vận hành và quản lý dữ liệu.
Thành viên Ban ISO, Ban kiểm kê khí nhà kính, Ban Net Zero.
Chuyên gia tư vấn, đánh giá, xác minh phát thải khí nhà kính và các tổ chức cung cấp dịch vụ liên quan.
Doanh nghiệp đang xây dựng hệ thống kiểm kê khí nhà kính theo ISO 14064-1:2018 hoặc chuẩn bị xác minh báo cáo phát thải khí nhà kính.',
            'objectives' => 'Hiểu và áp dụng các yêu cầu của ISO 14064-1:2018 trong xây dựng và vận hành hệ thống kiểm kê khí nhà kính cấp tổ chức.
Thiết lập hệ thống kiểm kê khí nhà kính, xác định Scope 1, Scope 2, Scope 3, thu thập dữ liệu và tính toán phát thải theo tiêu chuẩn quốc tế.
Xây dựng hệ thống tài liệu, quy trình quản lý dữ liệu và báo cáo kiểm kê khí nhà kính, đáp ứng yêu cầu xác minh độc lập.
Nâng cao năng lực quản lý phát thải khí nhà kính, tạo nền tảng triển khai ESG, Net Zero, SBTi và các chương trình quản lý carbon của doanh nghiệp.',
            'content_summary' => '– KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
– XÁC ĐỊNH NGUỒN PHÁT THẢI VÀ THU THẬP DỮ LIỆU
– XÂY DỰNG HỆ THỐNG TÍNH TOÁN PHÁT THẢI
– XÂY DỰNG HỆ THỐNG TÀI LIỆU VÀ BÁO CÁO
– ĐÀO TẠO VÀ HOÀN THIỆN HỆ THỐNG',
            'content_detail' => '– KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
• Thành lập Ban GHG
• Xây dựng kế hoạch triển khai
• Phân công vai trò và trách nhiệm
• Đào tạo nhận thức ISO 14064-1:2018
• Tổng quan về biến đổi khí hậu và khí nhà kính
• Tổng quan Scope 1, Scope 2 và Scope 3
• Tổng quan ESG và Carbon Management
– XÁC ĐỊNH NGUỒN PHÁT THẢI VÀ THU THẬP DỮ LIỆU
• Xác định Organizational Boundary
• Xác định Operational Boundary
• Xác định nguồn phát thải Scope 1
• Xác định nguồn phát thải Scope 2
• Xác định nguồn phát thải Scope 3
• Thiết lập danh mục nguồn phát thải
• Thu thập Activity Data
• Xây dựng Data Collection System
• Đánh giá chất lượng dữ liệu
– XÂY DỰNG HỆ THỐNG TÍNH TOÁN PHÁT THẢI
• Xây dựng phương pháp tính toán phát thải
• Áp dụng Emission Factor
• Thiết lập bảng tính GHG
• Tính toán Scope 1
• Tính toán Scope 2
• Tính toán Scope 3
• Kiểm tra và rà soát dữ liệu
• Xây dựng hệ thống quản lý dữ liệu GHG
– XÂY DỰNG HỆ THỐNG TÀI LIỆU VÀ BÁO CÁO
• Xây dựng GHG Procedure
• Xây dựng SOP thu thập dữ liệu
• Xây dựng Data Management Procedure
• Xây dựng Risk and Opportunity Assessment
• Xây dựng Quality Management Plan
• Xây dựng báo cáo kiểm kê khí nhà kính
• Xây dựng hồ sơ phục vụ xác minh
– ĐÀO TẠO VÀ HOÀN THIỆN HỆ THỐNG
• Đào tạo đội ngũ thực hiện GHG
• Đào tạo quản lý dữ liệu phát thải
• Đào tạo kiểm soát chất lượng dữ liệu
• Hướng dẫn chuẩn bị xác minh
• Rà soát và hoàn thiện hệ thống
• Coaching thực tế tại doanh nghiệp',
        ],
        [
            'code' => 'KHC-15',
            'name' => 'MÔ HÌNH HÓA VÀ ĐỊNH LƯỢNG KHÍ NHÀ KÍNH: PHƯƠNG PHÁP LUẬN TÍNH TOÁN DẤU CHÂN CARBON THEO ISO 14069:2013',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'GREECO',
            'audience' => 'Ban lãnh đạo doanh nghiệp và các cấp quản lý.
Cán bộ phụ trách ESG, phát triển bền vững, môi trường, HSE/EHS và quản lý carbon.
Cán bộ quản lý năng lượng, kỹ thuật, sản xuất, chất lượng, R&D và quản lý chuỗi cung ứng.
Thành viên Ban ISO, Ban kiểm kê khí nhà kính, Ban Net Zero và Ban phát triển bền vững.
Chuyên gia tư vấn, kiểm toán, đánh giá và xác minh phát thải khí nhà kính.
Doanh nghiệp có nhu cầu triển khai GHG Protocol để kiểm kê phát thải, xây dựng chiến lược giảm phát thải và đáp ứng yêu cầu của khách hàng, nhà đầu tư và thị trường quốc tế.',
            'objectives' => 'Hiểu và áp dụng GHG Protocol trong kiểm kê, định lượng và báo cáo phát thải khí nhà kính ở cấp doanh nghiệp và vòng đời sản phẩm.
Thiết lập ranh giới kiểm kê, xác định Scope 1, Scope 2, Scope 3, thu thập dữ liệu và tính toán phát thải theo chuẩn mực quốc tế.
Xây dựng báo cáo phát thải, quản lý chất lượng dữ liệu (QA/QC), thiết lập mục tiêu và lộ trình giảm phát thải phù hợp với yêu cầu ESG và Net Zero.
Nâng cao năng lực triển khai quản lý carbon, đáp ứng các yêu cầu của GHG Protocol, ISO 14064, ISO 14069, SBTi, CDP và các chuỗi cung ứng toàn cầu.',
            'content_summary' => '- Chuẩn mực tính toán và báo cáo khí nhà kính cho doanh nghiệp
+ Nguyên tắc Tính toán và Báo cáo khí nhà kính
+ Thiết lập ranh giới tổ chức
+ Thiết lập ranh giới hoạt động
+ Theo dõi lượng phát thải theo thời gian
+ Xác định và tính toán phát thải khí nhà kính
+ Quản lý chất lượng kiểm kê
+ Tính toán mức giảm phát thải khí nhà kính
+ Báo cáo phát thải khí nhà kính
+ Thẩm tra phát thải khí nhà kính
+ Đặt mục tiêu khí nhà kính
- Tiêu chuẩn báo cáo và tính toán vòng đời sản phẩm
+ Nguyên tắc tính toán và báo cáo khí nhà kính trong vòng đời sản phẩm
+ Nguyên tắc cơ bản của vòng đời sản phẩm
+ Thiết lập phạm vi kiểm kê sản phẩm
+ Thiết lập ranh giới
+ Thu thập dữ liệu và đánh giá chất lượng dữ liệu
+ Phân bổ
+ Đánh giá sự không chắc chắn
+ Tính kết quả kiểm kê khí nhà kính
+ Hoạt động đánh giá đảm bảo
+ Báo cáo
+ Đặt mục tiêu giảm nhẹ và theo dõi thay đổi khí nhà kính',
            'content_detail' => '– TỔNG QUAN VỀ GHG PROTOCOL VÀ QUẢN LÝ PHÁT THẢI KHÍ NHÀ KÍNH
+ Tổng quan về biến đổi khí hậu và xu hướng quản lý phát thải khí nhà kính.
+ Giới thiệu Greenhouse Gas Protocol (GHG Protocol).
+ Vai trò của GHG Protocol trong kiểm kê và báo cáo phát thải khí nhà kính.
+ Mối liên hệ giữa GHG Protocol với ISO 14064, ISO 14069, SBTi, CDP và các yêu cầu ESG.
+ Cấu trúc và phạm vi áp dụng của Corporate Standard và Product Life Cycle Standard.
+ Nguyên tắc minh bạch, đầy đủ, nhất quán, chính xác và phù hợp trong kiểm kê khí nhà kính.
– CHUẨN MỰC TÍNH TOÁN VÀ BÁO CÁO KHÍ NHÀ KÍNH CHO DOANH NGHIỆP (CORPORATE STANDARD)
+ Nguyên tắc tính toán và báo cáo khí nhà kính cho doanh nghiệp.
+ Thiết lập ranh giới tổ chức (Organizational Boundary).
+ Thiết lập ranh giới hoạt động (Operational Boundary).
+ Phân loại phát thải Scope 1, Scope 2 và Scope 3.
+ Phương pháp thu thập dữ liệu hoạt động.
+ Theo dõi lượng phát thải khí nhà kính theo thời gian.
+ Lựa chọn hệ số phát thải và phương pháp tính toán.
+ Thực hành xác định nguồn phát thải cho doanh nghiệp.
– ĐỊNH LƯỢNG, QUẢN LÝ CHẤT LƯỢNG VÀ BÁO CÁO PHÁT THẢI
+ Phương pháp xác định và tính toán phát thải khí nhà kính.
+ Quản lý chất lượng dữ liệu kiểm kê (QA/QC).
+ Tính toán mức giảm phát thải khí nhà kính.
+ Phương pháp theo dõi hiệu quả các biện pháp giảm phát thải.
+ Xây dựng báo cáo phát thải khí nhà kính theo GHG Protocol.
+ Chuẩn bị hồ sơ phục vụ hoạt động thẩm tra phát thải khí nhà kính.
+ Hướng dẫn đặt mục tiêu giảm phát thải khí nhà kính.
+ Thực hành lập báo cáo kiểm kê khí nhà kính.
– TIÊU CHUẨN TÍNH TOÁN VÀ BÁO CÁO KHÍ NHÀ KÍNH TRONG VÒNG ĐỜI SẢN PHẨM
+ Tổng quan về Product Life Cycle Standard.
+ Nguyên tắc tính toán và báo cáo khí nhà kính trong vòng đời sản phẩm.
+ Các giai đoạn của vòng đời sản phẩm.
+ Thiết lập phạm vi kiểm kê sản phẩm.
+ Thiết lập ranh giới hệ thống.
+ Xác định các nguồn phát thải trong toàn bộ vòng đời sản phẩm.
+ Mối liên hệ giữa đánh giá vòng đời (LCA) và kiểm kê khí nhà kính.
– THU THẬP DỮ LIỆU, TÍNH TOÁN VÀ ĐÁNH GIÁ KẾT QUẢ KIỂM KÊ SẢN PHẨM
+ Thu thập dữ liệu và đánh giá chất lượng dữ liệu.
+ Phương pháp phân bổ (Allocation) trong kiểm kê vòng đời sản phẩm.
+ Đánh giá sự không chắc chắn của dữ liệu.
+ Phương pháp tính toán kết quả kiểm kê khí nhà kính.
+ Phân tích kết quả và xác định các điểm phát thải trọng yếu.
+ Hoạt động đánh giá đảm bảo (Assurance Activities).
+ Thực hành tính toán phát thải cho một sản phẩm điển hình.
– BÁO CÁO KẾT QUẢ VÀ THIẾT LẬP MỤC TIÊU GIẢM PHÁT THẢI
+ Hướng dẫn xây dựng báo cáo phát thải khí nhà kính cho sản phẩm.
+ Trình bày kết quả kiểm kê theo yêu cầu của GHG Protocol.
+ Thiết lập mục tiêu giảm nhẹ phát thải khí nhà kính.
+ Theo dõi sự thay đổi lượng phát thải theo thời gian.
+ Xây dựng kế hoạch cải tiến và giảm phát thải.
+ Thực hành xây dựng lộ trình giảm phát thải cho doanh nghiệp và sản phẩm.
+ Thảo luận các tình huống thực tế và giải đáp khó khăn trong triển khai.',
        ],
        [
            'code' => 'KHC-16',
            'name' => 'ĐÁNH GIÁ VÒNG ĐỜI VÀ ĐỊNH LƯỢNG DẤU CHÂN CARBON SẢN PHẨM THEO ISO 14067:2018',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'GREECO',
            'audience' => 'Ban lãnh đạo doanh nghiệp và các cấp quản lý.
Cán bộ phụ trách ESG, phát triển bền vững, môi trường, HSE/EHS và quản lý carbon.
Cán bộ R&D, thiết kế sản phẩm, quản lý chất lượng (QA/QC), sản xuất, kỹ thuật và quản lý chuỗi cung ứng.
Thành viên Ban ISO, Ban Net Zero, Ban kiểm kê khí nhà kính và Ban phát triển bền vững.
Chuyên gia tư vấn, đánh giá vòng đời sản phẩm (LCA), kiểm kê khí nhà kính và xác minh môi trường.
Doanh nghiệp sản xuất, xuất khẩu hoặc cung ứng sản phẩm có nhu cầu tính toán Carbon Footprint of Product (CFP) theo ISO 14067:2018 nhằm đáp ứng yêu cầu của khách hàng, thị trường và các tiêu chuẩn quốc tế.',
            'objectives' => 'Hiểu và áp dụng các yêu cầu của ISO 14067:2018 trong tính toán Vết Carbon của Sản phẩm (Carbon Footprint of Product – CFP).
Thiết lập phạm vi nghiên cứu, ranh giới hệ thống, thu thập dữ liệu vòng đời và định lượng phát thải khí nhà kính của sản phẩm theo tiêu chuẩn quốc tế.
Xây dựng báo cáo CFP, đánh giá độ không đảm bảo, chuẩn bị hồ sơ phục vụ xem xét phản biện và truyền thông kết quả minh bạch.
Xác định các cơ hội giảm phát thải trong vòng đời sản phẩm, nâng cao năng lực đáp ứng yêu cầu ESG, Net Zero và chuỗi cung ứng toàn cầu.',
            'content_summary' => '- Giới thiệu tiêu chuẩn
- Thuật ngữ, định nghĩa
- Các nguyên tắc
- Phương pháp luận về định lượng CFP và CFP riêng phần
- Các giai đoạn vòng đời sản phẩm
- Ranh giới hệ thống
- Đánh giá GHG
- Quản lý chất lượng dữ liệu
- Độ không đảm bảo đo
- Phân bổ
- Báo cáo nghiên cứu CFP
- Xem xét phản biện
- Truyền thông',
            'content_detail' => '– GIỚI THIỆU ISO 14067:2018
Giới thiệu tiêu chuẩn ISO 14067:2018.
Mối liên hệ với ISO 14040, ISO 14044 và GHG Protocol.
Phạm vi áp dụng của CFP.
Các thuật ngữ và định nghĩa quan trọng.
Các nguyên tắc của tiêu chuẩn.
– PHƯƠNG PHÁP LUẬN ĐỊNH LƯỢNG CFP
Phương pháp luận về định lượng CFP.
Định lượng CFP riêng phần.
Các giai đoạn vòng đời sản phẩm.
Thiết lập ranh giới hệ thống.
Nhận diện các nguồn phát thải và loại bỏ khí nhà kính.
– THU THẬP DỮ LIỆU VÀ ĐÁNH GIÁ GHG
Phương pháp thu thập dữ liệu hoạt động.
Lựa chọn hệ số phát thải.
Đánh giá GHG trong vòng đời sản phẩm.
Quản lý chất lượng dữ liệu.
Kiểm soát và lưu trữ dữ liệu.
– ĐỘ KHÔNG ĐẢM BẢO VÀ PHÂN BỔ
Khái niệm độ không đảm bảo đo.
Phương pháp đánh giá độ không đảm bảo.
Nguyên nhân gây sai số.
Nguyên tắc phân bổ.
Các phương pháp phân bổ trong CFP.
Thực hành phân bổ trong các trường hợp đồng sản phẩm.
– BÁO CÁO NGHIÊN CỨU CFP
Cấu trúc báo cáo nghiên cứu CFP.
Trình bày kết quả định lượng.
Giả định và giới hạn nghiên cứu.
Yêu cầu về minh bạch và truy xuất dữ liệu.
Thực hành xây dựng báo cáo CFP.
– XEM XÉT PHẢN BIỆN VÀ TRUYỀN THÔNG
Yêu cầu xem xét phản biện.
Chuẩn bị hồ sơ phục vụ phản biện.
Nguyên tắc truyền thông kết quả CFP.
Công bố thông tin ra bên ngoài.
Các lưu ý khi sử dụng nhãn và tuyên bố môi trường.
– WORKSHOP THỰC HÀNH
Chọn một sản phẩm cụ thể của doanh nghiệp.
Thiết lập phạm vi và ranh giới hệ thống.
Thu thập dữ liệu vòng đời.
Tính toán CFP.
Đánh giá độ không đảm bảo.
Lập báo cáo CFP hoàn chỉnh.
Thảo luận kết quả và cơ hội giảm phát thải.',
        ],
        [
            'code' => 'KHC-17',
            'name' => 'CHIẾN LƯỢC TRUNG HÒA CARBON: KỸ THUẬT XÁC MINH VÀ TUYÊN BỐ ĐẠT CHUẨN QUỐC TẾ PAS 2060:2014',
            'duration' => '2 ngày',
            'fee' => 2660000.0,
            'instructor' => 'HUY THANH',
            'audience' => 'Ban lãnh đạo doanh nghiệp và các cấp quản lý.
Cán bộ phụ trách ESG, phát triển bền vững, môi trường, HSE/EHS và quản lý carbon.
Cán bộ quản lý năng lượng, kỹ thuật, sản xuất, chất lượng và quản lý chuỗi cung ứng.
Thành viên Ban ISO, Ban Net Zero, Ban kiểm kê khí nhà kính và Ban phát triển bền vững.
Chuyên gia tư vấn, kiểm toán, xác minh phát thải khí nhà kính và triển khai chương trình Carbon Neutral.
Doanh nghiệp, tổ chức có nhu cầu xây dựng, công bố hoặc duy trì Carbon Neutral theo PAS 2060:2014, phục vụ yêu cầu của khách hàng, nhà đầu tư và thị trường quốc tế.',
            'objectives' => 'Hiểu và áp dụng các yêu cầu của PAS 2060:2014 để xây dựng và công bố trung hòa carbon (Carbon Neutral) cho tổ chức, sản phẩm, dịch vụ hoặc sự kiện.
Thiết lập phạm vi, định lượng phát thải khí nhà kính, xây dựng kế hoạch giảm phát thải và lựa chọn giải pháp bù đắp carbon phù hợp với tiêu chuẩn.
Xây dựng Carbon Management Plan, Qualifying Explanatory Statement (QES) và hồ sơ phục vụ hoạt động đánh giá, xác minh trung hòa carbon.
Nâng cao năng lực triển khai chiến lược quản lý carbon, đáp ứng yêu cầu ESG, Net Zero và nâng cao năng lực cạnh tranh trong chuỗi cung ứng toàn cầu.',
            'content_summary' => '- Giới thiệu tiêu chuẩn.
- Thuật ngữ, định nghĩa.
- Thể hiện tính trung hòa carbon.
- Xác định và chứng minh chủ thể và các phát thải khí nhà kính (KNK) liên quan.
- Định lượng lượng phát thải carbon.
- Cam kết trung hòa carbon.
- Thành tựu giảm phát thải khí nhà kính.
- Bù đắp phát thải khí nhà kính dư thừa.
- Tuyên bố rõ ràng về tính trung hòa carbon.
- Duy trì trạng thái trung tính carbon.',
            'content_detail' => '– TỔNG QUAN VỀ PAS 2060:2014
+ Giới thiệu tiêu chuẩn PAS 2060:2014.
+ Vai trò của Carbon Neutral trong chiến lược phát triển bền vững.
+ Mối liên hệ giữa PAS 2060 với ISO 14064, ISO 14067, GHG Protocol và Net Zero.
+ Phạm vi áp dụng của tiêu chuẩn.
+ Thuật ngữ và định nghĩa quan trọng.
+ Các nguyên tắc của chứng nhận trung hòa carbon.
– XÁC ĐỊNH PHẠM VI VÀ ĐỊNH LƯỢNG PHÁT THẢI KHÍ NHÀ KÍNH
+ Thể hiện tính trung hòa carbon theo yêu cầu của PAS 2060.
+ Xác định chủ thể áp dụng (tổ chức, sản phẩm, dịch vụ, sự kiện hoặc công trình).
+ Xác định phạm vi đánh giá và các nguồn phát thải khí nhà kính liên quan.
+ Thiết lập ranh giới kiểm kê.
+ Phương pháp thu thập dữ liệu hoạt động.
+ Định lượng lượng phát thải carbon.
+ Kiểm soát và quản lý chất lượng dữ liệu phát thải.
– XÂY DỰNG CAM KẾT VÀ KẾ HOẠCH TRUNG HÒA CARBON
+ Xây dựng cam kết trung hòa carbon.
+ Thiết lập mục tiêu giảm phát thải.
+ Xây dựng lộ trình giảm phát thải theo từng giai đoạn.
+ Lập kế hoạch hành động và phân công trách nhiệm.
+ Theo dõi và đánh giá kết quả thực hiện.
+ Quản lý các thay đổi trong quá trình triển khai.
– GIẢM PHÁT THẢI VÀ BÙ ĐẮP PHÁT THẢI CÒN LẠI
+ Thành tựu giảm phát thải khí nhà kính.
+ Đánh giá hiệu quả các biện pháp giảm phát thải.
+ Xác định lượng phát thải còn lại sau khi thực hiện giảm thiểu.
+ Nguyên tắc lựa chọn tín chỉ carbon và dự án bù đắp phát thải.
+ Bù đắp lượng phát thải khí nhà kính dư thừa.
+ Yêu cầu đối với tín chỉ carbon sử dụng cho trung hòa carbon.
– TUYÊN BỐ VÀ DUY TRÌ TRẠNG THÁI TRUNG HÒA CARBON
+ Xây dựng tuyên bố rõ ràng về tính trung hòa carbon.
+ Hồ sơ và tài liệu chứng minh sự phù hợp.
+ Chuẩn bị cho hoạt động đánh giá độc lập hoặc xác minh.
+ Công bố thông tin về trung hòa carbon.
+ Duy trì trạng thái trung hòa carbon theo chu kỳ.
+ Cập nhật dữ liệu và cải tiến liên tục hệ thống quản lý phát thải.
– WORKSHOP THỰC HÀNH
+ Xác định phạm vi áp dụng PAS 2060 cho doanh nghiệp.
+ Thực hành định lượng lượng phát thải carbon.
+ Xây dựng kế hoạch giảm phát thải và bù đắp lượng phát thải còn lại.
+ Soạn thảo Carbon Management Plan và Qualifying Explanatory Statement (QES).
+ Thực hành xây dựng tuyên bố trung hòa carbon.
+ Thảo luận các tình huống thực tế và giải đáp khó khăn trong quá trình triển khai.',
        ],
        [
            'code' => 'KHC-18',
            'name' => 'BIẾN ĐỔI KHÍ HẬU VÀ LỘ TRÌNH TRUNG HÒA CARBON THEO CHUẨN QUỐC TẾ ISO 14068-1:2023',
            'duration' => '2 ngày',
            'fee' => 2660000.0,
            'instructor' => 'HUY THANH',
            'audience' => 'Ban lãnh đạo doanh nghiệp và các cấp quản lý.
Cán bộ phụ trách ESG, phát triển bền vững, môi trường, HSE/EHS và quản lý carbon.
Cán bộ quản lý năng lượng, kỹ thuật, sản xuất, chất lượng và quản lý chuỗi cung ứng.
Thành viên Ban ISO, Ban Net Zero, Ban kiểm kê khí nhà kính và Ban phát triển bền vững.
Chuyên gia tư vấn, đánh giá, xác minh phát thải khí nhà kính và triển khai các chương trình Carbon Neutral, Net Zero.
Doanh nghiệp, tổ chức có nhu cầu xây dựng và công bố Trung hòa Carbon theo ISO 14068-1:2023, đáp ứng yêu cầu của khách hàng, nhà đầu tư và các thị trường quốc tế.',
            'objectives' => 'Hiểu và áp dụng các yêu cầu của ISO 14068-1:2023 trong xây dựng và triển khai chương trình Trung hòa Carbon (Carbon Neutrality) cho tổ chức, sản phẩm, dịch vụ hoặc dự án.
Thiết lập phạm vi, định lượng phát thải và loại bỏ khí nhà kính, xây dựng kế hoạch quản lý carbon và lộ trình giảm phát thải theo tiêu chuẩn quốc tế.
Xây dựng báo cáo trung hòa carbon, chuẩn bị hồ sơ phục vụ hoạt động xác minh và công bố thông tin minh bạch theo ISO 14068-1:2023.
Nâng cao năng lực triển khai chiến lược Net Zero, quản lý carbon và đáp ứng yêu cầu ESG, khách hàng và chuỗi cung ứng toàn cầu.',
            'content_summary' => '- Giới thiệu tiêu chuẩn.
- Thuật ngữ, định nghĩa.
- Các nguyên tắc
- Cách tiếp cận
- Cam kết trung hòa carbon
- Lựa chọn chủ đề và ranh giới
- Định lượng phát thải khí nhà kính và loại bỏ KNK
- Kế hoạch quản lý trung hòa carbon
- Giảm nhẹ phát thải khí nhà kính và tăng cường loại bỏ KNK
- Bù đắp lượng khí thải carbon
- Báo cáo trung hòa carbon',
            'content_detail' => '– TỔNG QUAN VỀ ISO 14068-1:2023
+ Giới thiệu tiêu chuẩn ISO 14068-1:2023.
+ Bối cảnh và xu hướng trung hòa carbon trên thế giới.
+ Mối liên hệ giữa ISO 14068-1 với ISO 14064, ISO 14067, GHG Protocol và Net Zero.
+ Thuật ngữ và định nghĩa.
+ Các nguyên tắc của tiêu chuẩn.
+ Vai trò của trung hòa carbon trong chiến lược phát triển bền vững.
– CÁCH TIẾP CẬN VÀ XÁC ĐỊNH PHẠM VI ÁP DỤNG
+ Cách tiếp cận đối với trung hòa carbon theo ISO 14068-1.
+ Cam kết trung hòa carbon của tổ chức.
+ Lựa chọn chủ thể áp dụng (tổ chức, sản phẩm, dịch vụ, sự kiện hoặc dự án).
+ Thiết lập ranh giới tổ chức và ranh giới hoạt động.
+ Xác định nguồn phát thải và nguồn loại bỏ khí nhà kính.
+ Thiết lập đường cơ sở phát thải khí nhà kính.
– ĐỊNH LƯỢNG PHÁT THẢI KHÍ NHÀ KÍNH VÀ XÂY DỰNG KẾ HOẠCH QUẢN LÝ
+ Phương pháp định lượng phát thải khí nhà kính.
+ Phương pháp định lượng lượng loại bỏ khí nhà kính.
+ Thu thập dữ liệu và lựa chọn hệ số phát thải.
+ Đánh giá chất lượng dữ liệu và quản lý thông tin.
+ Xây dựng kế hoạch quản lý trung hòa carbon.
+ Thiết lập mục tiêu và chỉ tiêu giảm phát thải.
– GIẢM NHẸ PHÁT THẢI VÀ TĂNG CƯỜNG LOẠI BỎ KHÍ NHÀ KÍNH
+ Nguyên tắc ưu tiên giảm phát thải theo ISO 14068-1.
+ Xây dựng lộ trình giảm phát thải khí nhà kính.
+ Các biện pháp nâng cao hiệu quả sử dụng năng lượng và tài nguyên.
+ Các giải pháp tăng cường loại bỏ khí nhà kính.
+ Theo dõi và đánh giá hiệu quả các biện pháp giảm phát thải.
+ Cải tiến liên tục nhằm đạt mục tiêu trung hòa carbon.
– BÙ ĐẮP PHÁT THẢI VÀ BÁO CÁO TRUNG HÒA CARBON
+ Nguyên tắc sử dụng tín chỉ carbon trong trung hòa carbon.
+ Tiêu chí lựa chọn dự án bù đắp phát thải.
+ Bù đắp lượng phát thải carbon còn lại.
+ Chuẩn bị hồ sơ chứng minh tính trung hòa carbon.
+ Xây dựng báo cáo trung hòa carbon theo ISO 14068-1.
+ Công bố thông tin và chuẩn bị cho hoạt động xác minh.
– WORKSHOP THỰC HÀNH
+ Xác định phạm vi áp dụng cho doanh nghiệp.
+ Thiết lập ranh giới và đường cơ sở phát thải.
+ Thực hành định lượng phát thải và loại bỏ khí nhà kính.
+ Xây dựng kế hoạch quản lý trung hòa carbon.
+ Đề xuất các giải pháp giảm phát thải và tăng cường loại bỏ khí nhà kính.
+ Xác định nhu cầu bù đắp lượng phát thải còn lại.
+ Thực hành xây dựng báo cáo trung hòa carbon.
+ Thảo luận các tình huống thực tế và giải đáp khó khăn trong quá trình triển khai.',
        ],
        [
            'code' => 'KHC-19',
            'name' => 'PHÁT TRIỂN DỰ ÁN TÍN CHỈ CARBON: THIẾT KẾ HỒ SƠ PDD VÀ THẨM ĐỊNH PHƯƠNG PHÁP LUẬN VCS',
            'duration' => '2 ngày',
            'fee' => 2660000.0,
            'instructor' => 'HUY THANH',
            'audience' => 'Ban lãnh đạo và các cấp quản lý doanh nghiệp.
Cán bộ ESG, HSE/EHS, môi trường, phát triển bền vững và quản lý carbon.
Cán bộ quản lý dự án, kỹ thuật, năng lượng, nông nghiệp và lâm nghiệp.
Chuyên gia tư vấn, đơn vị phát triển dự án carbon và các tổ chức tham gia thị trường carbon.
Doanh nghiệp có nhu cầu phát triển dự án và tạo tín chỉ carbon theo VCS.',
            'objectives' => 'Hiểu các yêu cầu của Verified Carbon Standard (VCS) và quy trình phát triển dự án carbon.
Xây dựng Project Design Document (PDD), xác định ranh giới dự án và tính toán lượng giảm phát thải.
Thiết lập kế hoạch giám sát, chuẩn bị hồ sơ phục vụ Validation, Verification và phát hành tín chỉ carbon.
Nâng cao năng lực phát triển dự án carbon, đáp ứng yêu cầu của thị trường carbon quốc tế.',
            'content_summary' => 'Phát triển Project Design Document bao gồm các yêu cầu chung, chọn lựa phương pháp, ranh giới của dự án, kịch bản cơ sở, leakage, additionality, kế hoạch quản lý và giám sát dự án, phát triển bền vững & các bên liên quan.',
            'content_detail' => '– TỔNG QUAN VỀ VERIFIED CARBON STANDARD (VCS)
+ Giới thiệu Verified Carbon Standard (VCS).
+ Vai trò của VCS trong thị trường carbon tự nguyện.
+ Chu trình phát triển dự án carbon theo VCS.
+ Các loại dự án đủ điều kiện đăng ký.
+ Cấu trúc và thành phần của Project Design Document (PDD).
+ Mối liên hệ giữa VCS với các tiêu chuẩn và cơ chế carbon khác.
– PHÁT TRIỂN PROJECT DESIGN DOCUMENT (PDD)
+ Cấu trúc và yêu cầu chung của Project Design Document.
+ Thu thập thông tin và dữ liệu phục vụ xây dựng PDD.
+ Lựa chọn phương pháp luận (Methodology) phù hợp với loại hình dự án.
+ Mô tả hoạt động và mục tiêu dự án.
+ Xác định thời gian tín chỉ (Crediting Period).
+ Thực hành xây dựng các nội dung cơ bản của PDD.
– XÁC ĐỊNH RANH GIỚI DỰ ÁN VÀ ĐỊNH LƯỢNG GIẢM PHÁT THẢI
+ Thiết lập ranh giới dự án (Project Boundary).
+ Xây dựng kịch bản cơ sở (Baseline Scenario).
+ Xác định và định lượng phát thải khí nhà kính trong kịch bản cơ sở và kịch bản dự án.
+ Đánh giá phát thải rò rỉ (Leakage).
+ Đánh giá tính bổ sung (Additionality).
+ Phương pháp tính toán lượng giảm phát thải hoặc tăng cường hấp thụ khí nhà kính.
– KẾ HOẠCH QUẢN LÝ VÀ GIÁM SÁT DỰ ÁN
+ Xây dựng kế hoạch quản lý dự án.
+ Thiết lập kế hoạch giám sát (Monitoring Plan).
+ Thu thập, quản lý và lưu trữ dữ liệu.
+ Kiểm soát chất lượng dữ liệu (QA/QC).
+ Quản lý rủi ro trong quá trình thực hiện dự án.
+ Chuẩn bị hồ sơ phục vụ hoạt động giám sát và xác minh.
– PHÁT TRIỂN BỀN VỮNG, THAM VẤN CÁC BÊN LIÊN QUAN VÀ ĐĂNG KÝ DỰ ÁN
+ Đánh giá đóng góp của dự án đối với phát triển bền vững.
+ Nhận diện và phân tích các bên liên quan.
+ Quy trình tham vấn các bên liên quan.
+ Tích hợp kết quả tham vấn vào Project Design Document.
+ Quy trình thẩm định (Validation), xác minh (Verification) và đăng ký dự án.
+ Quy trình phát hành Verified Carbon Units (VCUs).
– WORKSHOP THỰC HÀNH
+ Lựa chọn một loại hình dự án carbon phù hợp.
+ Xây dựng đề cương Project Design Document.
+ Xác định ranh giới dự án và xây dựng kịch bản cơ sở.
+ Thực hành đánh giá Additionality và Leakage.
+ Xây dựng kế hoạch giám sát dự án.
+ Chuẩn bị hồ sơ phục vụ Validation và Verification.
+ Thảo luận các tình huống thực tế trong phát triển dự án VCS.',
        ],
        [
            'code' => 'KHC-20',
            'name' => 'LẬP BÁO CÁO PHÁT TRIỂN BỀN VỮNG THEO TIÊU CHUẨN GRI 2021',
            'duration' => '2 ngày',
            'fee' => 2660000.0,
            'instructor' => 'HUY THANH',
            'audience' => 'Ban lãnh đạo và các cấp quản lý doanh nghiệp.
Cán bộ phụ trách ESG, phát triển bền vững, HSE/EHS, môi trường và nhân sự.
Thành viên Ban ISO, Ban ESG, Ban Net Zero và Ban phát triển bền vững.
Chuyên gia tư vấn và các đơn vị triển khai báo cáo ESG.
Doanh nghiệp có nhu cầu lập Báo cáo Phát triển Bền vững theo GRI Standards.',
            'objectives' => 'Hiểu và áp dụng GRI Standards trong xây dựng Báo cáo Phát triển Bền vững.
Xác định chủ đề trọng yếu, thu thập dữ liệu và xây dựng các chỉ số ESG theo GRI.
Lập báo cáo GRI đáp ứng yêu cầu của nhà đầu tư, khách hàng và các bên liên quan.
Nâng cao năng lực công bố thông tin ESG, hướng tới phát triển bền vững và tuân thủ thông lệ quốc tế.',
            'content_summary' => '- Giới thiệu tiêu chuẩn
- Định nghĩa, thuật ngữ
- Module các nội dung liên quan đến trách nhiệm xã hội và nhân quyền
- Module các yêu cầu về sức khỏe và an toàn
- Module các yêu cầu về môi trường và đa dạng sinh học
- Module các yêu cầu về chống biến đổi khí hậu
- Module các các yêu cầu về đạo đức kinh doanh',
            'content_detail' => '– TỔNG QUAN VỀ GRI STANDARDS
+ Giới thiệu Global Reporting Initiative (GRI).
Cấu trúc và hệ thống GRI Standards.
Mối liên hệ giữa GRI với ISSB, ESRS, IFRS Sustainability Disclosure Standards và các khung báo cáo ESG khác.
Thuật ngữ và định nghĩa.
Nguyên tắc lập báo cáo theo GRI.
Xác định các bên liên quan và chủ đề trọng yếu (Material Topics).
– MODULE BÁO CÁO VỀ TRÁCH NHIỆM XÃ HỘI VÀ NHÂN QUYỀN
Các yêu cầu của GRI về lao động và việc làm.
Quyền con người trong hoạt động sản xuất và kinh doanh.
Bình đẳng, đa dạng và hòa nhập (DE&I).
Quyền tự do hiệp hội và thương lượng tập thể.
Lao động trẻ em và lao động cưỡng bức.
Quan hệ với cộng đồng và các bên liên quan.
Thực hành thu thập dữ liệu và xây dựng chỉ tiêu báo cáo xã hội.
– MODULE VỀ SỨC KHỎE, AN TOÀN VÀ MÔI TRƯỜNG
Phần A – Sức khỏe và an toàn
Các yêu cầu của GRI về an toàn và sức khỏe nghề nghiệp.
Nhận diện mối nguy và đánh giá rủi ro.
Chỉ số tai nạn lao động và bệnh nghề nghiệp.
Thu thập và quản lý dữ liệu an toàn.
Phần B – Môi trường và đa dạng sinh học
Quản lý tài nguyên và sử dụng năng lượng.
Quản lý nước và nước thải.
Quản lý chất thải và kinh tế tuần hoàn.
Đa dạng sinh học và bảo tồn hệ sinh thái.
Đánh giá tác động môi trường.
Xây dựng các chỉ số môi trường theo GRI.
– MODULE BÁO CÁO VỀ BIẾN ĐỔI KHÍ HẬU
Các yêu cầu của GRI về chống biến đổi khí hậu.
Kiểm kê và báo cáo phát thải khí nhà kính.
Quản lý rủi ro và cơ hội liên quan đến khí hậu.
Mục tiêu giảm phát thải và chỉ tiêu theo dõi.
Công bố thông tin về chiến lược ứng phó biến đổi khí hậu.
Liên kết với các chuẩn mực quốc tế về báo cáo khí hậu.
– MODULE VỀ ĐẠO ĐỨC KINH DOANH VÀ QUẢN TRỊ
Các yêu cầu về đạo đức kinh doanh.
Cơ cấu quản trị doanh nghiệp.
Chống tham nhũng và chống hối lộ.
Quản lý tuân thủ pháp luật.
Quản trị rủi ro và minh bạch thông tin.
Cơ chế tiếp nhận và xử lý khiếu nại.
Thực hành xây dựng các chỉ số quản trị theo GRI.
– WORKSHOP THỰC HÀNH LẬP BÁO CÁO GRI
Xác định chủ đề trọng yếu cho doanh nghiệp.
Lựa chọn các tiêu chuẩn GRI áp dụng.
Thu thập và tổng hợp dữ liệu ESG.
Xây dựng các chỉ tiêu công bố theo GRI.
Thực hành lập Báo cáo Phát triển Bền vững theo GRI Standards.
Thảo luận các tình huống thực tế và giải đáp khó khăn trong quá trình triển khai.',
        ],
        [
            'code' => 'KHC-21',
            'name' => 'QUẢN TRỊ ĐỒNG BỘ CHUỖI GIÁ TRỊ RỪNG BỀN VỮNG THEO CHUẨN MỰC ESG VÀ CHỨNG NHẬN FSC/PEFC',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => 'Ban lãnh đạo và các cấp quản lý doanh nghiệp.
Cán bộ thu mua, chuỗi cung ứng, xuất nhập khẩu và quản lý chất lượng.
Cán bộ ESG, phát triển bền vững, môi trường và HSE/EHS.
Doanh nghiệp hoạt động trong lĩnh vực lâm nghiệp, gỗ, giấy, nội thất, bao bì và các ngành sử dụng nguyên liệu từ rừng.
Doanh nghiệp có nhu cầu triển khai hoặc đáp ứng yêu cầu chứng nhận FSC và thị trường quốc tế.',
            'objectives' => 'Hiểu các nguyên tắc và yêu cầu của FSC trong quản lý rừng và chuỗi cung ứng bền vững.
Áp dụng FSC vào quản lý nguồn cung, truy xuất nguồn gốc và đánh giá rủi ro nhà cung cấp.
Đáp ứng yêu cầu của khách hàng và thị trường xuất khẩu về nguồn nguyên liệu có trách nhiệm.
Nâng cao năng lực triển khai phát triển bền vững, ESG và quản lý chuỗi cung ứng.',
            'content_summary' => '- Tổng quan FSC
- Nguyên tắc FSC
- FSC và Phát triển bền vững
- FSC và Chuỗi cung ứng
- FSC và Doanh nghiệp',
            'content_detail' => '- Tổng quan FSC
• Lịch sử hình thành FSC. 
• Cơ cấu tổ chức FSC. 
• Hệ thống chứng nhận FSC. 
- Nguyên tắc FSC
• Tuân thủ pháp luật. 
• Quyền người lao động. 
• Quyền cộng đồng địa phương. 
• Quan hệ với người dân bản địa. 
• Lợi ích từ rừng. 
• Giá trị môi trường. 
• Kế hoạch quản lý. 
• Giám sát. 
• Giá trị bảo tồn cao. 
• Thực hiện các hoạt động quản lý. 
- FSC và Phát triển bền vững
• ESG. 
• Kinh tế tuần hoàn. 
• Truy xuất nguồn gốc. 
• Bảo tồn đa dạng sinh học. 
• Quản lý tài nguyên thiên nhiên. 
- FSC và Chuỗi cung ứng
• Quản lý nhà cung cấp. 
• Truy xuất nguồn gốc. 
• Đánh giá rủi ro nguồn cung. 
• Tuyên bố FSC. 
- FSC và Doanh nghiệp
• Lợi ích thương mại. 
• Yêu cầu khách hàng quốc tế. 
• FSC và các thị trường xuất khẩu. 
• FSC và chiến lược phát triển bền vững.',
        ],
        [
            'code' => 'KHC-22',
            'name' => 'GIẢM PHÁT THẢI KHÍ NHÀ KÍNH: KỊCH BẢN PHI CARBON HÓA THEO LỘ TRÌNH SBTI NET-ZERO',
            'duration' => '2 ngày',
            'fee' => 2660000.0,
            'instructor' => 'HUY THANH',
            'audience' => 'Ban lãnh đạo và các cấp quản lý doanh nghiệp.
Cán bộ phụ trách ESG, phát triển bền vững, HSE/EHS, môi trường và quản lý carbon.
Thành viên Ban Net Zero, Ban kiểm kê khí nhà kính và Ban phát triển bền vững.
Chuyên gia tư vấn về quản lý carbon và ESG.
Doanh nghiệp có nhu cầu xây dựng mục tiêu giảm phát thải theo SBTi và đáp ứng yêu cầu của khách hàng, nhà đầu tư và chuỗi cung ứng toàn cầu.',
            'objectives' => 'Hiểu các yêu cầu và quy trình thiết lập mục tiêu giảm phát thải theo SBTi.
Xây dựng mục tiêu giảm phát thải ngắn hạn cho Scope 1, Scope 2 và Scope 3 theo cơ sở khoa học.
Chuẩn bị hồ sơ đăng ký, xác nhận và công bố mục tiêu theo yêu cầu của SBTi.
Nâng cao năng lực xây dựng lộ trình giảm phát thải, hướng tới mục tiêu Net Zero và đáp ứng yêu cầu ESG.',
            'content_summary' => '- Giới thiệu tiêu chuẩn SBTi
- Giới thiệu sáng kiến dựa trên mục tiêu khoa học
- Tổng quan về quy trình thiết lập mục tiêu của SBTi
- Cách thức phát triển mục tiêu dựa trên cơ sở khoa học ngắn hạn',
            'content_detail' => '- Giới thiệu tiêu chuẩn SBTi
- Giới thiệu sáng kiến dựa trên mục tiêu khoa học
- Tổng quan về quy trình thiết lập mục tiêu của SBTi
+ Bước 1: Cam kết đặt mục tiêu dựa trên khoa học trong ngày hạn
+ Bước 2: Phát triển mục tiêu dựa trên khoa học ngắn hạn
+ Bước 3: Gửi mục tiêu của bạn để xác nhận
+ Bước 4: Công bố mục tiêu
+ Bước 5: Công bố tiến độ
+ Bước 6: Quy trình tính toán mục tiêu
- Cách thức phát triển mục tiêu dựa trên cơ sở khoa học ngắn hạn
+ Soát xét mục tiêu gần nhất
+ Xây dựng chuẩn mực & các khuyến nghị cho việc thiết lập mục tiêu gần nhất
+ Chọn năm cơ sở
+ Chọn năm mục tiêu
+ Đảm bảo ranh giới mục tiêu phù hợp với ranh giới kiểm kê GHG
+ Xác định cách thức xác định ranh giới của các công ty con
+ Loại trừ việc sử dụng off-set
+ Loại trừ giảm nhẹ phát thải
+ Tối ưu hóa cách thức xử lý phát thải Scope 3
+ Các cân nhắc cho lĩnh vực ngành nghề
+ Chọn lựa mục tiêu có hoài bão
+ Thiết lập mục tiêu ngắn hạn cho phát thải Scope 1, Scope 2
+ Thiết lập mục tiêu ngắn hạn cho phát thải Scope 3',
        ],
        [
            'code' => 'KHC-23',
            'name' => 'KIỂM SOÁT VẬN HÀNH FSC CoC: SỐ HÓA DỮ LIỆU ĐỊNH DANH VÀ VÒNG ĐỜI SẢN PHẨM TỪ RỪNG',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => 'Ban lãnh đạo và các cấp quản lý doanh nghiệp.
Cán bộ thu mua, kho, sản xuất, chất lượng và xuất nhập khẩu.
Cán bộ ESG, phát triển bền vững, môi trường và HSE/EHS.
Thành viên Ban ISO, Ban FSC và nhóm triển khai chứng nhận.
Doanh nghiệp sản xuất, chế biến, kinh doanh gỗ, giấy, nội thất, bao bì và các đơn vị có nhu cầu đạt chứng nhận FSC CoC.',
            'objectives' => 'Hiểu và áp dụng các yêu cầu của FSC Chain of Custody (FSC CoC) trong doanh nghiệp.
Xây dựng hệ thống quản lý, truy xuất nguồn gốc và kiểm soát sản phẩm theo tiêu chuẩn FSC.
Thiết lập tài liệu, quy trình và triển khai vận hành hệ thống FSC CoC hiệu quả.
Chuẩn bị đánh giá nội bộ và sẵn sàng cho hoạt động chứng nhận FSC CoC.',
            'content_summary' => '- KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
- ĐÁNH GIÁ THỰC TRẠNG (GAP ASSESSMENT)
- XÂY DỰNG HỆ THỐNG FSC CoC
- TRIỂN KHAI HỆ THỐNG VÀ ĐÀO TẠO CHUYÊN SÂU
- ĐÁNH GIÁ NỘI BỘ VÀ CHUẨN BỊ CHỨNG NHẬN',
            'content_detail' => '- KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
Nội dung:
• Tổng quan FSC. 
• Tổng quan FSC CoC. 
• Các loại chứng nhận FSC. 
• FSC 100%. 
• FSC Mix. 
• FSC Recycled. 
• Yêu cầu khách hàng và thị trường. 
• Vai trò và trách nhiệm các bộ phận. 
• Thành lập nhóm triển khai FSC. 
Đầu ra
• Kế hoạch triển khai. 
• Hồ sơ đào tạo nhận thức. 
• Ban triển khai FSC. 
- ĐÁNH GIÁ THỰC TRẠNG (GAP ASSESSMENT)
Nội dung:
• Đánh giá hệ thống hiện tại. 
• Đánh giá nhà cung cấp. 
• Đánh giá quản lý kho. 
• Đánh giá truy xuất nguồn gốc. 
• Đánh giá quản lý hồ sơ. 
• Đánh giá hoạt động sản xuất. 
• Đánh giá ghi nhãn sản phẩm. 
• Đánh giá năng lực nhân sự. 
Đầu ra
• Báo cáo GAP. 
• Danh mục điểm chưa phù hợp. 
• Kế hoạch hành động. 
- XÂY DỰNG HỆ THỐNG FSC CoC
Xây dựng:
• Chính sách FSC. 
• Mục tiêu FSC. 
• Quy trình mua hàng FSC. 
• Quy trình tiếp nhận nguyên liệu FSC. 
• Quy trình quản lý kho FSC. 
• Quy trình truy xuất nguồn gốc. 
• Quy trình sản xuất sản phẩm FSC. 
• Quy trình bán hàng FSC. 
• Quy trình quản lý tuyên bố FSC. 
• Quy trình quản lý nhãn FSC. 
• Quy trình kiểm soát hồ sơ. 
• Quy trình đánh giá nội bộ. 
Đầu ra
• Bộ tài liệu FSC CoC. 
• Biểu mẫu FSC. 
• Danh mục hồ sơ kiểm soát. 
- TRIỂN KHAI HỆ THỐNG VÀ ĐÀO TẠO CHUYÊN SÂU
Nội dung:
• Quản lý nhà cung cấp FSC. 
• Kiểm tra chứng chỉ FSC. 
• Kiểm soát hóa đơn và chứng từ FSC. 
• Kiểm soát tồn kho FSC. 
• Quản lý sản xuất sản phẩm FSC. 
• Quản lý tuyên bố FSC. 
• Quản lý nhãn FSC. 
• Truy xuất nguồn gốc sản phẩm. 
Đầu ra
• Hồ sơ đào tạo. 
• Hồ sơ vận hành thực tế. 
• Hồ sơ truy xuất nguồn gốc. 
- ĐÁNH GIÁ NỘI BỘ VÀ CHUẨN BỊ CHỨNG NHẬN
Nội dung:
• Đào tạo đánh giá viên nội bộ. 
• Thực hiện đánh giá nội bộ. 
• Hành động khắc phục. 
• Xem xét của lãnh đạo. 
• Chuẩn bị đánh giá chứng nhận. 
Đầu ra
• Báo cáo đánh giá nội bộ. 
• CAPA Plan. 
• Hồ sơ sẵn sàng chứng nhận-',
        ],
        [
            'code' => 'KHC-24',
            'name' => 'ISCC (PLUS/EU/CORSIA) – TRIỂN KHAI HỆ THỐNG CHỨNG NHẬN BỀN VỮNG VÀ CARBON',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => 'Ban lãnh đạo và các cấp quản lý doanh nghiệp.
Cán bộ thu mua, chuỗi cung ứng, kho, sản xuất, chất lượng và xuất nhập khẩu.
Cán bộ ESG, phát triển bền vững, HSE/EHS, môi trường và quản lý carbon.
Thành viên Ban ISO, Ban ISCC và nhóm triển khai chứng nhận.
Doanh nghiệp sản xuất, chế biến, năng lượng, nhiên liệu sinh học, hóa chất, nhựa tái chế và các đơn vị có nhu cầu đạt chứng nhận ISCC PLUS, ISCC EU hoặc ISCC CORSIA.',
            'objectives' => 'Hiểu các yêu cầu của ISCC PLUS, ISCC EU và ISCC CORSIA trong quản lý chuỗi cung ứng bền vững.
Xây dựng hệ thống truy xuất nguồn gốc, Mass Balance và quản lý phát thải khí nhà kính theo yêu cầu ISCC.
Thiết lập tài liệu, quản lý hồ sơ và vận hành hệ thống ISCC hiệu quả.
Chuẩn bị đánh giá nội bộ và sẵn sàng cho hoạt động chứng nhận ISCC.',
            'content_summary' => '– KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
– XÂY DỰNG HỆ THỐNG ISCC',
            'content_detail' => '– KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
+ TỔNG QUAN VỀ ISCC
• Tổng quan về phát triển bền vững
• Tổng quan về ISCC
• Cấu trúc hệ thống ISCC
• Phạm vi áp dụng ISCC
• Yêu cầu khách hàng và thị trường quốc tế
• Tổng quan chuỗi cung ứng bền vững
+ ISCC PLUS / ISCC EU / ISCC CORSIA
• Phạm vi áp dụng ISCC PLUS
• Phạm vi áp dụng ISCC EU
• Phạm vi áp dụng ISCC CORSIA
• Điểm khác biệt giữa các hệ thống ISCC
• Yêu cầu chứng nhận theo từng chương trình
• Quy trình chứng nhận ISCC
+ TRUY XUẤT NGUỒN GỐC VÀ MASS BALANCE
• Traceability System
• Chain of Custody
• Mass Balance System
• Quản lý nguyên liệu đầu vào
• Quản lý sản phẩm đầu ra
• Kiểm soát tồn kho
• Quản lý hồ sơ truy xuất nguồn gốc
• Thực hành Mass Balance
• Thực hành truy xuất nguồn gốc
• Thực hành kiểm tra hồ sơ ISCC
+ YÊU CẦU HỆ THỐNG QUẢN LÝ ISCC
• ISCC Management System
• Roles and Responsibilities
• Supplier Management
• Internal Audit
• Document Control
• Record Control
• Training and Competency
• Complaint Management
• CAPA Management
– XÂY DỰNG HỆ THỐNG ISCC
+ QUẢN LÝ GHG TRONG ISCC
• Tổng quan GHG trong ISCC
• Scope phát thải trong ISCC
• GHG Calculation
• Emission Factor
• RED II Requirement
• GHG Saving Requirement
• Quản lý dữ liệu GHG
• Hồ sơ tính toán GHG
+ QUẢN LÝ HỒ SƠ VÀ CHỨNG TỪ ISCC
• Self Declaration
• Sustainability Declaration
• Proof of Sustainability
• Delivery Documentation
• Supplier Documentation
• Record Retention
• Audit Trail
• Kiểm soát hồ sơ ISCC
+ ĐÁNH GIÁ NỘI BỘ VÀ CHUẨN BỊ CHỨNG NHẬN
• Internal Audit ISCC
• Audit Checklist
• Audit Technique
• Nonconformity Management
• Root Cause Analysis
• CAPA Management
• Chuẩn bị đánh giá chứng nhận
• Tiếp đoàn đánh giá ISCC
+ WORKSHOP THỰC HÀNH ISCC
Nội dung thực hành
• Thực hành Mass Balance
• Thực hành truy xuất nguồn gốc
• Thực hành kiểm tra hồ sơ
• Phân tích tình huống thực tế
• Coaching thực tế theo hoạt động doanh nghiệp
+ TỔNG KẾT VÀ ĐÁNH GIÁ CUỐI KHÓA
Nội dung thực hiện
• Ôn tập nội dung đào tạo
• Kiểm tra cuối khóa
• Đánh giá năng lực học viên
• Tổng kết chương trình đào tạo',
        ],
        [
            'code' => 'KHC-25',
            'name' => 'TRUY XUẤT NGUỒN GỐC VÀ ĐỊNH LƯỢNG HÀM LƯỢNG NHỰA TÁI CHẾ THEO EN 15343',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Chị Tuyền',
            'audience' => 'Ban lãnh đạo và các cấp quản lý doanh nghiệp.
Cán bộ thu mua, sản xuất, chất lượng (QA/QC), kỹ thuật và chuỗi cung ứng.
Cán bộ ESG, phát triển bền vững, môi trường và HSE/EHS.
Thành viên Ban ISO và nhóm triển khai chứng nhận.
Doanh nghiệp sản xuất, tái chế nhựa, bao bì và các đơn vị có nhu cầu chứng minh hàm lượng nhựa tái chế theo EN 15343.',
            'objectives' => 'Hiểu và áp dụng các yêu cầu của EN 15343:2007 trong truy xuất nguồn gốc và xác định hàm lượng nhựa tái chế.
Thiết lập hệ thống kiểm soát nguyên liệu, quản lý dữ liệu và truy xuất nguồn gốc trong sản xuất.
Tính toán, xác minh và công bố hàm lượng nhựa tái chế theo yêu cầu của tiêu chuẩn.
Chuẩn bị hệ thống tài liệu và sẵn sàng đáp ứng yêu cầu đánh giá, chứng nhận và thị trường quốc tế.',
            'content_summary' => '- Giới thiệu tiêu chuẩn EN 15343:2007
- Thuật ngữ và định nghĩa
- Phương pháp và quy trình.
- Kiểm soát nguyên liệu đầu vào
- Kiểm soát quá trình sản xuất tái chế
- Đặc tính tái chế nhựa
- Truy xuất nguồn gốc
- Đảm bảo chất lượng
- Hàm lượng tái chế',
            'content_detail' => '– TỔNG QUAN VỀ EN 15343:2007
+ Giới thiệu tiêu chuẩn EN 15343:2007.
+ Vai trò của tiêu chuẩn trong kinh tế tuần hoàn và quản lý nhựa tái chế.
+ Phạm vi áp dụng.
+ Thuật ngữ và định nghĩa.
+ Mối liên hệ giữa EN 15343 với các tiêu chuẩn EN 15347, ISO 14021, ISO 22095 và các yêu cầu về hàm lượng tái chế của thị trường quốc tế.
– PHƯƠNG PHÁP VÀ QUY TRÌNH KIỂM SOÁT NGUYÊN LIỆU
+ Phương pháp và quy trình áp dụng EN 15343.
+ Kiểm soát nguyên liệu đầu vào.
+ Phân loại và xác minh nguồn gốc nguyên liệu tái chế.
+ Tiêu chí lựa chọn nhà cung cấp.
+ Kiểm tra, tiếp nhận và lưu trữ nguyên liệu.
+ Thiết lập hồ sơ và chứng từ truy xuất nguồn gốc.
– KIỂM SOÁT QUÁ TRÌNH SẢN XUẤT TÁI CHẾ
+ Kiểm soát quá trình sản xuất tái chế.
+ Nhận diện các công đoạn ảnh hưởng đến chất lượng vật liệu tái chế.
+ Kiểm soát dòng nguyên liệu trong quá trình sản xuất.
+ Quản lý dữ liệu sản xuất.
+ Kiểm soát thay đổi và phòng ngừa sai lệch.
+ Thiết lập hồ sơ theo dõi quá trình.
– ĐẶC TÍNH NHỰA TÁI CHẾ VÀ TRUY XUẤT NGUỒN GỐC
+ Đặc tính của nhựa tái chế.
+ Các chỉ tiêu đánh giá chất lượng vật liệu tái chế.
+ Thiết lập hệ thống truy xuất nguồn gốc.
+ Quản lý mã lô và dòng nguyên liệu.
+ Theo dõi chuỗi hành trình của nguyên liệu tái chế.
+ Kiểm soát hồ sơ và bằng chứng truy xuất.
– ĐẢM BẢO CHẤT LƯỢNG VÀ XÁC ĐỊNH HÀM LƯỢNG NHỰA TÁI CHẾ
+ Thiết lập hệ thống đảm bảo chất lượng.
+ Kiểm soát dữ liệu và hồ sơ.
+ Phương pháp xác định hàm lượng nhựa tái chế.
+ Tính toán tỷ lệ hàm lượng tái chế trong sản phẩm.
+ Chuẩn bị hồ sơ đánh giá sự phù hợp.
+ Yêu cầu công bố và chứng minh hàm lượng tái chế.
– WORKSHOP THỰC HÀNH
+ Xây dựng sơ đồ truy xuất nguồn gốc nguyên liệu tái chế.
+ Thiết lập hệ thống kiểm soát nguyên liệu đầu vào.
+ Thực hành tính toán hàm lượng nhựa tái chế trong sản phẩm.
+ Xây dựng biểu mẫu quản lý và hồ sơ truy xuất.
+ Đánh giá sự phù hợp theo EN 15343.
+ Phân tích các tình huống thực tế và giải đáp khó khăn trong quá trình triển khai.',
        ],
        [
            'code' => 'KHC-26',
            'name' => 'CHIẾN LƯỢC VÒNG ĐỜI SẢN PHẨM KHÔNG PHẾ THẢI: TIÊU CHUẨN CRADLE TO CRADLE CERTIFIED',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Chị Tuyền',
            'audience' => 'Ban lãnh đạo và các cấp quản lý doanh nghiệp.
Cán bộ R&D, thiết kế sản phẩm, kỹ thuật, sản xuất và quản lý chất lượng (QA/QC).
Cán bộ phụ trách ESG, phát triển bền vững, môi trường và HSE/EHS.
Thành viên Ban ISO, Ban ESG và nhóm triển khai chứng nhận.
Doanh nghiệp sản xuất, thiết kế, phân phối sản phẩm và các đơn vị có nhu cầu đạt chứng nhận Cradle to Cradle Certified®.',
            'objectives' => 'Hiểu và áp dụng các yêu cầu của Cradle to Cradle Certified® Product Standard trong thiết kế và phát triển sản phẩm tuần hoàn.
Đánh giá sức khỏe vật liệu, tính tuần hoàn, tác động môi trường và trách nhiệm xã hội của sản phẩm theo tiêu chuẩn.
Xây dựng lộ trình cải tiến sản phẩm và chuẩn bị hồ sơ phục vụ đánh giá, chứng nhận.
Nâng cao năng lực phát triển sản phẩm bền vững, đáp ứng yêu cầu ESG và kinh tế tuần hoàn.',
            'content_summary' => '- Giới thiệu tiêu chuẩn
- Định nghĩa, thuật ngữ
- Yêu cầu sức khoẻ vật liệu
- Yêu cầu về tính tuần hoàn của sản phẩm
- Yêu cầu về bảo vệ khí hậu và không khí sạch
- Yêu cầu quản lý đất và nước
- Yêu cầu công bằng xã hội
- Bao bì cho sản phẩm được chứng nhận
- Yêu cầu về phúc lợi động vật
- Yêu cầu về sản phẩm nhãn hiệu riêng',
            'content_detail' => '– TỔNG QUAN VỀ CRADLE TO CRADLE CERTIFIED®
+ Giới thiệu Cradle to Cradle Certified® Product Standard.
+ Nguyên tắc thiết kế sản phẩm tuần hoàn.
+ Phạm vi áp dụng và cấu trúc của tiêu chuẩn.
+ Thuật ngữ và định nghĩa.
+ Hệ thống cấp độ chứng nhận (Bronze, Silver, Gold và Platinum).
+ Mối liên hệ giữa C2C Certified® với kinh tế tuần hoàn, ESG và các tiêu chuẩn phát triển bền vững khác.
– SỨC KHỎE VẬT LIỆU (MATERIAL HEALTH)
+ Yêu cầu về sức khỏe vật liệu.
+ Nhận diện và đánh giá thành phần hóa học của sản phẩm.
+ Quản lý các chất hạn chế và chất cần loại bỏ.
+ Đánh giá rủi ro đối với sức khỏe con người và môi trường.
+ Quản lý dữ liệu thành phần vật liệu.
+ Lập hồ sơ đánh giá vật liệu phục vụ chứng nhận.
– TÍNH TUẦN HOÀN CỦA SẢN PHẨM
+ Yêu cầu về tính tuần hoàn của sản phẩm.
+ Thiết kế phục vụ tái sử dụng, tái sản xuất và tái chế.
+ Lựa chọn vật liệu phù hợp với mô hình kinh tế tuần hoàn.
+ Đánh giá khả năng thu hồi và tái chế sản phẩm.
+ Chiến lược kéo dài vòng đời sản phẩm.
+ Thiết lập các chỉ số đánh giá tính tuần hoàn.
– BẢO VỆ KHÍ HẬU, QUẢN LÝ TÀI NGUYÊN ĐẤT VÀ NƯỚC
Phần A – Bảo vệ khí hậu và không khí sạch
+ Yêu cầu về bảo vệ khí hậu và không khí sạch.
+ Kiểm kê và giảm phát thải khí nhà kính.
+ Quản lý năng lượng và sử dụng năng lượng tái tạo.
+ Giảm phát thải trong quá trình sản xuất.
Phần B – Quản lý đất và nước
+ Yêu cầu quản lý đất và nước.
+ Sử dụng tài nguyên nước hiệu quả.
+ Bảo vệ nguồn nước và hệ sinh thái.
+ Quản lý chất lượng nước thải và đất.
+ Thực hành đánh giá các chỉ tiêu môi trường.
– CÔNG BẰNG XÃ HỘI VÀ CÁC YÊU CẦU BỔ SUNG
+ Yêu cầu về công bằng xã hội.
+ Quản lý trách nhiệm xã hội trong chuỗi cung ứng.
+ Quyền con người và điều kiện làm việc.
+ Yêu cầu đối với bao bì của sản phẩm được chứng nhận.
+ Yêu cầu về phúc lợi động vật (đối với các sản phẩm có nguồn gốc động vật).
+ Yêu cầu đối với sản phẩm nhãn hiệu riêng (Private Label Products).
+ Chuẩn bị hồ sơ chứng minh sự phù hợp.
– WORKSHOP THỰC HÀNH
+ Đánh giá mức độ đáp ứng của một sản phẩm theo Cradle to Cradle Certified®.
+ Phân tích thành phần vật liệu và khả năng tuần hoàn.
+ Xây dựng lộ trình cải tiến sản phẩm để đạt chứng nhận.
+ Thiết lập hồ sơ phục vụ đánh giá.
+ Phân tích các tình huống thực tế trong quá trình chứng nhận.
+ Thảo luận và giải đáp các khó khăn khi triển khai tại doanh nghiệp.',
        ],
        [
            'code' => 'KHC-27',
            'name' => 'KIỂM SOÁT VẬN HÀNH HIGG FEM: SỐ HÓA VÀ TỐI ƯU HÓA CHỈ SỐ TÁC ĐỘNG MÔI TRƯỜNG',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => 'Ban lãnh đạo và các cấp quản lý doanh nghiệp.
Cán bộ HSE/EHS, môi trường, ESG và phát triển bền vững.
Cán bộ kỹ thuật, sản xuất, bảo trì, năng lượng và quản lý dữ liệu.
Thành viên Ban Higg, Ban ISO và nhóm triển khai Higg FEM.
Doanh nghiệp sản xuất, đặc biệt trong ngành dệt may, da giày, may mặc và các đơn vị có nhu cầu triển khai hoặc xác minh Higg FEM.',
            'objectives' => 'Hiểu và áp dụng các yêu cầu của Higg Facility Environmental Module (Higg FEM) trong quản lý môi trường tại nhà máy.
Thiết lập hệ thống quản lý dữ liệu môi trường, xây dựng KPI và triển khai các module của Higg FEM.
Quản lý hiệu quả năng lượng, nước, chất thải, khí thải và hóa chất theo yêu cầu của Higg FEM.
Chuẩn bị hồ sơ, dữ liệu và sẵn sàng cho hoạt động Verification.',
            'content_summary' => '– KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
– THU THẬP DỮ LIỆU VÀ XÂY DỰNG HỆ THỐNG QUẢN LÝ
– TRIỂN KHAI CÁC MODULE HIGG FEM
– ĐÀO TẠO VÀ TRIỂN KHAI THỰC TẾ
– RÀ SOÁT VÀ HOÀN THIỆN HỆ THỐNG',
            'content_detail' => '– KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
• Thành lập Ban Higg FEM
• Xây dựng kế hoạch triển khai
• Phân công vai trò và trách nhiệm
• Đào tạo nhận thức Higg FEM
• Tổng quan về Sustainability và ESG
• Tổng quan cấu trúc Higg FEM
• Tổng quan Verification Process
• Hướng dẫn sử dụng nền tảng Higg FEM
– THU THẬP DỮ LIỆU VÀ XÂY DỰNG HỆ THỐNG QUẢN LÝ
• Thu thập dữ liệu môi trường
• Xây dựng hệ thống quản lý dữ liệu
• Kiểm soát tài liệu và hồ sơ môi trường
• Thiết lập KPI môi trường
• Kiểm soát dữ liệu tiêu thụ năng lượng
• Kiểm soát dữ liệu nước
• Kiểm soát dữ liệu chất thải
• Kiểm soát dữ liệu khí thải
• Kiểm soát hóa chất
– TRIỂN KHAI CÁC MODULE HIGG FEM
Environmental Management System
• Chính sách môi trường
• Mục tiêu môi trường
• Legal Compliance
• Internal Audit
• Management Review
Energy and GHG
• Energy Management
• GHG Inventory
• Energy KPI
• Energy Saving Program
Water Management
• Water Consumption
• Water Balance
• Water Reduction Program
• Wastewater Management
Waste Management
• Waste Inventory
• Waste Segregation
• Waste Tracking
• Hazardous Waste Management
Air Emission
• Air Emission Inventory
• Air Pollution Control
• Monitoring and Reporting
Chemical Management
• Chemical Inventory
• SDS Management
• Chemical Risk Assessment
• Spill Response
– ĐÀO TẠO VÀ TRIỂN KHAI THỰC TẾ
• Đào tạo đội ngũ thực hiện Higg FEM
• Đào tạo quản lý dữ liệu môi trường
• Đào tạo kiểm soát hồ sơ
• Coaching hiện trường
• Hướng dẫn nhập dữ liệu Higg FEM
• Hướng dẫn chuẩn bị Verification
• Theo dõi hiệu quả áp dụng thực tế
– RÀ SOÁT VÀ HOÀN THIỆN HỆ THỐNG
• Rà soát dữ liệu Higg FEM
• Kiểm tra hồ sơ môi trường
• Kiểm tra hiện trường
• Đánh giá mức độ đáp ứng Higg FEM
• Theo dõi hành động cải tiến
• Hoàn thiện hồ sơ Verification
• Chuẩn bị tiếp đoàn Verification',
        ],
        [
            'code' => 'KHC-28',
            'name' => 'QUẢN LÝ CHẤT LƯỢNG NHÀ CUNG CẤP (SQE): PHƯƠNG PHÁP LUẬN THẨM ĐỊNH VÀ MÔ HÌNH HÓA HIỆU SUẤT',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => 'Ban lãnh đạo và các cấp quản lý doanh nghiệp.
Cán bộ thu mua, chuỗi cung ứng, chất lượng (QA/QC), sản xuất và logistics.
Cán bộ ESG, HSE/EHS, môi trường và quản lý rủi ro.
Thành viên Ban ISO và nhóm phụ trách đánh giá nhà cung cấp.
Doanh nghiệp có nhu cầu xây dựng và nâng cao hệ thống quản lý, đánh giá và phát triển nhà cung cấp.',
            'objectives' => 'Hiểu và áp dụng các phương pháp đánh giá, phân loại và quản lý nhà cung cấp theo thông lệ quốc tế.
Xây dựng tiêu chí đánh giá, KPI, Scorecard và hệ thống quản lý nhà cung cấp.
Thực hiện đánh giá hiện trường, phân tích rủi ro và triển khai CAPA nhằm nâng cao hiệu quả chuỗi cung ứng.
Nâng cao năng lực quản lý nhà cung cấp, đáp ứng yêu cầu về chất lượng, ESG và phát triển bền vững.',
            'content_summary' => '– TỔNG QUAN VỀ ĐÁNH GIÁ NHÀ CUNG CẤP
+ TỔNG QUAN QUẢN LÝ NHÀ CUNG CẤP
+ HỆ THỐNG ĐÁNH GIÁ NHÀ CUNG CẤP
+ ĐÁNH GIÁ HỆ THỐNG NHÀ CUNG CẤP
+ KỸ NĂNG ĐÁNH GIÁ NHÀ CUNG CẤP
– TRIỂN KHAI ĐÁNH GIÁ VÀ CẢI TIẾN NHÀ CUNG CẤP
+ ĐÁNH GIÁ HIỆN TRƯỜNG NHÀ CUNG CẤP
+ PHÂN TÍCH RỦI RO NHÀ CUNG CẤP
+ CAPA VÀ PHÁT TRIỂN NHÀ CUNG CẤP
+ XÂY DỰNG HỆ THỐNG QUẢN LÝ NHÀ CUNG CẤP
+ TỔNG KẾT VÀ ĐÁNH GIÁ CUỐI KHÓA',
            'content_detail' => '– TỔNG QUAN VỀ ĐÁNH GIÁ NHÀ CUNG CẤP
+ TỔNG QUAN QUẢN LÝ NHÀ CUNG CẤP
• Vai trò của nhà cung cấp trong chuỗi cung ứng
• Rủi ro trong chuỗi cung ứng
• Supplier Lifecycle Management
• Tiêu chí lựa chọn nhà cung cấp
• Yêu cầu khách hàng và tiêu chuẩn quốc tế
• Vai trò của Supplier Assessment
+ HỆ THỐNG ĐÁNH GIÁ NHÀ CUNG CẤP
Nội dung đào tạo
• Supplier Evaluation Process
• Supplier Approval Process
• Supplier Risk Assessment
• Supplier Classification
• Supplier KPI
• Approved Vendor List
Nội dung thực hành
• Xây dựng tiêu chí đánh giá nhà cung cấp
• Thiết lập bảng chấm điểm
• Phân loại nhà cung cấp
+ ĐÁNH GIÁ HỆ THỐNG NHÀ CUNG CẤP
• Đánh giá hệ thống quản lý chất lượng
• Đánh giá năng lực sản xuất
• Đánh giá kiểm soát chất lượng
• Đánh giá truy xuất nguồn gốc
• Đánh giá năng lực giao hàng
• Đánh giá quản lý kho
• Đánh giá quản lý thay đổi
• Đánh giá quản lý rủi ro
+ KỸ NĂNG ĐÁNH GIÁ NHÀ CUNG CẤP
Nội dung đào tạo
• Kỹ năng phỏng vấn
• Kỹ năng quan sát hiện trường
• Kỹ năng đặt câu hỏi
• Kỹ năng thu thập bằng chứng
• Kỹ năng viết báo cáo đánh giá
• Kỹ năng giao tiếp với nhà cung cấp
Nội dung thực hành
• Thực hành phỏng vấn
• Thực hành đánh giá tình huống
• Workshop đánh giá nhà cung cấp
– TRIỂN KHAI ĐÁNH GIÁ VÀ CẢI TIẾN NHÀ CUNG CẤP
+ ĐÁNH GIÁ HIỆN TRƯỜNG NHÀ CUNG CẤP
• Đánh giá hiện trường sản xuất
• Đánh giá kiểm soát chất lượng
• Đánh giá thiết bị và bảo trì
• Đánh giá nhân sự và đào tạo
• Đánh giá quản lý nguyên vật liệu
• Đánh giá an toàn và môi trường
• Đánh giá kiểm soát sản phẩm không phù hợp
+ PHÂN TÍCH RỦI RO NHÀ CUNG CẤP
Nội dung đào tạo
• Supplier Risk Matrix
• Quality Risk
• Delivery Risk
• Capacity Risk
• Compliance Risk
• ESG Risk
• Business Continuity Risk
Nội dung thực hành
• Đánh giá rủi ro nhà cung cấp
• Thiết lập Supplier Risk Register
• Thảo luận tình huống thực tế
+ CAPA VÀ PHÁT TRIỂN NHÀ CUNG CẤP
• Supplier CAPA Management
• Root Cause Analysis
• Theo dõi hành động cải tiến
• Supplier Development Program
• Supplier Improvement Plan
• Supplier Performance Monitoring
+ XÂY DỰNG HỆ THỐNG QUẢN LÝ NHÀ CUNG CẤP
• Supplier Management Procedure
• Supplier KPI Dashboard
• Supplier Audit Checklist
• Supplier Scorecard
• Supplier Monitoring System
• Tích hợp với ISO 9001, ISO 14001 và ISO 45001
+ TỔNG KẾT VÀ ĐÁNH GIÁ CUỐI KHÓA
• Ôn tập nội dung đào tạo
• Kiểm tra cuối khóa
• Đánh giá năng lực học viên
• Tổng kết chương trình đào tạo',
        ],
        [
            'code' => 'KHC-29',
            'name' => 'QUẢN TRỊ CHIẾN LƯỢC TRÁCH NHIỆM XÃ HỘI TRONG TRỤ CỘT ESG: NĂNG LỰC THỰC THI VÀ KIỂM TOÁN NỘI BỘ THEO ISO SA 8000:2026',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => 'Ban lãnh đạo và các cấp quản lý doanh nghiệp.
Cán bộ nhân sự (HR), HSE/EHS, tuân thủ (Compliance) và phát triển bền vững.
Cán bộ quản lý sản xuất, chất lượng và quan hệ lao động.
Thành viên Ban ISO, Ban SA8000 và nhóm triển khai chứng nhận.
Doanh nghiệp có nhu cầu xây dựng hệ thống trách nhiệm xã hội và đạt chứng nhận SA8000.',
            'objectives' => 'Hiểu và áp dụng các yêu cầu của SA8000 trong xây dựng hệ thống quản lý trách nhiệm xã hội.
Xây dựng chính sách, quy trình và triển khai các yêu cầu về lao động, nhân quyền và an toàn tại doanh nghiệp.
Thiết lập hệ thống đánh giá nội bộ, hành động khắc phục và cải tiến liên tục.
Chuẩn bị hồ sơ và sẵn sàng cho hoạt động đánh giá, chứng nhận SA8000.',
            'content_summary' => '- KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
- ĐÁNH GIÁ HIỆN TRẠNG VÀ PHÂN TÍCH KHOẢNG CÁCH
- XÂY DỰNG HỆ THỐNG QUẢN LÝ SA8000
- TRIỂN KHAI VÀ ĐÀO TẠO CHUYÊN SÂU
- ĐÁNH GIÁ NỘI BỘ VÀ HÀNH ĐỘNG KHẮC PHỤC
- PRE-ASSESSMENT VÀ CHUẨN BỊ CHỨNG NHẬN',
            'content_detail' => '- KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
• Họp khởi động dự án. 
• Thành lập Ban SA8000. 
• Thành lập Nhóm thực hiện trách nhiệm xã hội. 
• Xây dựng kế hoạch triển khai. 
• Đào tạo nhận thức SA8000 cho Ban lãnh đạo. 
• Đào tạo nhận thức SA8000 cho cán bộ quản lý. 
• Giới thiệu các Công ước của Tổ chức Lao động Quốc tế (ILO). 
• Giới thiệu Tuyên ngôn Quốc tế về Nhân quyền. 
• Giới thiệu các nguyên tắc trách nhiệm xã hội và nhân quyền. 
• Giới thiệu cấu trúc hệ thống quản lý SA8000. 
- ĐÁNH GIÁ HIỆN TRẠNG VÀ PHÂN TÍCH KHOẢNG CÁCH
Đánh giá mức độ phù hợp đối với 09 nội dung cốt lõi của SA8000:
1. Lao động trẻ em
• Chính sách lao động trẻ em. 
• Hồ sơ xác minh độ tuổi. 
• Biện pháp phòng ngừa. 
2. Lao động cưỡng bức
• Tự nguyện làm việc. 
• Giấy tờ tùy thân. 
• Tiền đặt cọc. 
• Hạn chế đi lại. 
3. Sức khỏe và an toàn
• Đánh giá rủi ro. 
• Tai nạn lao động. 
• PPE. 
• Hóa chất. 
• PCCC. 
4. Tự do hiệp hội
• Công đoàn. 
• Đại diện người lao động. 
• Đối thoại lao động. 
5. Phân biệt đối xử
• Tuyển dụng. 
• Thăng tiến. 
• Đào tạo. 
• Chế độ phúc lợi. 
6. Kỷ luật lao động
• Nội quy. 
• Biện pháp xử lý. 
• Hồ sơ kỷ luật. 
7. Giờ làm việc
• Thời giờ làm việc. 
• Làm thêm giờ. 
• Nghỉ ngơi. 
8. Tiền lương
• Lương cơ bản. 
• Lương làm thêm. 
• Phúc lợi. 
9. Hệ thống quản lý
• Chính sách. 
• Mục tiêu. 
• Đánh giá nội bộ. 
• Xem xét lãnh đạo. 
- XÂY DỰNG HỆ THỐNG QUẢN LÝ SA8000
Xây dựng Chính sách SA8000
Bao gồm cam kết:
• Tuân thủ pháp luật. 
• Tôn trọng nhân quyền. 
• Không lao động trẻ em. 
• Không lao động cưỡng bức. 
• Không phân biệt đối xử. 
• Không quấy rối. 
• Đảm bảo an toàn lao động. 
• Cải tiến liên tục. 
Xây dựng hệ thống quy trình
• Quy trình tuyển dụng. 
• Quy trình xác minh độ tuổi. 
• Quy trình quản lý lao động trẻ em. 
• Quy trình quản lý lao động cưỡng bức. 
• Quy trình khiếu nại. 
• Quy trình đối thoại lao động. 
• Quy trình đánh giá nội bộ. 
• Quy trình hành động khắc phục. 
• Quy trình xem xét của lãnh đạo. 
• Quy trình quản lý nhà cung cấp. 
- TRIỂN KHAI VÀ ĐÀO TẠO CHUYÊN SÂU
Mô-đun 1: Nhân quyền và lao động
• Lao động trẻ em. 
• Lao động cưỡng bức. 
• Nhân quyền. 
• Chống phân biệt đối xử. 
Mô-đun 2: Quan hệ lao động
• Tự do hiệp hội. 
• Đối thoại lao động. 
• Khiếu nại. 
• Cơ chế phản ánh. 
Mô-đun 3: Điều kiện làm việc
• An toàn sức khỏe nghề nghiệp. 
• Hóa chất. 
• Máy móc thiết bị. 
• Ứng phó khẩn cấp. 
Mô-đun 4: Quản lý hệ thống
• Mục tiêu SA8000. 
• KPI trách nhiệm xã hội. 
• Đánh giá nội bộ. 
• Xem xét lãnh đạo. 
- ĐÁNH GIÁ NỘI BỘ VÀ HÀNH ĐỘNG KHẮC PHỤC
• Đào tạo đánh giá viên nội bộ. 
• Thực hiện đánh giá nội bộ. 
• Phỏng vấn người lao động. 
• Kiểm tra hồ sơ. 
• Kiểm tra hiện trường. 
• Xác định điểm chưa phù hợp. 
• Phân tích nguyên nhân. 
• Thực hiện hành động khắc phục. 
- PRE-ASSESSMENT VÀ CHUẨN BỊ CHỨNG NHẬN
• Đánh giá thử toàn bộ hệ thống. 
• Kiểm tra hồ sơ. 
• Kiểm tra hiện trường. 
• Kiểm tra việc thực hiện hành động khắc phục. 
• Đánh giá mức độ sẵn sàng. 
• Hướng dẫn tiếp đoàn đánh giá. 
• Hướng dẫn phỏng vấn người lao động. 
• Hướng dẫn Ban lãnh đạo tham gia đánh giá.',
        ],
        [
            'code' => 'KHC-30',
            'name' => 'ĐẠO ĐỨC VÀ TRÁCH NHIỆM XÃ HỘI: QUY TRÌNH THỰC THI VÀ PHÒNG VỆ RỦI RO THEO KHUNG SMETA',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Thầy Nhã',
            'audience' => 'Ban lãnh đạo và các cấp quản lý doanh nghiệp.
Cán bộ nhân sự (HR), HSE/EHS, Compliance, môi trường và phát triển bền vững.
Cán bộ sản xuất, chất lượng, mua hàng, kho, bảo trì và quản lý nhà cung cấp.
Thành viên Ban SMETA, Ban ISO và nhóm triển khai trách nhiệm xã hội.
Doanh nghiệp có nhu cầu xây dựng hệ thống và đáp ứng yêu cầu đánh giá SMETA của khách hàng và chuỗi cung ứng toàn cầu.',
            'objectives' => 'Hiểu và áp dụng các yêu cầu của SMETA 7.0 trong xây dựng hệ thống quản lý trách nhiệm xã hội.
Xây dựng chính sách, quy trình và hồ sơ đáp ứng các yêu cầu về lao động, an toàn, môi trường và đạo đức kinh doanh.
Thực hiện đánh giá nội bộ, khắc phục điểm không phù hợp và cải tiến hệ thống.
Chuẩn bị đầy đủ hồ sơ, hiện trường và nhân sự cho hoạt động đánh giá SMETA.',
            'content_summary' => '- KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
- ĐÁNH GIÁ THỰC TRẠNG VÀ PHÂN TÍCH KHOẢNG CÁCH
- XÂY DỰNG VÀ HOÀN THIỆN HỆ THỐNG TÀI LIỆU
- ĐÀO TẠO CHUYÊN SÂU VÀ HƯỚNG DẪN TRIỂN KHAI
- ĐÁNH GIÁ NỘI BỘ THEO SMETA VÀ HƯỚNG DẪN KHẮC PHỤC
- PRE-ASSESSMENT VÀ CHUẨN BỊ ĐÁNH GIÁ CHÍNH THỨC',
            'content_detail' => '- KHỞI ĐỘNG DỰ ÁN VÀ ĐÀO TẠO NHẬN THỨC
• Tổ chức cuộc họp khởi động dự án với Ban lãnh đạo và các bộ phận liên quan.
• Thống nhất mục tiêu, phạm vi, lộ trình và phương pháp triển khai chương trình SMETA 7.0.
• Thành lập Ban triển khai trách nhiệm xã hội hoặc nhóm phụ trách SMETA tại doanh nghiệp.
• Phân công vai trò, trách nhiệm và đầu mối phối hợp cho từng phòng ban.
• Đào tạo nhận thức tổng quan về SMETA 7.0.
• Giới thiệu cấu trúc đánh giá SMETA, bao gồm các nội dung về lao động, sức khỏe an toàn, môi trường và đạo đức kinh doanh.
• Giới thiệu yêu cầu của Sedex, yêu cầu khách hàng và vai trò của SMETA trong chuỗi cung ứng.
• Đào tạo nhận thức về các vấn đề trọng yếu: lao động cưỡng bức, lao động trẻ em, phân biệt đối xử, kỷ luật lao động, giờ làm việc, tiền lương, phúc lợi, khiếu nại và đối thoại người lao động.
• Hướng dẫn cách doanh nghiệp cần chuẩn bị dữ liệu, hồ sơ và nhân sự cho các giai đoạn tiếp theo.
- ĐÁNH GIÁ THỰC TRẠNG VÀ PHÂN TÍCH KHOẢNG CÁCH
• Rà soát hiện trạng hệ thống quản lý trách nhiệm xã hội của doanh nghiệp.
• Đánh giá hồ sơ pháp lý liên quan đến lao động, an toàn vệ sinh lao động, phòng cháy chữa cháy, môi trường và điều kiện làm việc.
• Rà soát hồ sơ nhân sự, hợp đồng lao động, hồ sơ người lao động, hồ sơ lao động thời vụ, lao động thử việc, lao động chưa thành niên nếu có.
• Rà soát hệ thống chấm công, bảng lương, phiếu lương, hồ sơ làm thêm giờ, nghỉ phép, nghỉ lễ, bảo hiểm và phúc lợi.
• Đánh giá việc kiểm soát giờ làm việc, thời gian nghỉ ngơi, làm thêm giờ và sự phù hợp với yêu cầu pháp luật cũng như yêu cầu SMETA.
• Kiểm tra hiện trạng an toàn sức khỏe nghề nghiệp tại nhà xưởng, kho, khu vực sản xuất, khu vực hóa chất, khu vực máy móc thiết bị, nhà ăn, nhà vệ sinh, ký túc xá nếu có.
• Rà soát hồ sơ đào tạo an toàn, khám sức khỏe, quan trắc môi trường lao động, đánh giá rủi ro, kiểm định thiết bị, bảo trì máy móc và ứng phó khẩn cấp.
• Đánh giá cơ chế khiếu nại, đối thoại người lao động, hoạt động công đoàn hoặc đại diện người lao động.
• Rà soát yêu cầu về môi trường, quản lý chất thải, nước thải, khí thải, hóa chất và các giấy phép liên quan.
• Đánh giá hệ thống kiểm soát đạo đức kinh doanh, chống tham nhũng, chống hối lộ, bảo mật thông tin và cơ chế tố giác vi phạm.
• Xác định các điểm phù hợp, điểm chưa phù hợp, rủi ro trọng yếu và nội dung cần cải thiện trước đánh giá chính thức.
- XÂY DỰNG VÀ HOÀN THIỆN HỆ THỐNG TÀI LIỆU
• Xây dựng hoặc rà soát chính sách trách nhiệm xã hội.
• Xây dựng chính sách lao động và nhân quyền.
• Xây dựng chính sách không sử dụng lao động trẻ em.
• Xây dựng chính sách không sử dụng lao động cưỡng bức.
• Xây dựng chính sách chống phân biệt đối xử.
• Xây dựng chính sách chống quấy rối, lạm dụng và đối xử vô nhân đạo.
• Xây dựng chính sách tự do hiệp hội và thương lượng tập thể.
• Xây dựng chính sách tiền lương, phúc lợi và thời giờ làm việc.
• Xây dựng chính sách an toàn sức khỏe nghề nghiệp.
• Xây dựng chính sách bảo vệ môi trường.
• Xây dựng chính sách đạo đức kinh doanh.
• Xây dựng quy trình tuyển dụng và tiếp nhận lao động.
• Xây dựng quy trình quản lý hồ sơ nhân sự.
• Xây dựng quy trình kiểm soát lao động chưa thành niên nếu có.
• Xây dựng quy trình quản lý thời giờ làm việc và làm thêm giờ.
• Xây dựng quy trình kiểm soát tiền lương và phúc lợi.
• Xây dựng quy trình tiếp nhận và xử lý khiếu nại.
• Xây dựng quy trình đối thoại người lao động.
• Xây dựng quy trình quản lý kỷ luật lao động.
• Xây dựng quy trình đánh giá rủi ro an toàn sức khỏe nghề nghiệp.
• Xây dựng quy trình quản lý nhà thầu.
• Xây dựng quy trình quản lý nhà cung cấp liên quan đến trách nhiệm xã hội.
• Xây dựng quy trình ứng phó khẩn cấp.
• Xây dựng quy trình điều tra tai nạn lao động và hành động khắc phục.
• Xây dựng bộ biểu mẫu hồ sơ phục vụ đánh giá SMETA.
- ĐÀO TẠO CHUYÊN SÂU VÀ HƯỚNG DẪN TRIỂN KHAI
• Đào tạo chuyên sâu cho phòng nhân sự về hồ sơ lao động, hợp đồng lao động, thử việc, nghỉ việc, kỷ luật lao động, lao động chưa thành niên, bảo hiểm, phép năm, làm thêm giờ và phúc lợi.
• Đào tạo chuyên sâu cho bộ phận tính lương về kiểm soát bảng công, bảng lương, phiếu lương, làm thêm giờ, khấu trừ lương, phụ cấp, thưởng và các khoản phúc lợi bắt buộc.
• Đào tạo cho bộ phận sản xuất và quản lý chuyền về kiểm soát thời giờ làm việc, làm thêm giờ tự nguyện, nghỉ giữa ca, phân công ca kíp và cách phối hợp khi phỏng vấn đánh giá.
• Đào tạo cho bộ phận an toàn sức khỏe nghề nghiệp về đánh giá rủi ro, kiểm soát máy móc thiết bị, hóa chất, phương tiện bảo vệ cá nhân, phòng cháy chữa cháy, sơ cấp cứu, tai nạn lao động và ứng phó khẩn cấp.
• Đào tạo cho bộ phận mua hàng, kho và nhà thầu về trách nhiệm xã hội trong chuỗi cung ứng, kiểm soát nhà cung cấp, hồ sơ nhà thầu và yêu cầu tuân thủ khi làm việc tại nhà máy.
• Đào tạo cho công đoàn hoặc đại diện người lao động về đối thoại, khiếu nại, bảo vệ quyền lợi người lao động và cơ chế phản ánh.
• Hướng dẫn các phòng ban áp dụng biểu mẫu, cập nhật hồ sơ và chuẩn bị bằng chứng.
• Hướng dẫn cách sắp xếp hồ sơ đánh giá theo từng nhóm nội dung SMETA.
• Hướng dẫn phương pháp trả lời phỏng vấn cho cán bộ phụ trách và người lao động trên tinh thần trung thực, rõ ràng, đúng thực tế.
- ĐÁNH GIÁ NỘI BỘ THEO SMETA VÀ HƯỚNG DẪN KHẮC PHỤC
• Thực hiện đánh giá nội bộ theo phương pháp tiếp cận của SMETA.
• Rà soát hồ sơ nhân sự, tiền lương, thời giờ làm việc, an toàn sức khỏe nghề nghiệp, môi trường và đạo đức kinh doanh.
• Kiểm tra thực tế hiện trường nhà xưởng, khu vực sản xuất, kho, nhà ăn, nhà vệ sinh, khu vực hóa chất, phòng y tế, khu vực phòng cháy chữa cháy và ký túc xá nếu có.
• Phỏng vấn đại diện Ban lãnh đạo, nhân sự, sản xuất, an toàn, bảo trì, công đoàn và người lao động.
• Đối chiếu giữa hồ sơ, hiện trường và thông tin phỏng vấn để xác định mức độ phù hợp.
• Lập danh sách điểm chưa phù hợp, điểm quan sát và khuyến nghị cải tiến.
• Hướng dẫn doanh nghiệp phân tích nguyên nhân và xây dựng kế hoạch hành động khắc phục.
• Theo dõi việc cập nhật hồ sơ, hình ảnh, bằng chứng và tài liệu chứng minh hành động khắc phục.
- PRE-ASSESSMENT VÀ CHUẨN BỊ ĐÁNH GIÁ CHÍNH THỨC
• Rà soát lần cuối toàn bộ hệ thống trước đánh giá chính thức.
• Kiểm tra mức độ hoàn thiện của hồ sơ pháp lý, hồ sơ nhân sự, hồ sơ tiền lương, hồ sơ an toàn, hồ sơ môi trường và hồ sơ đạo đức kinh doanh.
• Kiểm tra việc sắp xếp hồ sơ theo danh mục phục vụ đoàn đánh giá.
• Rà soát hiện trường và hướng dẫn khắc phục các điểm còn tồn tại.
• Hướng dẫn Ban lãnh đạo và các bộ phận cách tiếp đoàn đánh giá.
• Hướng dẫn đầu mối phụ trách cách cung cấp hồ sơ, trả lời câu hỏi và giải trình bằng chứng.
• Hướng dẫn người lao động tham gia phỏng vấn theo nguyên tắc trung thực, tự nhiên và đúng thực tế làm việc.
• Rà soát kế hoạch hành động khắc phục cuối cùng.
• Tổng kết mức độ sẵn sàng của doanh nghiệp trước đánh giá chính thức.',
        ],
        [
            'code' => 'KHC-31',
            'name' => 'TRÁCH NHIỆM XÃ HỘI TRONG CHUỖI CUNG ỨNG: PHƯƠNG PHÁP LUẬN VÀ THỰC THI TIÊU CHUẨN THEO BSCI',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'Chị Tuyền',
            'audience' => 'Ban lãnh đạo và các cấp quản lý doanh nghiệp.
Cán bộ nhân sự (HR), HSE/EHS, Compliance, môi trường và phát triển bền vững.
Cán bộ sản xuất, chất lượng, mua hàng và quản lý chuỗi cung ứng.
Thành viên Ban BSCI, Ban ISO và nhóm triển khai trách nhiệm xã hội.
Doanh nghiệp có nhu cầu xây dựng hệ thống và đáp ứng yêu cầu đánh giá amfori BSCI của khách hàng và thị trường quốc tế.',
            'objectives' => 'Hiểu và áp dụng các yêu cầu của amfori BSCI trong xây dựng hệ thống quản lý trách nhiệm xã hội.
Thiết lập chính sách, quy trình và hồ sơ đáp ứng các yêu cầu về lao động, an toàn và điều kiện làm việc.
Thực hiện đánh giá nội bộ, xây dựng kế hoạch khắc phục (CAP) và cải tiến hệ thống.
Chuẩn bị đầy đủ hồ sơ, hiện trường và nhân sự cho hoạt động đánh giá BSCI.',
            'content_summary' => '- Quyền tự do lập hiệp hội và quyền đàm phán tập thể
- Không phân biệt đối xữ
- Trả công công bằng
- Giờ công làm việc xứng đáng
- Sức khỏe và an toàn lao động
- Không sữ dụng lao động trẻ em
- Bảo vệ đặc biệt đối với lao động trẻ tuổi
- Không cung cấp việc làm tạm thời
- Không sữ dụng lao động tạm thời
- Không sữ dụng lao động lệ thuộc',
            'content_detail' => '– TỔNG QUAN VỀ AMFORI BSCI
+ Giới thiệu amfori BSCI.
+ Phạm vi áp dụng và đối tượng đánh giá.
+ Nguyên tắc hoạt động của BSCI.
+ Mối liên hệ giữa BSCI với SA8000, SMETA, WRAP, ISO 26000 và các chuẩn mực lao động quốc tế.
+ Quy trình đánh giá BSCI và các yêu cầu về hồ sơ, bằng chứng.
- QUYỀN CỦA NGƯỜI LAO ĐỘNG VÀ ĐIỀU KIỆN LÀM VIỆC
+ Quyền tự do lập hiệp hội và quyền thương lượng tập thể.
+ Không phân biệt đối xử.
+ Không quấy rối, lạm dụng và đối xử vô nhân đạo.
+ Trả công công bằng và đúng quy định.
+ Giờ làm việc hợp lý.
+ Hợp đồng lao động và quyền lợi của người lao động.
+ Thiết lập cơ chế tiếp nhận và giải quyết khiếu nại.
– LAO ĐỘNG TRẺ EM VÀ CÁC HÌNH THỨC LAO ĐỘNG KHÔNG ĐƯỢC CHẤP NHẬN
+ Không sử dụng lao động trẻ em.
+ Bảo vệ đặc biệt đối với lao động trẻ tuổi.
+ Không sử dụng lao động cưỡng bức, lao động lệ thuộc hoặc lao động bắt buộc.
+ Không sử dụng lao động bấp bênh (No Precarious Employment).
+ Tuyển dụng và sử dụng lao động theo đúng quy định pháp luật.
+ Thực hành đánh giá rủi ro về lao động trong doanh nghiệp.
– SỨC KHỎE, AN TOÀN VÀ QUẢN LÝ NƠI LÀM VIỆC
+ Các yêu cầu về sức khỏe và an toàn lao động.
+ Nhận diện mối nguy và đánh giá rủi ro.
+ Kiểm soát hóa chất, máy móc và thiết bị.
+ Phòng cháy chữa cháy và ứng phó khẩn cấp.
+ Điều kiện vệ sinh, ký túc xá và phúc lợi người lao động.
+ Thiết lập hệ thống quản lý an toàn và sức khỏe nghề nghiệp.
– HỆ THỐNG QUẢN LÝ VÀ CHUẨN BỊ ĐÁNH GIÁ BSCI
+ Thiết lập hệ thống quản lý trách nhiệm xã hội.
+ Quản lý hồ sơ và bằng chứng tuân thủ.
+ Đánh giá nội bộ theo yêu cầu của BSCI.
+ Khắc phục điểm không phù hợp và cải tiến liên tục.
+ Chuẩn bị cho hoạt động đánh giá của bên thứ ba.
+ Duy trì sự tuân thủ trong chuỗi cung ứng.
– WORKSHOP THỰC HÀNH
+ Đánh giá mức độ tuân thủ BSCI của doanh nghiệp.
+ Thực hành kiểm tra hồ sơ nhân sự và hồ sơ tiền lương.
+ Đánh giá điều kiện làm việc tại nhà máy.
+ Nhận diện các điểm không phù hợp thường gặp trong đánh giá BSCI.
+ Xây dựng kế hoạch hành động khắc phục (Corrective Action Plan – CAP).
+ Thảo luận các tình huống thực tế và giải đáp khó khăn trong quá trình triển khai.',
        ],
        [
            'code' => 'KHC-32',
            'name' => 'CÔNG TRÌNH XANH: KIẾN TRÚC HIỆU SUẤT VÀ CHỨNG THỰC ĐẦU TƯ THEO TIÊU CHUẨN LEED',
            'duration' => '2 ngày',
            'fee' => 1860000.0,
            'instructor' => 'HƯƠNG',
            'audience' => 'Ban lãnh đạo và các cấp quản lý doanh nghiệp.
Chủ đầu tư, ban quản lý dự án và đơn vị phát triển bất động sản.
Kiến trúc sư, kỹ sư xây dựng, MEP, tư vấn thiết kế và giám sát.
Cán bộ HSE/EHS, môi trường, ESG, quản lý năng lượng và quản lý vận hành tòa nhà.
Doanh nghiệp có nhu cầu xây dựng, vận hành hoặc đạt chứng nhận LEED cho công trình.',
            'objectives' => 'Hiểu và áp dụng các yêu cầu của LEED trong thiết kế, xây dựng và vận hành công trình xanh.
Xác định các hạng mục, điểm số và giải pháp đáp ứng tiêu chí đánh giá LEED.
Chuẩn bị hồ sơ, minh chứng và quy trình đăng ký chứng nhận LEED.
Nâng cao năng lực phát triển công trình xanh, tiết kiệm năng lượng và đáp ứng mục tiêu ESG, Net Zero.',
            'content_summary' => '- Giới thiệu tiêu chuẩn.
- Thuật ngữ và định nghĩa.
- Hạng mục 1: Tiết kiệm năng lượng.
- Hạng mục 2: Hiệu quả sử dụng nước.
- Hạng mục 3: Vật liệu và tài nguyên.
- Hạng mục 4: Tiếp cận môi trường.
- Hạng mục 5: Chất lượng không khí trong nhà.
- Hạng mục 6: Tiếp cận giao thông.
- Hạng mục 7: Sức khỏe và phúc lợi.
- Hạng mục 8: Quản lý môi trường.',
            'content_detail' => '– TỔNG QUAN VỀ LEED
+ Giới thiệu hệ thống đánh giá LEED.
+ Lịch sử phát triển và phạm vi áp dụng.
+ Các cấp độ chứng nhận (Certified, Silver, Gold và Platinum).
+ Thuật ngữ và định nghĩa.
+ Cấu trúc hệ thống chấm điểm LEED.
+ Mối liên hệ giữa LEED với Net Zero, ESG và các hệ thống công trình xanh khác (LOTUS, BREEAM, EDGE...).
– TIẾT KIỆM NĂNG LƯỢNG VÀ HIỆU QUẢ SỬ DỤNG NƯỚC
Phần A – Tiết kiệm năng lượng
+ Hạng mục 1: Tiết kiệm năng lượng.
+ Thiết kế công trình hiệu quả năng lượng.
+ Sử dụng năng lượng tái tạo.
+ Giảm phát thải khí nhà kính trong vận hành công trình.
+ Đánh giá hiệu quả năng lượng.
Phần B – Hiệu quả sử dụng nước
+ Hạng mục 2: Hiệu quả sử dụng nước.
+ Giảm tiêu thụ nước trong và ngoài công trình.
+ Tái sử dụng và tái chế nước.
+ Quản lý nước mưa.
+ Theo dõi và kiểm soát tiêu thụ nước.
– VẬT LIỆU, TÀI NGUYÊN VÀ TIẾP CẬN MÔI TRƯỜNG
Phần A – Vật liệu và tài nguyên
+ Hạng mục 3: Vật liệu và tài nguyên.
+ Lựa chọn vật liệu bền vững.
+ Sử dụng vật liệu tái chế và vật liệu có chứng nhận.
+ Quản lý chất thải xây dựng.
+ Đánh giá vòng đời vật liệu.
Phần B – Tiếp cận môi trường
+ Hạng mục 4: Tiếp cận môi trường.
+ Lựa chọn vị trí xây dựng bền vững.
+ Giảm tác động đến hệ sinh thái.
+ Quản lý cảnh quan và đa dạng sinh học.
+ Kiểm soát hiệu ứng đảo nhiệt và ô nhiễm ánh sáng.
– CHẤT LƯỢNG MÔI TRƯỜNG TRONG NHÀ VÀ GIAO THÔNG BỀN VỮNG
Phần A – Chất lượng không khí trong nhà
+ Hạng mục 5: Chất lượng không khí trong nhà.
+ Kiểm soát thông gió và chất lượng không khí.
+ Kiểm soát tiếng ồn, ánh sáng và tiện nghi nhiệt.
+ Lựa chọn vật liệu phát thải thấp.
+ Thiết kế không gian nâng cao sức khỏe và phúc lợi người sử dụng.
+ Phần B – Tiếp cận giao thông
+ Hạng mục 6: Tiếp cận giao thông.
+ Quy hoạch giao thông bền vững.
+ Khuyến khích sử dụng phương tiện công cộng.
+ Hạ tầng cho xe đạp và xe điện.
+ Giảm phát thải từ hoạt động giao thông.
– QUẢN LÝ MÔI TRƯỜNG VÀ TÍCH HỢP CÁC YÊU CẦU LEED
+ Hạng mục 7: Sức khỏe và phúc lợi.
+ Hạng mục 8: Quản lý môi trường.
+ Quy trình tích hợp trong thiết kế và quản lý dự án (Integrative Process).
+ Quản lý vận hành công trình xanh.
+ Chuẩn bị hồ sơ và minh chứng cho chứng nhận LEED.
+ Quy trình đăng ký, đánh giá và chứng nhận LEED.
+ Cải tiến liên tục sau khi công trình được chứng nhận.
– WORKSHOP THỰC HÀNH
+ Đánh giá sơ bộ một dự án theo hệ thống LEED.
+ Xác định các hạng mục và điểm số tiềm năng.
+ Lựa chọn giải pháp thiết kế nhằm tối ưu điểm LEED.
+ Thực hành lập kế hoạch đáp ứng các tiêu chí đánh giá.
+ Chuẩn bị hồ sơ chứng nhận LEED.
+ Phân tích các công trình xanh tiêu biểu và thảo luận tình huống thực tế.',
        ],
        [
            'code' => 'KHC-33',
            'name' => 'ĐỒNG BỘ CHUỖI GIÁ TRỊ SẢN PHẨM XANH: THIẾT LẬP TUYÊN BỐ MÔI TRƯỜNG SẢN PHẨM THEO CHUẨN QUỐC TẾ EPD',
            'duration' => '2 ngày',
            'fee' => 2660000.0,
            'instructor' => 'HUY THANH',
            'audience' => 'Ban lãnh đạo và các cấp quản lý doanh nghiệp.
Cán bộ R&D, kỹ thuật, chất lượng (QA/QC), môi trường và phát triển bền vững.
Cán bộ ESG, HSE/EHS, quản lý sản phẩm và quản lý carbon.
Thành viên Ban ISO và nhóm triển khai LCA, EPD.
Doanh nghiệp sản xuất có nhu cầu xây dựng EPD để đáp ứng yêu cầu của khách hàng, chứng nhận công trình xanh và xuất khẩu.',
            'objectives' => 'Hiểu và áp dụng các yêu cầu của Environmental Product Declaration (EPD) theo ISO 14025.
Thực hiện đánh giá vòng đời sản phẩm (LCA) và xây dựng hồ sơ công bố EPD.
Thu thập, quản lý dữ liệu và sử dụng phần mềm LCA phục vụ xây dựng EPD.
Chuẩn bị hồ sơ xác minh, đáp ứng yêu cầu của khách hàng, công trình xanh và thị trường quốc tế.',
            'content_summary' => '- Tổng quát về EPD và các PO (Program Owners).
- Quy trình công bố EPD.
- Hướng dẫn về các yêu cầu của GPI (General Programme Instructions).
- Hướng dẫn về PCR (Quy tắc phân loại sản phẩm).
- Hướng dẫn về LCA (Life Cycle Assessment – Đánh giá vòng đời sản phẩm).
- Quy tắc phân bổ (Allocation).
- Kế hoạch quản lý dữ liệu.
- Đánh giá chất lượng dữ liệu.
- Mô hình hóa điện và biogas.
- Xây dựng kịch bản cho giai đoạn sử dụng và kết thúc vòng đời sản phẩm.
- Hướng dẫn sử dụng phần mềm tính toán và diễn giải tác động môi trường (SimaPro/One Click LCA).',
            'content_detail' => '– TỔNG QUAN VỀ EPD VÀ CÁC CHƯƠNG TRÌNH CÔNG BỐ
Tổng quan về Environmental Product Declaration (EPD).
Tiêu chuẩn ISO 14025 và vai trò của EPD trong phát triển bền vững.
Các Program Owners (POs) và hệ thống công bố EPD trên thế giới.
Lợi ích của EPD đối với doanh nghiệp và chuỗi cung ứng.
Mối liên hệ giữa EPD với ISO 14040, ISO 14044, ISO 14067, LEED và các chương trình mua sắm xanh.
– QUY TRÌNH XÂY DỰNG EPD
Quy trình công bố EPD.
Hướng dẫn các yêu cầu của General Programme Instructions (GPI).
Hướng dẫn áp dụng Product Category Rules (PCR).
Xác định đơn vị chức năng (Functional Unit).
Thiết lập ranh giới hệ thống.
Chuẩn bị hồ sơ phục vụ xác minh EPD.
– ĐÁNH GIÁ VÒNG ĐỜI SẢN PHẨM (LCA)
Hướng dẫn phương pháp đánh giá vòng đời sản phẩm (Life Cycle Assessment – LCA).
Xác định mục tiêu và phạm vi nghiên cứu.
Phân tích kiểm kê vòng đời (Life Cycle Inventory – LCI).
Đánh giá tác động vòng đời (Life Cycle Impact Assessment – LCIA).
Diễn giải kết quả LCA.
Quy tắc phân bổ (Allocation) trong LCA.
– QUẢN LÝ DỮ LIỆU VÀ MÔ HÌNH HÓA
Xây dựng kế hoạch quản lý dữ liệu.
Thu thập và kiểm soát dữ liệu đầu vào.
Đánh giá chất lượng dữ liệu.
Nguồn dữ liệu sơ cấp và thứ cấp.
Mô hình hóa điện và biogas.
Thiết lập các giả định và kiểm soát độ không chắc chắn của dữ liệu.
– XÂY DỰNG KỊCH BẢN VÒNG ĐỜI VÀ SỬ DỤNG PHẦN MỀM LCA
Xây dựng kịch bản cho giai đoạn sử dụng sản phẩm.
Xây dựng kịch bản cho giai đoạn kết thúc vòng đời sản phẩm.
Hướng dẫn sử dụng phần mềm SimaPro.
Hướng dẫn sử dụng One Click LCA.
Phân tích và diễn giải kết quả tác động môi trường.
Chuẩn bị báo cáo và tài liệu công bố EPD.
– WORKSHOP THỰC HÀNH
Lựa chọn PCR phù hợp cho một sản phẩm.
Xây dựng mô hình LCA cho sản phẩm mẫu.
Thu thập và xử lý dữ liệu.
Thực hành sử dụng SimaPro hoặc One Click LCA.
Diễn giải kết quả đánh giá tác động môi trường.
Xây dựng dự thảo Environmental Product Declaration.
Phân tích tình huống thực tế và giải đáp khó khăn trong quá trình triển khai.',
        ],
        [
            'code' => 'KHC-34',
            'name' => 'ESG – NHẬN THỨC VỀ PHÁT TRIỂN BỀN VỮNG VÀ QUẢN TRỊ DOANH NGHIỆP',
            'duration' => '2 ngày',
            'fee' => 2660000.0,
            'instructor' => 'phượng',
            'audience' => '- Lãnh đạo doanh nghiệp, cán bộ quản lý các phòng ban.
- Cán bộ phụ trách phát triển bền vững, môi trường, an toàn – sức khỏe nghề nghiệp (EHS/HSE).
- Cán bộ phụ trách quản trị doanh nghiệp, quản lý rủi ro, nhân sự, tài chính, đầu tư, quan hệ khách hàng.
- Cán bộ nghiên cứu, giảng viên, chuyên viên tư vấn trong lĩnh vực môi trường, kinh tế, quản trị và phát triển bền vững.
- Cá nhân quan tâm đến việc xây dựng năng lực ESG và chuyển đổi mô hình kinh doanh bền vững.',
            'objectives' => '- Hiểu được các khái niệm, nguyên tắc và xu hướng phát triển bền vững, ESG trong bối cảnh hiện nay.
- Nhận diện được vai trò của ESG đối với chiến lược phát triển, quản trị rủi ro và nâng cao năng lực cạnh tranh của doanh nghiệp.
- Nắm được các yêu cầu cơ bản về quản lý môi trường, trách nhiệm xã hội và quản trị doanh nghiệp theo định hướng ESG.
- Có khả năng đánh giá hiện trạng ESG, xác định các vấn đề trọng yếu và đề xuất định hướng triển khai ESG phù hợp với tổ chức.
- Hiểu được các yêu cầu cơ bản trong xây dựng báo cáo và công bố thông tin ESG theo các thông lệ quốc tế.',
            'content_summary' => '1: Tổng quan về phát triển bền vững và ESG
2: Trụ cột Môi trường (Environmental)
3: Trụ cột Xã hội (Social)
4: Trụ cột Quản trị (Governance)
5: Xây dựng và triển khai chiến lược ESG trong doanh nghiệp
6: Báo cáo và công bố thông tin ESG',
            'content_detail' => '1: Tổng quan về phát triển bền vững và ESG
- Khái niệm, nguyên tắc và xu hướng phát triển bền vững trên thế giới và tại Việt Nam.
- Sự hình thành và vai trò của ESG trong chiến lược phát triển doanh nghiệp.
- Mối liên hệ giữa ESG, trách nhiệm xã hội doanh nghiệp (CSR) và mục tiêu phát triển bền vững (SDGs).
2: Trụ cột Môi trường (Environmental)
- Các vấn đề môi trường trong hoạt động doanh nghiệp.
- Quản lý phát thải khí nhà kính, biến đổi khí hậu và sử dụng hiệu quả tài nguyên.
- Kinh tế tuần hoàn, sản xuất sạch hơn và các giải pháp giảm tác động môi trường.
- Tổng quan về kiểm kê phát thải và báo cáo môi trường theo các tiêu chuẩn quốc tế.
3: Trụ cột Xã hội (Social)
- Vai trò của yếu tố con người trong phát triển bền vững.
- Quản trị nguồn nhân lực, an toàn lao động, sức khỏe nghề nghiệp và quyền lợi người lao động.
- Trách nhiệm với cộng đồng và các bên liên quan.
4: Trụ cột Quản trị (Governance)
- Nguyên tắc quản trị doanh nghiệp bền vững.
- Đạo đức kinh doanh, minh bạch thông tin và quản trị rủi ro.
- Vai trò của ban lãnh đạo trong xây dựng chiến lược ESG.
5: Xây dựng và triển khai chiến lược ESG trong doanh nghiệp
- Đánh giá hiện trạng ESG của tổ chức.
- Xác định các vấn đề trọng yếu (Materiality Assessment).
- Thiết lập mục tiêu, chỉ số đánh giá ESG (ESG Metrics/KPIs).
- Lộ trình triển khai ESG và tích hợp ESG vào chiến lược kinh doanh.
6: Báo cáo và công bố thông tin ESG
- Tổng quan các tiêu chuẩn, hướng dẫn báo cáo ESG phổ biến.
- Nguyên tắc thu thập dữ liệu và quản lý thông tin ESG.
- Xu hướng yêu cầu ESG từ chuỗi cung ứng, thị trường quốc tế và nhà đầu tư.',
        ],
        [
            'code' => 'KHC-35',
            'name' => 'KỸ NĂNG PHÂN TÍCH CHUYÊN SÂU VÀ LẬP BÁO CÁO PHÁT TRIỂN BỀN VỮNG ESG',
            'duration' => '3 ngày',
            'fee' => 4860000.0,
            'instructor' => 'Cô Cẩm Chi',
            'audience' => '',
            'objectives' => '',
            'content_summary' => '',
            'content_detail' => '',
        ]
        ];

        foreach ($courses as $item) {
            Course::query()->updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'duration' => $item['duration'],
                    'fee' => $item['fee'],
                    'instructor' => $item['instructor'],
                    'audience' => $item['audience'],
                    'objectives' => $item['objectives'],
                    'content_summary' => $item['content_summary'],
                    'content_detail' => $item['content_detail'],
                    'location' => 'Trung tâm đào tạo GREECO',
                ]
            );
        }
    }
}
