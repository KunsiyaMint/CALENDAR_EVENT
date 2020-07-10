<!DOCTYPE html>
<html>

<head>
    <title>Calendar Event</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>



    <link rel="stylesheet" href="fullcalendar/fullcalendar.min.css" />
    <script src="fullcalendar/lib/jquery.min.js"></script>
    <script src="fullcalendar/lib/moment.min.js"></script>
    <script src="fullcalendar/fullcalendar.min.js"></script>
    <script>
    $(document).ready(function() {
        var cal0 = $('#calendar1');
        var cal2 = $('#calendar3');
        var cal1 = $('#calendarPresent');

        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();

        cal1.fullCalendar({
            header: {
                left: 'title',
                center: '',
                right: ''
            },
            defaultDate: moment().format("YYYY-MM-DD"),
            navLinks: true, // can click day/week names to navigate views
            //eventLimit: true, // allow "more" link when too many eventss
            editable: true,
            events: "fetch-event.php",
            displayEventTime: false,
            eventRender: function(event, element, view) {
                if (event.allDay === 'true') {
                    event.allDay = true;
                } else {
                    event.allDay = false;
                }
            },
            selectable: true,
            selectHelper: true,
            eventDrop: function (event, delta) {
                    //var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                    var start = null;
                    var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
                    $.ajax({
                        url: 'edit-event.php',
                        data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id + '&f1=' + event.f1 + '&m1=' + event.m1 + '&b1=' + event.b1,
                        type: "POST",
                        success: function (response) {
                            if(response.length == 8){
                                alert("Holiday is not Finishday");
                                setTimeout((function() {
                                   window.location.reload();
                                }), 300);
                            }else{
                                displayMessage("Updated Successfully");
                            }
                            // if(response == ''){
                            //     alert("Holiday is not Finishday");
                            // }else{
                            //     console.log("NO");
                            // }
                            // if(response == ""){
                            //     alert("Holiday is not Finishday");
                            //     window.location.reload();
                            //     setTimeout((function() {
                            //        window.location.reload();
                            //     }), 300);
                            // }else{
                            //     displayMessage("Updated Successfully");
                            // }
                            // setTimeout((function() {
                            // window.location.reload();
                            // }), 300);
                            //displayMessage("Updated Successfully");
                        }
                    });
                }
        });
        
        // <div class="row">
        // <div class="col-6 col-md-6"></div>
        // </div>


        $("#calendarPresent .fc-left").append(
            '<div class="col-sm-2 col-md-2 clear-pad"><select class="select_month form-control" style="padding-right: 0px;"><option value="">Month</option><option value="1">Jan</option><option value="2">Feb</option><option value="3">March</option><option value="4">April</option><option value="5">May</option><option value="6">June</option><option value="7">July</option><option value="8">Aug</option><option value="9">Sep</option><option value="10">Oct</option><option value="11">Nov</option><option value="12">Dec</option></select></div>'
        );
        $("#calendarPresent .fc-left").append(
            '<div class="col-sm-2 col-md-2 clear-pad"><select class="select_year form-control"><option value="">Year</option><option value="2019">2019</option><option value="2020">2020</option><option value="2021">2021</option></select></div>'
        );

        var years = '';
        var month = '';
        $(".select_year").on("change", function(event) {
            years = this.value;
        });
           if (years == '') {
                years = y;
            }
            if (month == '') {
                month = m;
            }
        
        //SELECT MONTH
        $(".select_month").on("change", function(event) {
            month = this.value;
            $('#calendarPresent').fullCalendar('changeView', 'month', month);
            $('#calendarPresent').fullCalendar('gotoDate', years + "-" + month + "-1");
            // if (years == '') {
            //     years = y;
            // }
            if (month == '') {
                month = m;
            }
            //PRESENT LEFT 1
            var x = new Date(years + "-" + month + "-1");
            x.setDate(1);
            x.setMonth(x.getMonth() - 1);
            $('#calendar1').fullCalendar('gotoDate', x);
            cal0.fullCalendar({
                header: {
                    left: 'title',
                    center: '',
                    right: ''
                },
                defaultDate: x,
                navLinks: false, // can click day/week names to navigate views
                editable: false,
                events: "fetch-event.php",
                displayEventTime: false,
                eventRender: function(event, element, view) {
                    if (event.allDay === 'true') {
                        event.allDay = true;
                    } else {
                        event.allDay = false;
                    }
                },
                selectable: true,
                selectHelper: true,
                viewRender: function(view, element) {
                    cur = view.intervalStart;
                    d = moment(cur).add('months', 1);
                    cal1.fullCalendar('gotoDate', d);
                }
            });

            // //PRESENT LEFT 2
            var y = new Date(years + "-" + month + "-1");
            y.setDate(1);
            y.setMonth(y.getMonth() - 2);
            $('#calendar3').fullCalendar('gotoDate', y);
            cal2.fullCalendar({
                header: {
                    left: 'title',
                    center: '',
                    right: ''
                },
                defaultDate: y,
                navLinks: false, // can click day/week names to navigate views
                editable: false,
                events: "fetch-event.php",
                displayEventTime: true,
                eventRender: function(event, element, view) {
                    if (event.allDay === 'true') {
                        event.allDay = true;
                    } else {
                        event.allDay = false;
                    }
                },
                selectable: true,
                selectHelper: true,
                viewRender: function(view, element) {
                    cur = view.intervalStart;
                    d = moment(cur).add('months', 1);
                    cal0.fullCalendar('gotoDate', d);
                }
            });
        });
    });


    $(document).ready(function() {
    // var calendar = $('#calendar').fullCalendar({
    //     editable: true,
    //     events: "fetch-event.php",
    //     displayEventTime: false,
    //     eventRender: function (event, element, view) {
    //         if (event.allDay === 'true') {
    //             event.allDay = true;
    //         } else {
    //             event.allDay = false;
    //         }
    //     },
    //     selectable: true,
    //     selectHelper: true,
    //     select: function (start, end, allDay) {
    //         var title = prompt('Event Title:');
    //         if (title) {
    //             var start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
    //             var end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");

    //             $.ajax({
    //                 url: 'add-event.php',
    //                 data: 'title=' + title + '&start=' + start + '&end=' + end,
    //                 type: "POST",
    //                 success: function (data) {
    //                     displayMessage("Added Successfully");
    //                 }
    //             });
    //             calendar.fullCalendar('renderEvent',
    //                     {
    //                         title: title,
    //                         start: start,
    //                         end: end,
    //                         allDay: allDay

    //                     },
    //             true
    //                     );
    //         }
    //         calendar.fullCalendar('unselect');
    //   },

        //   editable: true,
        // eventDrop: function (event, delta) {
        //             //var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
        //             var start = null;
        //             var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
        //             $.ajax({
        //                 url: 'edit-event.php',
        //                 data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id + '&workdate=' + event.workday,
        //                 type: "POST",
        //                 success: function (response) {
        //                     // console.log("RESPONSE==>",response);
        //                     //if(response = 'HAVEHOLIDAY'){
        //                         //alert("Holiday is not Finishday");
        //                         //swal("Success", "Save Data Success!", "success");

        //                     //}else{
        //                         displayMessage("Updated Successfully");
        //                     //}
        //                     setTimeout((function() {
        //                     window.location.reload();
        //                     }), 300);
        //                     //displayMessage("Updated Successfully");
        //                 }
        //             });
        //         },

    //     //CHECK TO DELETE
    //     // eventClick: function (event) {
    //     //     var deleteMsg = confirm("Do you really want to delete?");
    //     //     if (deleteMsg) {
    //     //         $.ajax({
    //     //             type: "POST",
    //     //             url: "delete-event.php",
    //     //             data: "&id=" + event.id,
    //     //             success: function (response) {
    //     //                 if(parseInt(response) > 0) {
    //     //                     $('#calendar').fullCalendar('removeEvents', event.id);
    //     //                     displayMessage("Deleted Successfully");
    //     //                 }
    //     //             }
    //     //         });
    //     //     }
    //     // }

    // });
    });



    function displayMessage(message) {
        $(".response").html("<div class='success'>" + message + "</div>");
        setInterval(function() {
            $(".success").fadeOut();
        }, 1000);
    }
    </script>

    <style>
    body {
        margin-top: 50px;
        text-align: center;
        font-size: 12px;
        font-family: "Lucida Grande", Helvetica, Arial, Verdana, sans-serif;
    }

    #calendar {
        width: 700px;
        margin: 0 auto;
    }

    .response {
        height: 60px;
    }

    .success {
        background: #cdf3cd;
        padding: 10px 60px;
        border: #c3e6c3 1px solid;
        display: inline-block;
    }
    

  .clear-pad{
    padding-left: 0px;
    padding-right: 0px;
   }

    </style>


</head>

<body>

    <h2>Planning Event</h2>

    <div class="container">
        <div class='selected-years'></div>
        <div class="row">
            <div class="col-sm-4 col-md-4">
                <div id="calendar3"></div>
            </div>
            <div class="col-sm-4 col-md-4">
                <div id="calendar1"></div>
            </div>
            <div class="col-sm-4 col-md-4">
                <div id='calendarPresent'></div>
            </div>
        </div>



    </div>











    <!-- 
<div id="calendar2"></div>
<div id="calendar3"></div> -->





</body>

</html>