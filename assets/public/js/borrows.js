$(document).ready(function(){
    var url = window.location.href;
    var loading = "<i class='fas fa-circle-notch fa-spin fa-xl'></i>";

    function append(table, offset, book, borrower, issue, due, returned, id){
        var status, fine = "-", action;
        var overdue = moment(due).diff(moment(),"days");
        if(returned){
            status = "text-success'>Returned";
            returned = moment(returned).format("DD-MM-YYYY");
            action = "-";
        }else{
            if(overdue<0){
                status = "text-danger'>Late";
                fine = overdue*-5;
            }else{
                status = "'>Pending";
            }
            returned = "-";
            action = "<button type='button' class='btn btn-primary btn-sm checkin-btn' value="+id+"><i class='fa-solid fa-check'></i></button>";
        }
        issue = moment(issue).format("DD-MM-YYYY");
        due = moment(due).format("DD-MM-YYYY");
        table.append("<tr><td class='text-center'>"+(i+offset)+"</td><td class='text-nowrap'>"+book+"</td><td>"+borrower.toUpperCase()+"</td><td class='text-center text-nowrap'>"+issue+"</td><td class='text-center text-nowrap'>"+due+"</td><td class='text-center "+status+"</td><td class='text-center text-nowrap'>"+returned+"</td><td class='text-end'>"+fine+"</td><td class='text-center'>"+action+"</td></tr>");
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
            {action: "load-data", page: page},
        ).done(function(data){
            var feedback = JSON.parse(data);
            var total_records = feedback[2];
            var offset = (page-1)*25+1;
            var count = $("#records-count");
            if(feedback[0]==true){
                var rows = feedback[1];
                for(i=0; i<rows.length; i++){
                    var row = rows[i];
                    append(table, offset, row[1], row[2], row[3], row[4], row[5], row[0]);
                }
                count.html(total_records);
                var total_pages = Math.ceil(total_records/25);
                $("#pagination>*").remove();
                for(i=1; i<=total_pages; i++){
                    $("#pagination").append("<li class='page-item'><a href='#' class='page-link' data-page="+i+">"+i+"</a></li>");
                }
                $("[data-page='"+page+"']").addClass("active");
            }else{
                table.append("<tr><td colspan='9' class='text-center'>"+feedback[1]+"</td></tr>")
                count.html(0);
            }
        })
    }

    load_data();

    //pagination
    $(document).on("click", ".page-link", function(e){
        e.preventDefault();
        load_data($(this).data("page"));
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
                load_data();
            }
            alert(feedback[1]);
        })
    })

    $(document).on("click",".checkin-btn", function(){
        if(confirm("Do you want to check in this item?")){
            var id = $(this).val();
            $.post(
                url,
                {action: "update", id: id}
            ).done(function(data){
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
            $.post(
                url,
                {action: "search", search: search}
            ).done(function(data){
                var table = $("#data-table");
                table.find("tr:not(:first-child)").remove();
                $("#pagination>*").remove();
                var feedback = JSON.parse(data);
                if(feedback[0]==true){
                    var rows = feedback[1];
                    var count = rows.length;
                    for(i=0; i<count; i++){
                        var row = rows[i];
                        append(table, 1, row[1], row[2], row[3], row[4], row[5], row[0]);
                    }
                    $("#records-count").html(count);
                }else{
                    table.append("<tr><td colspan='9' class='text-center'>"+feedback[1]+"</td></tr>")
                    $("#records-count").html(0);
                }
            })
        }else{
            load_data();
        }
    })

})