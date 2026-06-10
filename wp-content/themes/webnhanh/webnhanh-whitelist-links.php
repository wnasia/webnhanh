<?php
/**
 * =========================================================
 * WEBNHANH WHITELIST LINKS (FULL VERSION)
 * ---------------------------------------------------------
 * ✅ Giữ nofollow mặc định, nhưng FOLLOW cho domain uy tín & hệ sinh thái
 * ✅ Dành cho webnhanh.net – Dịch vụ thiết kế web, Google Ads, Facebook Ads
 * =========================================================
 */

if (!function_exists('webnhanh_whitelist_nofollow')) {
    function webnhanh_whitelist_nofollow($content) {

        /**
         * 1️⃣ Nhóm domain uy tín quốc tế (Google, Meta, Ads, SEO, Dev)
         */
        $authority_domains = array(
            // Google / Ads / Analytics / Performance
            'google.com', 'developers.google.com', 'support.google.com',
            'ads.google.com', 'marketingplatform.google.com',
            'analytics.google.com', 'tagmanager.google.com',
            'search.google.com/search-console', 'pagespeed.web.dev',
            'web.dev', 'sites.google.com',

            // Meta / Facebook / Instagram Ads & Docs
            'facebook.com/business', 'business.facebook.com',
            'developers.facebook.com', 'help.instagram.com',

            // Microsoft Ads (Bing)
            'ads.microsoft.com', 'learn.microsoft.com/advertising',
            'help.ads.microsoft.com',

            // LinkedIn Ads
            'business.linkedin.com', 'ads.linkedin.com', 'linkedin.com',

            // TikTok Ads
            'ads.tiktok.com', 'business.tiktok.com',

            // Chuẩn web / công cụ dev
            'wordpress.org', 'wordpress.com', 'w3.org', 'mozilla.org',
            'schema.org', 'github.com',

            // Tool/nguồn SEO lớn
            'ahrefs.com', 'semrush.com', 'moz.com', 'yoast.com', 'rankmath.com',
            'wikipedia.org',

            // Báo / nguồn tham khảo có thẩm quyền
            'bbc.com', 'cnn.com', 'forbes.com', 'reuters.com',

            // 🇻🇳 Nguồn Việt Nam chính thống
'gdt.gov.vn',                 // Tổng cục Thuế
'moj.gov.vn',                 // Bộ Tư pháp
'luatvietnam.vn',             // Trang luật Việt Nam
'chinhphu.vn',                // Cổng thông tin Chính phủ
'thuvienphapluat.vn',         // Thư viện Pháp luật
'baochinhphu.vn',             // Báo Chính phủ

// 🏛️ Các Bộ, Ngành Nhà nước
'mof.gov.vn',                 // Bộ Tài chính
'mpi.gov.vn',                 // Bộ Kế hoạch & Đầu tư
'mic.gov.vn',                 // Bộ Thông tin & Truyền thông
'moet.gov.vn',                // Bộ Giáo dục & Đào tạo
'molisa.gov.vn',              // Bộ Lao động - Thương binh & Xã hội
'most.gov.vn',                // Bộ Khoa học & Công nghệ
'moi.gov.vn',                 // Bộ Nội vụ
'moh.gov.vn',                 // Bộ Y tế
'mt.gov.vn',                  // Bộ Giao thông vận tải
'monre.gov.vn',               // Bộ Tài nguyên & Môi trường
'customs.gov.vn',             // Tổng cục Hải quan
'bct.gov.vn',                 // Bộ Công Thương
'vss.gov.vn',                 // Bảo hiểm Xã hội Việt Nam
'na.gov.vn',                  // Quốc hội Việt Nam


// 🧾 Tài chính, đăng ký, báo cáo
'dkkd.gov.vn',                // Cổng thông tin đăng ký doanh nghiệp
'baocaotaichinh.mof.gov.vn',  // Báo cáo tài chính doanh nghiệp
'vanban.chinhphu.vn',         // Văn bản Chính phủ
'vbpl.vn',                    // Văn bản pháp luật

// 🎓 Giáo dục & học thuật
'moet.gov.vn',                // Bộ Giáo dục & Đào tạo
'moha.gov.vn',                // Bộ Nội vụ (liên quan quản lý giáo dục công)
'vnu.edu.vn',                 // Đại học Quốc gia Hà Nội
'vnuhcm.edu.vn',              // Đại học Quốc gia TP.HCM
'hust.edu.vn',                // Đại học Bách Khoa Hà Nội
'hcmut.edu.vn',               // Đại học Bách Khoa TP.HCM
'neu.edu.vn',                 // Đại học Kinh tế Quốc dân
'ueh.edu.vn',                 // Đại học Kinh tế TP.HCM
'ftu.edu.vn',                 // Đại học Ngoại Thương
'ptit.edu.vn',                // Học viện Công nghệ Bưu chính Viễn thông
'hvnh.edu.vn',                // Học viện Ngân hàng
'hvctqg.vn',                  // Học viện Chính trị Quốc gia
'hcmussh.edu.vn',             // ĐH KHXH & NV TPHCM
'hnue.edu.vn',                // Đại học Sư phạm Hà Nội

// ⚙️ Tổ chức & hiệp hội
'vcci.com.vn',                // Phòng Thương mại & Công nghiệp VN
'vneconomy.vn',               // Báo Kinh tế Việt Nam
'vietnam.gov.vn',             // Cổng thông tin quốc gia
'vietbao.vn',                 // Báo Việt Báo
'vietnamnet.vn',              // Báo Vietnamnet
'vnexpress.net',              // Báo VNExpress
'nongthonvaphattrien.vn',       // Báo Nông thôn phát triển

        );

        /**
         * 2️⃣ Nhóm domain hệ sinh thái của anh (web chính, khách hàng, vệ tinh)
         */
        $ecosystem_domains = array(
            // Web chính
            'webnhanh.net',

            // Khách hàng (portfolio / case study)
            'mauweb.asia', 'goodesign.vn', 'yensaoyangbay.vn',
            'dichvuketoananduc.vn', 'tekco.vn', 'amyliving.vn',
            'drmedia.vn', 'quanglamdc.vn', 'bachvietbaove.vn',
            'thanglongsg.io.vn', 'semofood.com',

            // Site vệ tinh anh sở hữu
            'sites.google.com/view/thietkewebchuanseo',
            'sites.google.com/view/thietkewebbatdongsanhcm',
            'nguyenduchuy.io.vn'
        );

        /**
         * 3️⃣ Gộp whitelist chung
         */
        $whitelist_domains = array_merge($authority_domains, $ecosystem_domains);

        /**
         * 4️⃣ Quét và xử lý link
         */
        if (preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>/i', $content, $matches)) {
            foreach ($matches[0] as $i => $tag) {
                $url = $matches[1][$i];

                foreach ($whitelist_domains as $domain) {
                    if (strpos($url, $domain) !== false) {

                        // 🧹 Gỡ bỏ rel="nofollow" hoặc combo nofollow khác
                        $new_tag = preg_replace_callback('/\srel=("|\')(.*?)\1/i', function($m){
                            $vals = preg_split('/\s+/', trim($m[2]));
                            $vals = array_values(array_diff($vals, ['nofollow']));
                            return $vals ? ' rel="'.implode(' ', $vals).'"' : '';
                        }, $tag);

                        // 🔒 Thêm noopener noreferrer nếu có target="_blank"
                        $has_blank = (bool)preg_match('/\starget=("|\')_blank\1/i', $new_tag);
                        if ($has_blank) {
                            if (preg_match('/\srel=("|\')(.*?)\1/i', $new_tag)) {
                                $new_tag = preg_replace_callback('/\srel=("|\')(.*?)\1/i', function($m){
                                    $vals = preg_split('/\s+/', trim($m[2]));
                                    foreach (['noopener','noreferrer'] as $t) {
                                        if (!in_array($t, $vals)) $vals[] = $t;
                                    }
                                    return ' rel="'.implode(' ', array_unique($vals)).'"';
                                }, $new_tag);
                            } else {
                                $new_tag = preg_replace('/<a\s/i', '<a rel="noopener noreferrer" ', $new_tag, 1);
                            }
                        }

                        // 🔁 Thay thẻ cũ bằng thẻ mới
                        $content = str_replace($tag, $new_tag, $content);
                        break;
                    }
                }
            }
        }

        return $content;
    }

    // ⏱️ Hook chạy sau Rank Math (độ ưu tiên cao)
    add_filter('the_content', 'webnhanh_whitelist_nofollow', 999);
}
