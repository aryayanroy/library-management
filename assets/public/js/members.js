$(document).ready(function(){
    var url = window.location.href;

    function append(table, offset, mid, name, reg, ren, id){
        table.append("<tr><td class='text-center'>"+(i+offset)+"</td><td>"+mid.toUpperCase()+"</td><td>"+name+"</td><td class='text-center text-nowrap'>"+reg+"</td><td class='text-center text-nowrap'>"+ren+"</td><td class='text-center'></td><td class='text-center'><button type='button' class='btn btn-primary btn-sm view-btn' value='"+id+"'><i class='fa-regular fa-eye'></i></button></td><td class='text-center'><button type='button' class='btn btn-primary btn-sm edit-btn' value='"+id+"'><i class='fa-solid fa-pen-to-square'></i></button></td><td class='text-center'><button type='button' class='btn btn-primary btn-sm renew-btn' value='"+id+"'><i class='fa-regular fa-calendar-plus'></i></button></td></tr>");
    }

    function load_data(page){
        if(!page){
            if($(".active[data-page]").data("page")){
                page = $(".active[data-page]").data("page");
            }else{
                page = 1;
            }
        }
        var table = $("#data-table");
        table.find("tr:not(:first-child)").remove();
        $.post(
            url,
            {action: "load-data", page: page}
        ).done(function(data){
            var feedback = JSON.parse(data);
            var total_records = feedback[2];
            var offset = (page-1)*25+1;
            if(feedback[0]==true){
                var rows = feedback[1];
                for(i=0; i<rows.length; i++){
                    var row = rows[i];
                    append(table, offset, row[1], row[2], row[3], row[4], row[0]);
                }
                $("#records-count").html(total_records);
                var total_pages = Math.ceil(total_records/25);
                $("#pagination>*").remove();
                for(i=1; i<=total_pages; i++){
                    $("#pagination").append("<li class='page-item'><a href='#' class='page-link' data-page="+i+">"+i+"</a></li>");
                }
                $("[data-page='"+page+"']").addClass("active");
            }else{
                table.append("<tr><td colspan='9' class='text-center'>"+feedback[1]+"</td></tr>")
                $("#records-count").html(0);
            }
        })
    }

    function data_modal(action, title, button){
        $(".action-title").html(title);
        $("#submit-btn").html(button).data("action",action);
        $("#data-modal").modal("show");
    }

    load_data();

    $("#insert-btn").click(function(){
        $("#data-form")[0].reset();
        data_modal("insert","New record","Add record");
    })

    $("#data-form").submit(function(e){
        e.preventDefault();
        var form_data = $(this).serializeArray();
        var element = $("#submit-btn");
        var action = element.data("action");
        form_data.push({name: "action", value: action});
        if(action=="update"){
            form_data.push({name: "id", value: element.data("id")});
        }
        form_data = $.param(form_data);
        $.post(
            url,
            form_data
        ).done(function(data){
            var feedback = JSON.parse(data);
            if(feedback[0]==true){
                $("#data-form")[0].reset();
                $("#data-modal").modal("hide");
                load_data();
            }
            alert(feedback[1]);
        })
    })
})