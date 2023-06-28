$(document).ready(function(){
    var url = window.location.href;
    var loading = "<i class='fas fa-circle-notch fa-spin fa-xl'></i>";

    function append(table, offset, title, authors, isbn, call_number, id){
        table.append("<tr><td class='text-center'>"+(i+offset)+"</td><td>"+title+"</td><td>"+authors+"</td><td class='text-nowrap'>"+isbn+"</td><td></td><td class='text-nowrap'>"+call_number+"</td><td class='text-center'><button type='button' class='btn btn-primary btn-sm edit-btn' value='"+id+"'><i class='fa-solid fa-pen'></i></button></td><td class='text-center'><button type='button' class='btn btn-danger btn-sm delete-btn' value="+id+"><i class='fa-solid fa-trash'></i></button></td></tr>");
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
        table.append("<tr><td colspan='7' class='text-center'>"+loading+"</td></tr>");
        $.post(
            url,
            {action: "load-data", page: page}
        ).always(function(){
            table.find("tr:nth-child(2)").remove();
        }).done(function(data){
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
                table.append("<tr><td colspan='7' class='text-center'>"+feedback[1]+"</td></tr>")
                $("#records-count").html(0);
            }
        })
    }

    //data modal
    function data_modal(element, action, title, button, selected){
        var initial_text = element.html();
        element.prop("disabled", true).html(loading);
        $.post(
            url,
            {action: "load-genre"}
        ).always(function(){
            element.prop("disabled", false).html(initial_text);
        }).done(function(data){
            var feedback = JSON.parse(data);
            if(feedback[0]==true){
                var select = $("#genre");
                select.find("option").remove();
                if(action=="insert"){
                    select.append("<option value=''>-select-</option>");
                }
                var rows = feedback[1];
                for(i=0; i<rows.length; i++){
                    var row = rows[i];
                    var option = "<option value='"+row[0]+"'>"+row[1]+"</option>";
                    select.append(option);
                }
                $("#genre>option[value="+selected+"]").prop("selected", true);
                $(".action-title").html(title);
                $("#submit-btn").html(button).data("action",action);
                $("#data-modal").modal("show");
            }else{
                alert(feedback[1]);
            }
        })
    }

    load_data();
    
    //pagination
    $(document).on("click", ".page-link", function(e){
        e.preventDefault();
        load_data($(this).data("page"));
    })

    $("#insert-btn").click(function(){
        $("#data-form")[0].reset();
        data_modal($(this), "insert","New record","Add record");
    })

    $(document).on("click",".edit-btn", function(){
        var element = $(this);
        var initial_text = element.html();
        element.prop("disabled", true).html(loading);
        var id = element.val();
        $.post(
            url,
            {action: "load-edit", id: id}
        ).always(function(){
            element.prop("disabled", false).html(initial_text);
        }).done(function(data){
            var feedback = JSON.parse(data);
            if(feedback[0]==true){
                data = feedback[1]
                data_modal(element, "update","Edit record","Update", data[4]);
                $("#submit-btn").data("id",data[0]);
                $("#isbn").val(data[3]);
                $("#title").val(data[1]);
                $("#authors").val(data[2]);
            }else{
                alert(feedback[1]);
            }
        })
    })

    $("#data-form").submit(function(e){
        e.preventDefault();
        var element= $("#submit-btn");
        var initial_text = element.html();
        element.prop("disabled", true).html(loading);
        var action = element.data("action");
        var form_data = $(this).serializeArray();
        form_data.push({name: "action", value: action});
        if(action=="update"){
            form_data.push({name: "id", value: element.data("id")});
        }
        form_data = $.param(form_data);
        $.post(
            url,
            form_data
        ).always(function(){
            element.prop("disabled", false).html(initial_text);
        }).done(function(data){
            var feedback = JSON.parse(data);
            if(feedback[0]==true){
                $("#data-form")[0].reset();
                $("#data-modal").modal("hide");
                load_data();
            }
            alert(feedback[1]);
        })
    })

    //delete record
    $(document).on("click",".delete-btn",function(){
        var element = $(this);
        var initial_text = element.html();
        var id = element.val();
        if(confirm("Are you sure want to delete the record? This will also delete it's all related records.")){
            element.prop("disabled", true).html(loading);
            $.post(
                url,
                {action: "delete", id: id}
            ).always(function(){
                element.prop("disabled", false).html(initial_text);
            }).done(function(data){
                var feedback = JSON.parse(data);
                alert(feedback[1]);
                if(feedback[0]==true){
                    load_data();
                }
            })
        }
    })

    $("#search-form").submit(function(e){
        e.preventDefault();
        var search = $.trim($("#search-field").val());
        if(search!=""){
            var element = $("#search-btn");
            var initial_text = element.html();
            element.prop("disabled", true).html(loading);
            $.post(
                url,
                {action: "search", search: search}
            ).done(function(data){
                var table = $("#data-table");
                table.find("tr:not(:first-child)").remove();
                var feedback = JSON.parse(data);
                if(feedback[0]==true){
                    var rows = feedback[1];
                    var count = rows.length;
                    for(i=0; i<count; i++){
                        var row = rows[i];
                        append(table, 1, row[1], row[2], row[3], row[4], row[0]);
                    }
                    $("#records-count").html(count);
                }else{
                    table.append("<tr><td colspan='7' class='text-center'>"+feedback[1]+"</td></tr>")
                    $("#records-count").html(0);
                }
            }).always(function(){
                element.prop("disabled", false).html(initial_text);
            })
        }else{
            load_data();
        }
    })
    
})