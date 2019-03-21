<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Dict List</title>
        <style type="text/css">
        body{
        padding:0px;
        margin:0px;
        font-size:12px;
        }
        body a{
        margin-left: 10px;
        color:#03515d;
        text-decoration:none;
        }
        body button{
        color:#03515d;
        }
        body span{
        color:#03515d;
        }
        .center_bottom input{
        color:#03515d;
        font-size:12px;
        height:20px;
        width:40px;
        padding:2px;
        vertical-align:middle;
        text-align:center;
        margin-bottom:6px;
        }
        /**************************布局部分********************/
        .table_div{
        width:1000px;
        padding:10px;
        margin:0 auto;
        }
        .div_clear{
            width: 100%;
        clear:both;
        }
        .left_top{
        height:30px;
        width:12px;
        float:left;
        }
        .left_center{
        height:400px;
        width:12px;
        float:left;
        }
        .left_bottom{
        height:35px;
        width:12px;
        float:left;
        }
        .center_top{
        height:30px;
        line-height:30px;
        width:900px;
        float:left;
        }
        .center_center{
            width: 99%;
        float:left;
        }
        .center_bottom{
        height:35px;
        width:900px;
        float:left;
        line-height:35px;
        }
        .right_top{
        height:30px;
        width:15px;
        float:left;
        }
        .right_center{
        height:400px;
        width:15px;
        float:left;
        }
        .right_bottom{
        height:35px;
        width:15px;
        float:left;
        }
        .table_content{
        width: 100%;
        margin:5px;
        border:1px solid #B5D6E6;
        overflow-x:hidden;
        overflow-y:auto;
        }
        .table_content table{
        width:100%;
        border:0;
        border-collapse:collapse;
        font-size:12px;
        }
        .table_content table tr:hover{
        background-color:#C1EBff;
        }
        .table_content table th{
        border-collapse:collapse;
        height:22px;
        border-right:1px solid #B5D6E6;
        border-bottom:1px solid #B5D6E6;
        }
        .table_content table td{
        height:30px;
        word-wrap:break-word;
        max-width:200px;
        vertical-align:middle;
        border-right:1px solid #B5D6E6;
        border-bottom:1px solid #B5D6E6;
        }
        .td-id {width: 5%}
        .td-title {width: 90}
        .table_content table td span {
            display:block;
            text-align: center;
        }
        </style>
    </head>

    <body>
        <div class="table_div">
            <div class="div_clear">
                <div class="center_center">
                    <div class="table_content">
                        <table cellspacing="0px" cellpadding="0px">
                            <thead >
                                <tr>
                                    <th width="5%">ID</th>
                                    <th wdith="90%">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{CONTENT}>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="right_center"></div>
            </div>

        </div>
    </body>
</html>