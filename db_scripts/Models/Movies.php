
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

        if (!$stmt->bind_param("ssssss", $id,$title,$start_date,$end_date,$cinema_name,$category))
            logger("Binding error while adding Movie.");

        $id = $this->id;
        $title = $this->title;
        $start_date = $this->start_date;
        $end_date = $this->end_date;
        $cinema_name = $this->cinema_name;
        $category = $this->category;

        if (!$stmt->execute())
            logger("Add Movie failed: " . $stmt->error . ".");
        else
            logger("Added Movie successfully!.");

        $stmt->close();
        CloseCon($conn);
    }

    public static function removeFromDB(string $id)
    {
        $conn = OpenCon(true);

        $sql_str = "DELETE FROM movies WHERE id=?";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("s",$id);

        if (!$stmt->execute())
            logger("Remove Movie failed " . $stmt->error);
        else
            logger("Removed Movie successfully!");

        $stmt->close();

        CloseCon($conn);

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

}
