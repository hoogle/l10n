<div class="container">
    <div class="col-sm-12">
        <div class="pull-right d-flex" style="position: relative;">
            <!-- Button trigger modal -->
            <button class="btn btn-primary waves-effect waves-light m-r-10" id="NewKeyBtn" data-toggle="modal" data-target="#myAdd"><i class="fa fa-plus"></i> New Key</button>
            <div>
<?php if ($pf_modified) : ?>
            <b style="position: absolute; top: -8px; right: -8px; display: inline-block; width: 18px; height: 18px; border-radius: 50%; background-color: #ff5757; color:white; z-index:3; text-align:center; font-size: 9pt">!</b>
<?php endif ?>
<?php if (isset($s3_link)) : ?>
            <button class="btn btn-warning waves-effect waves-light" data-toggle="modal" data-target="#myExport"><i class="fa fa-share"></i> Export</button>
<?php else : ?>
            <button class="btn btn-success waves-effect waves-light" id="downloadBtn"><i class="fa fa-download"></i> Download</button>
<?php endif ?>
            </div>
        </div>
        <h4 class="page-title">Environment : <span id="platform"><?php echo $production; ?> / <?php echo $platform; ?></span></h4>
        <ol class="breadcrumb"></ol>
    </div>
     
    <div class="col-sm-12">
        <div class="card-box table-responsive" id="mainTable">
            <input type="hidden" name="email" value="<?php echo $email; ?>">
            <div class="row m-b-20">
                <h4 class="m-t-0 header-title col-md-2 col-sm-12"><b>Key List</b></h4>
<?php if (isset($s3_link)) : ?>
                <div class="col-md-10 col-sm-12 text-right">
                    <b style="color: #beaf5f">Export to S3 url: </b>
                    <i id="copy-url" style="font-size:13px;cursor:pointer;" class="fa" title="Copy S3 url to clipboard">&#xf0c5;</i>
                    <a id="s3-url" target="_blank" href="<?php echo $s3_link . "?t=" . time(); ?>"><?php echo $s3_link; ?></a>
                    <i style="font-size:9px" class="fa">&#xf064;</i>
                </div>
<?php endif ?>
            </div>
            <div class="row">
                <div class="col-xs-6 m-t-10">
                <button type="button" class="btn btn-white btn-sm ti-eye btn-rounded" id="showKeyBtn"> Key</button>
                </div>
                <div class="col-xs-6 pull-right">
                    <form name="searchTranslate" class="form-horizontal" method="post">
                    <div class="form-group m-r-10">
                        <label class="col-md-4 control-label">Search:</label>
                        <div class="col-md-8">
                            <input type="search" name="key" class="form-control input-sm" placeholder="">
                        </div>
                    </div>
                    </form>
                </div>
            </div>
            <div class="p-10">
                <div class="row m-b-10" style="padding: 8px; font-weight: 600; vertical-align: bottom; border-bottom: 2px solid #ebeff2;">
                    <div class="d-flex justify-content-around" id="mainColGroup">
                        <div id="col_id" class="text-center ti- idCol">ID</div>
                        <div id="col_keyword" class="text-center ti- keyCol">KEY</div>
<?php foreach ($user_langs as $lang) : ?>
                        <div id="col_<?php echo $lang; ?>" class="text-center ti-"><i class="hideColBtn ti ti-shift-left"></i><?php echo $lang; ?><i class="showColBtn ti ti-shift-right"></i></div>
<?php endforeach ?>
                        <div id="col_ui_key" class="text-center ti-"><i class="hideColBtn ti ti-shift-left"></i>UI Key<i class="showColBtn ti ti-shift-right"></i></div>
                        <div class="hide text-white translateSubmit">.</div>
                        <div class="" style="flex: 0 0 24px;"></div>
                    </div>
                </div>
                <div id="translateList" class="col-sm-12"></div>
            </div>
            <div id="pagination" class="text-center"></div>
        </div>
    </div>
</div>

<template id="transListRowTemp">
    <div class="row translateRow m-b-10">
        <form name="l10n_{id}" class="d-flex align-item-center">
            <input type="hidden" name="platform" value="<?php echo $platform; ?>"/>
            <div class="idCol">{id}</div>
            <div class="translateCol keyCol">
                <input type="hidden" name="id" value="{id}">
                <textarea class="form-control text-center" name="keyword" title="{keyword}" readonly>{keyword}</textarea>
            </div>
<?php foreach ($user_langs as $lang) : ?>
<?php   $input_name = strtolower(str_replace("-", "", $lang)); ?>
            <div class="translateCol edit_<?php echo $lang; ?>">
                <textarea class="form-control" name="<?php echo $input_name; ?>" title="{<?php echo $lang; ?>}" readonly>{<?php echo $lang; ?>}</textarea>
            </div>
<?php endforeach ?>
            <div class="translateCol edit_ui_key">
                <textarea class="form-control" name="uikey" title="{ui_key}" readonly>{ui_key}</textarea>
            </div>
            <div class="urlCol">
                <input type="hidden" value="{url}" />
                <i class="ti ti-sharethis-alt" title="Copy url here" style="font-size: 2rem;"></i>
            </div>
            <div class="translateSubmit hide">
                <button type="submit" class="btn btn-default waves-effect waves-light btn-md">Save</button>
            </div>
        </form>
    </div>
</template>

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

<div id="myAdd" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabelAdd" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form name="addKey" class="form-horizontal" role="form" action="/trans/add" data-parsley-validate novalidate>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabelAdd">New Key</h4>
            </div>
            <div class="modal-body">
            <input type="hidden" name="production" value="<?php echo $production; ?>">
            <input type="hidden" name="platform" value="<?php echo $platform; ?>">
            <input type="hidden" name="keyword" value="">
                <div class="form-group">
                    <label class="col-md-2 control-label">en-US</label>
                    <div class="col-md-10">
                        <textarea name="enus" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ja-JP</label>
                    <div class="col-md-10">
                        <textarea name="jajp" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">zh-TW</label>
                    <div class="col-md-10">
                        <textarea name="zhtw" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">id-ID</label>
                    <div class="col-md-10">
                        <textarea name="idid" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ms-MY</label>
                    <div class="col-md-10">
                        <textarea name="msmy" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">UI Key</label>
                    <div class="col-md-10">
                        <textarea name="uikey" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
