<?php

function enable_product_filter() {
    ?>
    <input name="enable_product_filter" type="checkbox" value="1" <?php echo get_option("enable_product_filter") ? 'checked' : ''; ?>>

    <?php
}

function delete_plugin_settings() {
    ?>
    <input name="delete_settings" type="checkbox" value="1" <?php echo get_option("delete_settings") ? 'checked' : ''; ?>>
    <?php
}

function display_wpf_panel_fields() {
    $logger = new WooProductFilter();

    add_settings_section("wpf-settings-group", "Product Filter Settings", null, "wpf-plugin-options");

    add_settings_field("enable_product_filter", "Enable Product Filters", "enable_product_filter", "wpf-plugin-options", "wpf-settings-group");

    add_settings_field("delete_settings", "Delete Settings on Plugin Deactivation", "delete_plugin_settings", "wpf-plugin-options", "wpf-settings-group");

    //add_settings_field("logger_carving_form", "Enable carving form<br><span style='font-style:italic;font-weight: 500;'>Craving is enabled as log option if checked.</span>", "logger_carving_form", "nl-plugin-options", "nl-settings-group");

    //add_settings_field("enable_view_all", "Enable View all<br><span style='font-style:italic;font-weight: 500;'>Enable View All link in log form if checked.</span>", "enable_view_all", "nl-plugin-options", "nl-settings-group");
    //add_settings_field("view_all_page", "Select page<br><span style='font-style:italic;font-weight: 500;'>Enabled notebook logger on selected page if checked.</span>", "view_all_page", "nl-plugin-options", "nl-settings-group");
    //add_settings_field("view_all_custom_link", "View all custom link<br><span style='font-style:italic;font-weight: 500;'>Add custom link for view all entries option where you want to see all entries.</span>", "view_all_custom_link", "nl-plugin-options", "nl-settings-group");

    register_setting("wpf-options", "enable_product_filter");
    register_setting("wpf-options", "delete_settings");
    //register_setting("nl-options", "logger_carving_form");
    //register_setting("nl-options", "enable_view_all");
    //register_setting("nl-options", "view_all_page");
    //register_setting("nl-options", "view_all_custom_link");

}

add_action("admin_init", "display_wpf_panel_fields");