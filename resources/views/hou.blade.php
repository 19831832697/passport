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
<table border="1">
    <tr>
        <td>id</td>
        <td>用户名</td>
        <td>token</td>
        <td>过期时间</td>
    </tr>
    @foreach($dataInfo as $k=>$v)
    <tr>
        <td>{{$v->user_id}}</td>
        <td>{{$v->user_name}}</td>
        <td>{{$v->token}}</td>
        <td><input type="text" id="time"></td>
    </tr>
        @endforeach
</table>
</body>
</html>
<script src="js/jquery.js"></script>
<script>
    $(document).ready(function(){
        $(document).on('blur','#time',function(){
            var time=$("#time").val();
            $.ajax({
                url:"usertime",
                method:"post",
                data:{time:time},
                datatype:"json",
                success:function(data){
                    if(data.code==1){
                        alert('设置成功');
                    }
                }
            })
        })

    })
</script>