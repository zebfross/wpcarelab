<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP_Bootstrap_Starter
 */

?>
<?php if(!is_page_template( 'blank-page.php' ) && !is_page_template( 'blank-page-with-container.php' )): ?>
			</div><!-- .row -->
		</div><!-- .container -->
	</div><!-- #content -->
    <?php get_template_part( 'footer-widget' ); ?>
	<footer id="colophon" class="site-footer <?php echo wp_bootstrap_starter_bg_class(); ?>" role="contentinfo">
		<div class="container pt-3 pb-3">
            <div class="site-info">
                &copy; <?php echo date('Y'); ?> <?php echo '<a href="'.home_url().'">'.get_bloginfo('name').'</a>'; ?>
                <span class="sep"> | </span>
                <a class="credits" href="https://afterimagedesigns.com/wp-bootstrap-starter/" target="_blank" title="WordPress Technical Support" alt="Bootstrap WordPress Theme"><?php echo esc_html__('Bootstrap WordPress Theme','wp-bootstrap-starter'); ?></a>

            </div><!-- close .site-info -->
		</div>
	</footer><!-- #colophon -->
<?php endif; ?>
</div><!-- #page -->

<script type="text/javascript">

var $zoho = $zoho || {};
$zoho.salesiq = $zoho.salesiq ||
{
    widgetcode: "d2b004022068525e658558d18adea8de0f659987c310b3f1daab76679f6b1dfe",
    values:
    {},
    ready: function () {}
};
var timeout = window.location.pathname.indexOf("contact") > -1 ? 0 : 5000;
setTimeout(function ()
{
	var d = document;
	s = d.createElement("script");
	s.type = "text/javascript";
	s.id = "zsiqscript";
	s.defer = true;
	s.src = "https://salesiq.zoho.com/widget";
	t = d.getElementsByTagName("script")[0];
	t.parentNode.insertBefore(s, t);
	//d.body.append("<div id='zsiqwidget'></div>");
}, timeout);
</script>
<?php wp_footer(); ?>
</body>
</html>