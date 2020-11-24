
<?php

class Favorite
{
    public string $id;
    public string $owner;
    public string $name;

    const ID_PREFIX = "m";

    public function __construct($owner, $name)
    {
        $this->generateID();
        $this->owner = $owner;
        $this->name = $name;
    }

    public function addToDB():bool
    {
        if (empty($this->id))
        {
            logger("ID was empty");
            return false;
        }

        if (empty($this->name))
        {
            logger("Cinema name was empty");
            return false;
        }

        if ($this->checkIfAlreadyExists())
            logger("Cinema already exists!");

        $conn = OpenCon(true);

        $sql_str = "INSERT INTO cinemas VALUES(?, ?, ?)";

        $stmt = $conn->prepare($sql_str);

        if (!$stmt->bind_param("sss", $id,$user_id, $movie_id))
            logger("Binding error while Adding Cinema");

        $id = $this->id;
        $user_id = $this->owner;
        $movie_id = $this->name;

        if (!$stmt->execute())
        {
            logger("Add Cinema failed: " . $stmt->error);
            $stmt->close();
            CloseCon($conn);
            return false;
        }
        else
        {
            logger("Added Cinema successfully.");
            $stmt->close();
            CloseCon($conn);
            return true;
        }
    }

    public static function removeFromDB(string $id)
    {
        $conn = OpenCon(true);

        $sql_str = "DELETE FROM cinemas WHERE id=?";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("s",$id);

        if (!$stmt->execute())
            logger("Remove Cinema failed " . $stmt->error);
        else
            logger("Removed Cinema successfully!");

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

        $sql_str = "SELECT ID FROM cinemas WHERE id=?";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("s",$id);
        $id = $this->id;

        if (!$stmt->execute())
            logger("Check Cinemas ID failed " . $stmt->error);

        if ($stmt->affected_rows === 1)
            return false;
        else
            return true;
    }

    public function checkIfAlreadyExists():bool
    {
        $conn = OpenCon(true);

        $sql_str = "SELECT ID FROM movies WHERE CINEMANAME=?";
        $stmt = $conn->prepare($sql_str);
        $stmt->bind_param("s",$movie_id);
        $movie_id = $this->name;

        if (!$stmt->execute())
            logger("Check for duplicate Cinema failed " . $stmt->error);

        if ($stmt->affected_rows === 1)
            return false;
        else
            return true;
    }

}
