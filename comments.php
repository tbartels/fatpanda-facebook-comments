<?php $WPFBC = FatPandaFacebookComments::load(); ?>

<?php if ($app_id = $WPFBC->get_app_id()) { ?>
  <script>
    (function($) {
      var subscribe = function() {
        <?php if ($WPFBC->is_import_enabled()) { ?>
          return false;
        <?php } ?>
        FB.Event.subscribe('comment.create', function(response) {
          $.post('<?php echo admin_url('admin-ajax.php') ?>', { action: 'fb_create_comment', response: response });
        });
        FB.Event.subscribe('comment.remove', function(response) {
          $.post('<?php echo admin_url('admin-ajax.php') ?>', { action: 'fb_remove_comment', response: response });
        });
      }

      $(function() {
        if (!$('#fb-root').size()) {
          $('body').append('<div id="fb-root"></div>');
          window.fbAsyncInit = function() {
            FB.init({
              appId:  '<?php echo htmlentities($app_id) ?>',
              status: true,
              cookie: true,
              xfbml:  true
            });
            subscribe();
          };

          (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?php echo htmlentities($app_id) ?>";
            fjs.parentNode.insertBefore(js, fjs);
          }(document, 'script', 'facebook-jssdk')); 
        } else {
          var i = setInterval(function() {
            if ('FB' in window) {
              clearInterval(i);
              subscribe();
            }
          }, 100);
        }
      })  
    })(jQuery);
  </script>
<?php } ?>

<?php do_action('fb_before_comments') ?>

<div id="<?php echo get_class($WPFBC) ?>">
  <?php if ($WPFBC->should_support_xid() && ( $xid = $WPFBC->get_xid() )) { ?>
    <fb:comments xid="<?php echo esc_attr($xid) ?>" url="<?php the_permalink() ?>" numposts="<?php echo esc_attr($WPFBC->get_num_posts()) ?>" width="<?php echo esc_attr($WPFBC->get_width()) ?>" publish_feed="true" migrated="1"></fb:comments>
  <?php } else { ?>
    <div class="fb-comments" data-href="<?php the_permalink(); ?>" data-num-posts="<?php echo esc_attr($WPFBC->get_num_posts()) ?>" data-width="<?php echo esc_attr($WPFBC->get_width()) ?>"></div>
  <?php } ?>
</div>

<?php do_action('fb_after_comments') ?>

<?php if ( $WPFBC->setting('show_old_comments', 'on') == 'on' && have_comments() ) { ?>
  <div class="navigation">
    <div class="alignleft"><?php previous_comments_link() ?></div>
    <div class="alignright"><?php next_comments_link() ?></div>
  </div>

  <div class="commentlist">
    <?php wp_list_comments(array('style' => 'div', 'type' => 'comment', 'reverse_top_level' => 1)); ?>
  </div>

  <div class="navigation">
    <div class="alignleft"><?php previous_comments_link() ?></div>
    <div class="alignright"><?php next_comments_link() ?></div>
  </div>
<?php } else { ?>
  <noscript>
    <?php wp_list_comments(array('style' => 'div', 'type' => 'comment', 'reverse_top_level' => 1)); ?>
  </noscript>
<?php } ?>

<noscript>
  <?php wp_list_comments(array('style' => 'div', 'type' => 'facebook', 'reverse_top_level' => 1)); ?>
</noscript>