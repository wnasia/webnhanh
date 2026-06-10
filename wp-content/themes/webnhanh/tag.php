<?php
/* Template dùng chung cho mọi trang TAG
 * Layout giống /blog, lọc theo tag hiện tại
 */
get_header();

$term   = get_queried_object();
$tag_id = isset($term->term_id) ? (int) $term->term_id : 0;
?>

<div class="page-wrapper archive">
  <div class="container">

    <?php if ( function_exists('flatsome_breadcrumbs') ) flatsome_breadcrumbs(); ?>

    <div class="page-title page-title-inner">
      <h1 class="page-title">
        <?php echo single_tag_title('', false); ?>
      </h1>

      <?php
      // Nếu muốn hiển thị mô tả ngắn của tag (chỉ text, không hình), mở comment khối dưới:
      /*
      if ( ! empty( $term->description ) ) {
        echo '<p class="term-desc-short">'
             . wp_trim_words( wp_strip_all_tags( $term->description ), 25, '…' )
             . '</p>';
      }
      */
      ?>
    </div>

    <?php
    // List bài giống /blog nhưng lọc theo TAG hiện tại
    echo do_shortcode(
      '[blog_posts
          style="normal"
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
          tags="'.$tag_id.'"
       ]'
    );
    ?>

  </div>
</div>

<?php get_footer(); ?>
