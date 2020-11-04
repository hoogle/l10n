<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="page-title my-page-title"><?php echo $title; ?></h4>
        </div>
    </div>

    <!-- Forms -->
    <form role="form" id="passwdUpdateForm" data-parsley-validate>
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label for="exampleNickName">Email</label>
                            <span class="my-form-mustmark">*</span>
                            <input type="email" id="email" class="form-control" readonly="" value="<?php echo $email; ?>">
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label for="exampleNickName">New password</label>
                            <span class="my-form-mustmark">*</span>
                            <input type="password" class="form-control" id="passwd" name="passwd" required placeholder="New password" >
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label for="exampleNickName">Confirm password</label>
                            <span class="my-form-mustmark">*</span>
                            <input type="password" class="form-control" id="re-passwd" name="re-passwd" data-parsley-equalto="#passwd" placeholder="Confirm password" data-parsley-equalto-message="Confirm password not match">
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="col-md-12 my-button-row">
                            <a href="/l10n/translate" class="btn btn-white waves-effect waves-light">Cancel</a>
                            <button id="saveButton" type="submit" class="btn btn-primary waves-effect waves-light">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>
<!-- container -->
