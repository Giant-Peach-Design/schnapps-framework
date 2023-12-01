<?php

namespace Giantpeach\Schnapps\Framework;

use Giantpeach\Schnapps\Config\Facades\Config;
use Giantpeach\Schnapps\Images\Images;
use Giantpeach\Schnapps\Query\Cli\Cli as QueryCli;
use Giantpeach\Schnapps\Theme\Blocks\Blocks;
use Giantpeach\Schnapps\Theme\Routes\Api;

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
