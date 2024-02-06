<?php

namespace Giantpeach\Schnapps\Framework;

use Giantpeach\Schnapps\Config\Facades\Config;
use Giantpeach\Schnapps\Images\Images;
use Giantpeach\Schnapps\Query\Cli\Cli as QueryCli;
use Giantpeach\Schnapps\Theme\Blocks\Blocks;
use Giantpeach\Schnapps\Theme\Routes\Api;
use Giantpeach\Schnapps\Twiglet\Twiglet;

class SchnappsFramework
{
  public function __construct()
  {
    $this->loadDependencies();
    $this->setupTheme();
    $this->setupFilters();
    // Load Blocks
    new Blocks();

    // Load API Routes
    new Api();
  }

  private function loadDependencies()
  {
    Config::load();
    new QueryCli();
    Images::getInstance();
    \add_action('init', [$this, 'createThemeOptionsPage']);
  }

  public function setupTheme(): void
  {
    add_action('init', [$this, 'registerPostTypes']);
    add_action('init', [$this, 'registerMenus']);

    add_action('wp_enqueue_scripts', [$this, 'stylesheets']);
    add_action('wp_enqueue_scripts', [$this, 'scripts']);

    add_action('enqueue_block_editor_assets', [$this, 'blockEditorStylesheets']);
    add_action('enqueue_block_editor_assets', [$this, 'blockEditorScripts']);

    $themeDir = get_template_directory();

    if (file_exists($themeDir . '/src/Components/head.twig')) {
      add_action('wp_head', function () use ($themeDir) {
        Twiglet::getInstance()->display('head.twig');
      });
    }

    add_action('wp_head', function () {
      if (Config::get('seo.analytics.ga')) {
        echo '<!-- Google tag (gtag.js) -->
              <script async src="https://www.googletagmanager.com/gtag/js?id=' . Config::get('seo.analytics.ga') . '"></script>
              <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag("js", new Date());

                gtag("config", "' . Config::get('seo.analytics.ga') . '");
              </script>';
      }

      if (Config::get('seo.tagmanager.gtm')) {
        echo '<!-- Google Tag Manager -->
              <link rel="dns-prefetch" href="https://www.googletagmanager.com">
              <link rel="preload" href="https://www.googletagmanager.com/gtm.js?id=' . Config::get('seo.tagmanager.gtm') . '" as="script">
              <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({"gtm.start":
              new Date().getTime(),event:"gtm.js"});var f=d.getElementsByTagName(s)[0],
              j=d.createElement(s),dl=l!="dataLayer"?"&l="+l:"";j.async=true;j.src=
              "https://www.googletagmanager.com/gtm.js?id="+i+dl;f.parentNode.insertBefore(j,f);
              })(window,document,"script","dataLayer","' . Config::get('seo.tagmanager.gtm') . '");</script>
              <!-- End Google Tag Manager -->';
      }
    });

    add_action('after_setup_theme', function () {
      // need to add colors here, be good to get them
      // from the Tailwind config.
      add_theme_support('custom-spacing');
    });
  }

  public function setupFilters(): void
  {
    add_filter('acf/blocks/no_fields_assigned_message', function () {
      return 'This block contains no editable fields.';
    });

    add_filter('upload_mimes', function ($mimes) {
      $mimes['svg'] = 'image/svg+xml';
      return $mimes;
    });

    add_filter('acf/load_field/name=post_type', function ($field) {
      foreach (get_post_types('', 'names') as $post_type) {
        $field['choices'][$post_type] = $post_type;
      }

      // return the field
      return $field;
    });

    add_filter('acf/load_field/name=taxonomy', function ($field) {
      foreach (get_taxonomies('', 'names') as $taxonomy) {
        $field['choices'][$taxonomy] = $taxonomy;
      }

      // return the field
      return $field;
    });

    add_filter('acf/load_field/name=image_sizes', function ($field) {
      $sizes = Config::get('image-sizes');

      foreach ($sizes as $key => $imageSize) {
        $field['choices'][$key] = $key;
      }

      return $field;
    });
  }

  /**
   * Create theme options page
   *
   * @return void
   */
  public function createThemeOptionsPage(): void
  {
    if (function_exists('acf_add_options_page')) {
      \acf_add_options_page([
        'page_title' => 'Theme Options',
        'menu_title' => 'Theme Options',
        'menu_slug' => 'theme-options',
        'capability' => 'edit_posts',
        'redirect' => false,
      ]);
    }
  }
}
