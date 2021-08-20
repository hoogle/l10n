<div class="container">
    <div class="col-sm-12">
        <div class="pull-right d-flex" style="position: relative;">
        </div>
        <h4 class="page-title">Environment : <span id="platform"><?php echo $production; ?> - Relationship for Android / iOS</span></h4>
        <ol class="breadcrumb"></ol>
    </div>
     
    <div class="col-sm-12">
        <div class="card-box table-responsive" id="mainTable">
            <input type="hidden" name="email" value="<?php echo $email; ?>">
            <div class="row m-b-20">
                <h4 class="m-t-0 header-title col-md-2 col-sm-12"><b>Key List</b></h4>
                <div class="col-md-6 col-sm-12 pull-right">
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
            <form name="addKey" class="form-horizontal" role="form" action="/index/add" data-parsley-validate novalidate>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabelAdd">New Key</h4>
            </div>
            <div class="modal-body">
            <input type="hidden" name="production" value="<?php echo $production; ?>">
            <input type="hidden" name="platform" value="<?php echo $platform; ?>">
            <input type="hidden" name="keyword" value="">
                <div class="form-group">
                    <label class="col-md-2 control-label">Description</label>
                    <div class="col-md-10">
                        <textarea name="d4str" class="form-control" rows="2"></textarea>
                    </div>
                </div>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
