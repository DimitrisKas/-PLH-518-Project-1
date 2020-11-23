
<?php

class Movie
{
    public string $id;
    public string $title;
    public string $start_date;
    public string $end_date;
    public string $cinema_name;
    public string $category;

    const MOVIE_ID_PREFIX = "m";

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
            echo "ID was empty\n";
            return false;
        }
        if (empty($this->title))
        {
            echo "Title was empty\n";
            return false;
        }

        $conn = OpenCon(true);

        $sql_str = "INSERT INTO movies VALUES(?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql_str);

        if (!$stmt->bind_param("ssssss", $id,$title,$start_date,$end_date,$cinema_name,$category))
            echo "Binding error while Adding Movie\n";

        $id = $this->id;
        $title = $this->title;
        $start_date = $this->start_date;
        $end_date = $this->end_date;
        $cinema_name = $this->cinema_name;
        $category = $this->category;

        if (!$stmt->execute())
            echo "Add Movie failed: " . $stmt->error . "\n";
        else
            echo "Added Movie successfully!\n";

        $stmt->close();
        CloseCon($conn);
    }

    public static function removeFromDB(string $id)
    {
        $conn = OpenCon(true);

        $sql_str = "DELETE FROM Users WHERE id=?";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("s",$id);

        if (!$stmt->execute())
            echo "Remove User failed " . $stmt->error;
        else
            echo "Removed user successfully!";

        $stmt->close();

        CloseCon($conn);

    }

    private function generateID()
    {
        do {
            $this->id = getRandomString(9, Movie::MOVIE_ID_PREFIX);
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
            echo "Check Movie ID failed " . $stmt->error . "\n";

        if ($stmt->affected_rows === 1)
            return false;
        else
            return true;
    }

}
