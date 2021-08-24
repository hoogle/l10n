<div class="container">
    <div class="col-sm-12">
        <h4 class="page-title">Environment : <span id="platform"><?php echo $production; ?> / <?php echo $platform; ?></span></h4>
        <ol class="breadcrumb"></ol>
    </div>
    <div class="col-sm-12">
        <div class="panel">
            <ul class="nav nav-tabs tabs" id="emailLanTab" role="tablist">
<?php $i=0; foreach ($email_contents as $lang => $sections) : $i++;?>
                <li class="nav-item tab <?php echo $i===1 ? 'active' : ''; ?>" role="presentation">
                    
                    <a class="nav-link" id="<?php echo $lang .'-tab'; ?>" data-toggle="tab" href="<?php echo '#' . $lang; ?>" role="tab" aria-controls="<?php echo $lang; ?>" aria-selected="true">
                   <i class="ti-pencil-alt"></i>
                   <?php echo $lang; ?></a>
                </li>
<?php endforeach ?>
            </ul>
            <div class="tab-content" id="emailLanTabContent">
<?php $i=0; foreach ($email_contents as $lang => $sections) : $i++;?>
            <div class="tab-pane fade  <?php echo $i==1 ? 'active in' : ''; ?>" id="<?php echo $lang; ?>" role="tabpanel" aria-labelledby="<?php echo $lang .'-tab'; ?>">
<?php   foreach ($sections as $sec => $contents) : ?>
                <form class="m-b-20" method="post" action="/email/update" name="emailLanForm">
                    <label class="m-b-10" for="<?php echo $lang . '_field' ?>"><?php echo $sec; ?>:</label>
                    <textarea class="form-control" cols=100 rows=5 name="contents"><?php echo $contents["val"]; ?></textarea>
                    <input type="hidden" name="prod_plat" value="<?php echo $production . "_" . $platform; ?>">
                    <input type="hidden" name="id" value="<?php echo $contents["id"]; ?>">
                    <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                    <input type="hidden" disabled name="orVal" value="<?php echo $contents["val"]; ?>">
                </form>
<?php   endforeach ?>
            </div>
<?php endforeach ?>
</div>
        </div>
    </div>
</div>



</div>
