<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of client
 *
 * @author eshan
 */
include("connect.php");
class client {

    public $c;
    public $conn;

    public function __construct() {
        $this->c = new connect();
        $this->conn = $this->c->con();
    }

    public function submit($name, $pass, $phone, $address, $mail) {
        if (!self::exists($name)) {
            $query1 = "Insert into login values('" . $name . "','" . $pass . "','1');";
            $query2 = "Insert into customer values(NULL,'" . $name . "','" . $phone . "','" . $address . "','" . $mail . "');";
            $r = $this->c->insert($this->conn, $query1);
            $s = $this->c->insert($this->conn, $query2);
            return true;
        }
        return false;
    }

    public function exists($name) {
        $query = "Select Name from login where Name='" . $name . "';";
        $result = $this->c->execute($this->conn, $query);
        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    public function getName($id) {
        $query = "Select name from customer where id='" . $id . "';";
        $result = $this->c->execute($this->conn, $query);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row["name"];
        }
        return 0;
    }

    public function details($name) {
        if (self::exists($name)) {
            $query = "Select * from customer where Name='" . $name . "';";
            $result = $this->c->execute($this->conn, $query);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo "ID : " . $row["id"] . "</br>";
                echo "Name : " . $row["name"] . "</br>";
                echo "Phone : " . $row["phone"] . "</br>";
                echo "Address : " . $row["address"] . "</br>";
                echo "Email : " . $row["mail"] . "</br>";
                echo "Booked : " . $row["booked"] . "</br>";
            }
        }
    }

    public function getSchedule($source, $destination) {
        $_SESSION["schedule"]=NULL;
        $query = "Select * from route,schedule,station,train where route.source=\"" . $source . "\" and route.destination=\"" . $destination . "\" and route.id=schedule.RouteNo and schedule.station=station.id and schedule.trainNo=train.no and train.freeSeat>0;";
        echo $query;
        $result = $this->c->execute($this->conn, $query);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<a href="details.php?name=' . $row["name"] . '&schedule=' . $row["ScheduleNo"] . '">' . $row["name"] . "  " . $row["Location"] . "  " . $row["Arrival"] . "  " . $row["Departure"] . "  " . $row["trainName"] . "  " . $row["freeSeat"] . "</a></br>";
            }
        } else {
            echo "0 results\n";
        }
    }

    public function CheckBooking($schedule, $class, $seatAmounts) {
        $query = "Select COUNT(*) from seat where seat.empty=0 and seat.schedule='" . $schedule . "' and seat.ClassNo='" . $class . "';";
        //echo $query;
        $result = $this->c->execute($this->conn, $query);
        $row = $result->fetch_assoc();
        // echo $row["COUNT(*)"]." ".$seatAmounts;
        if ((int) $row["COUNT(*)"] >= $seatAmounts) {
            $query = "Select TicketPrice from class where classNo='" . $class . "';";
            $result = $this->c->execute($this->conn, $query);
            $r = $result->fetch_assoc();
            echo "Total Cost : " . $seatAmounts * $r["TicketPrice"]."</br>";
            return $seatAmounts * $r["TicketPrice"];
        } else {
            echo "Not enought seats </br>";
            return -1;
        }
    }

    public function makeBooking($schedule, $class, $seatAmounts, $date, $user) {
        $query = "Select COUNT(*) from seat where seat.empty=0 and seat.schedule='" . $schedule . "' and seat.ClassNo='" . $class . "';";
        // echo $query;
        $result = $this->c->execute($this->conn, $query);
        $row = $result->fetch_assoc();
        // echo $row["COUNT(*)"]." ".$seatAmounts;
        // 
        //echo $seatAmounts;
        $cost = self::checkBooking($schedule, $class, $seatAmounts);
        if ((int) $row["COUNT(*)"] >= $seatAmounts) {
            $count = 0;
            while ($count < $seatAmounts) {
                self::booking($schedule, $class, $cost, $date, $user);
                $count++;
            }
        }
        echo "Booking Made";
    }

    public function booking($schedule, $class,$cost, $date, $user) {
        $query = "Select * from seat,schedule,class where schedule.ScheduleNo=" . $schedule . " and schedule.ScheduleNo=seat.schedule and seat.empty=0 and class.classNo='" . $class . "' and class.classNo=seat.ClassNo;";
        ;
        //echo $query;
        $result = $this->c->execute($this->conn, $query);
        
        if ($cost != -1) {
            $row = $result->fetch_assoc();
            $query = "Insert into booking values(NULL,'" . $date . "','" . $row["SeatNo"] . "','" . $user . "','0','" . $row["ScheduleNo"] . "');";
            //echo $query;
            $this->c->insert($this->conn, $query);
            $query="Update seat set empty=1 where SeatNo='".$row["SeatNo"]."';";
            $this->c->insert($this->conn, $query);
        } else {
            echo "Not enought </br>";
        }
    }

}
