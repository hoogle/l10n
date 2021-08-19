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

<?php if ($email_data) : ?>
<table border=0 width="100%">
    <tr><td>Email category</td><td>Subject</td><td>Last update time</td><td>Action</td></tr>
<?php foreach ($email_data as $row) : ?>
    <tr>
        <td><?php echo $row["item"]; ?></td>
        <td>
            <?php echo $row["zh-TW"]; ?>
        </td>
        <td><?php echo $row["last_updated_at"] ?></td>
        <td>
            <button class="emailPreviewBtn btn btn-success" data-id="<?php echo $row["item"]; ?>"><i class="fa fa-tv"></i> Preview</button>
            <button class="emailEditBtn btn btn-primary" data-id="<?php echo $row["item"]; ?>"><i class="ti-pencil"></i> Edit</button>
        </td>
    </tr>
<?php endforeach ?>
</table>
<?php endif ?>

