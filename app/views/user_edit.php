<div class="container">
        <form action="/user/update" method="post">
            Email:<br>
            <input type="text" name="email" disabled="disabled" value="<?php echo $user_data["email"]; ?>"><br>
            Using language:<br> 
<?php foreach ($lang_arr as $lang) : ?>
<?php   $show_checked = in_array($lang, json_decode($user_data["using_lang"], TRUE)) ? ' checked="checked"' : ""; ?>
            <input type="checkbox" name="using_lang[]" value="<?php echo $lang; ?>" <?php echo $show_checked; ?>/><?php echo $lang; ?>
<?php endforeach ?>
            <br>
            Last login at:<br>
            <input type="text" name="last_login_at" disabled="disabled" value="<?php echo $user_data["last_login_at"]; ?>"><br>
            <input type="hidden" name="email" value="<?php echo $user_data["email"]; ?>">
            <input type="submit" value="Save">
        </form>
</div>
