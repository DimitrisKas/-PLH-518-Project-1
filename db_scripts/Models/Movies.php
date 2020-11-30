<?php

class Movie
{
    public string $id;
    public string $title;

    // TODO: mktime(hour, minute, second, month, day, year)
    public string $start_date;
    public string $end_date;
    public string $cinema_name;
    public string $category;

    const ID_PREFIX = "m";

    public function __construct($title, $start_date, $end_date, $cinema_name, $category)
    {
        $this->generateID();
        $this->title = $title;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->cinema_name = $cinema_name;
        $this->category = $category;
    }

    public function addToDB():bool
    {
        if (empty($this->id))
        {
            logger("ID was empty.");
            return false;
        }
        if (empty($this->title))
        {
            logger("Title was empty.");
            return false;
        }

        $conn = OpenCon(true);

        $sql_str = "INSERT INTO movies VALUES(?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_str);

        if (!$stmt->bind_param("ssssss", $this->id,$this->title,$this->start_date,$this->end_date,$this->cinema_name,$this->category))
            logger("Binding error while adding Movie.");

        if (!$stmt->execute())
        {
            logger("Add Movie failed: " . $stmt->error . ".");
            $succes_flag = false;
        }
        else
        {
            logger("Added Movie successfully!.");
            $succes_flag = true;
        }

        $stmt->close();
        CloseCon($conn);
        return $succes_flag;
    }

    private function generateID()
    {
        do {
            $this->id = getRandomString(9, $this::ID_PREFIX);
        } while($this->checkIfUniqueID() === false);
    }

    public function checkIfUniqueID():bool
    {
        $conn = OpenCon(true);

        $sql_str = "SELECT ID FROM movies WHERE id=?";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("s",$id);
        $id = $this->id;

        if (!$stmt->execute())
            logger("Check Movie ID failed " . $stmt->error . ".");

        if ($stmt->affected_rows === 1)
            return false;
        else
            return true;
    }

    public static function CreateExistingMovieObj($id, $name, $start_date, $end_date, $cinema_name, $category):Movie
    {
        $movie = new Movie($name, $start_date, $end_date, $cinema_name, $category);
        $movie->id = $id;
        return $movie;
    }

    public static function EditMovie($id, $title, $start_date, $end_date, $cinema_name, $category):bool
    {
        $conn = OpenCon(true);

        $sql_str = "UPDATE movies SET TITLE=?, STARTDATE=?, ENDDATE=?, CINEMANAME=?, CATEGORY=? WHERE id=?";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("ssssss", $title,$start_date, $end_date, $cinema_name, $category, $id);

        if (!$stmt->execute())
        {
            logger("Edit Movie failed " . $stmt->error);
            $success = false;
        }
        else
        {
            logger("Edited Movie successfully!");
            $success = true;
        }

        // Cleanup
        $stmt->close();
        CloseCon($conn);

        return $success;
    }

    public static function DeleteMovie(string $id)
    {
        $conn = OpenCon(true);

        $sql_str = "DELETE FROM movies WHERE id=?";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("s",$id);

        if (!$stmt->execute())
        {
            logger("Remove Movie failed " . $stmt->error);
            $success = false;
        }
        else
        {
            logger("Removed Movie successfully!");
            $success = true;
        }

        // Clean up
        $stmt->close();
        CloseCon($conn);

        return $success;
    }

    public static function GetAllMovies():array {
        $conn = OpenCon(true);

        $sql_str = "SELECT * FROM Movies";
        $stmt = $conn->prepare($sql_str);

        if (!$stmt->execute())
            logger("Get all movies failed " . $stmt->error);

        $result = $stmt->get_result();

        $num_of_rows = $result->num_rows;
        logger("Found " . $num_of_rows . " movies.");

        $ret_array = array();
        while ($row = $result->fetch_assoc()) {

            // Create object and append to return array
            $movie = Movie::CreateExistingMovieObj(
                $row['ID'], $row['TITLE'], $row['STARTDATE'], $row['ENDATE'],
                $row['CINEMANAME'], $row['CATEGORY']);
            $ret_array[] = $movie;
        }

        $stmt->free_result();
        $stmt->close();

        CloseCon($conn);

        return $ret_array;
    }

    public static function GetAllOwnerMovies(string $user_id):array
    {
        $conn = OpenCon(true);

        $sql_str = "SELECT m.* FROM movies m JOIN cinemas c ON c.OWNER = ? AND c.NAME = m.CINEMANAME";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("s", $user_id);

        if (!$stmt->execute())
            logger("Get all movies failed " . $stmt->error);

        $result = $stmt->get_result();

        $num_of_rows = $result->num_rows;
        logger("Found " . $num_of_rows . " movies.");

        $ret_array = array();
        while ($row = $result->fetch_assoc()) {
            $movie = Movie::CreateExistingMovieObj(
                $row['ID'], $row['TITLE'], $row['STARTDATE'], $row['ENDDATE'],
                $row['CINEMANAME'], $row['CATEGORY']);
            $ret_array[] = $movie;
        }

        $stmt->free_result();
        $stmt->close();

        CloseCon($conn);

        return $ret_array;
    }
}
