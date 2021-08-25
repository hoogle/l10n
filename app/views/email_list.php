<div class="container">
    <div class="col-sm-12">
        <div class="btn-group pull-right">
            <!-- Button trigger modal -->
            <button class="btn btn-warning waves-effect waves-light" data-toggle="modal" data-target="#myExport"><i class="fa fa-share"></i> Export</button>
<?php if ($pf_modified) : ?>
            <b style="position: absolute; top: -8px; right: -8px; display: inline-block; width: 18px; height: 18px; border-radius: 50%; background-color: #ff5757; color:white; z-index:3; text-align:center; font-size: 9pt">!</b>
<?php endif ?>
        </div>
        <h4 class="page-title">Environment : <span id="platform"><?php echo $production; ?> / <?php echo $platform; ?></span></h4>
        <ol class="breadcrumb"></ol>
    </div>
     
    <div class="col-sm-12">
    </div>

<div id="myExport" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabelExport" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form name="exportIt" class="form-horizontal" role="form" action="/tool/export?p=<?php echo $production; ?>_<?php echo $platform; ?>" data-parsley-validate novalidate>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabelExport">Export translation</h4>
                </div>
                <div class="modal-body">
                    <h3>Make sure export translation to S3?</h3>
                    <div>
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
                        &nbsp;
                        <button type="submit" class="btn btn-danger waves-effect waves-light">Export</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
    <div class="col-sm-12">
        <div class="card-box table-responsive" id="mainTable">
<?php if ($email_data) : ?>
            <div class="row m-b-10" style="padding-bottom: 8px; font-weight: 600; vertical-align: bottom; border-bottom: 2px solid #ebeff2;">
                <div class="d-flex emailLanRow">
                    <div>Email category</div><div class="flex-fill">Subject</div><div style="flex: 0 1 190px;">Last update time</div><div style="flex: 0 1 190px;">Action</div>
                </div>
            </div>
            <div id="emalLanList">
<?php foreach ($email_data as $row) : ?>
    <div class="row m-b-10">
        <div class="d-flex justify-content-around align-item-center emailLanRow">

            <div><?php echo $row["item"]; ?></div>
            <div class="flex-fill">
                <?php echo $row["zh-TW"]; ?>
            </div>
            <div style="flex: 0 1 190px;"><?php echo $row["last_updated_at"] ?></div>
            <div class="d-flex" style="flex: 0 1 190px;">
                <div class="btn-group dropdown">
                    <button class="btn btn-success dropdown-toggle" type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown">
                    <span class="fa fa-chevron-circle-down"></span>
                    Preview
                    </button>
                        <ul class="dropdown-menu" style="">
    <?php foreach ($user_langs as $lang) : ?>
                            <li><a href="/email/preview?p=<?php echo $p; ?>&item=<?php echo $row["item"]; ?>&lang=<?php echo $lang; ?>"><?php echo $lang; ?></a></li>
    <?php endforeach ?>
                        </ul>
                </div>
                <button class="emailEditBtn btn btn-primary m-l-10" data-id="<?php echo $row["item"]; ?>"><i class="ti-pencil"></i> Edit</button>
            </div>
        </div>
    </div>
<?php endforeach ?>
</div>
<?php endif ?>
</div>
</div>
</div>

