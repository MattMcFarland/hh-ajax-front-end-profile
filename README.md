# WP Front End Ajax Profile Editor 0.1 #
HTML5 Front end Profile editor Plugin for WP users, designed for hvac-hacks.com but am sharing with the world.
*A super light weight plugin that lets your users edit their profile in an intuitive way.*
By Matt McFarland


> NOTE:
> I made this plugin mainly for my own project, so this plugin is not for newbies.  It takes a lot of time and effort to create a plugin that has
all the necessary things front-end only people love. Unfortunately I don't have time.
If you dont enjoy coding, then this plugin is not for you.

# Table of Contents #
1.  Features
    1.  UI Features
    2.  Backend Features
2.  Prerequisites
    1.  Dependencies
    2.  Knowledge Prerequisites
3.  Usage
    1.  Adding Form to Site
    2.  Adding Custom Fields
4.  Troubleshooting

___

##  1. Features ##

### 1.1. UI Features ###
*   Click on the field you wish to edit, once you hit enter or the save button it you're done.
*   Completely responsive, works on any device.
*   Profile Photos:
    * Gravatar photos are converted into local ones without anyone noticing.
    * Users can upload and crop their own photo.
    * You can reset the photos at any time in the users section of the wordpress dashboard

### 1.2. Backend Features ###
*   Add the profile editor with shortcode or html.
*   Uses jQuery in NO-Conflict Mode to avoid compatibility issues
*   Traditional form submit fall-back if ajax fails.
*   Sweet PHP function that allows for easy use of adding more user_meta

___

## 2. Prerequisites ##

### 2.1. Dependencies ###
*   Wordpress 3.8.1
*   Twitter Bootstrap 3.x Components
    * Grid CSS
    * Alert CSS
    * Buttons CSS
*   Font Awesome 4.x
*   jQuery 10.x

> NOTE:
> This plugin is not compatable with buddypress.

### 2.2. Knowledge Prerequisites ###
*   Intermediate Understanding of the Wordpress Codex
*   Basic Understanding of PHP

___

## 3. Usage ##

### 3.1. Adding Form to Site ###
1.  Create a blank page in wordpress
2.  Insert the following shortcode:
    *   `[display_profile_editor]`
3.  OR Insert the following HTML:
    *   `<div onload="hh_profile_edit_ajax_load_template()" id="profile-editor-container"></div>`

### 3.2. Adding Custom User Meta ###
Wordpress comes out of the box with giving room for custom fields!
To add custom fields, simply add this function to your functions.php file:

> NOTE:
> You'll want to read up on wp_user_meta if you're confused.

```PHP
    function my_hh_custom_fields() {
         /**
          * hh_generate_text_field(Form Label, meta_key);
          */
         hh_generate_text_field('Company','user_company');
         hh_generate_text_field('Job Title','user_job_title');
         hh_generate_text_field('Certifications','user_certifications');
         hh_generate_text_field('Education','user_education');
         hh_generate_text_field('Bio','description');
    }
    add_action('hh_add_form_fields','my_hh_custom_fields');
```

---

## 4. Troubleshooting ##

### 4.1. Profile Picture Errors ###

If you are getting any errors, the most common culprit is the profile picture.  This is because it is using data
specific to my server.  Follow the instructions in the comments at templates/profile-pic.php (line 18)