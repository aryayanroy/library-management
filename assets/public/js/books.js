$(document).ready(function(){
    var url = window.location.href;
        
    function load_data(page, search){
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
            console.log(data);
            var feedback = JSON.parse(data);
            $("#data-table tr:not(:first-child)").remove();
            if(feedback[0]==true){
                console.log(feedback[3]);
                var rows = feedback[1];
                var count = feedback[2];
                var pages = Math.ceil(count/rpp);
                var offset = (page - 1) * rpp + 1;
                $("#records-count").html(count);
                for(i=0; i<rows.length; i++){
                    var row = rows[i];
                    $("#data-table").append("<tr><td class='text-center'>"+(i+offset)+"</td><td>"+row[1]+"</td><td>"+row[2]+"</td><td class='text-nowrap'>"+row[3]+"</td><td></td><td class='text-nowrap'>"+row[4]+"</td><td class='text-center'><button type='button' class='btn btn-primary btn-sm edit-btn' value='"+row[0]+"'><i class='fa-solid fa-pen'></i></button></td><td class='text-center'><button type='button' class='btn btn-danger btn-sm' value='"+row[0]+"'><i class='fa-solid fa-trash'></i></button></td></tr>");
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

    $("#data-form").submit(function(e){
        e.preventDefault();
        $("#submit-data").prop("disabled", true).html("<i class='fas fa-spinner fa-pulse'></i>");
        var id = $("#submit-data[data-id]").data("id");
        var type = $("#submit-data[data-type]").data("type");
        var form_data = $(this).serializeArray();
        if(type == "insert"){
            form_data.push({name: "action", value: "insert"});
        }else if(type == "update"){
            form_data.push({name: "action", value: "update"}, {name: "id", value: id});
        }
        form_data = $.param(form_data);
        $.post(
            url,
            form_data
        ).done(function(data){
            console.log(data);
            var feedback = JSON.parse(data);
            if(feedback[0]==true){
                $("#submit-modal").modal("hide");
            }
            alert(feedback[1]);
            load_data();
        }).fail(function(){
            alert("Unexpected error");
        }).always(function(){
            $("#submit-data").prop("disabled", false).html("Add record");
        })
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