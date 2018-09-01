<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:53:"themes/admin_simpleboot3/admin\agency\agency_add.html";i:1533026289;s:81:"C:\xampp\htdocs\smart_property\public\themes\admin_simpleboot3\public\header.html";i:1529914518;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->


    <link href="/themes/admin_simpleboot3/public/assets/themes/<?php echo cmf_get_admin_style(); ?>/bootstrap.min.css" rel="stylesheet">
    <link href="/themes/admin_simpleboot3/public/assets/simpleboot3/css/simplebootadmin.css" rel="stylesheet">
    <link href="/static/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]>
    <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        form .input-order {
            margin-bottom: 0px;
            padding: 0 2px;
            width: 42px;
            font-size: 12px;
        }

        form .input-order:focus {
            outline: none;
        }

        .table-actions {
            margin-top: 5px;
            margin-bottom: 5px;
            padding: 0px;
        }

        .table-list {
            margin-bottom: 0px;
        }

        .form-required {
            color: red;
        }
    </style>
    <script type="text/javascript">
        //全局变量
        var GV = {
            ROOT: "/",
            WEB_ROOT: "/",
            JS_ROOT: "static/js/",
            APP: '<?php echo \think\Request::instance()->module(); ?>'/*当前应用名*/
        };
    </script>
    <script src="/themes/admin_simpleboot3/public/assets/js/jquery-1.10.2.min.js"></script>
    <script src="/static/js/wind.js"></script>
    <script src="/themes/admin_simpleboot3/public/assets/js/bootstrap.min.js"></script>
    <script>
        Wind.css('artDialog');
        Wind.css('layer');
        $(function () {
            $("[data-toggle='tooltip']").tooltip({
                container:'body',
                html:true,
            });
            $("li.dropdown").hover(function () {
                $(this).addClass("open");
            }, function () {
                $(this).removeClass("open");
            });
        });
    </script>
    <?php if(APP_DEBUG): ?>
        <style>
            #think_page_trace_open {
                z-index: 9999;
            }
        </style>
    <?php endif; ?>
</head>
<body>
<div class="wrap js-check-wrap">
    <ul class="nav nav-tabs">
        <li><a href="<?php echo url('agency/index'); ?>">机构列表</a></li>
        <li class="active"><a >添加机构</a></li>
    </ul>
    <form class="form-horizontal js-ajax-form margin-top-20" action="<?php echo url('agency/addPost'); ?>" method="post">
        <div class="form-group">
            <label for="input-name" class="col-sm-2 control-label"><span class="form-required">*</span>机构名称</label>
            <div class="col-md-6 col-sm-10">
                <input type="text" class="form-control" id="input-name" name="agency_name">
            </div>
        </div>
        <div class="form-group">
            <label for="input-remark" class="col-sm-2 control-label">机构描述</label>
            <div class="col-md-6 col-sm-10">
                <textarea type="text" class="form-control" id="input-remark" name="intro"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">联系方式</label>
            <div class="col-md-6 col-sm-10">
                <input type="text" class="form-control"  name="contact_method">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">小程序appId</label>
            <div class="col-md-6 col-sm-10">
                <input type="text" class="form-control"  name="wxapp_appid">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">小程序appSecret</label>
            <div class="col-md-6 col-sm-10">
                <input type="text" class="form-control"  name="wxapp_app_secret">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">状态</label>
            <div class="col-md-6 col-sm-10">
                <label class="radio-inline">
                    <input type="radio" name="is_using" value="1" checked="checked" > 开启
                </label>
                <label class="radio-inline">
                    <input type="radio" name="is_using" value="0"> 禁用
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary js-ajax-submit"><?php echo lang('ADD'); ?></button>
            </div>
        </div>
    </form>
</div>
<script src="/static/js/admin.js"></script>
</body>
</html>