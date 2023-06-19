$(document).ready(function(){
    
    var url = window.location.href;
    
    //Load data
    function load_data(page,search){
        if(!page){
            if($(".active[data-page]").data("page")){
                page = $(".active[data-page]").data("page");
            }else{
                page = 1;
            }
        }
        var rpp = 3;
        if(!search){
            search = "";
        }
        $.post(
            url,
            {action: "load-data", page: page, rpp: rpp, search: search}
        ).done(function(data){
            var feedback = JSON.parse(data);
            $("#data-table tr:not(:first-child)").remove();
            if(feedback[0]==true){
                var rows = feedback[1];
                var count = feedback[2];
                var pages = Math.ceil(count/rpp);
                var offset = (page - 1) * rpp + 1;
                $("#records-count").html(count);
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
                    $("#data-table").append("<tr><td class='text-center'>"+(i+offset)+"</td><td>"+genre+"</td><td>"+sub_genre+"</td><td></td><td class='text-center'><button type='button' class='btn btn-primary btn-sm edit-btn' value='"+rows[i][0]+"' data-title='"+rows[i][1]+"'><i class='fa-solid fa-pen'></i></button></td><td class='text-center'>"+action+"</td></tr>");
                }
                $("#pagination>*").remove();
                for(i=1; i<=pages; i++){
                    $("#pagination").append("<li class='page-item'><a href='#' class='page-link' data-page="+i+">"+i+"</a></li>");
                    $("[data-page='"+page+"']").addClass("active");
                }
            }else{
                $("#data-table").append("<tr><td colspan='5' class='text-center'>No records found</td></tr>")
                $("#records-count").html(0);
            }
        })
    }
    
    load_data();

    //Load Genre
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
    
    //Create
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

    //Edit
    $(document).on("click", ".edit-btn", function(){
        var title = prompt("Enter the Genre title",$(this).data("title"));
        if(title){
            var id = $(this).val();
            title = $.trim(title);
            if(title!=""){
                $.post(
                url,
                {action: "edit", title: title, id: id}
                ).done(function(data){
                    var feedback = JSON.parse(data);
                    alert(feedback[1]);
                    load_data();
                }).fail(function(){
                    alert("Unexpected error");
                })
            }else{
                alert("Title cannot be empty");
            }
        }
    })

    //Delete
    $(document).on("click", ".delete-btn", function(){
        if(confirm("Do you really want to delete this record? This will also delete the book records linked to this genere.")){
            var id = $(this).val();
            $.post(
                url,
                {action: "delete", id: id}
            ).done(function(data){
                var feedback = JSON.parse(data);
                alert(feedback[1]);
                load_data();
            }).fail(function(){
                alert("Unexpected error");
            })   
        }
    })

    //Pagination
    $(document).on("click", ".page-link", function(e){
        e.preventDefault();
        load_data($(this).data("page"), null);
    })

    //Search
    $("#search-form").submit(function(e){
        e.preventDefault();
        load_data(null, $("#search-field").val());
    })
})