<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
{{--<h1>欢迎登录</h1>--}}
</body>
</html>
<script src="js/jquery.js"></script>
<script>
    $(document).ready(function(){
        $.ajax({
            url:"come",
            method:"get",
            dataType:"json",
            success:function(data){
                if(data.code==40020){
                    alert(data.msg);
                    window.location.reload();
                }else if(data.code==40021){
                    alert(data.msg);
                    window.location.href="login";
                    window.location.reload();
                }
            }
        })
    })
</script>
