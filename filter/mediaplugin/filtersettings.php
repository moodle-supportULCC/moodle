<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_mp3', get_string('mediapluginmp3','admin'), '', 1));

    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_swf', get_string('mediapluginswf','admin'), get_string('mediapluginswfnote','admin'), 0)); // sorry, this is a big potential security hole (skodak)

    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_mov', get_string('mediapluginmov','admin'), '', 1));

    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_wmv', get_string('mediapluginwmv','admin'), '', 1));

    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_mpg', get_string('mediapluginmpg','admin'), '', 1));

    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_avi', get_string('mediapluginavi','admin'), '', 1));

    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_flv', get_string('mediapluginflv','admin'), '', 1));

    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_ram', get_string('mediapluginram','admin'), '', 1));

    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_rpm', get_string('mediapluginrpm','admin'), '', 1));

    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_rm', get_string('mediapluginrm','admin'), '', 1));

    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_youtube', get_string('mediapluginyoutube','admin'), '', 1));

    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_ogg', get_string('mediapluginogg','admin'), '', 1));

    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_ogv', get_string('mediapluginogv','admin'), '', 1));

    $settings->add(new admin_setting_configcheckbox('filter_mediaplugin_enable_img', get_string('mediapluginimg','admin'), '', 1));
}
