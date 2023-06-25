$(document).ready(function(){
    var url = window.location.href;
    var loading = "<i class='fas fa-circle-notch fa-spin fa-xl'></i>";

    function append(table, offset, genre, sub_genre, id){
        table.append("<tr><td class='text-center'>"+(i+offset)+"</td><td>"+genre+"</td><td>"+sub_genre+"</td><td></td><td class='text-center'><button type='button' class='btn btn-primary btn-sm edit-btn' value='"+id+"' data-title='"+genre+"'><i class='fa-solid fa-pen'></i></button></td><td class='text-center'><button type='button' class='btn btn-danger btn-sm delete-btn' value="+id+"><i class='fa-solid fa-trash'></i></button></td></tr>");
    }

    //load data
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
        table.append("<tr><td colspan='6' class='text-center'>"+loading+"</td></tr>");
        $.post(
            url,
            {action: "load-data", page: page},
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
                    if(row[1]==null){
                        row[1] = "-" ;
                    }
                    append(table, offset, row[0], row[1], row[2]);
                }
                $("#records-count").html(total_records);
                var total_pages = Math.ceil(total_records/25);
                var pages = "";
                for(i=1; i<=total_pages; i++){
                    pages += "<li class='page-item'><a href='#' class='page-link' data-page="+i+">"+i+"</a></li>";
                }
                $("#pagination").html(pages);
                $("[data-page='"+page+"']").addClass("active");
            }else{
                table.append("<tr><td colspan='5' class='text-center'>"+feedback[1]+"</td></tr>")
                $("#records-count").html(0);
            }
        })
    }

    load_data();

    //pagination
    $(document).on("click", ".page-link", function(e){
        e.preventDefault();
        load_data($(this).data("page"));
    })

    //insert modal
    $("#insert-btn").click(function(){
        $(".action-text").html("Add record");
        var element = $(this);
        var initial_text = element.html();
        element.prop("disabled", true).html(loading);
        $.post(
            url,
            {action: "load-genre"}
        ).always(function(){
            element.prop("disabled", false).html(initial_text);
        }).done(function(data){
            var select = $("#parent-genre");
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
            $("#data-modal").modal("show");
        })
    })

    //insert record
    $("#data-form").submit(function(e){
        e.preventDefault();
        var element = $("#submit-btn");
        var initial_text = element.html();
        element.prop("disabled", true).html(loading);
        var form_data = $(this).serializeArray();
        form_data.push({name: "action", value: "insert"});
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

    //edit record
    $(document).on("click",".edit-btn",function(){
        var title = prompt("Enter the Genre title", $(this).data("title"))
        if(title){
            title = $.trim(title);
            if(title!=""){
                var element = $(this);
                var initial_text = element.html();
                element.prop("disabled", true).html(loading);
                var id = element.val();
                $.post(
                    url,
                    {action: "edit", title: title, id: id}
                ).always(function(){
                    element.prop("disabled", false).html(initial_text);
                }).done(function(data){
                    var feedback = JSON.parse(data);
                    alert(feedback[1]);
                    load_data();
                })
            }else{
                alert("Title cannot be empty");
            }
        }
    })

    //delete record
    $(document).on("click",".delete-btn",function(){
        var element = $(this);
        var initial_text = element.html();
        var id = element.val();
        if(confirm("Are you sure want to delete the record? This will also delete it's subgenere.")){
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

    //search
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
            ).always(function(){
                element.prop("disabled", false).html(initial_text);
            }).done(function(data){
                var table = $("#data-table");
                table.find("tr:not(:first-child)").remove();
                $("#pagination>li").remove();
                var feedback = JSON.parse(data);
                if(feedback[0]==true){
                    var rows = feedback[1];
                    var count = rows.length;
                    for(i=0; i<count; i++){
                        var row = rows[i];
                        if(row[1]==null){
                            row[1] = "-" ;
                        }
                        append(table, 1, row[0], row[1], row[2]);
                    }
                    $("#records-count").html(count);
                }else{
                    table.append("<tr><td colspan='5' class='text-center'>"+feedback[1]+"</td></tr>")
                    $("#records-count").html(0);
                }
            })
        }else{
            load_data();
        }
    })
})