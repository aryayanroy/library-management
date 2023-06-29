$(document).ready(function(){
    var url = window.location.href;

    function append(table, offset, mid, name, reg, ren, id){
        var status;
        if(moment(ren).diff(moment(reg),"days")>0){
            status = "text-success'>Active";
        }else{
            status = "text-danger'>Expired";
        }
        reg = moment(reg).format("DD-MM-YYYY");
        ren = moment(ren).format("DD-MM-YYYY");
        table.append("<tr><td class='text-center'>"+(i+offset)+"</td><td>"+mid.toUpperCase()+"</td><td>"+name+"</td><td class='text-center text-nowrap'>"+reg+"</td><td class='text-center text-nowrap'>"+ren+"</td><td class='text-center "+status+"</td><td class='text-center'><button type='button' class='btn btn-primary btn-sm view-btn' value='"+id+"'><i class='fa-regular fa-eye'></i></button></td><td class='text-center'><button type='button' class='btn btn-primary btn-sm edit-btn' value='"+id+"'><i class='fa-solid fa-pen-to-square'></i></button></td><td class='text-center'><button type='button' class='btn btn-primary btn-sm renew-btn' value='"+id+"'><i class='fa-regular fa-calendar-plus'></i></button></td></tr>");
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
            var count = $("#records-count");
            if(feedback[0]==true){
                var rows = feedback[1];
                for(i=0; i<rows.length; i++){
                    var row = rows[i];
                    append(table, offset, row[1], row[2], row[3], row[4], row[0]);
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

    function data_modal(action, title, button){
        $(".action-title").html(title);
        $("#submit-btn").html(button).data("action",action);
        $("#data-modal").modal("show");
    }

    load_data();

    //pagination
    $(document).on("click", ".page-link", function(e){
        e.preventDefault();
        load_data($(this).data("page"));
    })

    $("#insert-btn").click(function(){
        $("#data-form")[0].reset();
        data_modal("insert","New record","Add record");
    })

    $("#data-form").submit(function(e){
        e.preventDefault();
        var form_data = $(this).serializeArray();
        var element = $("#submit-btn")
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

    function data_modal(action, title, button){
        $(".action-title").html(title);
        $("#submit-btn").html(button).data("action",action);
        $("#data-modal").modal("show");
    }

    $(document).on("click", ".edit-btn", function(){
        var id = $(this).val();
        $.post(
            url,
            {action: "load-edit", id: id}
        ).done(function(data){
            var feedback = JSON.parse(data);
            if(feedback[0]==true){
                var data = feedback[1];
                data_modal("update","Update record","Update");
                $("#submit-btn").data("id",data[0]);
                $("#name").val(data[1]);
                $("#dob").val(data[2]);
                $("#phone").val(data[3]);
                $("#email").val(data[4]);
                $("#gender>option[value="+data[5]+"]").prop("selected", true);
                $("#address").val(data[6]);
            }else{
                alert(feedback[1]);
            }
        })
    })

    $(document).on("click", ".renew-btn", function(){
        $("#renew-btn").data("id",$(this).val());
        $("#renew-modal").modal("show");
    })

    $("#renew-form").submit(function(e){
        e.preventDefault();
        $.post(
            url,
            {action: "renew", id: $("#renew-btn").data("id"), months: $("#renew-months").val()}
        ).done(function(data){
            var feedback = JSON.parse(data);
            if(feedback[0]==true){
                $("#renew-form")[0].reset();
                $("#renew-modal").modal("hide");
                load_data();
            }
            alert(feedback[1]);
        })
    })

    $(document).on("click", ".view-btn", function(){
        var id = $(this).val();
        $.post(
            url,
            {action: "load-view", id: id}
        ).done(function(data){
            var feedback = JSON.parse(data);
            if(feedback[0]==true){
                var member = feedback[1];
                $("#view-id").html(member[0].toUpperCase());
                $("#view-name").html(member[1]);
                $("#view-dob").html(moment(member[2]).format("DD-MM-YYYY"));
                var gender;
                if(member[3]){
                    gender = "Male";
                }else{
                    gender = "Female";
                }
                $("#view-gender").html(gender);
                $("#view-reg").html(moment(member[4]).format("DD-MM-YYYY"));
                $("#view-ren").html(moment(member[5]).format("DD-MM-YYYY"));
                $("#view-phone").html(member[6]);
                $("#view-email").html(member[7]);
                $("#view-address").html(member[8]);
                $("#view-modal").modal("show");
            }else{
                alert(feedback[1]);
            }
        })
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
                        append(table, 1, row[1], row[2], row[3], row[4], row[0]);
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