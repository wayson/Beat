<html>
    <header>
        <script src="js/jquery-1.11.3.min.js"></script>
        <script type="application/javascript">
            $(document).ready(function(){
                $('#part1Btn').click(function(){
                    var form1 = $('#part1Form');
                    $.post(form1.attr('action'), form1.serialize(), function(data){
                        $('#part1Result').html(data);
                    });
                });

                $('#part2Btn').click(function(){
                    var form2 = $('#part2Form');
                    $.post(form2.attr('action'), form2.serialize(), function(data){
                        $('#part2Result').html(data);
                    });
                });
            });
        </script>
    </header>
    <body>
        <h1>Part 1</h1>
        <form action="part1.php" method="post" id="part1Form">
            <textarea name="input_data" rows="10" cols="40"></textarea>
            <br/>
            <input type="button" value="Submit" id="part1Btn"/>
            <br/>
            <br/>
            <h3>Part 1 Result:</h3>
            <div id="part1Result"></div>
        </form>

        <br/>
        <br/>
        <h1>Part 2</h1>
        <form method="post" action="part2.php" id="part2Form">
            <input name="input_data"/>
            <br/>
            <br/>
            <input value="submit" type="button" id="part2Btn"/>
            <br/>
            <br/>
            <h3>Part 2 Result:</h3>
            <div id="part2Result"></div>
        </form>
    </body>
</html>