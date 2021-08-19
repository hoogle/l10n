<div class="container">
    <div class="col-sm-12">
        <h4 class="page-title">User Profile</h4>
        <ol class="breadcrumb"></ol>
    </div>
    <div class="col-sm-12">
        <div class="card card-box" id="mainTable" style="width: 48rem;">
            <div class="card-body">
                <div class="form-group">
                    <label for="emailField"> Email:</label>
                    <input id="emailField" class="form-control" type="text" name="email" disabled="disabled" value="<?php echo $user_data["email"]; ?>">
                </div>
                <div class="form-group">
                    <label for="loginField"> Last login at:</label>
                    <input id="loginField" type="text" class="form-control" name="last_login_at" disabled="disabled" value="<?php echo $user_data["last_login_at"]; ?>">
                </div>
                <form id="usingLanForm" action="/user/update" method="post">
                    <div class="form-group">
                        <label>Using language:</label>
                        <div class="d-flex" role="group" aria-label="Basic checkbox toggle button group">
<?php foreach ($lang_arr as $lang) : ?>
<?php   $show_checked = in_array($lang, json_decode($user_data["using_lang"], TRUE)) ? ' checked="checked"' : ""; ?>
                            <input type="checkbox" id="<?php echo $lang; ?>" class="btn-check" name="using_lang[]" value="<?php echo $lang; ?>" <?php echo $show_checked; ?>/>
                            <label class="btn btn-outline-primary flex-fill m-r-5" for="<?php echo $lang; ?>" ><?php echo $lang; ?></label>
<?php endforeach ?>
                        </div>
                    </div>
                    <input type="hidden" name="email" value="<?php echo $user_data["email"]; ?>">
                </form>
            </div>
        </div>
    </div>
</div>
