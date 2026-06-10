<?php
$mods = get_theme_mods();
foreach (['footer_1','footer_2','footer_block','footer_1_columns','footer_2_columns','footer_1_bg_color','footer_1_color','footer_1_bg_image','footer_bottom_color','footer_bottom_text','footer_bottom_align','footer_2_bg_color','footer_2_color'] as $k) {
    echo $k . ' = ' . (isset($mods[$k]) ? (is_string($mods[$k]) ? $mods[$k] : json_encode($mods[$k])) : '(not set)') . "\n";
}
echo "footer_left_text_len = " . (isset($mods['footer_left_text']) ? strlen($mods['footer_left_text']) : 0) . "\n";
echo "--- blocks CPT ---\n";
global $wpdb;
$rows = $wpdb->get_results("SELECT ID, post_status, post_name, post_title, LENGTH(post_content) as len, MD5(post_content) as md5 FROM {$wpdb->posts} WHERE post_type='blocks'");
foreach ($rows as $r) {
    echo "ID={$r->ID} status={$r->post_status} name={$r->post_name} title={$r->post_title} len={$r->len} md5={$r->md5}\n";
}
