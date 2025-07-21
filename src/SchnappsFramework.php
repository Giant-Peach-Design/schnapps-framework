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
        add_action('wp_enqueue_scripts', [$this, 'processScripts']);

        add_action('enqueue_block_assets', [$this, 'blockEditorStylesheets']);
        add_action('enqueue_block_assets', [$this, 'blockEditorScripts']);

        add_action('acf/init', function () {
            if (function_exists("acf_add_options_page")) {
                \acf_add_options_page([
                    "page_title" => "Scripts",
                    "menu_title" => "Scripts",
                    "menu_slug" => "scripts",
                    "capability" => "edit_posts",
                    "redirect" => false,
                ]);
            }
        });

        add_action("acf/include_fields", function () {
            if (!function_exists("acf_add_local_field_group")) {
                return;
            }

            acf_add_local_field_group([
                "key" => "scripts_group",
                "title" => "Scripts",
                "fields" => [
                    [
                        "key" => "field_65c232c87e9c1",
                        "label" => "Scripts",
                        "name" => "scripts",
                        "aria-label" => "",
                        "type" => "repeater",
                        "instructions" => "",
                        "required" => 0,
                        "conditional_logic" => 0,
                        "wrapper" => [
                            "width" => "",
                            "class" => "",
                            "id" => "",
                        ],
                        "layout" => "block",
                        "pagination" => 0,
                        "min" => 0,
                        "max" => 0,
                        "collapsed" => "",
                        "button_label" => "Add Script",
                        "rows_per_page" => 20,
                        "sub_fields" => [
                            [
                                "key" => "field_65c232eb7e9c2",
                                "label" => "Src",
                                "name" => "src",
                                "aria-label" => "",
                                "type" => "text",
                                "instructions" => "",
                                "required" => 0,
                                "conditional_logic" => [
                                    [
                                        [
                                            "field" => "field_65c233747e9c5",
                                            "operator" => "==empty",
                                        ],
                                    ],
                                ],
                                "wrapper" => [
                                    "width" => "",
                                    "class" => "",
                                    "id" => "",
                                ],
                                "default_value" => "",
                                "maxlength" => "",
                                "placeholder" => "",
                                "prepend" => "",
                                "append" => "",
                                "parent_repeater" => "field_65c232c87e9c1",
                            ],
                            [
                                "key" => "field_65c233747e9c5",
                                "label" => "Snippet",
                                "name" => "snippet",
                                "aria-label" => "",
                                "type" => "textarea",
                                "instructions" => "",
                                "required" => 0,
                                "conditional_logic" => [
                                    [
                                        [
                                            "field" => "field_65c232eb7e9c2",
                                            "operator" => "==empty",
                                        ],
                                    ],
                                ],
                                "wrapper" => [
                                    "width" => "",
                                    "class" => "",
                                    "id" => "",
                                ],
                                "default_value" => "",
                                "maxlength" => "",
                                "rows" => "",
                                "placeholder" => "",
                                "new_lines" => "",
                                "parent_repeater" => "field_65c232c87e9c1",
                            ],
                            [
                                "key" => "field_65c233237e9c4",
                                "label" => "Load",
                                "name" => "load",
                                "aria-label" => "",
                                "type" => "select",
                                "instructions" => "",
                                "required" => 0,
                                "conditional_logic" => [
                                    [
                                        [
                                            "field" => "field_65c232eb7e9c2",
                                            "operator" => "!=empty",
                                        ],
                                    ],
                                ],
                                "wrapper" => [
                                    "width" => "",
                                    "class" => "",
                                    "id" => "",
                                ],
                                "choices" => [
                                    "async" => "Async",
                                    "defer" => "Defer",
                                    "eager" => "Eager",
                                ],
                                "default_value" => "async",
                                "return_format" => "value",
                                "multiple" => 0,
                                "allow_null" => 0,
                                "ui" => 0,
                                "ajax" => 0,
                                "placeholder" => "",
                                "parent_repeater" => "field_65c232c87e9c1",
                            ],
                            [
                                "key" => "field_65c233fd0d664",
                                "label" => "Location",
                                "name" => "location",
                                "aria-label" => "",
                                "type" => "select",
                                "instructions" => "",
                                "required" => 0,
                                "conditional_logic" => [
                                    [
                                        [
                                            "field" => "field_65c233747e9c5",
                                            "operator" => "!=empty",
                                        ],
                                    ],
                                ],
                                "wrapper" => [
                                    "width" => "",
                                    "class" => "",
                                    "id" => "",
                                ],
                                "choices" => [
                                    "head" => "Head",
                                    "foot" => "Footer",
                                ],
                                "default_value" => "foot",
                                "return_format" => "value",
                                "multiple" => 0,
                                "allow_null" => 0,
                                "ui" => 0,
                                "ajax" => 0,
                                "placeholder" => "",
                                "parent_repeater" => "field_65c232c87e9c1",
                            ],
                        ],
                    ],
                ],
                "location" => [
                    [
                        [
                            "param" => "options_page",
                            "operator" => "==",
                            "value" => "scripts",
                        ],
                    ],
                ],
                "menu_order" => 0,
                "position" => "normal",
                "style" => "default",
                "label_placement" => "top",
                "instruction_placement" => "label",
                "hide_on_screen" => "",
                "active" => true,
                "description" => "",
                "show_in_rest" => 0,
            ]);
        });

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

    public function processScripts(): void
    {
        $scripts = get_field("scripts", "option");

        if (is_array($scripts)) {
            foreach ($scripts as $script) {
                if (!empty($script["src"])) {
                    // check if src snippet includes <script> tags and remove them, we just want the src attribute
                    $src = preg_replace(
                        "/<script.*?src=['\"](.*?)['\"].*?>.*?<\/script>/",
                        "$1",
                        $script["src"],
                    );

                    wp_enqueue_script(
                        handle: uniqid(),
                        src: $src,
                        deps: [],
                        ver: null,
                        args: [
                            "strategy" => $script["load"] ?? "defer",
                        ],
                    );
                }

                if (!empty($script["snippet"])) {
                    $uuid = uniqid();

                    wp_register_script(
                        handle: $uuid,
                        src: null,
                        deps: [],
                        ver: null,
                        args: [
                            "in_footer" => $script["location"] === "foot",
                        ],
                    );

                    wp_enqueue_script(
                        handle: $uuid,
                        src: null,
                        deps: [],
                        ver: null,
                        args: [
                            "in_footer" => $script["location"] === "foot",
                        ],
                    );

                    // check if snippet includes <script> tags and remove them
                    $snippet = preg_replace(
                        "/<script.*?>|<\/script>/",
                        "",
                        $script["snippet"],
                    );

                    wp_add_inline_script(
                        handle: $uuid,
                        data: $snippet,
                        position: "after",
                    );
                }
            }
        }
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
