function data_modal(title, button, select, id){
    $.post(
        url,
        {action: "load-genre"}
    ).done(function(data){
        var select = $("#genre");
        select.html("<option value='' selected>None</option>");
        var feedback = JSON.parse(data);
        if(feedback[0]==true){
            var rows = feedback[1];
            for(i=0; i<rows.length; i++){
                var row = rows[i];
                var option = "<option value='"+row[0]+"'";
                if(select && select==row[0]){
                    option += "selected";
                }
                option += ">"+row[1]+"</option>";
                select.append(option);
            }
        }
        $(".action-title").html(title);
        $("#submit-btn").html(button);
        $("#data-modal").modal("show");
    })
}

function load_data(){
    var table = $("#data-table");
    table.find("tr:not(:first-child)").remove();
    $.post(
        url,
        {action: "load-data"}
    ).done(function(data){
        var feedback = JSON.parse(data);
        if(feedback[0]==true){
            var rows = feedback[1];
            for(i=0; i<rows.length; i++){
                var row = rows[i];
                table.append("<tr><td class='text-center'>"+(i+1)+"</td><td>"+row[1]+"</td><td>"+row[2]+"</td><td class='text-nowrap'>"+row[3]+"</td><td></td><td class='text-nowrap'>"+row[4]+"</td><td class='text-center'><button type='button' class='btn btn-primary btn-sm edit-btn' value='"+row[0]+"'><i class='fa-solid fa-pen'></i></button></td><td class='text-center'><button type='button' class='btn btn-danger btn-sm delete-btn' value="+row[0]+"><i class='fa-solid fa-trash'></i></button></td></tr>");
            }
        }else{
            alert(feedback[1]);
        }
    })
}

load_data();

$("#insert-btn").click(function(){
    $.post(
        url,
        {action: "load-genre"}
    ).done(function(data){
        var select = $("#genre");
        select.html("<option value='0'>None</option>");
        var feedback = JSON.parse(data);
        if(feedback[0]==true){
            var rows = feedback[1];
            for(i=0; i<rows.length; i++){
                var row = rows[i];
                var option = "<option value='"+row[0]+"'";
                if(select && select==row[0]){
                    option += "selected";
                }
                option += ">"+row[1]+"</option>";
                select.append(option);
            }
        }
        data_modal("New Book record","Add record");
    })
})

$("#data-form").submit(function(e){
    e.preventDefault();
    var form_data = $(this).serializeArray();
    form_data.push({name: "action", value: "insert"});
    form_data = $.param(form_data);
    $.post(
        url,
        form_data
    ).done(function(data){
        var feedback = JSON.parse(data);
        if(feedback[0]==true){
            $("#data-form")[0].reset();
            $("#data-modal").modal("hide");
        }
        alert(feedback[1]);
    })
})

$(document).on("click", ".edit-btn", function(){
    data_modal("Update Book record","Update record");
})