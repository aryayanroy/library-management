$(document).ready(function(){
    var url = window.location.href;
        
    function load_data(){
        $.post(
            url,
            {action: "load-records"}
        ).done(function(data){
            var feedback = JSON.parse(data);
            $("#data-table tr:not(:first-child)").remove();
            if(feedback[0]==true){
                var rows = feedback[1];
                $("#records-count").html(rows.length);
                for(i=0; i<rows.length; i++){
                    var genre, sub_genre = "-", action = "-";
                    if(rows[i][2]==null){
                        genre = rows[i][1];
                    }else{
                        genre = rows[i][2];
                        sub_genre = rows[i][1];
                    }
                    if(rows[i][3]==true){
                        action = "<button type='button' class='btn btn-danger btn-sm delete-btn' value="+rows[i][0]+"><i class='fa-solid fa-trash'></i></button>";
                    }
                    $("#data-table").append("<tr><td class='text-center'>"+(i+1)+"</td><td>"+genre+"</td><td>"+sub_genre+"</td><td></td><td class='text-center'><button type='button' class='btn btn-primary btn-sm edit-btn' value='"+rows[i][0]+"' data-title='"+rows[i][1]+"'><i class='fa-solid fa-pen'></i></button></td><td class='text-center'>"+action+"</td></tr>");
                }
            }else{
                $("#data-table").append("<tr><td colspan='5' class='text-center'>No records found</td></tr>")
                $("#records-count").html(0);
            }
        })
    }
    
    load_data();

    function load_genre(){
        $("#parent-genre option:not(:first)").remove();
        $.post(
            url,
            {action: "load-genre"}
        ).done(function(data){
            var feedback = JSON.parse(data);
            if(feedback[0]==true){
                var rows = feedback[1];
                for(i=0; i<rows.length; i++){
                    $("#parent-genre").append("<option value='"+rows[i][0]+"'>"+rows[i][1]+"</option>");
                }
            }
        })
    }

    load_genre();
    
    $("#data-form").submit(function(e){
        e.preventDefault();
        $("#submit-data").prop("disabled", true).html("<i class='fas fa-spinner fa-pulse'></i>");
        var form_data = $(this).serializeArray();
        form_data.push({name: "action", value: "submit"});
        form_data = $.param(form_data);
        $.post(
            url,
            form_data
        ).done(function(data){
            var feedback = JSON.parse(data);
            if(feedback[0]==true){
                $("#data-form")[0].reset();
                $("#add-record").modal("hide");
            }
            alert(feedback[1]);
            load_data();
            load_genre();
        }).fail(function(){
            alert("Unexpected error");
        }).always(function(){
            $("#submit-data").prop("disabled", false).html("Add record");
        })
    })

    $(document).on("click", ".edit-btn", function(){
        var title = prompt("Enter the Genre title",$(this).data("title"));
        if(title){
            var id = $(this).val();
            title = $.trim(title);
            if(title!=""){
                $.post(
                url,
                {action: "edit-data", title: title, id: id}
                ).done(function(data){
                    var feedback = JSON.parse(data);
                    if(feedback[0]==true){
                        alert(feedback[1]);
                        load_data();
                    }
                }).fail(function(){
                    alert("Unexpected error");
                })
            }else{
                alert("Title cannot be empty");
            }
        }
    })
})