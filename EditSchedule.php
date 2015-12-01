<html>
    <head>
        <title>Schedule Details</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="Status.css"/>
    </head>
    <body>
        <div>
            <div>
                <h1>Railway E-Ticketing Service</h1>
            </div>
            <div>
                <table style="border:1px solid black; border-collapse:collapse;" width="1350" height="100"> 
                    <tr>                   
                        <?php
                        include ("Admin.php");
                        session_start();
                        $id = $_POST["scheduleNo"];
                        $arrival = $_POST["arrive"];
                        $departure = $_POST["depart"];

                        $admin = new Admin();
                        $admin->updateScheduleTime($id, $arrival, $departure);

                        echo 'Here are the details of the Schedules.';
                        $admin->getSchedule();
                        ?>
                    </tr>
                </table>
            </div>
            <div>
                <a href="Admin_Interface.html">Go back to the main page</a>
            </div>
        </div>
    </body>
</html>

