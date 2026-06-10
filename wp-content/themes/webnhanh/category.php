<?php
/* Category archive hiển thị giống trang /blog */
get_header();

$term    = get_queried_object();
$cat_id  = isset($term->term_id) ? (int) $term->term_id : 0;
$slug    = isset($term->slug) ? $term->slug : '';
?>

<div class="page-wrapper archive">
  <div class="container">

    <?php if ( function_exists('flatsome_breadcrumbs') ) flatsome_breadcrumbs(); ?>

    <div class="page-title page-title-inner">
      <h1 class="page-title"><?php single_cat_title(); ?></h1>
      <?php the_archive_description('<div class="term-description">','</div>'); ?>
    </div>

    <?php
    /**
     * Dùng đúng component "Blog Posts" của Flatsome (giống /blog)
     * - columns: 3 desktop / tự co tablet/mobile theo flatsome
     * - image_height ~ 56% là preset thường dùng ở /blog
     * - excerpt + meta bật sẵn
     * - cat: lọc theo category hiện tại (slug). Nếu site bạn cần ID, đổi cat="'.$cat_id.'"
     */
    echo do_shortcode(
      '[blog_posts style="normal"
                   type="row"
                   columns="3"
                   image_height="56%"
                   text_align="left"
                   excerpt="visible"
                   excerpt_length="24"
                   meta="true"
                   show_date="true"
                   show_category="true"
                   posts="12"
                   orderby="date"
                   order="desc"
                   cat="'.$slug.'"]'
    );
    ?>

    <div class="pagination-container">
      <?php // Pagination của shortcode đã có; để dự phòng nếu theme tắt:
      if (function_exists('the_posts_pagination')) {
        the_posts_pagination( array(
          'mid_size'  => 2,
          'prev_text' => '&laquo;',
          'next_text' => '&raquo;',
        ) );
      } ?>
    </div>

  </div>
</div>

<?php get_footer(); ?>
