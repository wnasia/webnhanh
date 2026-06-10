<?php
/**
 * Template: single-post-anduc.php (v7 - Footer 2 as sidebar, title size 22)
 * Scope: ONLY blog posts
 */
get_header(); ?>

<div class="anduc-container">
  <div class="anduc-post-layout">
    <main class="anduc-post-main">
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('anduc-post-content'); ?>>
          <h1 class="anduc-post-title"><?php the_title(); ?></h1>
          <div class="anduc-post-meta">
  <span class="meta-item date">
    <svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M7 2v2H5a2 2 0 0 0-2 2v2h18V6a2 2 0 0 0-2-2h-2V2h-2v2H9V2H7zm14 8H3v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V10zm-2 4h-6v6h6v-6z"/></svg>
    <?php echo esc_html( get_the_date('d/m/Y') ); ?>
  </span> <span class="meta-item cat">
    <svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M10 4H4v16h16V8h-6V4h-4zm4 0v4h4"/></svg>
    <?php echo wp_kses_post( get_the_category_list(', ') ); ?>
  </span> <span class="meta-item author">
    <svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z"/></svg>
    <?php echo esc_html( get_the_author() ); ?>
  </span> <span class="meta-item views">
    <svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 5C5 5 1 12 1 12s4 7 11 7 11-7 11-7-4-7-11-7zm0 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4z"/></svg>
    <?php echo intval(get_post_meta(get_the_ID(), 'anduc_post_views', true)); ?> lượt xem
  </span>
</div>
          <div class="entry-content">
            <?php the_content(); wp_link_pages(['before'=>'<div class="page-links">','after'=>'</div>']); ?>
          </div>
        </article>
        <nav class="anduc-post-nav">
          <div class="prev"><?php previous_post_link('%link', '← Bài trước'); ?></div>
          <div class="next"><?php next_post_link('%link', 'Bài tiếp →'); ?></div>
        </nav>
        <?php if (comments_open() || get_comments_number()) comments_template(); ?>
      <?php endwhile; endif; ?>
    </main>

    <aside class="anduc-post-side" role="complementary">
      <?php
        // Strictly use 'Chân trang 2' as sidebar (common ID: footer-2). Try a few common IDs.
        $ids = array('footer-2','footer_2','footer-two','footer2','footer-col-2','footer-02','footer_02');
        $printed = false;
        foreach ($ids as $sid) {
          if (is_active_sidebar($sid)) {
            dynamic_sidebar($sid);
            $printed = true;
            break;
          }
        }
        // If no matching sidebar ID is found, try to render all registered sidebars and pick the one with name containing '2'
        if (!$printed && function_exists('wp_get_sidebars_widgets')) {
          global $wp_registered_sidebars;
          $sidebars = wp_get_sidebars_widgets();
          foreach ($wp_registered_sidebars as $id => $sb) {
            if (strpos(strtolower($sb['name']), '2') !== false && !empty($sidebars[$id])) {
              dynamic_sidebar($id);
              $printed = true;
              break;
            }
          }
        }
      ?>
    </aside>
  </div>
</div>

<?php get_footer(); ?>