<div class="container">
    <div class="col-sm-12">
        <div class="btn-group pull-right">
            <!-- Button trigger modal -->
        </div>
        <h4 class="page-title">
            Environment : <span id="platform"><?php echo $production; ?> / <?php echo $platform; ?></span>
        </h4>
        <ol class="breadcrumb"></ol>
    </div>
     
    <div class="col-sm-12">
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

